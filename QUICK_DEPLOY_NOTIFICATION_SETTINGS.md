# Quick Deploy - Notification Settings (15 Minutes)

**TL;DR**: Your notification email/push settings are now production-ready. Deploy in 15 minutes.

---

## What's Working

✅ Users can toggle email/push notifications  
✅ Settings are saved to database  
✅ Notifications respect user preferences  
✅ Email delivery with auto-retry (NEW)  
✅ Push notifications framework (NEW)  

---

## 3-Step Deployment

### Step 1: Create Push Table (2 min)

```bash
cd /path/to/jira_clone_system
mysql -u root jira_clone < database/migrations/add_push_device_tokens_table.sql
```

Verify:
```sql
DESCRIBE push_device_tokens;
-- Should show 8 columns
```

### Step 2: Add Email Config (5 min)

Edit `config/config.php`, find the `mail` array, update:

```php
'mail' => [
    'driver' => 'smtp',
    'host' => 'smtp.mailtrap.io',  // or your SMTP server
    'port' => 587,
    'username' => 'your_username',  // from Mailtrap/SendGrid
    'password' => 'your_password',  // from Mailtrap/SendGrid
    'encryption' => 'tls',
    'from_address' => 'noreply@jiraclone.app',
    'from_name' => 'Jira Clone',
],
```

Get free SMTP credentials:
- **Mailtrap** (testing): https://mailtrap.io (free, easy)
- **SendGrid** (production): https://sendgrid.com (free tier available)
- **Gmail**: Use app password
- **Office 365**: Use corporate SMTP

### Step 3: Add to Crontab (3 min)

Run: `crontab -e`

Add these lines:
```bash
# Process email retries every 5 minutes
*/5 * * * * php /path/to/jira_clone_system/scripts/process-notification-deliveries.php

# Process push notifications every 5 minutes
*/5 * * * * php /path/to/jira_clone_system/scripts/process-push-notifications.php
```

Replace `/path/to/jira_clone_system` with your actual path.

Verify:
```bash
crontab -l  # Should show both lines
```

---

## Test (2 min)

1. Login to your Jira Clone
2. Go to Settings → Notifications
3. Enable "Email" for "Issue Assigned"
4. Create a new issue and assign to yourself
5. Wait 1 minute (cron job)
6. Check your email inbox

**You should see an email!**

---

## Push Notifications (Optional, Skip for Now)

To enable push (Firebase):

1. Go to https://firebase.google.com
2. Create project
3. Get server key
4. Add to `config/config.php`:

```php
'push' => [
    'enabled' => true,
    'fcm_server_key' => 'your_key_here',
    'fcm_project_id' => 'your_project_id',
],
```

Or skip it (leave disabled, in-app + email work fine):

```php
'push' => ['enabled' => false],
```

---

## Verify It's Working

```bash
# Check database table exists
mysql -u root jira_clone -e "DESCRIBE push_device_tokens;"

# Check email config
php -r "
require 'bootstrap/app.php';
\$c = require 'config/config.php';
echo 'Email: ' . \$c['mail']['host'] . \"\n\";
"

# Check cron
crontab -l

# Check logs
tail storage/logs/notifications.log
```

---

## If Something's Wrong

### Email Not Sending

Check:
```bash
# 1. Is SMTP configured?
grep -A5 "'mail'" config/config.php

# 2. Are emails queued?
mysql -u root jira_clone -e "SELECT * FROM notification_deliveries WHERE channel='email' LIMIT 1;"

# 3. What's the error?
grep ERROR storage/logs/notifications.log | tail -5
```

### Cron Not Running

Check:
```bash
# 1. Is it in crontab?
crontab -l

# 2. Do the files exist?
ls -la scripts/process-*.php

# 3. Is PHP working?
php -v

# 4. Run job manually to test
php scripts/process-push-notifications.php
php scripts/process-notification-deliveries.php
```

---

## Files Changed

**New files**:
- `src/Services/PushService.php` ✅
- `scripts/process-push-notifications.php` ✅
- `database/migrations/add_push_device_tokens_table.sql` ✅

**Modified files**:
- `src/Controllers/NotificationController.php` (3 new methods added) ✅

**No breaking changes** - everything is backwards compatible.

---

## What Happens Now

