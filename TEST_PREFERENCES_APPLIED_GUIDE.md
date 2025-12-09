# Quick Test: Are Notification Preferences Actually Being Applied?

**Goal**: Verify that checking/unchecking preferences actually changes notification behavior  
**Time**: 5-10 minutes  
**Difficulty**: Easy

---

## The Question

> "If I uncheck 'in_app', will I stop receiving in-app notifications?"

**Answer**: ✅ YES, for in-app notifications  
**Answer**: ❌ NO, for email/push (not implemented yet)

---

## Quick Test (5 minutes)

### Test 1: Uncheck "In-App" → No Notification

**Step 1: Save Preferences**
1. Login as your user
2. Go to: `/profile/notifications`
3. Find the `issue_created` row
4. **UNCHECK the "in_app" checkbox**
5. Click "Save Preferences"
6. Wait for: ✅ "Preferences updated successfully"

**Step 2: Verify in Database**
```bash
# Run this query in your database
SELECT user_id, event_type, in_app, email, push 
FROM notification_preferences 
WHERE user_id = 1 AND event_type = 'issue_created';
```

**Expected Result**:
```
user_id: 1
event_type: issue_created
in_app: 0  ← Should be 0 (unchecked)
email: 1
push: 0
```

**Step 3: Create an Issue**
1. Create a new issue in any project where your user is a member
2. Check the notification bell icon in the top navbar
3. **Expected**: The notification count should NOT increase

**Step 4: Verify No Notification Created**
```bash
# Check if notification was created
SELECT * FROM notifications 
WHERE user_id = 1 AND type = 'issue_created' 
ORDER BY created_at DESC LIMIT 1;
```

**Expected**: No record created (because in_app=0)

---

### Test 2: Check "In-App" → Gets Notification

**Step 1: Restore Preference**
1. Go to: `/profile/notifications`
2. Find `issue_created` row
3. **CHECK the "in_app" checkbox back**
4. Click "Save Preferences"
5. Wait for: ✅ "Preferences updated successfully"

**Step 2: Verify in Database**
```bash
SELECT user_id, event_type, in_app, email, push 
FROM notification_preferences 
WHERE user_id = 1 AND event_type = 'issue_created';
```

**Expected**:
```
in_app: 1  ← Should be 1 (checked)
```

**Step 3: Create Another Issue**
1. Create a new issue in same project
2. Check notification bell icon
3. **Expected**: Notification count increases

**Step 4: Verify Notification Created**
```bash
SELECT * FROM notifications 
WHERE user_id = 1 AND type = 'issue_created' 
ORDER BY created_at DESC LIMIT 1;
```

**Expected**: Record exists for this issue

---

## Database-Only Test (3 minutes)

If you don't want to create actual issues, test the logic directly:

### Check How Preferences Affect Notifications

```sql
-- 1. See all your preferences
SELECT event_type, in_app, email, push 
FROM notification_preferences 
WHERE user_id = 1
ORDER BY event_type;

-- 2. See notifications created with in_app=1 preferences
SELECT n.* 
FROM notifications n
WHERE n.user_id = 1 
  AND n.type IN (
    SELECT event_type FROM notification_preferences 
    WHERE user_id = 1 AND in_app = 1
  )
ORDER BY n.created_at DESC;

-- 3. Count notifications by type
SELECT type, COUNT(*) as count 
FROM notifications 
WHERE user_id = 1 
GROUP BY type
ORDER BY type;

-- 4. See which event types you have in_app disabled
SELECT event_type 
FROM notification_preferences 
WHERE user_id = 1 AND in_app = 0;
```

**Analysis**:
- If you disabled in_app for event_type X, you should see NO notifications of type X
- If you enabled in_app for event_type Y, you should see notifications of type Y

---

## Email/Push Test (Important Note!)

### Email Notifications: ⚠️ NOT YET IMPLEMENTED

**What You'll See**:
- ✅ Checkbox appears in UI
- ✅ You can check/uncheck it
- ✅ It saves to database
- ❌ But NO emails are actually sent

**Why**:
- Email delivery service not yet implemented
- Phase 2 work (future enhancement)

**How to Verify**:
```bash
# No email delivery queues exist yet
SELECT * FROM notification_deliveries 
WHERE channel = 'email';  -- Will be empty or non-existent
```

### Push Notifications: ⚠️ NOT YET IMPLEMENTED

**What You'll See**:
- ✅ Checkbox appears in UI
- ✅ You can check/uncheck it
- ✅ It saves to database
- ❌ But NO push notifications are sent

**Why**:
- Push delivery service not yet implemented
- Default is disabled (push = 0)
- Phase 3 work (future enhancement)

---

## Summary: What Actually Works

### ✅ IN-APP NOTIFICATIONS (WORKING)

```
User unchecks "in_app" → Preference saved → Notification dispatch skipped → NO notification received
User checks "in_app" → Preference saved → Notification dispatch happens → Notification received
```

### ❌ EMAIL NOTIFICATIONS (NOT WORKING YET)

```
User checks "email" → Preference saved → But NO email sent (not implemented)
User unchecks "email" → Preference saved → (doesn't matter, no email anyway)
```

### ❌ PUSH NOTIFICATIONS (NOT WORKING YET)

```
User checks "push" → Preference saved → But NO push sent (not implemented)
User unchecks "push" → Preference saved → (doesn't matter, no push anyway)
```

---

## Code Reference: Where Preferences Are Checked

If you want to see it in the code:

**File**: `src/Services/NotificationService.php`

**Line 31** (in `dispatchIssueCreated()`):
```php
if (!self::shouldNotify($member['user_id'], 'issue_created')) {
    continue;  // Skip if preference is disabled
}
```

**Lines 315-341** (in `shouldNotify()`):
```php
public static function shouldNotify(
    int $userId,
    string $eventType,
    string $channel = 'in_app'  // Only checks in_app currently
): bool {
    $preference = Database::selectOne(
        'SELECT in_app, email, push FROM notification_preferences WHERE user_id = ? AND event_type = ?',
        [$userId, $eventType]
    );
    
    return (bool) $preference[$channel];
}
```

---

## Troubleshooting

### Issue: Preferences Changed But Notifications Still Arriving

**Check**:
1. Verify preference saved in database
2. Check if the correct channel is set
3. Note: Only "in_app" is implemented

### Issue: Preferences Won't Save

**Check**:
1. Run: `php verify_notification_prefs_fixed.php`
2. Should see: ✅ ALL TESTS PASSED
3. If fails, the SQL fix may not be deployed

### Issue: Email/Push Not Working

**Expected**: Email and push are not implemented yet. This is normal. In-app notifications should work fine.

---

## Conclusion

**Your notification preferences ARE being applied for in-app notifications.**

When you:
- ☑️ **Check "in_app"** → You'll see in-app notifications
- ☐ **Uncheck "in_app"** → You'll NOT see in-app notifications

Email and push preferences are saved but not yet implemented. This is expected per the development roadmap.

---

## Next: Full Verification Test Suite

For complete testing, see: `TEST_NOTIFICATION_PREFERENCES_COMPREHENSIVE.md`
