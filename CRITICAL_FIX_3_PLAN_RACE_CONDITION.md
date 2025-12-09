# CRITICAL FIX #3: Race Condition in Notification Dispatch
## Implementation Plan for Next Thread

**Status**: PENDING  
**Severity**: 🔴 CRITICAL - Causes Duplicate Notifications  
**Estimated Effort**: 3-4 hours  
**Files to Modify**: 2 (NotificationService.php, schema migration)

---

## Executive Summary

The notification dispatch system has a critical race condition that causes **duplicate notifications** when multiple requests are processed simultaneously or when dispatch is retried.

### Current State
- ✅ Dispatch logic: Implemented (lines 489-649)
- ✅ Error handling: Complete (FIX 8)
- ✅ Retry queuing: Working
- ❌ Idempotency: MISSING (can dispatch multiple times)
- ❌ Atomicity: MISSING (non-transactional)
- ❌ Deduplication: MISSING (no check for existing notifications)

### Problem Impact
- **User Experience**: Users see the same notification 2-3 times
- **Database Bloat**: Duplicate records accumulate over time
- **Performance**: Unnecessary disk I/O and processing
- **Compliance**: Audit logs show fake activity

---

## Root Cause Analysis

### The Race Condition Scenario

**Timeline**:
```
Time    Event
----    -----
T0      Comment added to issue #100, triggers dispatchCommentAdded(100, ...)
T0.1    Query: SELECT watchers FROM issue_watchers WHERE issue_id = 100
        Result: User A, User B (2 watchers)
        
T0.15   ⚠️ RACE CONDITION STARTS
        Meanwhile: User C adds themselves as watcher
        
T0.2    Code creates notifications for A, B only
        Notification A: created, stored in DB
        Notification B: created, stored in DB
        
T0.25   ⚠️ RACE CONDITION ENDS
        Dispatch completes
        
T1      Retry logic triggers (due to timeout or error)
        Calls dispatchCommentAdded(100, ...) AGAIN
        
T1.1    Query watchers again
        Result: User A, User B, User C (now 3 watchers!)
        
T1.2    Creates notifications for A, B, C
        But A and B already have notifications!
        ❌ DUPLICATES CREATED
```

### Why This Happens

**Code Issue** (lines 489-566):
```php
public static function dispatchCommentAdded(int $issueId, ...): void {
    // No transaction wrapper
    // No idempotency key
    // No check for existing notifications
    
    // 1. Query issue
    $issue = Database::selectOne(
        'SELECT ... FROM issues WHERE id = ?',
        [$issueId]
    );
    // ⚠️ RACE: Watchers could change here
    
    // 2. Query watchers
    $watchers = Database::select(
        'SELECT ... FROM issue_watchers WHERE issue_id = ?',
        [$issueId]
    );
    
    // 3. Create notifications
    foreach ($recipients as $recipientId) {
        self::create(userId: $recipientId, ...);
        // ⚠️ No check: Does this notification already exist?
    }
    
    // ⚠️ If this function is called again before completion,
    //   duplicates will be created
}
```

**No Idempotency Protection**:
```php
// These calls create NEW notifications every time
// Even if same comment is dispatched twice
self::create(
    userId: $assigneeId,
    type: 'issue_commented',
    ...
);
```

**No Transaction Support**:
```php
// If error occurs after notification 1 is created
// but before notification 2 is created:
// - Notification 1 remains in DB
// - Retry creates notifications 1 and 2 again
// - Result: 2 notifications for same recipient
```

---

## Fix Implementation Strategy

### Phase 1: Add Idempotency Key

**Concept**: Each dispatch gets a unique ID. If we try to dispatch again with same ID, we skip it.

