# Production Cleanup Checklist

**Purpose**: Remove debug files, test scripts, and temporary code before production deployment  
**Date**: December 8, 2025  
**Status**: READY TO EXECUTE  

---

## Overview

Before deploying to production, you need to:
1. Remove debug/test files (60+ files)
2. Clean up test data scripts
3. Verify no debug code in production files
4. Update configuration for production
5. Remove development dependencies

---

## PART 1: Debug & Test Files to Delete

### Database Debug Scripts (15 files)
```
Delete:
✓ check_all_tables.php
✓ CHECK_BARAMATI_DATA.php
✓ check_baramati_issues.php
✓ check_baramati.php
✓ check_comments_table.php
✓ check_dates.php
✓ check_foreign_keys.php
✓ check_json_escaping.php
✓ check_notification_db.php
✓ check_notifications_schema.php
✓ check_projects.php
✓ check_schema.php
✓ check_workflow_setup.php
✓ show_columns.php
✓ quick_status_check.php
```

### Debug API Scripts (12 files)
```
Delete:
✓ debug_api_error.php
✓ debug_auth.php
✓ debug_comment.php
✓ debug_cumulative_flow.php
✓ debug_cumulative_flow2.php
✓ debug_cumulative_flow3.php
✓ debug_dropdown.php
✓ debug_flow_logic.php
✓ DEBUG_ISSUE_NOT_FOUND.php
✓ debug_issue_types.php
✓ debug_notification_api.php
✓ debug_notification_prefs_insert.php
✓ debug_notification_update.php
✓ debug_project_creation.php
✓ debug_search.php
✓ debug.php
```

### Debug Notification Scripts (8 files)
```
Delete:
✓ debug_notification_api.php
✓ debug_notification_prefs_insert.php
✓ debug_notification_update.php
✓ DIAGNOSE_ISSUE_COUNT.php
✓ diagnose_project_issue.php
✓ diagnose_velocity.php
✓ PRODUCTION_AUDIT_NOTIFICATION_SYSTEM.php
✓ quick_status_check.php
```

### Test & Verification Scripts (20 files)
```
Delete:
✓ test_api_response.php
✓ test_api_update_prefs.php
✓ test_bp7_issue.php
✓ test_button_visibility.php
✓ test_comment_endpoints.php
✓ test_comment_flow.php
✓ test_comments_table.php
✓ test_cumulative_flow_render.php
✓ test_data_structure.php
✓ test_debug.php
✓ test_fresh.php
✓ test_notification_fix.php
✓ test_notification_preferences.php
✓ test_notification_prefs.php
✓ test_notifications_api.php
✓ test_notifications_fix.php
✓ test_notifications_page.php
✓ test_notifications_setup.php
✓ test_parameter_binding.php
✓ test_preference_persistence.php
```

### HTML Test Pages (3 files)
```
Delete:
✓ test_dropdown_scroll.html
✓ test_modal_responsive.html
✓ test-create-modal.php
```

### Velocity Test Scripts (9 files)
```
Delete:
✓ test_velocity_chart.php
✓ test_velocity_controller.php
✓ test_velocity_data.php
✓ test_velocity_direct.php
✓ test_velocity_endpoint.php
✓ test_velocity_raw.php
✓ test_velocity_simple.php
✓ test_velocity_view.php
✓ test_view_render.php
```

### Utility Test Scripts (8 files)
```
Delete:
✓ test_prepared.php
✓ test_quick_create_endpoint.php
✓ test_schema_fix.php
✓ test_search_filter.php
✓ test_select2_projects.php
✓ verify_board_2.php
✓ verify_fixes.php
✓ verify_notification_fix.php
✓ verify_notification_prefs_fixed.php
✓ verify_notifications.php
```

### Setup & Seed Scripts to Review
```
Keep (needed for migrations):
✓ scripts/run-migrations.php
✓ scripts/initialize-notifications.php
✓ scripts/verify-and-seed.php
✓ scripts/process-notification-retries.php

Review/Remove as needed:
✓ scripts/setup_notifications.php
✓ scripts/seed_settings.php
✓ create_baramati.php
✓ create_notification_tables.php
✓ create_notifications_tables.php
✓ apply_cascade_fix.php
✓ assign_admin_issues.php
✓ assign_test_issues.php
✓ fix_missing_roles.php
✓ fix_notifications_schema.php
✓ fix_notifications_tables.php
✓ initialize_notification_preferences.php
✓ install_notifications.php
✓ run_fix_notifications.php
✓ simulate_full_test.php
```

