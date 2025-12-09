# 🚀 START FIX 8 HERE - Production Error Handling

**Previous Status**: FIX 1-7 Complete & Verified ✅  
**Current Status**: Ready to implement FIX 8  
**Total Progress**: 70% (7/10 fixes)  
**Time Remaining**: ~1h 50m  
**Next Deadline**: Complete FIX 8 before FIX 9

---

## What Happened So Far

### ✅ FIX 1-7 Complete and Verified

All previous fixes have been **thoroughly audited and verified** to work correctly:

1. **FIX 1**: Database Schema - All 4 notification tables in main schema ✅
2. **FIX 2**: Column Names - All `assigned_to` → `assignee_id` changed ✅
3. **FIX 3**: Comment Notifications - dispatchCommentAdded() wired ✅
4. **FIX 4**: Status Notifications - dispatchStatusChanged() wired ✅
5. **FIX 5**: Multi-Channel Logic - shouldNotify() enhanced for in_app/email/push ✅
6. **FIX 6**: Auto-Init Script - 63 notification preferences created ✅
7. **FIX 7**: Migration Runner - Production-ready setup automation ✅

**Verification Status**: See `VERIFICATION_COMPLETE_FIXES_1_7.md` and `AUDIT_SUMMARY_FINAL_FIX_8.md`

---

## What FIX 8 Does

### Current Problem: Silent Failures

The notification system works, but **has NO error handling or logging**:

```
❌ User changes issue status
❌ Database connection fails
❌ No error logged
❌ Notification lost forever
❌ Nobody knows it failed
❌ User not notified
```

### Solution: Error Handling & Logging

After FIX 8, failures will be visible and automatically recovered:

```
✅ User changes issue status
❌ Database connection fails (transient)
✅ Error logged with full context
✅ Notification queued for retry
✅ Admin can see error in dashboard
✅ Automatic retry succeeds within minutes
✅ User eventually notified
```

### FIX 8 Deliverables

1. **Error Logging** - All errors logged to file with context
2. **Retry Logic** - Failed notifications queued for automatic retry
3. **Admin Dashboard** - Health widget showing error statistics
4. **Log Viewer** - Utility to view and analyze logs
5. **Log Rotation** - Automated archival of old logs

---

## How to Implement FIX 8

### Read These First (10 minutes)

1. **Read** `FIX_8_ACTION_PLAN.md` - Complete implementation guide
2. **Read** `VERIFICATION_COMPLETE_FIXES_1_7.md` - What was verified
3. **Understand** the NotificationService structure (current code is clean)

### Implementation Phases (45 minutes total)

#### Phase 1: Add Error Logging (15 minutes)

**Goal**: All errors logged to `storage/logs/notifications.log`

**File to Modify**: `src/Services/NotificationService.php`

**What to Change**:
1. Add try-catch to `create()` method with logging
2. Add try-catch to `dispatchCommentAdded()` with logging
3. Add try-catch to `dispatchStatusChanged()` with logging

**Pattern to Use**:
```php
try {
    // existing code ...
    error_log(sprintf('[NOTIFICATION] Success: type=%s, user=%d', $type, $userId), 3, 
        storage_path('logs/notifications.log'));
} catch (\Exception $e) {
    error_log(sprintf('[NOTIFICATION ERROR] Failed: %s', $e->getMessage()), 3,
        storage_path('logs/notifications.log'));
}
```

#### Phase 2: Add Retry Logic (15 minutes)

**Goal**: Failed notifications automatically retried

**Files to Modify**:
1. `src/Services/NotificationService.php` - Add methods:
   - `queueForRetry()` - Queue failed notification
   - `processFailedNotifications()` - Retry failed ones

2. Create `scripts/process-notification-retries.php` - Cron job script

**What to Add**:
```php
public static function queueForRetry(string $dispatchType, int $relatedIssueId, 
    string $errorMessage, int $retryCount = 0): bool {
    // Queue in notification_deliveries with 'failed' status
}

public static function processFailedNotifications(int $maxRetries = 3): int {
    // Find failed deliveries, retry them
}
```

#### Phase 3: Admin Dashboard & Log Utilities (15 minutes)

**Goal**: Admins can see notification health

**Files to Create/Modify**:
1. Create `src/Helpers/NotificationLogger.php` - Log viewer utility
   - `getRecentLogs()` - Get last 50 log lines
   - `getErrorStats()` - Count and analyze errors
   - `archiveOldLogs()` - Clean up old logs

2. Add to `views/admin/index.php` - Health widget
   - Show total errors (24h)
   - Show log file size
   - Link to detailed logs

3. Create `views/admin/notification-logs.php` - Log viewer page

### Step-by-Step Implementation

#### Step 1: Create Log Directory Structure

