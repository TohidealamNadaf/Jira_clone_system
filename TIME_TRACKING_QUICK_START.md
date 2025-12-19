# Time Tracking Module - Quick Start Guide

**Get started in 5 minutes!**

---

## 🚀 Quick Setup

### 1. Run Migration (2 minutes)

```bash
mysql -u root -p jiira_clonee_system < database/migrations/006_create_time_tracking_tables.sql
```

### 2. Add Routes (2 minutes)

**routes/web.php** - add before closing brace:
```php
$router->get('/time-tracking', [TimeTrackingController::class, 'dashboard']);
$router->get('/time-tracking/project/{projectId}', [TimeTrackingController::class, 'projectReport']);
$router->get('/time-tracking/budgets', [TimeTrackingController::class, 'budgetDashboard']);
```

**routes/api.php** - add before closing brace:
```php
$router->post('/api/v1/time-tracking/start', [TimeTrackingApiController::class, 'start']);
$router->post('/api/v1/time-tracking/pause', [TimeTrackingApiController::class, 'pause']);
$router->post('/api/v1/time-tracking/resume', [TimeTrackingApiController::class, 'resume']);
$router->post('/api/v1/time-tracking/stop', [TimeTrackingApiController::class, 'stop']);
$router->get('/api/v1/time-tracking/status', [TimeTrackingApiController::class, 'status']);
$router->get('/api/v1/time-tracking/logs', [TimeTrackingApiController::class, 'logs']);
$router->get('/api/v1/time-tracking/issue/{issueId}', [TimeTrackingApiController::class, 'issueTimeLogs']);
$router->post('/api/v1/time-tracking/rate', [TimeTrackingApiController::class, 'setRate']);
$router->get('/api/v1/time-tracking/rate', [TimeTrackingApiController::class, 'getRate']);
$router->get('/api/v1/time-tracking/project/{projectId}/budget', [TimeTrackingApiController::class, 'projectBudget']);
```

### 3. Add CSS & JS (1 minute)

In **views/layouts/app.php** - in `<head>` section:
```html
<link rel="stylesheet" href="<?= url('/assets/css/floating-timer.css') ?>">
```

Before `</body>`:
```html
<script src="<?= url('/assets/js/floating-timer.js') ?>"></script>
```

### ✅ Done! Now test it.

---

## 🧪 Quick Test

### 1. Set Your Hourly Rate

```bash
curl -X POST http://localhost:8080/jira_clone_system/public/api/v1/time-tracking/rate \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: your_token" \
  -d '{
    "rate_type": "hourly",
    "rate_amount": 50,
    "currency": "USD"
  }'
```

### 2. Start a Timer (from Browser Console)

```javascript
FloatingTimer.startTimer(
    issueId = 1,           // Replace with real issue ID
    projectId = 1,         // Replace with real project ID
    issueSummary = "Fix login bug",
    issueKey = "BP-1"
);
```

### 3. Watch the Timer

The floating widget should appear in bottom-right corner with:
- Timer counting up (HH:MM:SS)
- Cost updating in real-time
- Pause/Stop buttons

### 4. Stop the Timer

```javascript
FloatingTimer.stopTimer("Fixed the authentication issue");
```

✅ Time logged! Check the database:

```sql
SELECT * FROM issue_time_logs 
ORDER BY created_at DESC LIMIT 1;
```

---

## 📱 On Issue Detail Page

Add this to `views/issues/show.php`:

```php
<!-- Timer Widget Section -->
<div class="issue-section">
    <h3>⏱ Time Tracking</h3>
    
    <?php
    $timeLogs = $timeTrackingService->getIssueTimeLogs($issue['id']);
    $totalSeconds = 0;
    $totalCost = 0;
    
    foreach ($timeLogs as $log) {
        $totalSeconds += (int)$log['duration_seconds'];
        $totalCost += (float)$log['total_cost'];
    }
    ?>
    
    <div class="time-tracking-info">
        <p>Total Time: <strong><?= gmdate('H:i:s', $totalSeconds) ?></strong></p>
        <p>Total Cost: <strong>$<?= number_format($totalCost, 2) ?></strong></p>
    </div>
    
    <button class="btn btn-primary" onclick="startTimerForIssue(<?= $issue['id'] ?>, <?= $issue['project_id'] ?>, '<?= addslashes($issue['summary']) ?>', '<?= $issue['key'] ?>')">
        Start Timer
    </button>
    
    <?php if (count($timeLogs) > 0): ?>
    <div class="time-logs">
        <h4>Time Logs</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Duration</th>
                    <th>Cost</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeLogs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['display_name']) ?></td>
                    <td><?= gmdate('H:i:s', (int)$log['duration_seconds']) ?></td>
                    <td>$<?= number_format((float)$log['total_cost'], 2) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function startTimerForIssue(issueId, projectId, summary, key) {
    FloatingTimer.startTimer(issueId, projectId, summary, key);
}
</script>
```

---

## 💰 Set User Rates

### Option 1: Via API

```bash
# Hourly
curl -X POST http://localhost:8080/jira_clone_system/public/api/v1/time-tracking/rate \
  -H "Content-Type: application/json" \
  -d '{"rate_type":"hourly","rate_amount":75,"currency":"USD"}'

# Minutely  
curl -X POST http://localhost:8080/jira_clone_system/public/api/v1/time-tracking/rate \
  -H "Content-Type: application/json" \
  -d '{"rate_type":"minutely","rate_amount":1.25,"currency":"USD"}'
```

### Option 2: Via Database

```sql
INSERT INTO user_rates (user_id, rate_type, rate_amount, currency, is_active, effective_from)
VALUES (1, 'hourly', 50.00, 'USD', 1, CURDATE());
```

### Option 3: Build Admin UI