### Capture/Report Scripts (2 files)
```
Delete:
✓ capture_report_page.php
✓ capture_report_page2.php
```

### SQL Fix Files (2 files)
```
Keep or delete after verification:
? fix_admin_issues.sql
? FIX_CASCADE_DELETE_CORRECT.sql
```

---

## PART 2: Documentation Files (Keep These)

### Critical Documentation (KEEP)
```
Keep - Required for system understanding:
✓ AGENTS.md - Development standards
✓ DEVELOPER_PORTAL.md - Navigation
✓ COMPREHENSIVE_PROJECT_SUMMARY.md - System overview
✓ README.md - Project overview
✓ PRODUCTION_READINESS_ASSESSMENT.md - Deployment guide
✓ PRODUCTION_DEPLOYMENT_CHECKLIST.md - Step-by-step
✓ START_PRODUCTION_DEPLOYMENT_HERE.md - Quick start
✓ PHASE_2_IMPLEMENTATION_MASTER_PLAN.md - Roadmap
```

### Implementation Documentation (KEEP)
```
Keep - Feature documentation:
✓ ADMIN_PAGES_IMPLEMENTATION.md
✓ ADMIN_AUTHORITY_VERIFICATION.md
✓ ADMIN_PROTECTION_FINAL_SUMMARY.md
✓ NOTIFICATION_PREFERENCES_COMPLETE_VERIFICATION_GUIDE.md
✓ REPORT_UI_STANDARDS.md
✓ UI_REDESIGN_COMPLETE.md
✓ UI_COMPONENT_GUIDE.md
✓ CREATE_MODAL_FIX_COMPLETE.md
```

### Optional Documentation (Can Delete or Archive)
```
Archive or delete - Intermediate steps/debugging:
? All "FIX_*" documents (keep 1-2 key ones)
? All "START_HERE_*" documents (except main ones)
? All debug/troubleshooting guides
? All test result documents
? All chat history/notes
```

**Recommendation**: Keep all `.md` files initially. After deployment, archive debug docs to folder `/archive/debug-docs/`

---

## PART 3: Code Cleanup

### Check for Debug Code in Production Files

**Files to scan for debug code**:
```
src/Controllers/*.php
src/Services/*.php
src/Middleware/*.php
src/Core/*.php
views/*.php
routes/api.php
routes/web.php
```

**Search for these patterns and REMOVE**:
```
// TODO: Remove debug
console.log()
var_dump()
die()
exit()
dd()
echo "DEBUG:"
print_r()
debug_print_backtrace()
```

**Command to find debug code**:
```bash
# Check PHP files for debug code
grep -r "var_dump\|die(\|exit(\|dd(\|echo.*DEBUG" src/ routes/ views/ public/ --include="*.php"

# Check JS files for console.log
grep -r "console.log" public/assets/js/ --include="*.js"
```

---

## PART 4: Configuration & Credentials

### Update config/config.php for Production

**BEFORE deployment, verify these are set**:

```php
// 1. Database credentials
'host' => env('DB_HOST', 'localhost'),      // ✓ Use env var, not hardcoded
'database' => env('DB_NAME', 'jira_clone'),
'user' => env('DB_USER', 'root'),            // ✓ Use strong password
'password' => env('DB_PASS', ''),            // ✓ Must be set in env

// 2. Security
'app_key' => env('APP_KEY', ''),            // ✓ Set long random string
'jwt_secret' => env('JWT_SECRET', ''),      // ✓ Set long random string

// 3. Error handling
'debug' => env('APP_DEBUG', false),         // ✓ MUST be false in production
'log_errors' => true,                       // ✓ Keep logs enabled
'log_file' => '/var/log/jira_clone.log',   // ✓ Point to log file

// 4. Email (Phase 2)
'email' => [
    'driver' => env('MAIL_DRIVER', 'log'),
    'from' => env('MAIL_FROM', 'noreply@example.com'),
    'smtp_host' => env('SMTP_HOST', ''),
    'smtp_port' => env('SMTP_PORT', 587),
    'smtp_user' => env('SMTP_USER', ''),
    'smtp_pass' => env('SMTP_PASS', ''),
]

// 5. Session
'session_secure' => true,                   // ✓ HTTPS only
'session_httponly' => true,                 // ✓ Prevent JS access
'session_samesite' => 'Lax',                // ✓ CSRF protection
```

### Environment Variables to Set

