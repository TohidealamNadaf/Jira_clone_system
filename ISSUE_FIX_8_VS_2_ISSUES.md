# Issue Fix: Baramati Project Shows 8 Issues in Card but Only 2 Exist

## Problem Statement

The Baramati Project card on the **Projects page** displays:
```
8 issues
```

But when you click on the project and view the actual issues, only **2 issues** exist in the database.

## Root Cause

The `projects.issue_count` column contains an outdated/incorrect value that doesn't match the actual number of issues in the `issues` table.

### What Happened

1. Issues were created → `issue_count` was incremented
2. Some issues were deleted → `issue_count` was NOT decremented properly
3. Result: Stored count (8) ≠ Actual count (2)

### Why This Happened

Possible causes:
- Issues were deleted via direct SQL query instead of through the proper delete logic
- The delete operation didn't properly decrement the counter
- Data was imported/migrated without recalculating counts
- A bug in the delete issue functionality

## Solution

Sync the `projects.issue_count` with the actual number of issues in the database.

### Method 1: Automated Fix (Recommended)

**Run this script:**
```
http://localhost:8080/jira_clone_system/FIX_PROJECT_ISSUE_COUNT.php
```

This script:
1. Finds all projects with mismatched issue counts
2. Counts the actual issues for each project
3. Updates the `issue_count` to match reality
4. Shows you a summary of what was fixed

### Method 2: Manual SQL Fix

If you prefer to do it manually, run this SQL:

```sql
-- Update all project issue counts to match actual issues
UPDATE projects p
SET p.issue_count = (
    SELECT COUNT(*) FROM issues i WHERE i.project_id = p.id
);

-- For Baramati Project specifically:
UPDATE projects
SET issue_count = (
    SELECT COUNT(*) FROM issues WHERE project_id = (
        SELECT id FROM projects WHERE key = 'BP' OR name LIKE '%Baramati%'
    )
)
WHERE key = 'BP' OR name LIKE '%Baramati%';
```

## Files Provided

**FIX_PROJECT_ISSUE_COUNT.php** - Automated script to sync all project counts

## Verification Steps

### Step 1: Run the Fix
1. Open: `http://localhost:8080/jira_clone_system/FIX_PROJECT_ISSUE_COUNT.php`
2. Review the table showing before/after counts
3. The script automatically applies fixes

### Step 2: Check Project Card
1. Go to: `http://localhost:8080/jira_clone_system/public/projects`
2. Look at Baramati Project card
3. ✅ Should now show **2 issues** (matching actual count)

### Step 3: Check Issues List
1. Click on "Baramati Project"
2. Go to Issues
3. ✅ Should display **2 issues** in the list

### Step 4: Database Verification
Run this query to confirm:
```sql
SELECT 
    p.key,
    p.name,
    p.issue_count,
    (SELECT COUNT(*) FROM issues i WHERE i.project_id = p.id) as actual_count
FROM projects p
WHERE p.key = 'BP';
```

Expected output:
```
| key | name              | issue_count | actual_count |
|-----|-------------------|-------------|--------------|
| BP  | Baramati Project  | 2           | 2            |
```

## Impact Analysis

### What This Fixes
✅ Project card now shows correct issue count  
✅ Card count matches actual issues  
✅ No data loss (issues are preserved)  
✅ All other project data remains unchanged  

### What This Does NOT Affect
- Issue data itself
- Issue content/details
- Comments
- Workflow
- Any other functionality
- User data
- Project settings

### Risk Level
**VERY LOW** - This is just syncing a counter with actual data

## Prevention Going Forward

### For Code
1. **Always use service methods** for deleting issues instead of direct SQL
2. **Verify issue deletion** includes decrementing the counter
3. Add **unit tests** to verify counter increments/decrements

### For Database
1. Consider adding a **trigger** to auto-sync counts:
```sql
CREATE TRIGGER sync_issue_count_after_insert
AFTER INSERT ON issues
FOR EACH ROW
BEGIN
    UPDATE projects SET issue_count = issue_count + 1 WHERE id = NEW.project_id;
END;

CREATE TRIGGER sync_issue_count_after_delete
AFTER DELETE ON issues
FOR EACH ROW
BEGIN
    UPDATE projects SET issue_count = issue_count - 1 WHERE id = OLD.project_id;
END;
```

2. Run periodic validation (e.g., weekly):
```sql
-- Check for mismatches
SELECT p.key, p.issue_count, COUNT(i.id) as actual
FROM projects p
LEFT JOIN issues i ON p.id = i.project_id
GROUP BY p.id
HAVING p.issue_count != COUNT(i.id);
```

## Summary

| Aspect | Details |
|--------|---------|
| **Problem** | Baramati card shows 8 issues, only 2 exist |
| **Root Cause** | Outdated `issue_count` in projects table |
| **Solution** | Sync counter with actual issue count |
| **Fix Method** | Run `FIX_PROJECT_ISSUE_COUNT.php` |
| **Time to Fix** | 2 minutes |
| **Risk Level** | Very Low |
| **Data Loss** | None |

## Next Steps

1. ✅ Run `http://localhost:8080/jira_clone_system/FIX_PROJECT_ISSUE_COUNT.php`
2. ✅ Verify the Baramati Project card now shows "2 issues"
3. ✅ Click on Baramati Project and verify 2 issues display in the list
4. 📌 Implement database triggers to prevent this in the future (optional)

**Status:** Ready to implement - Execute the fix immediately.