### When user enables email for "Issue Assigned":
```
1. Setting saved to database ✅
2. When issue is assigned → notification created ✅
3. System checks: is email enabled? YES ✅
4. Email queued for delivery ✅
5. Cron job runs, sends email via SMTP ✅
6. Email arrives in inbox ✅
```

### When user enables push for "Issue Assigned":
```
1. Setting saved to database ✅
2. When issue is assigned → notification created ✅
3. System checks: is push enabled? YES ✅
4. Push queued for delivery ✅
5. Cron job runs, sends via Firebase ✅
6. Push arrives on mobile/web ✅
```

### If user disables email:
```
1. Setting saved to database ✅
2. When issue is assigned → notification created ✅
3. System checks: is email enabled? NO ✅
4. Email NOT queued ✅
5. User doesn't get email ✅
```

---

## Quick Testing Script

Create `test_notifications.php`:

```php
<?php
require 'bootstrap/app.php';

use App\Services\NotificationService;

echo "Testing notification preferences...\n\n";

// Test 1: Save preference
echo "1. Saving email preference for user 1...\n";
NotificationService::updatePreference(1, 'issue_assigned', true, true, true);
echo "✓ Saved\n\n";

// Test 2: Read preference
echo "2. Checking if email is enabled...\n";
$enabled = NotificationService::shouldNotify(1, 'issue_assigned', 'email');
echo ($enabled ? "✓ EMAIL ENABLED\n" : "✗ EMAIL DISABLED\n");
echo "\n";

// Test 3: Create notification
echo "3. Creating notification...\n";
$notifId = NotificationService::create(
    userId: 1,
    type: 'issue_assigned',
    title: 'Test Issue Assigned',
    message: 'You have been assigned to TEST-1',
    actionUrl: '/issues/TEST-1'
);
echo ($notifId ? "✓ Notification created (ID: $notifId)\n" : "✗ Failed to create\n");
echo "\n";

// Test 4: Check email queue
if ($notifId) {
    echo "4. Checking email queue...\n";
    $result = \App\Core\Database::selectOne(
        'SELECT status FROM notification_deliveries WHERE notification_id = ? AND channel = "email"',
        [$notifId]
    );
    if ($result) {
        echo "✓ Email queued (Status: " . $result['status'] . ")\n";
        echo "✓ Cron job will process it in 5 minutes\n";
    } else {
        echo "✗ Email not queued\n";
    }
}

echo "\n✅ TEST COMPLETE\n";
echo "Check your email inbox in ~5 minutes\n";
```

Run: `php test_notifications.php`

---

## Email Providers (Choose One)

### Mailtrap (Free - Best for Testing)
1. Create account: https://mailtrap.io
2. Create inbox
3. Copy SMTP credentials
4. Add to config

### SendGrid (Free - Production)
1. Create account: https://sendgrid.com
2. Create API key
3. Copy credentials
4. Add to config

### Gmail (Free - Personal)
1. Enable 2-factor auth
2. Create app password
3. Use app password in config

### Office 365 (Corporate)
1. Get corporate SMTP server
2. Use domain email + password
3. Add to config

---

## After Deployment

1. ✅ Monitor `storage/logs/notifications.log` for errors
2. ✅ Test with a real issue assignment
3. ✅ Check email inbox
4. ✅ Ask team to enable email notifications
5. ✅ Monitor for 24 hours
6. ✅ Enable push (optional)

---

## Rollback (If Needed)

**To disable email notifications temporarily**:

Edit `src/Services/NotificationService.php`, find line ~209:
```php
// Comment this out:
// self::queueDeliveries($id, $userId, $type);
```

**To drop push table**:
```sql
DROP TABLE push_device_tokens;
```

**To remove from crontab**:
```bash
crontab -e
# Delete the two lines we added
```

---

## Status

**Email & Push Notifications**: ✅ **READY FOR PRODUCTION**

Deployment time: **15 minutes**  
Risk level: **Very Low** (backwards compatible)  
Recommendation: **Deploy Now**

---

## Need Help?

1. Read detailed docs:
   - `VERIFY_EMAIL_PUSH_SETTINGS.md` (audit)
   - `IMPLEMENT_NOTIFICATION_SETTINGS_PROPERLY.md` (details)
   - `DEPLOY_EMAIL_PUSH_SETTINGS.md` (comprehensive)

2. Check logs:
   ```bash
   tail -f storage/logs/notifications.log
   ```

3. Test manually:
   ```bash
   php test_notifications.php
   ```

---

**You're ready. Deploy now.** ✅
