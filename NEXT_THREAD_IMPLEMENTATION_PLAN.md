# Next Thread: Email & Push Notification Implementation Plan

**Purpose**: Complete the notification system by implementing email and push channels  
**Estimated Time**: 6-8 hours  
**Complexity**: High (external service integration)  
**Dependencies**: Notification preferences system (✅ Complete)

---

## Current State (December 8, 2025)

### ✅ What's Complete
- Notification preferences fully functional for **in-app channel**
- Database schema supports email and push channels
- `shouldNotify()` method checks all channels
- Preferences save correctly with positional parameters (CRITICAL FIX 11)
- API validates all event types and channels
- Client-side UI handles all 3 channels

### ⏳ What's Missing
- Email delivery service not implemented
- Push notification service not implemented
- `notification_deliveries` table not fully utilized
- Email/push preferences are saved but not acted upon

---

## Implementation Roadmap

### Phase 2: Email Notifications (4 hours)

#### Step 1: Choose Email Provider
**Options** (ranked by ease of implementation):

1. **SMTP (Simplest - Recommended)**
   - Use native PHP `mail()` or SMTP library
   - Time: 1-2 hours
   - Cost: Free (use company mail server)
   - Effort: Low

2. **SendGrid (Recommended for production)**
   - API-based, reliable, good deliverability
   - Time: 2 hours
   - Cost: Free tier (100 emails/day), $20/mo for more
   - Effort: Low-medium

3. **Mailgun**
   - Similar to SendGrid, good alternative
   - Time: 2 hours
   - Cost: Free tier, $35/mo for more
   - Effort: Low-medium

#### Step 2: Create Email Service Class

**File**: `src/Services/EmailService.php`

```php
<?php declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

class EmailService
{
    private static $provider = 'smtp'; // or 'sendgrid', 'mailgun'
    
    /**
     * Send notification email
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body HTML email body
     * @param array $data Additional context (user, issue, etc.)
     * @return bool Success status
     */
    public static function sendNotification(
        string $to,
        string $subject,
        string $body,
        array $data = []
    ): bool {
        try {
            if (self::$provider === 'smtp') {
                return self::sendViaSMTP($to, $subject, $body);
            } elseif (self::$provider === 'sendgrid') {
                return self::sendViaSendGrid($to, $subject, $body);
            } else {
                throw new \Exception("Unknown email provider: " . self::$provider);
            }
        } catch (\Exception $e) {
            error_log("Email send failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send via SMTP (using PHP mail() or SwiftMailer)
     */
    private static function sendViaSMTP(
        string $to,
        string $subject,
        string $body
    ): bool {
        $headers = [
            'From' => config('mail.from_address'),
            'Reply-To' => config('mail.from_address'),
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Mailer' => 'Jira Clone System'
        ];
        
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
    
    /**
     * Send via SendGrid API
     */
    private static function sendViaSendGrid(
        string $to,
        string $subject,
        string $body
    ): bool {
        $apiKey = config('services.sendgrid.key');
        $fromEmail = config('mail.from_address');
        
        // Call SendGrid API
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.sendgrid.com/v3/mail/send',
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'personalizations' => [[
                    'to' => [['email' => $to]]
                ]],
                'from' => ['email' => $fromEmail],
                'subject' => $subject,
                'content' => [['type' => 'text/html', 'value' => $body]]
            ])
        ]);
        
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $statusCode === 202; // SendGrid returns 202 on success
    }
}
```

#### Step 3: Create Email Templates

**Directory**: `views/emails/`

**Template Examples**:

`issue-created.html`:
```html
<h2>New Issue: {{issue.key}}</h2>
<p><strong>{{issue.summary}}</strong></p>
<p>Project: {{project.name}}</p>
<p>Reporter: {{creator.name}}</p>
<p><a href="{{app_url}}/issue/{{issue.key}}">View Issue</a></p>
```

`issue-assigned.html`:
```html
<h2>Issue Assigned to You</h2>
<p><strong>{{issue.key}}: {{issue.summary}}</strong></p>
<p>Assigned by: {{assigner.name}}</p>
<p><a href="{{app_url}}/issue/{{issue.key}}">View Issue</a></p>
```

