# FIX 8: Quick Start Guide

**Status**: ✅ COMPLETE  
**Duration**: 35 minutes  
**Implementation**: Phase 1-5 Complete  

---

## What Was Done

### 5 Phases Implemented

| Phase | Task | Status |
|-------|------|--------|
| 1 | Add error logging to all notification methods | ✅ COMPLETE |
| 2 | Add retry infrastructure for failed notifications | ✅ COMPLETE |
| 3 | Create logging utilities and admin dashboard | ✅ COMPLETE |
| 4 | Initialize log directory on app startup | ✅ COMPLETE |
| 5 | Create cron job for automatic retry processing | ✅ COMPLETE |

---

## Files Changed

### Modified (3 files)
1. **`src/Services/NotificationService.php`** (+150 lines)
   - Added logging to `create()`, `dispatchCommentAdded()`, `dispatchStatusChanged()`
   - Added `queueForRetry()` and `processFailedNotifications()` methods

2. **`bootstrap/app.php`** (+6 lines)
   - Initialize `storage/logs` directory on startup

3. **`views/admin/index.php`** (+72 lines)
   - Added "Notification System Health" widget to admin dashboard

### Created (2 files)
1. **`src/Helpers/NotificationLogger.php`** (180 lines)
   - Log viewing and analysis utility
   - Methods: getRecentLogs(), getErrorStats(), archiveOldLogs(), etc.

2. **`scripts/process-notification-retries.php`** (60 lines)
   - Cron job script for automatic retry processing
   - Runs every 5 minutes in production

---

## Key Features

### Error Logging ✅
Every notification now logs with context:
```
[NOTIFICATION] Created: type=issue_commented, user=2, issue=7, id=42
[NOTIFICATION ERROR] Failed to create: type=issue_commented, user=2, error=Connection timeout
```

### Retry Infrastructure ✅
Failed notifications automatically queued for retry:
- Stores in `notification_deliveries` table
- Tracks retry count (max 3)
- Automatic processing via cron job

### Admin Dashboard ✅
New widget showing:
- System status (Operational / Issues)
- Error count (24h)
- Retry queue count
- Log file size
- Recent errors (last 5)

### Log Rotation ✅
Automatic cleanup:
- Archives when > 10 MB
- Deletes archives > 30 days old
- Prevents disk space issues

---

## Testing

### Test 1: Verify Logging Works
```bash
# Go to any issue, add a comment
# Check logs
tail -f storage/logs/notifications.log
# Should see success logs
```

### Test 2: Check Admin Dashboard
- Go to `/admin`
- Look for "Notification System Health" section
- Should show stats (0 errors if working well)

### Test 3: Simulate Failure
```bash
# Stop MySQL temporarily
# Create an issue in browser
# Check logs - should see error entry
tail -f storage/logs/notifications.log
```

### Test 4: Run Cron Job
```bash
php scripts/process-notification-retries.php
# Should output:
# [2025-12-08 10:30:45] Processing failed notifications...
# [2025-12-08 10:30:45] Processed 0 failed notifications
```

---

## Installation

### Step 1: Code Already Applied ✅
All code changes have been made.

### Step 2: Create Log Directory (if needed)
```bash
mkdir -p storage/logs/archive
chmod 755 storage/logs
```

### Step 3: Set Up Cron Job (Production)
```bash
# Add to crontab
*/5 * * * * /usr/bin/php /path/to/jira_clone_system/scripts/process-notification-retries.php >> /var/log/notification-retries.log 2>&1
```

### Step 4: Verify Dashboard
- Open admin panel
- Should see notification health widget
- No errors expected initially

---

## Log File Location

```
storage/logs/
├── notifications.log          # Current log (active)
└── archive/
    ├── notifications_2025-12-08_093000.log
    └── ...
```

---

## Viewing Logs

### Via Command Line
```bash
# Last 50 lines
tail -50 storage/logs/notifications.log

# Watch in real-time
tail -f storage/logs/notifications.log

# Count errors
grep -c "ERROR" storage/logs/notifications.log

# Show only errors
grep "ERROR" storage/logs/notifications.log
```

