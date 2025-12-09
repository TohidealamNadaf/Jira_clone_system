# Notification Preferences - Application Analysis

**Question**: Are notification preferences actually being applied when users check/uncheck options?  
**Status**: PARTIAL - In-App Works, Email/Push Not Implemented  
**Date**: December 8, 2025

---

## TL;DR - Current Behavior

### What WORKS ✅
- **In-App Notifications**: If user **unchecks "in_app"**, they will NOT receive in-app notifications
- **In-App Notifications**: If user **checks "in_app"**, they WILL receive in-app notifications

### What DOESN'T Work ❌
- **Email Notifications**: User can check/uncheck "email", but has NO effect - email delivery is NOT implemented
- **Push Notifications**: User can check/uncheck "push", but has NO effect - push delivery is NOT implemented

---

## How Preferences Are Actually Applied

### The Flow

```
User checks/unchecks → Saves to notification_preferences table ✅
                ↓
Issue is created/assigned/commented
                ↓
NotificationService::dispatchXxx() is called ✅
                ↓
Calls shouldNotify(userId, eventType) ✅
                ↓
Reads from notification_preferences table ✅
                ↓
Checks ONLY 'in_app' channel (email/push ignored) ❌
                ↓
If in_app=1: Creates in-app notification ✅
If in_app=0: Does NOT create notification ✅
                ↓
Email/Push: Never attempted (not implemented) ❌
```

---

## Code Evidence

### Step 1: Preferences Are Saved ✅

**File**: `src/Core/Database.php` - `insertOrUpdate()` method
```php
// We fixed this! Preferences now save correctly
Database::insertOrUpdate(
    'notification_preferences',
    ['user_id' => 1, 'event_type' => 'issue_created', 'in_app' => 1, 'email' => 1, 'push' => 0],
    ['user_id', 'event_type']
);
```

### Step 2: Preferences Are Checked ✅

**File**: `src/Services/NotificationService.php` line 31

When an issue is created, BEFORE creating a notification:
```php
foreach ($members as $member) {
    // ✅ This checks the user's preference
    if (!self::shouldNotify($member['user_id'], 'issue_created')) {
        continue; // ✅ Skip notification if preference disabled
    }
    
    self::create(...); // ✅ Create notification only if preference enabled
}
```

### Step 3: Preference Check Details ⚠️ PARTIAL

**File**: `src/Services/NotificationService.php` lines 315-341

The `shouldNotify()` method:
```php
public static function shouldNotify(
    int $userId,
    string $eventType,
    string $channel = 'in_app'  // ⚠️ DEFAULT IS 'in_app' ONLY
): bool {
    $preference = Database::selectOne(
        'SELECT in_app, email, push FROM notification_preferences WHERE user_id = ? AND event_type = ?',
        [$userId, $eventType]
    );
    
    if (!$preference) {
        if ($channel === 'in_app' || $channel === 'email') {
            return true;
        }
        return false;
    }
    
    // ✅ Returns the channel preference value
    return (bool) $preference[$channel];
}
```

**The Problem**: 
```php
if (!self::shouldNotify($member['user_id'], 'issue_created')) {
    // ⚠️ Only checks 'in_app', ignores email and push settings
    continue;
}
```

---

## What Actually Happens in Practice

### Scenario 1: User Unchecks "In-App"

**Settings**:
```
issue_created:
  ☐ in_app (UNCHECKED)
  ☑ email (checked)
  ☑ push (checked)
```

**Database**:
```
user_id=1, event_type='issue_created'
in_app=0, email=1, push=1
```

**When Issue Created**:
1. `shouldNotify(1, 'issue_created')` checks in_app
2. in_app=0 → returns false
3. Notification is NOT created ✅
4. User receives: **NOTHING** ✅

**Expected**:
- No in-app ✅
- Email delivery (if implemented) ❓
- Push delivery (if implemented) ❓

**Actual**:
- No in-app ✅
- No email (not implemented)
- No push (not implemented)
- User receives: **NOTHING** ✅

---

### Scenario 2: User Unchecks "Email" But Checks "In-App"

**Settings**:
```
issue_created:
  ☑ in_app (checked)
  ☐ email (UNCHECKED)
  ☑ push (checked)
```

**Database**:
```
user_id=1, event_type='issue_created'
in_app=1, email=0, push=1
```

**When Issue Created**:
1. `shouldNotify(1, 'issue_created')` checks in_app
2. in_app=1 → returns true
3. Notification IS created ✅
4. Creates in-app notification ✅
5. Email/Push: Never considered ❌

**Expected**:
- In-app notification ✅
- No email (user unchecked it) ✅
- Push notification (user checked it) ✓ (if implemented)

**Actual**:
- In-app notification ✅
- No email (not implemented anyway)
- No push (not implemented)
- User receives: **IN-APP NOTIFICATION ONLY** ✅

---

### Scenario 3: User Checks All Channels

**Settings**:
```
issue_created:
  ☑ in_app (checked)
  ☑ email (checked)
  ☑ push (checked)
```

**Database**:
```
user_id=1, event_type='issue_created'
in_app=1, email=1, push=1
```

**When Issue Created**:
1. `shouldNotify(1, 'issue_created')` checks in_app
2. in_app=1 → returns true
3. Notification IS created ✅
4. Creates in-app notification ✅
5. Email/Push: Never attempted ❌