#### Step 4: Update NotificationService to Queue Email Deliveries

**File**: `src/Services/NotificationService.php`

**Modify**: `create()` method to call email queuing

```php
public static function create(
    int $userId,
    string $type,
    string $title,
    // ... other params
): ?int {
    // Create in-app notification
    $id = Database::insert('notifications', [
        // ... data ...
    ]);
    
    // NEW: Queue email/push deliveries
    if ($id) {
        self::queueDeliveries($id, $userId, $type);
    }
    
    return $id;
}

/**
 * Queue notification deliveries based on user preferences
 */
public static function queueDeliveries(
    int $notificationId,
    int $userId,
    string $eventType
): void {
    $preference = Database::selectOne(
        'SELECT in_app, email, push FROM notification_preferences 
         WHERE user_id = ? AND event_type = ?',
        [$userId, $eventType]
    );
    
    if (!$preference) {
        $preference = ['in_app' => 1, 'email' => 1, 'push' => 0];
    }
    
    // Queue email if enabled
    if ($preference['email']) {
        Database::insert('notification_deliveries', [
            'notification_id' => $notificationId,
            'channel' => 'email',
            'status' => 'pending',
            'retry_count' => 0
        ]);
    }
    
    // Queue push if enabled
    if ($preference['push']) {
        Database::insert('notification_deliveries', [
            'notification_id' => $notificationId,
            'channel' => 'push',
            'status' => 'pending',
            'retry_count' => 0
        ]);
    }
}
```

#### Step 5: Create Email Processing Script

**File**: `scripts/send-pending-emails.php`

```php
<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;
use App\Services\EmailService;

/**
 * Process pending email notifications
 * Run via: php scripts/send-pending-emails.php
 * Or as a cron job: */5 * * * * cd /path/to/jira && php scripts/send-pending-emails.php
 */

$pending = Database::select(
    'SELECT nd.id, nd.notification_id, n.user_id, n.type, n.title, n.message, u.email
     FROM notification_deliveries nd
     JOIN notifications n ON nd.notification_id = n.id
     JOIN users u ON n.user_id = u.id
     WHERE nd.channel = ? AND nd.status = ? AND nd.retry_count < ?
     LIMIT 50',
    ['email', 'pending', 3]
);

foreach ($pending as $delivery) {
    try {
        $subject = "Notification: {$delivery['title']}";
        $body = buildEmailBody($delivery);
        
        if (EmailService::sendNotification($delivery['email'], $subject, $body)) {
            Database::update('notification_deliveries', 
                ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$delivery['id']]
            );
        } else {
            throw new \Exception('Email service returned false');
        }
    } catch (\Exception $e) {
        Database::update('notification_deliveries',
            [
                'retry_count' => $delivery['retry_count'] + 1,
                'error_message' => $e->getMessage()
            ],
            'id = ?',
            [$delivery['id']]
        );
    }
}

function buildEmailBody(array $delivery): string {
    // Load template based on notification type
    // Render with data
    // Return HTML
}
```

#### Step 6: Setup Cron Job

**Add to system crontab** (run every 5 minutes):

```bash
*/5 * * * * /usr/bin/php /var/www/jira_clone_system/scripts/send-pending-emails.php >> /var/log/jira_emails.log 2>&1
```

---

### Phase 3: Push Notifications (3-4 hours)

#### Step 1: Choose Push Provider

**Recommended**: Firebase Cloud Messaging (FCM) - Free tier available

**Files Needed**:
- `src/Services/PushService.php`
- `scripts/send-pending-push.php`
- Database migration to store FCM tokens

#### Step 2: Implement Push Service

**High-level approach**:
1. Users register device tokens in their profile
2. When preference has push=1, add to queue
3. Cron job sends push notifications via FCM
4. Track delivery status in notification_deliveries

---

## File Checklist for Next Thread

### Files to Create

- [ ] `src/Services/EmailService.php` (200 lines)
- [ ] `views/emails/` directory with templates (5-10 templates)
- [ ] `scripts/send-pending-emails.php` (150 lines)
- [ ] `tests/EmailServiceTest.php` (150 lines)
- [ ] Database migration for email queue status tracking

### Files to Modify