Create `views/settings/rates.php`:

```html
<form method="POST" action="<?= url('/settings/rates') ?>">
    <?= csrf_token() ?>
    
    <div class="form-group">
        <label>Rate Type</label>
        <select name="rate_type" required>
            <option value="hourly">Hourly</option>
            <option value="minutely">Minutely</option>
            <option value="secondly">Secondly</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Rate Amount</label>
        <input type="number" name="rate_amount" step="0.01" min="0.01" required>
    </div>
    
    <div class="form-group">
        <label>Currency</label>
        <input type="text" name="currency" value="USD" maxlength="3" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Save Rate</button>
</form>
```

---

## 📊 View Reports

### User Time Report

```php
// In controller
$logs = $timeTrackingService->getUserTimeLogs($userId);
return $this->view('time-tracking.user-report', ['logs' => $logs]);
```

### Project Budget

```php
// In controller
$budget = $timeTrackingService->getProjectBudgetSummary($projectId);
return $this->view('time-tracking.project-report', ['budget' => $budget]);
```

### API Calls

```bash
# Get all my logs
curl http://localhost:8080/jira_clone_system/public/api/v1/time-tracking/logs

# Get logs for project
curl http://localhost:8080/jira_clone_system/public/api/v1/time-tracking/logs?project_id=1

# Get logs for issue
curl http://localhost:8080/jira_clone_system/public/api/v1/time-tracking/issue/1

# Get project budget
curl http://localhost:8080/jira_clone_system/public/api/v1/time-tracking/project/1/budget
```

---

## 🎯 Common Tasks

### Format Time Display

```php
<?php
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
}

echo formatDuration(3661); // 01:01:01
?>
```

### Check Running Timer

```javascript
const state = FloatingTimer.getState();
if (state.isRunning) {
    console.log('Timer is running on issue:', state.issueKey);
    console.log('Elapsed:', state.elapsedSeconds, 'seconds');
    console.log('Cost so far:', state.cost);
}
```

### Create Project Budget

```sql
INSERT INTO project_budgets (
    project_id, total_budget, start_date, end_date,
    status, alert_threshold, currency
) VALUES (
    1,                          -- project_id
    50000.00,                   -- $50,000 budget
    '2025-01-01',               -- starts Jan 1
    '2025-12-31',               -- ends Dec 31
    'active',                   -- currently active
    80.00,                       -- alert at 80%
    'USD'
);
```

### Calculate Project Cost

```php
<?php
$stats = $timeTrackingService->getCostStatistics($projectId);

echo "Total Time: " . formatDuration($stats['total_seconds']) . "\n";
echo "Total Cost: $" . number_format($stats['total_cost'], 2) . "\n";
echo "Average Cost per Log: $" . number_format($stats['avg_cost_per_log'], 2) . "\n";
echo "Users Tracked: " . $stats['unique_users'] . "\n";
?>
```

---

## 🔒 Security Checklist

- ✅ User rates set (cannot start timer without rate)
- ✅ CSRF token in all forms
- ✅ API endpoints require authentication
- ✅ Input validation on all fields
- ✅ Prepared statements (no SQL injection)
- ✅ Authorization checks in controllers

---

## 📚 File Reference

| File | Purpose |
|------|---------|
| `src/Services/TimeTrackingService.php` | All business logic |
| `src/Controllers/TimeTrackingController.php` | Web requests |
| `src/Controllers/Api/TimeTrackingApiController.php` | API endpoints |
| `public/assets/js/floating-timer.js` | Frontend widget |
| `public/assets/css/floating-timer.css` | Styling |
| `database/migrations/006_create_time_tracking_tables.sql` | Database schema |
| `TIME_TRACKING_IMPLEMENTATION.md` | Full documentation |

---

## ❓ FAQ

**Q: Timer stops when I close the browser?**  
A: No, the timer persists on the server. When you reopen, it will resume.

**Q: Can I have multiple timers running?**  
A: No, only ONE timer per user at a time (enforced by database).

**Q: What happens if I start a new timer while one is running?**  
A: The old timer automatically stops, then the new one starts.

**Q: Can I edit a time log?**  
A: Currently no (by design to prevent manipulation). We recommend implementing edit functionality with full audit trail if needed.

**Q: How accurate is the cost calculation?**  
A: Very accurate - it's calculated server-side in seconds with decimal precision.

**Q: Can users see each other's time logs?**  
A: Only if they have permission. Implement authorization in controllers as needed.

---

## 🆘 Troubleshooting

**Problem**: Floating widget not appearing
- **Solution**: Check CSS is loaded: `<link rel="stylesheet" href="<?= url('/assets/css/floating-timer.css') ?>">`

**Problem**: "No running timer" error when starting
- **Solution**: User needs hourly rate set. Run: `INSERT INTO user_rates (user_id, rate_type, rate_amount, is_active, effective_from) VALUES (USER_ID, 'hourly', 50, 1, CURDATE());`

**Problem**: Cost shows $0
- **Solution**: Check user rate is set correctly. Multiply rate × seconds to verify.

**Problem**: API returns 403 Forbidden
- **Solution**: Missing CSRF token. Include `X-CSRF-Token` header in requests.

---

## 🎓 Learning Path

1. **Understand the flow**: User → Timer Start → Server Logic → Database → Display
2. **Test timer manually**: Use browser console to start/stop
3. **Check database**: Verify records in `issue_time_logs` table
4. **Build UI**: Create views in `views/time-tracking/`
5. **Add admin panel**: User rate management
6. **Deploy**: Follow production checklist

---

**Ready to track time?** 🚀

Start with the 3-step quick setup above, then test with browser console!

Questions? Check `TIME_TRACKING_IMPLEMENTATION.md` for full details.