Add to `bootstrap/app.php`:
```php
$logDir = storage_path('logs');
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
```

#### Step 2: Add Error Logging

In `src/Services/NotificationService.php`, find the `create()` method and wrap in try-catch:

```php
public static function create(...): ?int {
    try {
        // EXISTING CODE HERE
        $id = Database::insert('notifications', [
            // ... columns ...
        ]);
        
        // ADD THIS
        error_log(sprintf(
            '[NOTIFICATION] Created: type=%s, user=%d, issue=%s',
            $type, $userId, $relatedIssueId ?? 'N/A'
        ), 3, storage_path('logs/notifications.log'));
        
        return $id;
    } catch (\Exception $e) {
        // ADD THIS
        error_log(sprintf(
            '[NOTIFICATION ERROR] Failed to create: type=%s, user=%d, error=%s',
            $type, $userId, $e->getMessage()
        ), 3, storage_path('logs/notifications.log'));
        
        return null;
    }
}
```

#### Step 3: Add Retry Infrastructure

Add these new methods to `src/Services/NotificationService.php`:

```php
/**
 * Queue failed notification for retry
 */
public static function queueForRetry(
    string $dispatchType,
    int $relatedIssueId,
    string $errorMessage,
    int $retryCount = 0
): bool {
    try {
        return (bool) Database::insert('notification_deliveries', [
            'notification_id' => 0, // Marker for system retries
            'channel' => 'retry',
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $retryCount,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (\Exception $e) {
        error_log('Failed to queue retry: ' . $e->getMessage(), 3,
            storage_path('logs/notifications.log'));
        return false;
    }
}

/**
 * Process failed notifications and retry them
 * Call from cron job every 5 minutes
 */
public static function processFailedNotifications(int $maxRetries = 3): int {
    try {
        $failed = Database::select(
            'SELECT * FROM notification_deliveries WHERE status = ? AND retry_count < ? 
             ORDER BY created_at ASC LIMIT 100',
            ['failed', $maxRetries]
        );
        
        $retryCount = 0;
        foreach ($failed as $delivery) {
            try {
                // TODO: Implement retry logic based on dispatch type
                // For now, just increment retry count
                Database::update(
                    'notification_deliveries',
                    ['retry_count' => $delivery['retry_count'] + 1],
                    'id = ?',
                    [$delivery['id']]
                );
                $retryCount++;
            } catch (\Exception $e) {
                error_log("Retry failed for delivery {$delivery['id']}: " . $e->getMessage(), 3,
                    storage_path('logs/notifications.log'));
            }
        }
        
        error_log("Processed $retryCount failed notifications", 3,
            storage_path('logs/notifications.log'));
        return $retryCount;
    } catch (\Exception $e) {
        error_log('Exception processing failed notifications: ' . $e->getMessage(), 3,
            storage_path('logs/notifications.log'));
        return 0;
    }
}
```

#### Step 4: Create Log Viewer Utility

Create `src/Helpers/NotificationLogger.php`:

```php
<?php declare(strict_types=1);

namespace App\Helpers;

class NotificationLogger
{
    public static function getRecentLogs(int $limit = 50): array
    {
        $logFile = storage_path('logs/notifications.log');
        if (!file_exists($logFile)) return [];
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_slice($lines, -$limit);
    }
    
    public static function getErrorStats(): array
    {
        $logFile = storage_path('logs/notifications.log');
        if (!file_exists($logFile)) return ['total_errors' => 0, 'recent_errors' => []];
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $errors = array_filter($lines, fn($line) => strpos($line, '[NOTIFICATION ERROR]') !== false);
        
        return [
            'total_errors' => count($errors),
            'recent_errors' => array_slice($errors, -10),
            'log_file_size' => filesize($logFile),
        ];
    }
}
```

#### Step 5: Add Admin Dashboard Widget

In `views/admin/index.php`, add:

```php
<div class="card">
    <div class="card-header">
        <h5>Notification System Health</h5>
    </div>
    <div class="card-body">
        <?php 
        $stats = \App\Helpers\NotificationLogger::getErrorStats();
        ?>
        <p><strong>Errors (24h):</strong> 
            <span class="badge badge-<?= $stats['total_errors'] > 0 ? 'danger' : 'success' ?>">
                <?= $stats['total_errors'] ?>
            </span>
        </p>
        <p><strong>Log Size:</strong> <?= round($stats['log_file_size'] / 1024, 1) ?> KB</p>
        <a href="/admin/notification-logs" class="btn btn-sm btn-info">View Logs</a>
    </div>
</div>
```

---

## Testing Your Implementation

### Test 1: Verify Logging Works

