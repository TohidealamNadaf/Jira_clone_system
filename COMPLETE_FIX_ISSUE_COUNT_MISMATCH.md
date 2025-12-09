# Complete Fix: Issue Count Mismatch (8 vs 2)

## Problem Summary

**Symptom:** Baramati Project card shows "8 issues" but only 2 issues actually exist  
**Location:** Projects page project card  
**Root Cause:** `projects.issue_count` is not being decremented when issues are deleted

## Root Cause Analysis

### The Bug Location
File: `src/Services/IssueService.php`  
Method: `deleteIssue()` (lines 383-393)

### What's Wrong
When an issue is deleted, the code:
- ✅ Deletes the issue from the database
- ✅ Logs an audit trail
- ❌ **FAILS to decrement** `projects.issue_count`

### Code Before
```php
public function deleteIssue(int $issueId, int $userId): bool
{
    $issue = $this->getIssueById($issueId);
    if (!$issue) {
        throw new \InvalidArgumentException('Issue not found');
    }

    $this->logAudit('issue_deleted', 'issue', $issueId, $issue, null, $userId);

    return Database::delete('issues', 'id = ?', [$issueId]) > 0;
}
```

### Why This Causes the Problem

1. **Issue created:** `issue_count` incremented from 0 → 1 ✅
2. **Issue deleted:** `issue_count` NOT decremented, stays at 1 ❌
3. **Repeat:** Create/delete 8 times
4. **Result:** `issue_count` = 8, but actual issues = 0
5. **Add 2 new issues:** `issue_count` stays 8, actual issues = 2 ❌

## Solution

### Code Fix Applied

File: `src/Services/IssueService.php`  
Lines: 383-402

The `deleteIssue()` method now:
- ✅ Deletes the issue
- ✅ Logs audit trail
- ✅ **Decrements** `projects.issue_count` when deletion succeeds

### Code After
```php
public function deleteIssue(int $issueId, int $userId): bool
{
    $issue = $this->getIssueById($issueId);
    if (!$issue) {
        throw new \InvalidArgumentException('Issue not found');
    }

    $this->logAudit('issue_deleted', 'issue', $issueId, $issue, null, $userId);

    $deleted = Database::delete('issues', 'id = ?', [$issueId]) > 0;
    
    if ($deleted) {
        // Decrement the issue count in the project
        Database::query(
            "UPDATE projects SET issue_count = issue_count - 1 WHERE id = ?",
            [$issue['project_id']]
        );
    }
    
    return $deleted;
}
```

## Data Cleanup Required

The code fix prevents the problem going forward, but the existing mismatch (8 vs 2) must be corrected.

### Option 1: Automated Fix (Recommended)

Run this script:
```
http://localhost:8080/jira_clone_system/FIX_PROJECT_ISSUE_COUNT.php
```

This script will:
1. Count actual issues for each project
2. Update `issue_count` to match reality
3. Show you a summary table

### Option 2: Manual SQL Fix

```sql
-- Sync all project counts
UPDATE projects p
SET p.issue_count = (
    SELECT COUNT(*) FROM issues i WHERE i.project_id = p.id
);

-- Verify Baramati specifically
SELECT 
    key, 
    name, 
    issue_count,
    (SELECT COUNT(*) FROM issues WHERE project_id = p.id) as actual_count
FROM projects p
WHERE key = 'BP';
```

## Complete Workflow

### Step 1: Code Fix ✅ DONE
The `deleteIssue()` method has been updated to decrement the counter.

### Step 2: Data Cleanup (YOU DO THIS)
Choose one:
- **Easy:** Run `FIX_PROJECT_ISSUE_COUNT.php` in your browser
- **Manual:** Run the SQL UPDATE query

### Step 3: Verify
1. Go to Projects page
2. Baramati Project card should show "2 issues"
3. Click on the project
4. Issues list should display exactly 2 issues

## Prevention Going Forward

### What This Fix Enables
- ✅ Issue count always stays in sync with database
- ✅ No more mismatches from deleting issues
- ✅ Count increments when creating (already working)
- ✅ Count decrements when deleting (NOW FIXED)

### Best Practices
1. **Always use the service method** for deleting issues (don't use direct SQL)
2. **Never manually modify** `issue_count` without updating issues
3. **Run periodic audits** to catch any mismatches

## Files Modified

### src/Services/IssueService.php
- **Method:** `deleteIssue()`
- **Lines:** 383-402 (was 383-393)
- **Changes:** Added counter decrement logic
- **Impact:** Prevents future count mismatches

## Files Provided

1. **FIX_PROJECT_ISSUE_COUNT.php** - Corrects existing mismatch
2. **COMPLETE_FIX_ISSUE_COUNT_MISMATCH.md** - This document
3. **src/Services/IssueService.php** - Code fix applied

## Testing Checklist

### Before Running Fix Script
- [ ] Database is backed up (if you want to be safe)
- [ ] You're logged in to the application
- [ ] You can access `http://localhost:8080/jira_clone_system/FIX_PROJECT_ISSUE_COUNT.php`

### After Running Fix Script
- [ ] Project card shows correct count
- [ ] Issues list shows correct number of issues
- [ ] Can click on issues without errors
- [ ] Can create new issues (counter increments)
- [ ] Can delete issues (counter decrements)

### Verification Queries
```sql
-- Check Baramati Project
SELECT key, name, issue_count FROM projects WHERE key = 'BP';

-- Count actual issues in Baramati
SELECT COUNT(*) FROM issues WHERE project_id = (
    SELECT id FROM projects WHERE key = 'BP'
);

-- They should be equal
```

## Impact Summary

| Aspect | Details |
|--------|---------|
| **Files Changed** | 1 (IssueService.php) |
| **Lines Added** | 10 |
| **Lines Removed** | 1 |
| **Net Change** | 9 lines |
| **Breaking Changes** | None |
| **Data Loss** | None |
| **Risk Level** | Very Low |
| **Time to Deploy** | 1 minute (code) + 2 minutes (data cleanup) |

## Summary

### Problem
Deleting issues didn't decrement the project's issue count counter, causing mismatches.

### Solution
1. **Code Fix:** Updated `deleteIssue()` to decrement counter
2. **Data Cleanup:** Sync existing counts with actual data

### Action Items
1. ✅ Code fix already applied
2. 📋 Run `FIX_PROJECT_ISSUE_COUNT.php` to fix existing mismatch
3. ✅ Future deletes will work correctly

## Status

**Code Fix:** ✅ COMPLETE  
**Data Cleanup:** ⏳ PENDING (run the fix script)  
**Ready to Deploy:** ✅ YES

---

## Next Steps

1. Navigate to: `http://localhost:8080/jira_clone_system/FIX_PROJECT_ISSUE_COUNT.php`
2. Review the table showing project counts
3. Allow the script to sync the counts
4. Go back to Projects page
5. Verify Baramati Project now shows "2 issues"

**Total Time Required:** ~5 minutes