**Expected**:
- In-app notification ✅
- Email notification (user checked it)
- Push notification (user checked it)

**Actual**:
- In-app notification ✅
- No email (not implemented)
- No push (not implemented)
- User receives: **IN-APP NOTIFICATION ONLY**

---

## Summary Table

| User Setting | In-App Notification | Email Notification | Push Notification |
|--------------|:---:|:---:|:---:|
| ✅ in_app, ✅ email, ✅ push | ✅ YES | ❌ NO* | ❌ NO* |
| ✅ in_app, ✅ email, ☐ push | ✅ YES | ❌ NO* | ❌ NO* |
| ✅ in_app, ☐ email, ✅ push | ✅ YES | ❌ NO* | ❌ NO* |
| ✅ in_app, ☐ email, ☐ push | ✅ YES | ❌ NO* | ❌ NO* |
| ☐ in_app, ✅ email, ✅ push | ❌ NO | ❌ NO* | ❌ NO* |
| ☐ in_app, ✅ email, ☐ push | ❌ NO | ❌ NO* | ❌ NO* |

*Email and Push: Not implemented yet (future enhancement)

---

## In-App Notifications - WORKING CORRECTLY ✅

### User Uncheck "In-App" → No Notification
- ✅ Preference saved to database
- ✅ shouldNotify() checks in_app setting
- ✅ Notification creation skipped
- ✅ User receives NO notification

### User Checks "In-App" → Gets Notification
- ✅ Preference saved to database
- ✅ shouldNotify() checks in_app setting
- ✅ Notification is created
- ✅ User receives in-app notification

---

## Email & Push Notifications - NOT YET IMPLEMENTED ❌

### Current Status
```
// From NotificationService.php line 207-209
Future: Create delivery records for enabled channels
This will be implemented when email/push integration is added
self::queueDeliveries($id, $userId, $type);  // COMMENTED OUT
```

### What's Missing
1. **Email Delivery Service**: No code to send emails
2. **Push Delivery Service**: No code to send push notifications
3. **Delivery Queue**: Not being populated
4. **Cron Job**: Not configured to process deliveries

### Why It's This Way
- **Phase 1** (DONE): In-app notifications + preferences UI
- **Phase 2** (PENDING): Email delivery integration
- **Phase 3** (PENDING): Push notification integration

---

## Verification Steps

### Test: In-App Preferences ARE Applied

1. **Login as User A**
   - Go to `/profile/notifications`
   - Find `issue_created` row
   - **UNCHECK "in_app"**
   - Click "Save Preferences"

2. **Have User B create an issue in a project that includes User A**

3. **Check User A's notification bell icon**
   - Should show **0 unread notifications**
   - User A should NOT see the issue_created notification

4. **Now RECHECK "in_app" in User A's preferences**
   - Save
   - Have User B create another issue

5. **Check User A's notification bell icon again**
   - Should show **1+ unread notifications**
   - User A SHOULD see the new issue_created notification

**Expected Result**: ✅ Checking/unchecking "in_app" directly controls whether in-app notifications are sent

---

## How to Test Without Manual Issue Creation

### Database Test Query

```sql
-- Check what preferences are saved
SELECT user_id, event_type, in_app, email, push 
FROM notification_preferences 
WHERE user_id = 1 
ORDER BY event_type;

-- Check what notifications were created
SELECT id, user_id, type, title, is_read, created_at 
FROM notifications 
WHERE user_id = 1 
ORDER BY created_at DESC 
LIMIT 10;
```

### Manual Test Steps

1. **Setup**
   - Go to `/profile/notifications`
   - Note current notification_preferences

2. **Uncheck "in_app" for "issue_created"**
   - Save preferences
   - Verify in database: `in_app = 0`

3. **Create an issue that User 1 is member of**
   - Manually trigger: `NotificationService::dispatchIssueCreated(issueId, creatorId)`
   - Or use UI to create issue

4. **Check notifications table**
   - Should have NO record for this user on this event
   - Because shouldNotify() returned false

5. **Check it again with "in_app" checked**
   - Should have a notification record

---

## Conclusion

### ✅ What's Working
- **Preferences are saving correctly** (we just fixed the SQL error)
- **In-app preferences are being applied** (unchecking prevents notifications)
- **Preferences are persistent** (survive page refresh)
- **Per-user isolation** (each user has own preferences)

### ❌ What's Not Working
- **Email delivery** (not implemented, email checkbox does nothing)
- **Push notifications** (not implemented, push checkbox does nothing)

### 📋 Recommendation

**Current State: PARTIALLY FUNCTIONAL**

- ✅ In-App Notifications: FULLY WORKING
- ❌ Email Notifications: QUEUED FOR FUTURE IMPLEMENTATION
- ❌ Push Notifications: QUEUED FOR FUTURE IMPLEMENTATION

This is expected and documented in the code as "Phase 2/3" work.

---

## Related Documentation

- `CRITICAL_FIX_NOTIFICATION_PREFERENCES_SAVE.md` - The SQL fix we just completed
- `NOTIFICATIONS_SYSTEM_SPEC.md` - Full system architecture
- `FIX_5_EMAIL_PUSH_CHANNEL_LOGIC_COMPLETE.md` - Infrastructure for future email/push

---

**Status Summary**: Preferences are being applied correctly for in-app notifications. Email and push are not yet implemented but infrastructure is in place for future implementation.