### Via Admin Dashboard
- Go to `/admin`
- Look at "Notification System Health" widget
- Shows error count and recent errors

---

## What Gets Logged

### Success Entry
```
[NOTIFICATION] Created: type=issue_commented, user=2, issue=7, priority=normal, id=42
[NOTIFICATION] Dispatched comment notifications: issue=7, comment=3, recipients=2
```

### Error Entry
```
[NOTIFICATION ERROR] Failed to create: type=issue_commented, user=2, error=Connection timeout
[NOTIFICATION ERROR] Failed to dispatch comment notifications: issue=7, error=Table not found
```

### Retry Entry
```
[NOTIFICATION RETRY] Queued for retry: type=comment_dispatch, issue=7, retries=0
[NOTIFICATION RETRY] Processed 5 failed notifications, max_retries=3
```

---

## Configuration

### Max Retries
Default: 3 (adjustable in `processFailedNotifications()` method)

### Log Rotation
- Archives when > 10 MB (adjustable in cron script)
- Deletes archives > 30 days old (adjustable)

### Cron Frequency
- Default: Every 5 minutes
- Can be adjusted in crontab

---

## Production Checklist

- [ ] Log directory is writable by web server
- [ ] Cron job scheduled for every 5 minutes
- [ ] Admin can access `/admin` dashboard
- [ ] Error count shows 0 (no issues)
- [ ] Log file grows over time (logging works)
- [ ] Old logs archived after 30 days
- [ ] Disk space monitoring enabled

---

## Troubleshooting

### No Log File Appears
```bash
# Check directory is writable
ls -ld storage/logs

# Should show:
# drwxr-xr-x  username  groupname

# If not writable:
chmod 755 storage/logs
```

### Logs Not Growing
- Check that notifications are being created
- Verify write permissions
- Check disk space

### Dashboard Shows "Issues Detected"
- Check `storage/logs/notifications.log` for errors
- Look at recent error entries
- Diagnose based on error message

---

## Success Indicators

✅ **System is working if**:
- `storage/logs/notifications.log` exists
- File grows when notifications created
- Admin dashboard shows 0 errors
- Recent errors section empty

❌ **Check if**:
- Admin dashboard shows errors
- Log file not growing
- Cannot write to `storage/logs`
- Cron job not running

---

## Documentation

- **Full Details**: `FIX_8_PRODUCTION_ERROR_HANDLING_COMPLETE.md`
- **Status Tracking**: `NOTIFICATION_FIX_STATUS.md`
- **API Docs**: Remaining in API documentation

---

## Code Examples

### Creating Notification (with logging)
```php
$id = NotificationService::create(
    userId: 1,
    type: 'issue_commented',
    title: 'New Comment',
    message: 'New comment on BP-7'
);
// Logs: [NOTIFICATION] Created: type=issue_commented, user=1, issue=N/A, id=42
```

### Checking Dashboard Stats
```php
$stats = \App\Helpers\NotificationLogger::getErrorStats();
echo $stats['total_errors'];        // 2
echo $stats['log_file_size'];       // 102400
echo count($stats['recent_errors']); // Last 10 errors
```

### Running Retries Manually
```bash
php scripts/process-notification-retries.php

# Output:
# [2025-12-08 10:30:45] Processing failed notifications...
# [2025-12-08 10:30:45] Processed 3 failed notifications
# [2025-12-08 10:30:45] Completed in 0.234s
```

---

## Next Steps

1. ✅ FIX 8 Complete
2. 📋 FIX 9: Verify API Routes (20 min)
3. 📋 FIX 10: Performance Testing (45 min)

---

## Progress

```
▓▓▓▓▓▓▓▓░░ 80% Complete (8/10 Fixes)

FIX 1-8: ✅ COMPLETE
FIX 9-10: ⏳ PENDING
```

---

## Support

For issues:
1. Check `storage/logs/notifications.log`
2. Look for `[NOTIFICATION ERROR]` entries
3. Diagnose based on error message
4. System auto-retries failed notifications

---

**FIX 8 is production-ready. Ready for FIX 9!**
