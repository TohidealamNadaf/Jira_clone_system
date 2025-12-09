# FIX 8: Production Error Handling & Logging - ACTION PLAN

**Status**: READY TO IMPLEMENT  
**Priority**: MEDIUM - Production Hardening  
**Estimated Duration**: 45 minutes  
**Deadline**: Complete before FIX 9  
**Progress**: 7/10 Complete (70%)

---

## Current State

### ✅ What's Already Done (FIX 1-7)

1. **Database Schema** - Consolidated, optimized, production-ready
2. **Column Names** - Fixed all `assigned_to` → `assignee_id` references
3. **Comment Notifications** - `dispatchCommentAdded()` exists and is wired (IssueService:973)
4. **Status Notifications** - `dispatchStatusChanged()` exists and is wired (IssueController:348)
5. **Multi-Channel Logic** - `shouldNotify()` enhanced for in_app/email/push
6. **Auto-Initialization** - Script exists to create preferences
7. **Migration Runner** - Production-ready automation script exists

### ❌ What's Missing (FIX 8)

Currently, the notification system works but has **NO ERROR HANDLING OR LOGGING**:

- ❌ Silent failures - If notification creation fails, nothing is logged
- ❌ No retry logic - Failed notifications are lost forever
- ❌ No visibility - Production issues invisible until users complain
- ❌ No debugging - Difficult to troubleshoot in production
- ❌ No audit trail - No record of what failed and why

---

## Problem Statement

### Current Risk: Silent Failures in Production

**Scenario**: User changes issue status in production

```php
// IssueController.php:348
NotificationService::dispatchStatusChanged($issue['id'], $newStatus, $this->userId());
```

What happens if database connection fails mid-notification?

**Before FIX 8** (Current):
- ❌ Exception thrown but caught by generic controller
- ❌ User sees "Notification failed" but no real error
- ❌ No log entry created
- ❌ No way to retry
- ❌ Notification is completely lost

