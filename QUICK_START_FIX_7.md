# FIX 7: Quick Start - Migration Runner

**Status**: ✅ COMPLETE  
**File**: `scripts/run-migrations.php`  
**One-Command Setup**: `php scripts/run-migrations.php`

---

## What This Does

Automatically sets up your entire database with a single command:

```
Schema + Migrations + Seed Data + Verification + Notifications = 🚀 Ready to Use
```

---

## How to Use (3 Steps)

### Step 1: Ensure MySQL is Running
```bash
# On Windows (XAMPP)
# Start MySQL from XAMPP Control Panel
# OR
mysql -u root -p
```

### Step 2: Run Migration Script
```bash
cd c:\xampp\htdocs\jira_clone_system
php scripts/run-migrations.php
```

### Step 3: Verify Success
Look for this at the end:
```
✅ MIGRATION COMPLETED SUCCESSFULLY
```

---

## What Happens

### Automatically Executed (In Order)

1. **Database Connection Check** - Verifies MySQL connection
2. **Main Schema** - Creates all core tables (users, projects, issues, etc.)
3. **Migration Files** - Applies all migrations in order
4. **Seed Data** - Inserts reference data
5. **Verification** - Confirms everything was created
6. **Notifications** - Initializes notification preferences (63 records)
7. **Final Check** - Displays statistics and confirms readiness

### Created Tables (10 Core)
- ✅ users
- ✅ projects
- ✅ issues
- ✅ comments
- ✅ notifications
- ✅ notification_preferences
- ✅ notification_deliveries
- ✅ roles
- ✅ issue_types
- ✅ statuses

### Created Records
- 5 issue types (Epic, Story, Task, Bug, Sub-task)
- 5 priorities (Highest, High, Medium, Low, Lowest)
- 3 project categories
- 8 statuses
- 63 notification preferences

---

## Success Output Example

```
============================================================
JIRA CLONE DATABASE MIGRATION RUNNER
Version: 1.0.0 (Production)
Date: 2025-12-08 10:30:45

✅ Database connection established

============================================================
STEP 1: Executing main database schema (schema.sql)
─────────────────────────────────────────────────────────
✅ Main schema executed (156 statements)
ℹ️  Database and tables created/verified

============================================================
STEP 2: Executing migration files (database/migrations/*.sql)
─────────────────────────────────────────────────────────
✅ Migration executed: 001_create_notifications_tables.sql
✅ Migration executed: add_comment_indexes.sql
✅ Migration executed: add_comment_history.sql
✅ 3 migration file(s) executed successfully

... (more steps) ...

============================================================
✅ MIGRATION COMPLETED SUCCESSFULLY
============================================================

Database Statistics:
  • users                     : 7 rows
  • projects                  : 2 rows
  • issues                    : 8 rows
  • comments                  : 15 rows
  • notifications             : 0 rows
  • notification_preferences  : 63 rows
  • notification_deliveries   : 0 rows
  • roles                      : 6 rows
  • issue_types              : 5 rows
  • statuses                  : 8 rows

✨ Database is now ready for production use!
```

---

## If Something Goes Wrong

### Error: "Cannot connect to database"
```
❌ Please ensure:
  1. MySQL server is running
  2. Database credentials in config/config.php are correct
  3. User has CREATE/ALTER/DROP privileges
```

**Fix**: Check your MySQL credentials in `config/config.php`

### Error: "Schema file not found"
```
❌ ERROR: Schema file not found: /path/to/schema.sql
```

**Fix**: Ensure `database/schema.sql` exists

### Warning: "Migrations directory not found"
```
⚠️  Migrations directory not found
ℹ️  Skipping migration files
```

**Fix**: Create `database/migrations/` directory (optional, but recommended)

---

## Advanced Usage

### Run Without Notifications Init (if issues)
Edit `scripts/run-migrations.php` line 253, comment out:
```php
// require INIT_NOTIFICATIONS_FILE;
```

### Run Only Schema (if custom setup)
Create `setup-minimal.php`:
```php
require_once __DIR__ . '/bootstrap/app.php';
use App\Core\Database;

$sql = file_get_contents(__DIR__ . '/database/schema.sql');
// Split and execute statements...
```

### Check Database After Setup
```bash
# Access via PHPMyAdmin
http://localhost/phpmyadmin/

# Or via MySQL CLI
mysql -u root -p jiira_clonee_system
SHOW TABLES;
SELECT COUNT(*) FROM users;
```

---

## Next Steps

After running the migration script:

1. ✅ **Database is set up**
2. 📝 **Create admin account** (if not auto-created)
3. ⚙️ **Configure settings** (if needed)
4. 🚀 **Start application**: http://localhost/jira_clone_system/public/
5. 🔑 **Login with test account** (email from seed data)

---

## Key Files

| File | Purpose |
|------|---------|
| `scripts/run-migrations.php` | Main migration runner |
| `database/schema.sql` | Core database schema |
| `database/migrations/*.sql` | Additional migrations |
| `database/seed.sql` | Reference data |
| `scripts/verify-and-seed.php` | Verification logic |
| `scripts/initialize-notifications.php` | Notification setup |

---

## Production Ready?

After running this script:
- ✅ Schema: Complete with all tables
- ✅ Migrations: All applied
- ✅ Data: Seeded with defaults
- ✅ Notifications: Initialized (63 preferences)
- ✅ Verification: All checks pass

**Status**: Ready for production use 🚀

---

## Documentation

For more details, see: `FIX_7_MIGRATION_RUNNER_COMPLETE.md`