- [ ] `src/Services/NotificationService.php` - Enable queueDeliveries() call
- [ ] `src/Core/Config.php` - Add email service configuration
- [ ] `routes/web.php` - Add email test route (for admin)

### Documentation to Create

- [ ] `EMAIL_NOTIFICATIONS_IMPLEMENTATION.md` - Complete guide
- [ ] `PUSH_NOTIFICATIONS_ROADMAP.md` - Future planning
- [ ] `NOTIFICATION_DELIVERY_TROUBLESHOOTING.md` - Debugging guide

---

## Testing Strategy

### Unit Tests
```php
// Test EmailService sends emails correctly
// Test email template rendering
// Test retry logic
// Test error handling
```

### Integration Tests
```php
// Create issue → preference has email=1 → verify email in queue
// Check preference email=0 → create issue → verify NO email queued
// Test email processing script with pending deliveries
// Test retry on failure
```

### Manual Testing
```
1. Create test user with email enabled
2. Have another user create issue in their project
3. Wait for cron job to run (or run script manually)
4. Verify email received with correct content
5. Repeat with email disabled
```

---

## Configuration Template

**File**: `config/mail.php`

```php
<?php

return [
    'driver' => env('MAIL_DRIVER', 'smtp'),
    'from_address' => env('MAIL_FROM', 'noreply@jira.local'),
    'from_name' => env('MAIL_FROM_NAME', 'Jira Clone'),
    
    'smtp' => [
        'host' => env('MAIL_HOST', 'localhost'),
        'port' => env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME', ''),
        'password' => env('MAIL_PASSWORD', ''),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    ],
    
    'sendgrid' => [
        'key' => env('SENDGRID_API_KEY', ''),
    ],
    
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN', ''),
        'key' => env('MAILGUN_KEY', ''),
    ],
];
```

---

## Success Criteria

✅ Email notifications send when user has preference enabled  
✅ Email notifications don't send when user has preference disabled  
✅ Push notifications queue when preference enabled  
✅ Preferences control both channels independently  
✅ Retry logic handles transient failures  
✅ All 9 event types work with email  
✅ All 9 event types work with push  
✅ Cron job runs reliably every 5 minutes  
✅ Admin can monitor email delivery status  
✅ Full error logging and metrics  

---

## Estimated Breakdown

| Task | Time | Effort |
|------|------|--------|
| Email service implementation | 1.5 hrs | Low |
| Email templates (5-10 types) | 1 hr | Low |
| Email processing script | 0.5 hr | Low |
| Email testing | 1 hr | Medium |
| Push service setup (FCM) | 1.5 hrs | Medium |
| Push processing script | 0.5 hr | Low |
| Push testing | 1 hr | Medium |
| Documentation & guides | 1 hr | Low |
| **Total** | **~8 hours** | **Medium** |

---

## Deliverables for Next Thread

1. **Email Notifications**: Fully functional and tested
2. **Push Notifications**: Infrastructure in place (tokens, queuing, basic send)
3. **Monitoring Dashboard**: View email/push delivery status in admin
4. **Configuration Guide**: Setup instructions for different providers
5. **Troubleshooting Guide**: Common issues and fixes
6. **Performance Report**: Delivery metrics and benchmarks

---

## Success Definition

After completion of this thread:

✅ Users can enable/disable email notifications per event type  
✅ Users can enable/disable push notifications per event type  
✅ Emails are sent reliably within 5 minutes  
✅ Push notifications are delivered reliably  
✅ Admins can monitor delivery status  
✅ System handles failures gracefully with retries  
✅ Full documentation for production deployment  

---

## Production Rollout Plan

**Phase 1 - Email (Week 1)**:
- Deploy email service with SMTP
- Test with staging email account
- Monitor delivery rates for 1 week
- Gradually enable for all users

**Phase 2 - SendGrid/Mailgun (Week 2)**:
- Optionally upgrade to commercial email service
- No code changes needed (just config)
- Better deliverability

**Phase 3 - Push (Week 3-4)**:
- Deploy push service infrastructure
- Allow users to register devices
- Start sending push notifications
- Monitor engagement metrics

---

**This plan is ready for the next development thread.**  
**All groundwork is complete; execution can begin immediately.**