**Database Migration Needed**:
```sql
ALTER TABLE notifications ADD COLUMN dispatch_id VARCHAR(255) NULL UNIQUE;
CREATE INDEX idx_notifications_dispatch_id ON notifications(dispatch_id);

-- Old notification dispatch tracking table
CREATE TABLE notification_dispatch_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dispatch_id VARCHAR(255) UNIQUE NOT NULL,
    type VARCHAR(50) NOT NULL,  -- 'comment_added', 'status_changed'
    issue_id INT NOT NULL,
    comment_id INT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (issue_id) REFERENCES issues(id)
);
```

**Code Implementation**:
```php
public static function dispatchCommentAdded(
    int $issueId,
    int $commenterId,
    int $commentId
): void {
    // Create idempotency key
    $dispatchId = 'comment_' . $commentId . '_' . time();
    
    try {
        // Check if already dispatched
        $existing = Database::selectOne(
            'SELECT id FROM notifications WHERE dispatch_id = ? LIMIT 1',
            [$dispatchId]
        );
        
        if ($existing) {
            error_log(sprintf(
                '[NOTIFICATION] Skipping duplicate dispatch for comment %d on issue %d',
                $commentId,
                $issueId
            ), 3, storage_path('logs/notifications.log'));
            return;
        }
        
        // ... rest of dispatch logic
    } catch (\Exception $e) {
        // ... error handling
    }
}
```

### Phase 2: Wrap in Transaction

**Database Transaction**:
```php
public static function dispatchCommentAdded(...): void {
    try {
        Database::beginTransaction();
        
        // All queries in one transaction
        $issue = Database::selectOne(...);
        $watchers = Database::select(...);
        
        // Create all notifications
        foreach ($recipients as $recipientId) {
            // This is now atomic
            self::create(userId: $recipientId, ...);
        }
        
        // Log dispatch
        Database::insert('notification_dispatch_log', [
            'dispatch_id' => $dispatchId,
            'type' => 'comment_added',
            'issue_id' => $issueId,
            'comment_id' => $commentId,
            'user_id' => $commenterId,
        ]);
        
        Database::commit();
        
    } catch (\Exception $e) {
        Database::rollback();
        error_log(...);
        self::queueForRetry(...);
    }
}
```

### Phase 3: Deduplication Query

**Before Creating**:
```php
// Check if recipient already has notification for this comment
$existing = Database::selectOne(
    'SELECT id FROM notifications 
     WHERE user_id = ? 
     AND type = ? 
     AND related_issue_id = ? 
     AND actor_user_id = ? 
     AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)',
    [$recipientId, 'issue_commented', $issueId, $commenterId]
);

if ($existing) {
    error_log("Duplicate notification detected, skipping for user $recipientId");
    continue; // Skip this recipient
}

// Safe to create
self::create(userId: $recipientId, ...);
```

---

## Detailed Implementation Plan

### Step 1: Database Schema Update

**Migration File**: `database/migrations/add_dispatch_tracking.sql`

```sql
-- Add dispatch tracking to notifications
ALTER TABLE notifications ADD COLUMN dispatch_id VARCHAR(255) NULL;
ALTER TABLE notifications ADD UNIQUE KEY idx_dispatch_id (dispatch_id);

-- Create dispatch log for audit trail
CREATE TABLE IF NOT EXISTS notification_dispatch_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dispatch_id VARCHAR(255) NOT NULL,
    dispatch_type VARCHAR(50) NOT NULL,  -- 'comment_added', 'status_changed', 'issue_created'
    issue_id INT NOT NULL,
    comment_id INT,
    commenter_id INT,
    recipients_count INT,
    duplicate_skipped INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_dispatch_type (dispatch_type),
    INDEX idx_issue_id (issue_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (issue_id) REFERENCES issues(id) ON DELETE CASCADE
);

-- Index for fast duplicate detection
CREATE INDEX idx_notifications_lookup ON notifications(
    user_id, 
    type, 
    related_issue_id, 
    actor_user_id, 
    created_at
);
```

### Step 2: Update NotificationService

**Method**: `dispatchCommentAdded()` (lines 489-566)