**After FIX 8** (What We're Building):
- ✅ Error caught and logged with full context
- ✅ User sees clear error message
- ✅ Error visible in logs for debugging
- ✅ Automatic retry queued
- ✅ Audit trail for compliance

---

## Implementation Plan

### Phase 1: Add Error Logging (15 minutes)

**File**: `src/Services/NotificationService.php`

**Task 1.1**: Add logging to `create()` method

Currently:
```php
public static function create(
    int $userId,
    string $type,
    string $title,
    ?string $message = null,
    ?string $actionUrl = null,
    ?int $actorUserId = null,
    ?int $relatedIssueId = null,
    ?int $relatedProjectId = null,
    string $priority = 'normal'
): ?int {
    try {
        $id = Database::insert('notifications', [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'actor_user_id' => $actorUserId,
            'related_issue_id' => $relatedIssueId,
            'related_project_id' => $relatedProjectId,
            'priority' => $priority,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $id;
    } catch (\Exception $e) {
        // ❌ Currently no logging here
        return null;
    }
}
```

**Change to**:
```php
public static function create(...): ?int {
    try {
        // ... existing code ...
        error_log(sprintf(
            '[NOTIFICATION] Created: type=%s, user=%d, issue=%s, priority=%s',
            $type,
            $userId,
            $relatedIssueId ?? 'N/A',
            $priority
        ), 3, storage_path('logs/notifications.log'));
        
        return $id;
    } catch (\Exception $e) {
        error_log(sprintf(
            '[NOTIFICATION ERROR] Failed to create: type=%s, user=%d, error=%s',
            $type,
            $userId,
            $e->getMessage()
        ), 3, storage_path('logs/notifications.log'));
        
        return null;
    }
}
```

**Task 1.2**: Add logging to `dispatchCommentAdded()`

```php
public static function dispatchCommentAdded(...): void {
    try {
        // ... existing code ...
        error_log(sprintf(
            '[NOTIFICATION] Dispatched comment notifications: issue=%d, recipients=%d',
            $issueId,
            count($recipients)
        ), 3, storage_path('logs/notifications.log'));
    } catch (\Exception $e) {
        error_log(sprintf(
            '[NOTIFICATION ERROR] Failed to dispatch comment notifications: issue=%d, error=%s',
            $issueId,
            $e->getMessage()
        ), 3, storage_path('logs/notifications.log'));
    }
}
```

**Task 1.3**: Add logging to `dispatchStatusChanged()`

```php
public static function dispatchStatusChanged(...): void {
    try {
        // ... existing code ...
        error_log(sprintf(
            '[NOTIFICATION] Dispatched status change notifications: issue=%d, status=%s, recipients=%d',
            $issueId,
            $newStatus,
            count($recipients)
        ), 3, storage_path('logs/notifications.log'));
    } catch (\Exception $e) {
        error_log(sprintf(
            '[NOTIFICATION ERROR] Failed to dispatch status notifications: issue=%d, error=%s',
            $issueId,
            $e->getMessage()
        ), 3, storage_path('logs/notifications.log'));
    }
}
```

---

### Phase 2: Add Error Handling to Dispatch Methods (15 minutes)

**File**: `src/Services/NotificationService.php`

**Task 2.1**: Enhance `dispatchCommentAdded()` error handling

**Before**:
```php
public static function dispatchCommentAdded(...): void {
    $issue = Database::selectOne(...);
    if (!$issue) return;
    
    // ... no error handling ...
}
```

**After**:
```php
public static function dispatchCommentAdded(...): void {
    try {
        $issue = Database::selectOne(...);
        if (!$issue) {
            error_log("Issue $issueId not found for comment notification", 3, 
                storage_path('logs/notifications.log'));
            return;
        }
        
        // ... existing code ...
        
        foreach ($recipients as $recipientId) {
            if (self::shouldNotify($recipientId, 'issue_commented')) {
                $notificationId = self::create(...);
                if (!$notificationId) {
                    error_log("Failed to create notification for user $recipientId", 3,
                        storage_path('logs/notifications.log'));
                    self::queueRetry($recipientId, 'issue_commented', $issueId);
                }
            }
        }
    } catch (\Exception $e) {
        error_log("Exception in dispatchCommentAdded: " . $e->getMessage(), 3,
            storage_path('logs/notifications.log'));
        // Queue for retry
        self::queueForRetry('comment_dispatch', $issueId, $e->getMessage());
    }
}
```

**Task 2.2**: Enhance `dispatchStatusChanged()` error handling

Same approach as Task 2.1

---

### Phase 3: Implement Retry Logic (15 minutes)

**File**: `src/Services/NotificationService.php`

**Task 3.1**: Add `queueForRetry()` method

```php
/**
 * Queue failed notification for retry
 * Stores in notification_deliveries table with 'failed' status
 */
public static function queueForRetry(
    string $dispatchType,
    int $relatedIssueId,
    string $errorMessage,
    int $retryCount = 0
): bool {
    try {
        // Store in notification_deliveries with failed status
        return (bool) Database::insert('notification_deliveries', [
            'notification_id' => 0, // Special marker for retries
            'channel' => 'retry',
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $retryCount,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (\Exception $e) {
        error_log("Failed to queue retry: " . $e->getMessage(), 3,
            storage_path('logs/notifications.log'));
        return false;
    }
}
```

**Task 3.2**: Add `processFailedNotifications()` method (for cron job)

```php
/**
 * Process failed notifications and retry
 * Run this via cron job every 5 minutes
 */
public static function processFailedNotifications(int $maxRetries = 3): int {
    try {
        $failed = Database::select(
            'SELECT * FROM notification_deliveries WHERE status = ? AND retry_count < ? ORDER BY created_at ASC LIMIT 100',
            ['failed', $maxRetries]
        );
        
        $retryCount = 0;
        foreach ($failed as $delivery) {
            try {
                // Attempt retry
                // ... implementation ...
                
                $retryCount++;
                error_log("Retried notification $delivery[id]", 3,
                    storage_path('logs/notifications.log'));
            } catch (\Exception $e) {
                error_log("Retry failed for delivery $delivery[id]: " . $e->getMessage(), 3,
                    storage_path('logs/notifications.log'));
                
                // Increment retry count
                Database::update(
                    'notification_deliveries',
                    ['retry_count' => $delivery['retry_count'] + 1],
                    'id = ?',
                    [$delivery['id']]
                );
            }
        }
        
        error_log("Processed $retryCount failed notifications", 3,
            storage_path('logs/notifications.log'));
        return $retryCount;
    } catch (\Exception $e) {
        error_log("Exception processing failed notifications: " . $e->getMessage(), 3,
            storage_path('logs/notifications.log'));
        return 0;
    }
}
```

---

### Phase 4: Add Logging Infrastructure (3 minutes)

**File**: `bootstrap/app.php`

Add at startup:

```php
// Create notifications log directory if needed
$logDir = storage_path('logs');
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Set up error logging path
define('NOTIFICATION_LOG', storage_path('logs/notifications.log'));
```

**File**: `config/config.php` (if logging config exists)

Add:
```php
'logging' => [
    'notifications' => [
        'path' => storage_path('logs/notifications.log'),
        'level' => 'info',
    ],
],
```

---

### Phase 5: Create Log Viewer Utility (10 minutes)

**File**: `src/Helpers/NotificationLogger.php` (NEW)

```php
<?php declare(strict_types=1);

namespace App\Helpers;

use App\Core\Database;

class NotificationLogger
{
    /**
     * Get recent notification logs
     */
    public static function getRecentLogs(int $limit = 50): array
    {
        $logFile = storage_path('logs/notifications.log');
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_slice($lines, -$limit);
    }
    
    /**
     * Get error statistics from log file
     */
    public static function getErrorStats(): array
    {
        $logFile = storage_path('logs/notifications.log');
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $errors = [];
        foreach ($lines as $line) {
            if (strpos($line, '[NOTIFICATION ERROR]') !== false) {
                $errors[] = $line;
            }
        }
        
        return [
            'total_errors' => count($errors),
            'recent_errors' => array_slice($errors, -10),
            'log_file_size' => filesize($logFile),
        ];
    }
    
    /**
     * Clear old logs (run via cron daily)
     */
    public static function archiveOldLogs(int $daysOld = 30): int
    {
        $logFile = storage_path('logs/notifications.log');
        $archiveDir = storage_path('logs/archive');
        
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0755, true);
        }
        
        // Archive current log
        $timestamp = date('Y-m-d_His');
        $archivePath = "$archiveDir/notifications_$timestamp.log";
        rename($logFile, $archivePath);
        
        // Clean up old archives
        $archived = 0;
        foreach (glob("$archiveDir/notifications_*.log") as $file) {
            if (time() - filemtime($file) > $daysOld * 86400) {
                unlink($file);
                $archived++;
            }
        }
        
        return $archived;
    }
}
```

---

### Phase 6: Add Admin Dashboard Widget (5 minutes)

**File**: `views/admin/index.php`

Add to admin dashboard:

```php
<!-- Notification System Health -->
<div class="card">
    <div class="card-header">
        <h5>Notification System Health</h5>
    </div>
    <div class="card-body">
        <?php 
        $errorStats = \App\Helpers\NotificationLogger::getErrorStats();
        ?>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Total Errors (24h):</strong> 
                    <span class="badge badge-<?= $errorStats['total_errors'] > 0 ? 'danger' : 'success' ?>">
                        <?= $errorStats['total_errors'] ?>
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Log File Size:</strong> 
                    <span><?= round($errorStats['log_file_size'] / 1024, 1) ?> KB</span>
                </p>
            </div>
        </div>
        <a href="/admin/notification-logs" class="btn btn-sm btn-info">View Logs</a>
    </div>
</div>
```

---

## Testing Plan

### Test 1: Verify Logging Works

```php
// Manually trigger a notification
NotificationService::create(
    userId: 1,
    type: 'issue_created',
    title: 'Test',
    message: 'Testing logging',
    priority: 'normal'
);

// Check log file
tail -f storage/logs/notifications.log
// Should see: [NOTIFICATION] Created: type=issue_created, user=1, ...
```

### Test 2: Verify Error Logging

Simulate database error:
```php
// Kill MySQL connection temporarily
// Try to create notification
// Observe error logged: [NOTIFICATION ERROR] Failed to create: ...
```

### Test 3: Verify Retry Logic

```php
// Check notification_deliveries table
SELECT * FROM notification_deliveries WHERE status = 'failed';
// Should see failed deliveries ready for retry
```

---

## Success Criteria

After completing FIX 8:

- ✅ All notification errors logged to `storage/logs/notifications.log`
- ✅ Retry logic queues failed notifications
- ✅ Admin can view error statistics
- ✅ Log rotation prevents disk issues
- ✅ Failed notifications eventually succeed via retries
- ✅ No silent failures

---

## Files to Modify

| File | Changes | Lines |
|------|---------|-------|
| `src/Services/NotificationService.php` | Add logging to all methods | 50-100 |
| `src/Helpers/NotificationLogger.php` | NEW - Log viewer utility | 80-120 |
| `bootstrap/app.php` | Initialize logging | 5-10 |
| `views/admin/index.php` | Add health widget | 10-15 |

**Total New Code**: 150-250 lines

---

## Rollout Plan

### Phase 1: Implement (45 minutes)
1. Add error logging to NotificationService (15 min)
2. Enhance error handling (15 min)
3. Implement retry logic (15 min)

### Phase 2: Test (10 minutes)
1. Unit test error logging
2. Unit test retry logic
3. Integration test end-to-end

### Phase 3: Deploy (5 minutes)
1. Merge to main branch
2. Deploy to production
3. Monitor logs

### Phase 4: Monitor (Ongoing)
1. Check error stats weekly
2. Review failed notifications monthly
3. Optimize retry thresholds based on data

---

## Production Impact

### ✅ Benefits
- **Visibility**: See notification errors immediately
- **Reliability**: Automatic retry recovery
- **Debugging**: Full error context in logs
- **Compliance**: Audit trail for troubleshooting
- **Confidence**: Know notifications are working

### ⚠️ Considerations
- Log rotation needed (archive after 30 days)
- Disk space for logs (estimate: 1-5 MB/month)
- Cron job for retry processing (every 5 minutes)

---

## Documentation to Create

After FIX 8:

1. **FIX_8_PRODUCTION_ERROR_HANDLING_COMPLETE.md** - Completion report
2. **NOTIFICATION_ERROR_RECOVERY.md** - How to handle errors
3. **NOTIFICATION_LOG_VIEWER_GUIDE.md** - How to view logs in admin

---

## Quick Reference

### Enable Logging
```php
error_log('Message', 3, storage_path('logs/notifications.log'));
```

### Check Logs
```bash
tail -f storage/logs/notifications.log
```

### Process Retries
```php
php scripts/process-notification-retries.php
```

---

## Next Steps (After FIX 8)

✅ FIX 8: Error Handling & Logging (This task)  
⏳ FIX 9: Verify API Routes (Already done, just needs documentation)  
⏳ FIX 10: Performance Testing (Load testing for 1000+ users)

---

## Handoff Notes

This FIX 8 plan provides complete error handling and observability for the notification system. Once implemented, the system will be production-hardened and ready for enterprise use.

**Status**: READY TO IMPLEMENT  
**Time Estimate**: 45 minutes  
**Complexity**: MEDIUM

Start implementing Phase 1 to add error logging first, then move to retry logic.
