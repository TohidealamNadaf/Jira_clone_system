# Notification Preferences: Are They Actually Applied?

**Your Question**: "If I uncheck push or in_app, will that actually control whether I receive notifications?"

**Answer**: ✅ **YES for IN-APP** | ❌ **NO for EMAIL/PUSH (not implemented yet)**

---

## The Real Situation

### ✅ IN-APP NOTIFICATIONS: WORKING PERFECTLY

When you:
- **✅ UNCHECK "in_app"** → You will **NOT** receive in-app notifications
- **✅ CHECK "in_app"** → You **WILL** receive in-app notifications

This works because the code checks your preference BEFORE creating notifications:

```php
// NotificationService.php line 31
if (!self::shouldNotify($member['user_id'], 'issue_created')) {
    continue;  // Don't create notification if preference disabled
}
```

### ❌ EMAIL & PUSH: NOT YET IMPLEMENTED

When you:
- **Check "email"** → Nothing happens (no email delivery yet)
- **Uncheck "email"** → Nothing changes (wasn't working anyway)
- **Check "push"** → Nothing happens (no push delivery yet)
- **Uncheck "push"** → Nothing changes (wasn't working anyway)

This is **expected and documented**:

```php
// NotificationService.php line 207-209
// Future: Create delivery records for enabled channels
// This will be implemented when email/push integration is added
// self::queueDeliveries($id, $userId, $type);  // COMMENTED OUT
```

---

## Proof: How In-App Notifications Work

### The Flow

```
1. User saves preferences
   └─→ notification_preferences table updated with in_app=1 or 0

2. Issue is created
   └─→ NotificationService::dispatchIssueCreated() called

3. Dispatch method checks preference
   └─→ if (!self::shouldNotify($userId, 'issue_created')) { skip }

4. shouldNotify() queries database
   └─→ SELECT in_app FROM notification_preferences
   └─→ Returns true/false based on in_app value

5. If true: Notification created
   └─→ INSERT INTO notifications table
   └─→ User sees in-app notification

6. If false: Notification NOT created
   └─→ User gets nothing
```

---

## How to Test This Yourself

### Test: Disable In-App Notifications

1. **Go to**: `/profile/notifications`
2. **Find**: `issue_created` row
3. **Action**: UNCHECK "in_app" checkbox
4. **Click**: "Save Preferences" → ✅ Success message
5. **Create**: A new issue in a shared project
6. **Result**: No notification appears in your notification bell
7. **Verify**: `SELECT * FROM notifications WHERE user_id=YOUR_ID AND type='issue_created'` should show nothing for this new issue

### Test: Enable In-App Notifications

1. **Go to**: `/profile/notifications`
2. **Find**: `issue_created` row
3. **Action**: CHECK "in_app" checkbox
4. **Click**: "Save Preferences" → ✅ Success message
5. **Create**: Another new issue in a shared project
6. **Result**: Notification APPEARS in your notification bell
7. **Verify**: `SELECT * FROM notifications WHERE user_id=YOUR_ID AND type='issue_created'` should show the new issue

---

## What's Actually Being Checked

When a notification is about to be created, this code runs:

**File**: `src/Services/NotificationService.php` line 315-341

```php
public static function shouldNotify(
    int $userId,
    string $eventType,
    string $channel = 'in_app'  // ← Only in_app is checked
): bool {
    // Query your saved preferences
    $preference = Database::selectOne(
        'SELECT in_app, email, push FROM notification_preferences WHERE user_id = ? AND event_type = ?',
        [$userId, $eventType]
    );
    
    if (!$preference) {
        // If no preference set, default to in_app and email ON, push OFF
        if ($channel === 'in_app' || $channel === 'email') {
            return true;  // Allow notification
        }
        return false;  // Block notification
    }
    
    // Return your saved preference value
    return (bool) $preference[$channel];  // This is checked
}
```

**The Key Part**: `return (bool) $preference[$channel];`

- If `channel = 'in_app'` and `in_app = 1` → Returns **true** → Notification created ✅
- If `channel = 'in_app'` and `in_app = 0` → Returns **false** → Notification NOT created ✅
- Email & push columns are read but never used ❌

---

## Database Proof

### Your Preferences Are Saved

```sql
-- Check what you saved
SELECT * FROM notification_preferences 
WHERE user_id = 1;

-- Example output if you unchecked in_app for issue_created:
-- user_id=1, event_type='issue_created', in_app=0, email=1, push=0
-- user_id=1, event_type='issue_assigned', in_app=1, email=1, push=0
-- etc.
```

### Notifications Are Conditional

```sql
-- Issues YOU created notifications for:
SELECT type, COUNT(*) as notification_count 
FROM notifications 
WHERE user_id = 1 
GROUP BY type;

-- Compare with:
SELECT event_type, in_app 
FROM notification_preferences 
WHERE user_id = 1;

-- Notice: You should only have notifications for event_type 
-- where in_app = 1
```

---

## Email & Push: Why They Don't Work

### Current State

Email and push preferences are:
- ✅ Stored in database
- ✅ Displayed in UI
- ✅ Editable by user
- ❌ **NOT used for anything**

### Why Not Implemented?

The system was built in phases:
- **Phase 1** (DONE): In-app notifications ✅
- **Phase 2** (TODO): Email delivery integration
- **Phase 3** (TODO): Push notification integration

The infrastructure is ready but the delivery services aren't implemented yet.

### The Code Comment

```php
// Line 207-209 in NotificationService.php
// Future: Create delivery records for enabled channels
// This will be implemented when email/push integration is added
// self::queueDeliveries($id, $userId, $type);
```

This shows where email/push will be queued when implemented.

---

## Summary Table

| Action | In-App Result | Email Result | Push Result |
|--------|---|---|---|
| Check "in_app" checkbox | ✅ Enables in-app | N/A | N/A |
| Uncheck "in_app" checkbox | ✅ Disables in-app | N/A | N/A |
| Check "email" checkbox | - | ❌ Does nothing | - |
| Uncheck "email" checkbox | - | ❌ Does nothing | - |
| Check "push" checkbox | - | - | ❌ Does nothing |
| Uncheck "push" checkbox | - | - | ❌ Does nothing |

---

## Frequently Asked Questions

### Q: If I uncheck "in_app", will I definitely NOT get notifications?

**A**: ✅ **YES**. The code explicitly skips notification creation if in_app is unchecked.

### Q: If I check "email", will I get emails?

**A**: ❌ **NO**. Email delivery is not yet implemented. You can save the preference, but nothing happens with it.

### Q: If I uncheck "push", will I stop getting push notifications?

**A**: ❌ **IRRELEVANT** - Push notifications aren't implemented yet, so you weren't getting them anyway.

### Q: Are my preferences actually saved?

**A**: ✅ **YES**. We fixed the SQL error. They save perfectly.

### Q: Why can I set preferences for email/push if they don't work?

**A**: Because the infrastructure is there for future implementation. The UI allows it, the database stores it, but the delivery service doesn't exist yet.

### Q: When will email/push be implemented?

**A**: Unknown. It's on the roadmap but not yet scheduled.

### Q: Is this a bug?

**A**: ❌ **NO**. It's working as designed. The system currently supports in-app notifications with email/push queued for future development.

---

## The Bottom Line

### Current Functionality: IN-APP NOTIFICATIONS ONLY

**What Works**:
- ✅ Save notification preferences
- ✅ Check/uncheck in-app option
- ✅ Preferences are applied when notifications are dispatched
- ✅ In-app notifications respect user settings

**What Doesn't Work**:
- ❌ Email notifications (not implemented)
- ❌ Push notifications (not implemented)
- ❌ Email/push preference checkboxes have no effect

### If You Only Care About In-App Notifications

You're good! Everything works perfectly. Unchecking "in_app" will stop you from receiving that notification type.

### If You Want Email or Push

Those features are not yet implemented. The infrastructure is in place, but the actual delivery mechanisms need to be built.

---

## What We Fixed Today

We fixed the **notification preferences save error** (`SQLSTATE[HY093]`). 

This means:
- ✅ Preferences now save without errors
- ✅ Preferences are persistent
- ✅ In-app preferences work correctly
- ✅ Email/push preferences can be saved (though not yet used)

---

**Conclusion**: Your in-app notification preferences are working correctly. Email and push are not implemented yet, but that's by design.