```php
public static function dispatchCommentAdded(
    int $issueId,
    int $commenterId,
    int $commentId
): void {
    // Generate idempotency key
    $dispatchId = 'comment_' . $commentId . '_comment_added';
    
    try {
        // Check if already dispatched
        $existing = Database::selectOne(
            'SELECT COUNT(*) as count FROM notification_dispatch_log WHERE dispatch_id = ?',
            [$dispatchId]
        );
        
        if ($existing && $existing['count'] > 0) {
            error_log(sprintf(
                '[NOTIFICATION] Idempotency check: skipping duplicate dispatch for comment_id=%d, issue_id=%d',
                $commentId,
                $issueId
            ), 3, storage_path('logs/notifications.log'));
            return;
        }
        
        // Start transaction for atomicity
        Database::beginTransaction();
        
        $issue = Database::selectOne(
            'SELECT id, key, title, project_id, assignee_id FROM issues WHERE id = ?',
            [$issueId]
        );
        
        if (!$issue) {
            Database::rollback();
            error_log("Issue $issueId not found for comment notification");
            return;
        }
        
        // Get recipients
        $recipients = [];
        
        // Add assignee
        if ($issue['assignee_id'] && $issue['assignee_id'] !== $commenterId) {
            $recipients[] = $issue['assignee_id'];
        }
        
        // Add watchers
        $watchers = Database::select(
            'SELECT DISTINCT user_id FROM issue_watchers WHERE issue_id = ? AND user_id != ?',
            [$issueId, $commenterId]
        );
        
        foreach ($watchers as $watcher) {
            $recipients[] = $watcher['user_id'];
        }
        
        $recipients = array_unique($recipients);
        
        // Create notifications with duplicate detection
        $duplicatesSkipped = 0;
        foreach ($recipients as $recipientId) {
            // Check for recent duplicate notification
            $recentDuplicate = Database::selectOne(
                'SELECT id FROM notifications 
                 WHERE user_id = ? 
                 AND type = ? 
                 AND related_issue_id = ? 
                 AND actor_user_id = ? 
                 AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)',
                [$recipientId, 'issue_commented', $issueId, $commenterId]
            );
            
            if ($recentDuplicate) {
                $duplicatesSkipped++;
                error_log(sprintf(
                    '[NOTIFICATION] Duplicate detected: user=%d, comment=%d, skipping',
                    $recipientId,
                    $commentId
                ), 3, storage_path('logs/notifications.log'));
                continue;
            }
            
            // Safe to create
            if (self::shouldNotify($recipientId, 'issue_commented')) {
                $notificationId = self::create(
                    userId: $recipientId,
                    type: 'issue_commented',
                    title: 'New Comment',
                    message: "New comment on {$issue['key']}",
                    actionUrl: "/issues/{$issue['key']}?comment={$commentId}",
                    actorUserId: $commenterId,
                    relatedIssueId: $issueId,
                    relatedProjectId: $issue['project_id'],
                    priority: 'normal'
                );
                
                if (!$notificationId) {
                    error_log("Failed to create notification for user $recipientId");
                }
            }
        }
        
        // Log dispatch to prevent retries from duplicating
        Database::insert('notification_dispatch_log', [
            'dispatch_id' => $dispatchId,
            'dispatch_type' => 'comment_added',
            'issue_id' => $issueId,
            'comment_id' => $commentId,
            'commenter_id' => $commenterId,
            'recipients_count' => count($recipients),
            'duplicate_skipped' => $duplicatesSkipped,
        ]);
        
        Database::commit();
        
        error_log(sprintf(
            '[NOTIFICATION] Dispatch complete: comment=%d, issue=%d, recipients=%d, duplicates_skipped=%d',
            $commentId,
            $issueId,
            count($recipients),
            $duplicatesSkipped
        ), 3, storage_path('logs/notifications.log'));
        
    } catch (\Exception $e) {
        Database::rollback();
        error_log(sprintf(
            '[NOTIFICATION ERROR] Failed to dispatch comment notifications: issue=%d, error=%s',
            $issueId,
            $e->getMessage()
        ), 3, storage_path('logs/notifications.log'));
        
        self::queueForRetry('comment_dispatch', $issueId, $e->getMessage());
    }
}
```

