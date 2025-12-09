# Quick Start: FIX 6 - Auto-Initialization Script

## What Needs to Be Done

Create a script that initializes notification preferences for all users when the system first runs.

## The Problem (Why FIX 6 Matters)

Without this:
- New users won't have notification preferences in the database
- `shouldNotify()` will use smart defaults (in_app & email enabled)
- System works, but preferences aren't persistent
- Users can't customize their notification settings

With this:
- Users automatically get notification preferences on first run
- All 9 event types initialized per user
- Users can then customize them
- Clean, production-ready initialization

## Quick Stats

| Item | Value |
|------|-------|
| **Time**: | 20 minutes |
| **Complexity**: | Low |
| **Files to Create**: | 1 (scripts/initialize-notifications.php) |
| **DB Changes**: | None (uses existing tables) |
| **Impact**: | Infrastructure only |

## What FIX 6 Will Do

```php
// For each user in the system (7 in seed data):
for ($userId = 1; $userId <= 7; $userId++) {
    // For each event type (9 total):
    foreach ($EVENT_TYPES as $eventType) {
        // Create preference with smart defaults
        Database::insertOrUpdate('notification_preferences', [
            'user_id' => $userId,
            'event_type' => $eventType,
            'in_app' => 1,      // Enabled by default
            'email' => 1,       // Enabled by default
            'push' => 0,        // Disabled by default
        ]);
    }
}
```

## Event Types to Initialize

```
1. issue_created
2. issue_assigned
3. issue_commented
4. issue_status_changed
5. issue_mentioned
6. issue_watched
7. project_created
8. project_member_added
9. comment_reply
```

## The 9 Event Types (From Database)

These come from the ENUM in the schema:
```php
const EVENT_TYPES = [
    'issue_created',
    'issue_assigned',
    'issue_commented',
    'issue_status_changed',
    'issue_mentioned',
    'issue_watched',
    'project_created',
    'project_member_added',
    'comment_reply',
];
```

## Expected Output

```
Initializing notification preferences...

Getting user count... Found 7 users
Getting user IDs... 
  - User 1 (admin@example.com)
  - User 2 (john@example.com)
  - User 3 (jane@example.com)
  - User 4 (bob@example.com)
  - User 5 (alice@example.com)
  - User 6 (charlie@example.com)
  - User 7 (diana@example.com)

Initializing 63 preferences (7 users × 9 events)...
✅ All preferences initialized successfully

Summary:
- Users: 7
- Event Types: 9
- Total Preferences: 63
- Status: ✅ COMPLETE
```

## How It Will Be Called

```bash
# Option 1: Direct execution
php scripts/initialize-notifications.php

# Option 2: From seed script (after running migrations)
php scripts/verify-and-seed.php
```

## Success Criteria

- [x] Script queries all users from database
- [x] Script iterates through all 9 event types
- [x] Creates 63 preference records (7 × 9)
- [x] Uses correct smart defaults
- [x] Idempotent (safe to run multiple times)
- [x] Provides clear output/logging
- [x] Error handling with meaningful messages

## File Location

**Create**: `scripts/initialize-notifications.php`

**Structure**:
```php
<?php declare(strict_types=1);

// Include autoloader
require_once __DIR__ . '/../bootstrap/autoload.php';

// Event types constant
const EVENT_TYPES = [
    'issue_created',
    'issue_assigned',
    // ... etc
];

// Main function
function initializeNotifications(): void {
    // Logic here
}

// Run if called directly
if (php_sapi_name() === 'cli') {
    initializeNotifications();
}
```

## Testing the Script

After FIX 6 is complete:

```bash
# Run the script
php scripts/initialize-notifications.php

# Verify in database
SELECT COUNT(*) FROM notification_preferences;
# Expected: 63 (or 7 × 9)

# Check specific user preferences
SELECT * FROM notification_preferences WHERE user_id = 1;
# Expected: 9 rows (one for each event type)

# Check defaults are correct
SELECT * FROM notification_preferences WHERE user_id = 1 AND event_type = 'issue_created';
# Expected: in_app=1, email=1, push=0
```

## Integration Notes

- FIX 5 made `shouldNotify()` smart (good defaults)
- FIX 6 makes those preferences persistent
- Future FIX 7 (migration runner) will call this automatically
- Together: Auto-setup on first run

## Next After FIX 6

- FIX 7: Create migration runner
- FIX 8: Add error logging
- FIX 9: Verify API routes
- FIX 10: Performance testing

---

**Ready to start FIX 6?** Go for it! 20 minutes to another milestone. 💪