**In production, set these env vars** (don't hardcode!):

```bash
# Database
export DB_HOST="your-production-db.com"
export DB_NAME="jira_production"
export DB_USER="jira_db_user"
export DB_PASS="strong-random-password-here"

# Security
export APP_KEY="long-random-string-64-chars"
export JWT_SECRET="another-long-random-string-64-chars"

# Email (Phase 2)
export MAIL_DRIVER="smtp"
export MAIL_FROM="notifications@yourdomain.com"
export SMTP_HOST="smtp.sendgrid.net"
export SMTP_PORT="587"
export SMTP_USER="apikey"
export SMTP_PASS="your-sendgrid-api-key"

# App
export APP_DEBUG="false"
export APP_ENV="production"
export APP_URL="https://jira.yourdomain.com"
```

---

## PART 5: Directory Permissions

### Verify Directory Permissions

```bash
# Storage directory - must be writable
chmod 755 storage/
chmod 755 storage/logs/
chmod 755 storage/cache/

# Upload directory - must be writable
chmod 755 public/uploads/

# View/Template cache (if using caching)
chmod 755 storage/views/

# Config should be readable, not writable
chmod 644 config/config.php

# Database directory
chmod 755 database/
```

### File Permissions for Security

```bash
# Sensitive files - NOT world readable
chmod 600 config/config.php
chmod 600 .env (if using env file)

# Bootstrap files
chmod 755 bootstrap/autoload.php
chmod 755 bootstrap/app.php

# Public files - accessible
chmod 755 public/*
```

---

## PART 6: Database Cleanup

### Before Deployment

1. **Backup existing database**
   ```bash
   mysqldump -u root -p jira_clone > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Verify test data is acceptable**
   - Check if test projects should remain
   - Check if test users (admin@example.com) should remain
   - Delete if test data only, keep if sample data useful

3. **Optimize database**
   ```sql
   -- Run this in MySQL
   OPTIMIZE TABLE projects;
   OPTIMIZE TABLE issues;
   OPTIMIZE TABLE comments;
   OPTIMIZE TABLE notifications;
   -- etc for all tables
   
   -- Analyze tables
   ANALYZE TABLE projects;
   -- etc for all tables
   ```

4. **Set up backups**
   ```bash
   # Create cron job for daily backup at 2am
   0 2 * * * /path/to/backup-script.sh
   
   # Keep 30 days of backups
   ```

---

## PART 7: Pre-Deployment Verification

### Code Quality Check
```bash
# Check for PHP syntax errors
for f in $(find src/ routes/ views/ -name "*.php"); do
    php -l "$f" || echo "ERROR in $f"
done

# Should output: "No syntax errors detected"
```

### Test Execution
```bash
# Run all tests
php tests/TestRunner.php

# Should show: "All tests passed ✓"
```

### Security Check
```bash
# Look for common vulnerabilities
grep -r "eval(" src/ views/ --include="*.php"  # Should be empty
grep -r "exec(" src/ views/ --include="*.php"  # Should be empty
grep -r "shell_exec(" src/ views/ --include="*.php"  # Should be empty
grep -r "\$\$" src/ views/ --include="*.php"  # Variable variables - avoid
```

### Performance Check
```bash
# Verify indexes exist
mysql -u root -p jira_clone < check_indexes.sql

# Verify query performance
# Test slow queries log
```

---

## PART 8: Deletion Commands

### Execute This Script to Delete Debug Files

**Save as `cleanup_debug_files.sh`**:

```bash
#!/bin/bash

# Define files to delete
DEBUG_FILES=(
    "check_all_tables.php"
    "CHECK_BARAMATI_DATA.php"
    "check_baramati_issues.php"
    "check_baramati.php"
    "check_comments_table.php"
    "check_dates.php"
    "check_foreign_keys.php"
    "check_json_escaping.php"
    "check_notification_db.php"
    "check_notifications_schema.php"
    "check_projects.php"
    "check_schema.php"
    "check_workflow_setup.php"
    "show_columns.php"
    "debug_api_error.php"
    "debug_auth.php"
    "debug_comment.php"
    "debug_cumulative_flow.php"
    "debug_cumulative_flow2.php"
    "debug_cumulative_flow3.php"
    "debug_dropdown.php"
    "debug_flow_logic.php"
    "DEBUG_ISSUE_NOT_FOUND.php"
    "debug_issue_types.php"
    "debug_notification_api.php"
    "debug_notification_prefs_insert.php"
    "debug_notification_update.php"
    "debug_project_creation.php"
    "debug_search.php"
    "debug.php"
    "test_api_response.php"
    "test_api_update_prefs.php"
    "test_bp7_issue.php"
    "test_button_visibility.php"
    "test_comment_endpoints.php"
    "test_comment_flow.php"
    "test_comments_table.php"
    "test_cumulative_flow_render.php"
    "test_data_structure.php"
    "test_debug.php"
    "test_fresh.php"
    "test_notification_fix.php"
    "test_notification_preferences.php"
    "test_notification_prefs.php"
    "test_notifications_api.php"
    "test_notifications_fix.php"
    "test_notifications_page.php"
    "test_notifications_setup.php"
    "test_parameter_binding.php"
    "test_preference_persistence.php"
    "test_prepared.php"
    "test_quick_create_endpoint.php"
    "test_schema_fix.php"
    "test_search_filter.php"
    "test_select2_projects.php"
    "test_velocity_chart.php"
    "test_velocity_controller.php"
    "test_velocity_data.php"
    "test_velocity_direct.php"
    "test_velocity_endpoint.php"
    "test_velocity_raw.php"
    "test_velocity_simple.php"
    "test_velocity_view.php"
    "test_view_render.php"
    "verify_board_2.php"
    "verify_fixes.php"
    "verify_notification_fix.php"
    "verify_notification_prefs_fixed.php"
    "verify_notifications.php"
    "test_dropdown_scroll.html"
    "test_modal_responsive.html"
    "test-create-modal.php"
    "capture_report_page.php"
    "capture_report_page2.php"
    "quick_status_check.php"
)

# Delete files
for file in "${DEBUG_FILES[@]}"; do
    if [ -f "$file" ]; then
        rm "$file"
        echo "Deleted: $file"
    fi
done

echo "Cleanup complete!"
```

**Run it**:
```bash
chmod +x cleanup_debug_files.sh
./cleanup_debug_files.sh
```

---

## PART 9: Verification Checklist

After cleanup, verify:

```
Code Cleanup:
- [ ] No debug files remain in root directory
- [ ] No test scripts in root
- [ ] No temporary files
- [ ] No backup files (*.bak, *.old)

Code Quality:
- [ ] No PHP syntax errors
- [ ] No console.log in production code
- [ ] No var_dump/dd() statements
- [ ] No hardcoded credentials

Configuration:
- [ ] debug = false in config
- [ ] Database credentials in env vars
- [ ] JWT secret configured
- [ ] App key configured
- [ ] HTTPS configured

Database:
- [ ] Backup taken
- [ ] Tables optimized
- [ ] Indexes verified
- [ ] Permissions correct

Security:
- [ ] config.php permissions: 600
- [ ] storage/ permissions: 755
- [ ] public/uploads/ permissions: 755
- [ ] No sensitive data in files

Documentation:
- [ ] Key docs in place
- [ ] Deployment guide available
- [ ] Runbooks documented
- [ ] Team trained
```

---

## PART 10: Post-Cleanup Actions

### Create Archive for Debug Files (Optional)

```bash
# Create archive folder
mkdir -p archive/debug-files-backup

# Move all debug docs there
mv DEBUG_*.md archive/debug-files-backup/
mv FIX_*.md archive/debug-files-backup/
mv START_HERE_*.md archive/debug-files-backup/
mv TEST_*.md archive/debug-files-backup/

# Keep only essential docs in root
```

### Final Review

**Before going live, verify**:

1. ✅ All debug files deleted
2. ✅ Code has no debug statements
3. ✅ Configuration set for production
4. ✅ Database backed up
5. ✅ Security settings verified
6. ✅ Tests passing
7. ✅ Performance acceptable

---

## Cleanup Summary

| Category | Files | Action |
|----------|-------|--------|
| Database debug | 15 | Delete |
| API debug | 16 | Delete |
| Test scripts | 30+ | Delete |
| HTML tests | 3 | Delete |
| Debug docs | 60+ | Archive or delete |
| Keep docs | 20+ | Keep |
| Config files | 1 | Update |

**Total**: ~100 files to clean, 20 files to keep

---

## Next Steps After Cleanup

1. ✅ Run final tests
2. ✅ Verify all systems working
3. ✅ Take full backup
4. ✅ Deploy to production
5. ✅ Monitor logs
6. ✅ Train team

---

**Status**: CLEANUP CHECKLIST READY  
**Estimated Time**: 1-2 hours  
**Recommended Before**: Production deployment  

**Start the cleanup only after you've committed all code to version control.**

> Cleanup is important for production security and maintainability.