### Step 3: Apply Same Fix to `dispatchStatusChanged()`

Same pattern as `dispatchCommentAdded()` with:
- `$dispatchId = 'status_' . $issueId . '_' . md5($newStatus) . '_status_changed'`
- `'dispatch_type' => 'status_changed'`

---

## Testing Strategy

### Test 1: Normal Dispatch (No Duplicates)
```bash
# Create issue and comment
curl -X POST /api/v1/issues \
  -d '{"project_id": 1, "title": "Test Issue", "issue_type_id": 1}'
# Returns: issue_id = 100

curl -X POST /api/v1/issues/100/comments \
  -d '{"body": "Test comment"}'
# Returns: comment_id = 1

# Verify: Only 1 notification per recipient
mysql> SELECT COUNT(*) FROM notifications WHERE related_issue_id = 100;
Result: 2 (assignee + 1 watcher)
```

### Test 2: Duplicate Dispatch (Should Skip)
```bash
# Simulate retry by calling dispatch manually
php -r '
require "bootstrap/autoload.php";
$service = new \App\Services\NotificationService;
// Call twice
$service->dispatchCommentAdded(100, 5, 1);
$service->dispatchCommentAdded(100, 5, 1);  // Should skip
'

# Verify: Still only 2 notifications (not 4)
mysql> SELECT COUNT(*) FROM notifications WHERE related_issue_id = 100;
Result: 2 (not increased)

# Check log
tail -f storage/logs/notifications.log | grep "Idempotency check"
Result: One log entry for skipped dispatch
```

### Test 3: Race Condition Test
```bash
# Concurrent requests simulation
for i in {1..5}; do
  php dispatch_test.php 100 5 1 &
done
wait

# Verify: Correct number of notifications despite concurrent calls
mysql> SELECT COUNT(*) FROM notifications WHERE related_issue_id = 100;
Result: 2 (not 10, which would happen without fix)
```

### Test 4: Watcher Added During Dispatch
```bash
# Start with 2 watchers (A, B)
mysql> INSERT INTO issue_watchers (issue_id, user_id) VALUES (100, 1), (100, 2);

# Dispatch comment
$service->dispatchCommentAdded(100, 5, 1);
# Creates 2 notifications

# During dispatch, add watcher C
mysql> INSERT INTO issue_watchers (issue_id, user_id) VALUES (100, 3);

# Retry dispatch (should not duplicate for A, B)
$service->dispatchCommentAdded(100, 5, 1);
# Should create 1 notification (for C only) due to duplicate detection
```

### Test 5: Transaction Rollback
```bash
# Simulate database error mid-dispatch
// Add error condition in create()
Database::insert(...); // Fail this
// Ensure rollback works

# Verify: No partial notifications created
mysql> SELECT * FROM notifications WHERE related_issue_id = 100 AND created_at > NOW() - INTERVAL 1 MINUTE;
Result: 0 rows (rollback successful)

# Check retry queue
mysql> SELECT * FROM notification_deliveries WHERE status = 'failed';
Result: Entry for retry
```

---

## Database Verification Queries

### Check for Duplicates Before Fix
```sql
-- Find duplicate notifications
SELECT 
    user_id,
    type,
    related_issue_id,
    actor_user_id,
    COUNT(*) as duplicate_count
FROM notifications
WHERE created_at > NOW() - INTERVAL 1 DAY
GROUP BY user_id, type, related_issue_id, actor_user_id
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC;

-- Result: Should find duplicates BEFORE fix
```

