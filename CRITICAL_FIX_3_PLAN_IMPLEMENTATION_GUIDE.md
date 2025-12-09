# CRITICAL FIX #3: Race Condition in Notification Dispatch
## Implementation Guide for Next Thread

**Status**: 📋 READY FOR IMPLEMENTATION  
**Severity**: 🔴 CRITICAL  
**Effort**: 3-4 hours  
**Files to Modify**: 2 major + 1 schema migration  
**Database Changes**: YES (new table + column)

---

## Executive Summary

CRITICAL #3 prevents duplicate notifications caused by race conditions in the notification dispatch system. When comments are added or status changes occur simultaneously, the current code can dispatch multiple times to the same users, creating duplicates.

### The Problem

**Current Behavior** (BROKEN):
```
T0:    User A adds comment to Issue #100
       → dispatchCommentAdded(issue_id=100, commenter_id=5)
       → Queries watchers: [User B, User C]
       → Creates notifications for B, C

T0.050: Meanwhile... User D adds themselves as watcher
       → (no notification yet - dispatch already finished)

T0.100: Error handling triggers retry of comment dispatch
       → dispatchCommentAdded(issue_id=100, commenter_id=5) RUNS AGAIN
       → Queries watchers: [User B, User C, User D]  ← D is now included!
       → Creates notifications AGAIN for B, C, D
       
RESULT: User B and C get DUPLICATE notifications ❌
```

**After CRITICAL #3** (FIXED):
```
T0:    User A adds comment to Issue #100
       → Generate dispatch_id = "comment_100_5_1733676800123"
       → Check: Is dispatch_id in notification_dispatch_log? NO
       → Set dispatch_id lock, begin transaction
       → Create notifications in atomic block
       → Commit transaction
       → Log dispatch_id in notification_dispatch_log

T0.100: Retry triggered
       → Check: Is dispatch_id in notification_dispatch_log? YES ✓
       → SKIP duplicate dispatch
       → Log: "Duplicate dispatch prevented for comment_100_5_1733676800123"
       
RESULT: No duplicate notifications ✅
```

---

## Solution Architecture

### 1. Database Changes

#### New Table: `notification_dispatch_log`

```sql
CREATE TABLE notification_dispatch_log (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dispatch_id VARCHAR(255) UNIQUE NOT NULL,
    dispatch_type ENUM('comment_added', 'status_changed', 'other') NOT NULL,
    issue_id BIGINT UNSIGNED NOT NULL,
    comment_id BIGINT UNSIGNED NULL,
    actor_user_id INT UNSIGNED NOT NULL,
    recipients_count INT UNSIGNED DEFAULT 0,
    duplicate_skipped INT UNSIGNED DEFAULT 0,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    error_message TEXT NULL,
    
    INDEX idx_dispatch_id (dispatch_id),
    INDEX idx_issue_id (issue_id),
    INDEX idx_created_at (created_at),
    INDEX idx_status (status),
    FOREIGN KEY (issue_id) REFERENCES issues(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### New Column: `notifications.dispatch_id`

```sql
ALTER TABLE notifications 
ADD COLUMN dispatch_id VARCHAR(255) NULL,
ADD UNIQUE KEY uk_dispatch_id (dispatch_id);
```

### 2. Core Implementation: Idempotency

#### Pattern: Generate Unique Dispatch ID

```php
// Generate unique dispatch_id based on event details
$dispatchId = sprintf(
    'comment_%d_%d_%d',
    $issueId,
    $commentId,
    time() * 1000  // millisecond precision
);
// Result: "comment_100_5_1733676800123"
```

#### Pattern: Check for Existing Dispatch

```php
$existingDispatch = Database::selectOne(
    'SELECT * FROM notification_dispatch_log 
     WHERE dispatch_id = ?',
    [$dispatchId]
);