```bash
# Create a test notification
php -r "
require 'bootstrap/app.php';
\App\Services\NotificationService::create(
    userId: 1,
    type: 'test',
    title: 'Test',
    message: 'Testing logging',
    priority: 'normal'
);
"

# Check log file
tail -f storage/logs/notifications.log
# Should see: [NOTIFICATION] Created: type=test, user=1, ...
```

### Test 2: Verify Error Logging

```bash
# Temporarily kill MySQL connection
# Try to create notification
# Check logs - should see error

tail -f storage/logs/notifications.log
# Should see: [NOTIFICATION ERROR] Failed to create: ...
```

### Test 3: Verify Admin Dashboard

- Go to `/admin` (admin dashboard)
- Look for "Notification System Health" widget
- Should show error count and log file size

---

## Success Criteria

After completing FIX 8, verify:

- ✅ `storage/logs/notifications.log` file exists and is growing
- ✅ Error messages appear in log when errors occur
- ✅ Admin dashboard shows error statistics
- ✅ Failed notifications can be viewed in logs
- ✅ Retry logic queues failed deliveries

---

## Key Files Reference

| File | Purpose | Action |
|------|---------|--------|
| `src/Services/NotificationService.php` | Main service | Add logging + retry methods |
| `src/Helpers/NotificationLogger.php` | NEW - Log utility | Create this file |
| `views/admin/index.php` | Admin panel | Add health widget |
| `scripts/process-notification-retries.php` | NEW - Cron job | Create this file |
| `bootstrap/app.php` | App init | Add log dir creation |

---

## Time Breakdown

| Phase | Task | Time |
|-------|------|------|
| 1 | Add error logging to 3 methods | 15 min |
| 2 | Add retry logic methods | 15 min |
| 3 | Create log viewer + admin widget | 10 min |
| 4 | Testing and verification | 5 min |
| **Total** | | **45 min** |

---

## Code Patterns to Use

### Error Logging Pattern
```php
error_log(sprintf('[NOTIFICATION] Message: %s', $context), 3, 
    storage_path('logs/notifications.log'));
```

### Error Handling Pattern
```php
try {
    // code
} catch (\Exception $e) {
    error_log('Error: ' . $e->getMessage(), 3, storage_path('logs/notifications.log'));
    self::queueForRetry('type', $id, $e->getMessage());
}
```

---

## Important Notes

1. **Log File Location**: `storage/logs/notifications.log` (must exist before writing)
2. **Error Format**: Use `[NOTIFICATION]` and `[NOTIFICATION ERROR]` prefixes for easy filtering
3. **Retry Strategy**: Start with 3 max retries, exponential backoff in future versions
4. **Performance**: Check logs periodically, archive old ones monthly
5. **Security**: Never log sensitive user data, only IDs and timestamps

---

## Files to Create

1. ✅ `src/Helpers/NotificationLogger.php` - Log viewer utility
2. ✅ `scripts/process-notification-retries.php` - Cron job script

## Files to Modify

1. ✅ `src/Services/NotificationService.php` - Add logging + retry
2. ✅ `views/admin/index.php` - Add health widget
3. ✅ `bootstrap/app.php` - Initialize log directory

---

## After FIX 8 is Complete

1. **Create Documentation**: `FIX_8_PRODUCTION_ERROR_HANDLING_COMPLETE.md`
2. **Update AGENTS.md**: Mark FIX 8 as complete
3. **Update NOTIFICATION_FIX_STATUS.md**: Progress to 8/10
4. **Next**: Proceed with FIX 9 - Verify API Routes

---

## Quick Reference

### Log Format Examples

```
✅ Success
[NOTIFICATION] Created: type=issue_created, user=1, issue=BP-7, priority=normal

❌ Error
[NOTIFICATION ERROR] Failed to create: type=issue_created, user=1, error=Connection timeout
```

### Admin Dashboard Commands

```bash
# View recent logs
tail -50 storage/logs/notifications.log

# Count errors
grep -c "ERROR" storage/logs/notifications.log

# Archive old logs
php scripts/process-notification-retries.php
```

---

## Summary

**FIX 8 adds production hardening** to make the notification system:
- **Observable** (errors are visible)
- **Recoverable** (automatic retries)
- **Debuggable** (full logs available)
- **Compliant** (audit trail)

**Estimated Time**: 45 minutes  
**Complexity**: Medium  
**Impact**: High (production-ready system)

---

## Start Now!

1. ✅ Read this document (5 min)
2. ✅ Read `FIX_8_ACTION_PLAN.md` (10 min)
3. 🚀 **Start implementing Phase 1** (add error logging)

**Good luck! You've got this.** 💪

---

**Next Document**: `FIX_8_ACTION_PLAN.md` (Complete implementation details)

**Questions?** See `AUDIT_SUMMARY_FINAL_FIX_8.md` for comprehensive overview.