### Verify Fix
```sql
-- After deploying fix
-- Same query should return NO duplicates (0 rows)

-- Check dispatch log
SELECT 
    dispatch_type,
    COUNT(*) as dispatch_count,
    SUM(duplicate_skipped) as duplicates_prevented
FROM notification_dispatch_log
WHERE created_at > NOW() - INTERVAL 1 DAY
GROUP BY dispatch_type;

-- Result: Shows how many duplicates were prevented
```

---

## Performance Impact

| Metric | Before | After | Impact |
|--------|--------|-------|--------|
| DB Queries per Dispatch | 3 | 5 | +67% (acceptable) |
| Notification Records | Duplicates | Deduped | -50-80% |
| Disk Space | Grows fast | Grows slow | ✓ Better |
| Network Traffic | Duplicates sent | Single per user | ✓ Better |

---

## Deployment Checklist

- [ ] **Database Migration**
  - [ ] Add `dispatch_id` column to notifications table
  - [ ] Create `notification_dispatch_log` table
  - [ ] Create required indexes
  - [ ] Verify schema changes
  
- [ ] **Code Changes**
  - [ ] Update `dispatchCommentAdded()`
  - [ ] Update `dispatchStatusChanged()`
  - [ ] Add transaction wrapper
  - [ ] Add duplicate detection logic
  - [ ] Add dispatch logging

- [ ] **Testing**
  - [ ] All 5 test cases pass
  - [ ] No new errors in logs
  - [ ] No performance regression
  - [ ] Concurrent tests pass

- [ ] **Monitoring**
  - [ ] Monitor `notification_dispatch_log` table
  - [ ] Check for high `duplicate_skipped` counts
  - [ ] Monitor transaction rollback rate
  - [ ] Alert on duplicate notifications created

- [ ] **Documentation**
  - [ ] Update API docs
  - [ ] Document dispatch tracking table
  - [ ] Add troubleshooting guide
  - [ ] Document monitoring queries

---

## Rollback Plan

If issues arise after deployment:

```bash
# Keep dispatch_id column (backward compatible)
# Just revert NotificationService methods:
git revert <commit-hash>

# Drop dispatch log if needed
DROP TABLE notification_dispatch_log;
```

**Impact**: System returns to old behavior (with race condition)  
**Mitigation**: Monitor for notification duplicates

---

## Success Criteria

After implementing CRITICAL #3:

✅ **No Duplicate Notifications**: Same event only notifies once per user  
✅ **Idempotent Dispatch**: Calling dispatch 10x = same result as calling once  
✅ **Transactional**: All or nothing - no partial notifications  
✅ **Audit Trail**: Full log of all dispatch attempts  
✅ **Race Condition Fixed**: Concurrent requests don't cause duplicates  
✅ **Performance**: Minimal overhead from new checks  
✅ **Rollback Safe**: Can be undone without data loss  

---

## Related Issues Fixed

- Fixes duplicate notifications sent to users
- Enables retry logic safely
- Supports concurrent notification creation
- Provides audit trail for compliance

---

## Next Steps

1. Implement CRITICAL #2 (input validation)
2. **Implement CRITICAL #3** (this race condition fix)
3. Deploy all 3 together
4. Run integration tests
5. Monitor production logs

---

## Timeline Estimate

| Phase | Duration | Notes |
|-------|----------|-------|
| Database Migration | 15 min | 3 SQL statements |
| Code Implementation | 90 min | dispatchCommentAdded + dispatchStatusChanged |
| Testing | 60 min | 5 test cases + verification |
| Documentation | 30 min | Updated API docs |
| **Total** | **3-4 hours** | Deployable in single window |

---

**Plan Version**: 1.0.0  
**Created**: December 8, 2025  
**Status**: Ready for implementation in Thread #3

---

## References

- [Idempotency in APIs](https://stripe.com/docs/idempotency)
- [Database Transactions](https://dev.mysql.com/doc/refman/8.0/en/commit.html)
- [Concurrency Control](https://en.wikipedia.org/wiki/Concurrency_control)