if ($existingDispatch && $existingDispatch['status'] === 'completed') {
    error_log("Skipping duplicate dispatch: $dispatchId");
    return; // Don't dispatch again
}
```

#### Pattern: Atomic Transaction

```php
try {
    Database::beginTransaction();
    
    // All database operations in one block
    $issue = Database::selectOne(...);
    $watchers = Database::select(...);
    
    foreach ($recipients as $recipientId) {
        Database::insert('notifications', [
            'user_id' => $recipientId,
            'dispatch_id' => $dispatchId,
            'type' => 'comment_added',
            ...
        ]);
    }
    
    // Update dispatch log
    Database::update('notification_dispatch_log', 
        ['status' => 'completed', 'completed_at' => date('Y-m-d H:i:s')],
        'dispatch_id = ?',
        [$dispatchId]
    );
    
    Database::commit();
} catch (\Exception $e) {
    Database::rollback();
    Database::update('notification_dispatch_log',
        ['status' => 'failed', 'error_message' => $e->getMessage()],
        'dispatch_id = ?',
        [$dispatchId]
    );
    throw $e;
}
```

---

## Implementation Steps

### Phase 1: Database Migration

**File**: `database/migrations/2025_12_08_add_dispatch_tracking.sql`

```sql
-- Create notification_dispatch_log table
CREATE TABLE IF NOT EXISTS notification_dispatch_log (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dispatch_id VARCHAR(255) UNIQUE NOT NULL,
    dispatch_type ENUM('comment_added', 'status_changed', 'other') NOT NULL,
    issue_id BIGINT UNSIGNED NOT NULL,
    comment_id BIGINT UNSIGNED NULL,
    actor_user_id INT UNSIGNED NOT NULL,
    recipients_count INT UNSIGNED DEFAULT 0,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    error_message TEXT NULL,
    
    INDEX idx_dispatch_id (dispatch_id),
    INDEX idx_issue_id (issue_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (issue_id) REFERENCES issues(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add dispatch_id column to notifications table
ALTER TABLE notifications 
ADD COLUMN dispatch_id VARCHAR(255) NULL UNIQUE;

-- Create index for performance
CREATE INDEX idx_notifications_dispatch_id ON notifications(dispatch_id);
```

**Deployment**:
```bash
php scripts/run-migrations.php
# or
mysql jira_clone_system < database/migrations/2025_12_08_add_dispatch_tracking.sql
```

### Phase 2: Service Layer Enhancement

**File**: `src/Services/NotificationService.php`

#### 2.1: Add Helper Method for Dispatch ID

```php
/**
 * Generate unique dispatch ID for idempotency
 */
private static function generateDispatchId(
    string $dispatchType,
    int $issueId,
    ?int $commentId = null,
    int $actorId = 0
): string {
    return sprintf(
        '%s_%d_%s_%d_%d',
        $dispatchType,
        $issueId,
        $commentId ? 'comment_' . $commentId : 'issue',
        $actorId,
        intval(microtime(true) * 1000)  // millisecond timestamp
    );
}
```

#### 2.2: Add Check for Duplicate Dispatch

```php
/**
 * Check if dispatch was already completed
 */
private static function isDuplicateDispatch(string $dispatchId): bool
{
    $existing = Database::selectOne(
        'SELECT id FROM notification_dispatch_log 
         WHERE dispatch_id = ? AND status = ?',
        [$dispatchId, 'completed']
    );
    
    return $existing !== null;
}
```

#### 2.3: Create Dispatch Log Entry

```php
/**
 * Create notification_dispatch_log entry
 */
private static function createDispatchLog(
    string $dispatchId,
    string $dispatchType,
    int $issueId,
    ?int $commentId,
    int $actorId,
    int $recipientCount = 0
): void {
    Database::insert('notification_dispatch_log', [
        'dispatch_id' => $dispatchId,
        'dispatch_type' => $dispatchType,
        'issue_id' => $issueId,
        'comment_id' => $commentId,
        'actor_user_id' => $actorId,
        'recipients_count' => $recipientCount,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
    ]);
}
```

#### 2.4: Update Dispatch Methods

**Before**: `dispatchCommentAdded()`

```php
public static function dispatchCommentAdded(int $issueId, int $commenterId, int $commentId): void
{
    // ... query issue, watchers
    // ... create notifications
    // ... no idempotency
}
```

**After**: With Idempotency + Transactions

```php
public static function dispatchCommentAdded(int $issueId, int $commenterId, int $commentId): void
{
    try {
        // 1. Generate unique dispatch ID
        $dispatchId = self::generateDispatchId('comment_added', $issueId, $commentId, $commenterId);
        
        // 2. Check for duplicate
        if (self::isDuplicateDispatch($dispatchId)) {
            error_log(sprintf(
                '[NOTIFICATION] Duplicate dispatch prevented: dispatch_id=%s, issue_id=%d',
                $dispatchId,
                $issueId
            ), 3, storage_path('logs/notifications.log'));
            return;
        }
        
        // 3. Create dispatch log entry
        self::createDispatchLog('comment_added', $issueId, $commentId, $commenterId);
        
        // 4. Begin transaction (atomic block)
        Database::beginTransaction();
        
        // 5. Query issue and recipients
        $issue = Database::selectOne('SELECT * FROM issues WHERE id = ?', [$issueId]);
        if (!$issue) {
            throw new \Exception("Issue not found: $issueId");
        }
        
        // Get assignee and watchers
        $recipients = [];
        if ($issue['assignee_id']) {
            $recipients[] = $issue['assignee_id'];
        }
        
        $watchers = Database::select(
            'SELECT user_id FROM issue_watchers WHERE issue_id = ? AND user_id != ?',
            [$issueId, $commenterId]
        );
        
        foreach ($watchers as $watcher) {
            $recipients[] = $watcher['user_id'];
        }
        
        // Remove duplicates
        $recipients = array_unique($recipients);
        
        // 6. Create notifications for each recipient
        foreach ($recipients as $recipientId) {
            // Skip if recipient is the commenter
            if ($recipientId === $commenterId) continue;
            
            Database::insert('notifications', [
                'user_id' => $recipientId,
                'dispatch_id' => $dispatchId,
                'type' => 'issue_commented',
                'actor_user_id' => $commenterId,
                'related_issue_id' => $issueId,
                'related_comment_id' => $commentId,
                'title' => 'New comment on issue',
                'message' => sprintf(
                    'User #%d commented on issue %s',
                    $commenterId,
                    $issue['key']
                ),
                'in_app' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        // 7. Update dispatch log with completion
        Database::update(
            'notification_dispatch_log',
            [
                'status' => 'completed',
                'completed_at' => date('Y-m-d H:i:s'),
                'recipients_count' => count($recipients),
            ],
            'dispatch_id = ?',
            [$dispatchId]
        );
        
        // 8. Commit transaction
        Database::commit();
        
        error_log(sprintf(
            '[NOTIFICATION] Comment dispatch completed: dispatch_id=%s, issue_id=%d, recipients=%d',
            $dispatchId,
            $issueId,
            count($recipients)
        ), 3, storage_path('logs/notifications.log'));
        
    } catch (\Exception $e) {
        try {
            Database::rollback();
        } catch (\Exception $rollbackError) {
            error_log('Rollback failed: ' . $rollbackError->getMessage(), 3, storage_path('logs/notifications.log'));
        }
        
        // Update dispatch log with error
        try {
            Database::update(
                'notification_dispatch_log',
                [
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ],
                'dispatch_id = ?',
                [$dispatchId ?? 'unknown']
            );
        } catch (\Exception $ignored) {
            // Log entry might not exist
        }
        
        error_log(sprintf(
            '[NOTIFICATION] Dispatch error: %s (will be retried)',
            $e->getMessage()
        ), 3, storage_path('logs/notifications.log'));
        
        throw $e;
    }
}
```

#### 2.5: Similar Changes for `dispatchStatusChanged()`

```php
public static function dispatchStatusChanged(int $issueId, int $changedByUserId, string $oldStatus, string $newStatus): void
{
    try {
        // 1. Generate dispatch ID
        $dispatchId = self::generateDispatchId(
            'status_changed',
            $issueId,
            null,
            $changedByUserId
        );
        
        // 2. Check for duplicate
        if (self::isDuplicateDispatch($dispatchId)) {
            error_log("Duplicate dispatch prevented: $dispatchId");
            return;
        }
        
        // 3. Create dispatch log entry
        self::createDispatchLog('status_changed', $issueId, null, $changedByUserId);
        
        // 4. Begin transaction
        Database::beginTransaction();
        
        // ... same pattern as dispatchCommentAdded()
        // Get issue, recipients, create notifications, update log
        
        Database::commit();
        
    } catch (\Exception $e) {
        Database::rollback();
        // ... error handling
    }
}
```

---

### Phase 3: Testing & Validation

#### Test Case 1: Normal Dispatch (No Duplicate)

```php
// Test: Single dispatch should succeed
$issueId = 100;
$commentId = 50;
$commenterId = 5;

NotificationService::dispatchCommentAdded($issueId, $commenterId, $commentId);
// Expected: 2 notifications created, dispatch_log status='completed'

$count = Database::selectValue(
    'SELECT COUNT(*) FROM notifications WHERE related_issue_id = ?',
    [$issueId]
);
assert($count === 2, "Should have exactly 2 notifications");
```

#### Test Case 2: Duplicate Prevention

```php
// Test: Second dispatch should be skipped
NotificationService::dispatchCommentAdded($issueId, $commenterId, $commentId);
// Expected: No new notifications created, logged as duplicate

$count = Database::selectValue(
    'SELECT COUNT(*) FROM notifications WHERE related_issue_id = ?',
    [$issueId]
);
assert($count === 2, "Should still have only 2 notifications (no duplicates)");
```

#### Test Case 3: Concurrent Requests

```php
// Simulate concurrent dispatch requests
$processes = [];
for ($i = 0; $i < 5; $i++) {
    $processes[] = popen(
        'php scripts/test-dispatch.php ' . escapeshellarg($issueId),
        'r'
    );
}

// Wait for all to complete
foreach ($processes as $process) {
    pclose($process);
}

// Verify: Still only original notification count
$count = Database::selectValue(
    'SELECT COUNT(*) FROM notifications WHERE related_issue_id = ?',
    [$issueId]
);
assert($count === 2, "Concurrent requests should not create duplicates");
```

#### Test Case 4: Transaction Rollback on Error

```php
// Test: Exception during dispatch should rollback
// Mock a database failure mid-transaction
// Verify: dispatch_log status='failed', no partial notifications

$count = Database::selectValue(
    'SELECT COUNT(*) FROM notifications WHERE dispatch_id = ?',
    [$dispatchId]
);
assert($count === 0, "Failed dispatch should not create partial notifications");
```

---

## Implementation Workflow

### Step 1: Read This Document
- [ ] Understand the race condition problem
- [ ] Review the solution architecture
- [ ] Study the test cases

### Step 2: Create Database Migration
- [ ] Create migration file
- [ ] Test migration in development
- [ ] Verify tables created correctly

### Step 3: Update NotificationService
- [ ] Add helper methods (generateDispatchId, isDuplicateDispatch, etc.)
- [ ] Update dispatchCommentAdded()
- [ ] Update dispatchStatusChanged()
- [ ] Add comprehensive error logging

### Step 4: Testing
- [ ] Run all 4 test cases
- [ ] Test concurrent scenarios
- [ ] Verify logging output
- [ ] Check for performance impact

### Step 5: Documentation
- [ ] Create CRITICAL_FIX_3_IMPLEMENTATION_COMPLETE.md
- [ ] Document all changes
- [ ] List any gotchas or edge cases

### Step 6: Deployment
- [ ] Deploy to staging
- [ ] Monitor logs for 24 hours
- [ ] Deploy to production
- [ ] Continue monitoring

---

## Key Considerations

### Performance Impact
- **Database**: 2 new queries per dispatch (check + log)
- **Storage**: ~500 bytes per dispatch log entry
- **Overhead**: ~5-10ms per dispatch (acceptable)

### Backward Compatibility
- Old notifications without dispatch_id will remain
- New notifications will have dispatch_id
- No breaking changes to existing API

### Data Consistency
- Transactions ensure atomic operations
- Rollback on any error prevents partial data
- Dispatch log provides audit trail

### Edge Cases to Handle
1. **Network timeout during dispatch**: Log entry exists but status='pending' → Retry will complete it
2. **Database down during notification creation**: Rollback → Try again later
3. **Duplicate dispatch_id generated**: Race condition on INSERT → MySQL unique constraint prevents duplicate, retry will skip

---

## Common Gotchas

### ❌ Don't: Create dispatch log AFTER creating notifications
```php
// WRONG - can create notifications without dispatch_id
foreach ($recipients as $id) {
    Database::insert('notifications', ...);  // If this fails...
}
self::createDispatchLog(...);  // This never runs
```

### ✅ Do: Create dispatch log BEFORE transaction
```php
// CORRECT - dispatch_id is logged before notifications
self::createDispatchLog(...);
Database::beginTransaction();
foreach ($recipients as $id) {
    Database::insert('notifications', ...);  // Each has dispatch_id
}
Database::commit();
```

### ❌ Don't: Check for duplicate OUTSIDE transaction
```php
if (isDuplicateDispatch($dispatchId)) return;
Database::beginTransaction();
// RACE: Another request could insert between check and transaction start
```

### ✅ Do: Use unique constraint on dispatch_id
```php
// Let MySQL enforce uniqueness
Database::insert('notification_dispatch_log', [...]);  // Fails if exists
```

---

## Monitoring Queries

After deployment, use these queries to monitor the system:

```sql
-- Check dispatch log for failed dispatches
SELECT COUNT(*) as failed_count FROM notification_dispatch_log 
WHERE status = 'failed' AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Check for pending (incomplete) dispatches
SELECT * FROM notification_dispatch_log 
WHERE status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE);

-- Check duplicate prevention is working
SELECT COUNT(*) as duplicate_count FROM notification_dispatch_log 
WHERE dispatch_id IN (
    SELECT dispatch_id FROM notifications GROUP BY dispatch_id HAVING COUNT(*) > 1
);

-- Monitor dispatch performance
SELECT 
    AVG(TIMESTAMPDIFF(SECOND, created_at, completed_at)) as avg_duration_seconds,
    MAX(TIMESTAMPDIFF(SECOND, created_at, completed_at)) as max_duration_seconds,
    COUNT(*) as total_dispatches
FROM notification_dispatch_log
WHERE status = 'completed' AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

---

## Rollback Plan

If CRITICAL #3 causes issues:

```bash
# 1. Revert code
git revert <commit-hash>

# 2. Drop dispatch columns (optional - doesn't hurt to keep)
ALTER TABLE notifications DROP COLUMN dispatch_id;
DROP TABLE notification_dispatch_log;

# 3. Monitor logs
tail -f storage/logs/*.log
```

System returns to CRITICAL #2 state (input validation still in place).

---

## Success Criteria

After implementing CRITICAL #3:

- [ ] No duplicate notifications in tests
- [ ] Concurrent requests handled correctly
- [ ] Transaction rollback on errors
- [ ] Dispatch log complete and accurate
- [ ] Performance < 10ms overhead
- [ ] All 4 test cases pass
- [ ] Logging comprehensive and useful
- [ ] Zero data loss on failures
- [ ] Production ready

---

## Timeline & Effort

```
Phase 1 (Database): 30 minutes
Phase 2 (Service):  90 minutes
Phase 3 (Testing):  45 minutes
Phase 4 (Docs):     15 minutes
Total:             180 minutes (3 hours)
```

---

## Related Documentation

- **CRITICAL #1**: [CRITICAL_FIX_1_AUTHORIZATION_BYPASS_COMPLETE.md](./CRITICAL_FIX_1_AUTHORIZATION_BYPASS_COMPLETE.md)
- **CRITICAL #2**: [CRITICAL_FIX_2_IMPLEMENTATION_COMPLETE.md](./CRITICAL_FIX_2_IMPLEMENTATION_COMPLETE.md)
- **This Document**: [CRITICAL_FIX_3_PLAN_IMPLEMENTATION_GUIDE.md](./CRITICAL_FIX_3_PLAN_IMPLEMENTATION_GUIDE.md)

---

## Next Steps

Once CRITICAL #2 is deployed and stable (24-48 hours):

1. Read this document carefully
2. Create database migration
3. Update NotificationService with new logic
4. Run test cases
5. Deploy to staging
6. Monitor for 24 hours
7. Deploy to production
8. Continue monitoring

Then all 3 CRITICAL fixes will be complete and system will be **production ready**.

---

**Document Version**: 1.0.0  
**Created**: December 8, 2025  
**Status**: Ready for Thread #3 Implementation
