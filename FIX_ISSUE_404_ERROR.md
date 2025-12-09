# Fix: "Issue not found" 404 Error

## Quick Diagnosis

Visit this diagnostic page to see what's wrong:
```
http://localhost/DEBUG_ISSUE_NOT_FOUND.php
```

This will show:
- ✅ What issues exist in your database
- ✅ What projects exist
- ✅ The correct URL format to use
- ✅ Whether your database is connected

---

## Common Causes & Solutions

### ❌ Problem 1: No Issues Created Yet

**Symptom:** Getting 404 on ANY issue key

**Solution:**
1. Go to dashboard
2. Click "Create Issue"
3. Fill in the form
4. Submit
5. You'll be redirected to the new issue page

---

### ❌ Problem 2: Wrong URL Format

**Symptom:** Your URL looks like `/issue/123` or `/issue/edit`

**Correct Format:**
```
/issue/PROJECT-NUMBER
```

**Examples:**
- ✅ `/issue/TEST-1`
- ✅ `/issue/PROJ-42`
- ✅ `/issue/BUG-999`
- ❌ `/issue/123` (wrong)
- ❌ `/issue/edit` (wrong)

---

### ❌ Problem 3: Issue Key Doesn't Exist

**Symptom:** You're using `/issue/TEST-1` but got 404

**Solution:**
1. Run the diagnostic: `http://localhost/DEBUG_ISSUE_NOT_FOUND.php`
2. Copy the issue key from the list
3. Use the correct key in your URL

---

### ❌ Problem 4: Database Connection Lost

**Symptom:** Error in database, or PHP errors

**Solution:**
1. Ensure XAMPP is running
2. Ensure MySQL is running
3. Check credentials in `.env`
4. Restart services

---

## Step-by-Step Fix

### Step 1: Verify Your Issue Exists
```
Visit: http://localhost/DEBUG_ISSUE_NOT_FOUND.php
Look for: ✅ Found X issues
Copy issue key from list
```

### Step 2: Use Correct URL
```
If issue key is TEST-1:
Go to: http://localhost/issue/TEST-1

If issue key is PROJ-42:
Go to: http://localhost/issue/PROJ-42
```

### Step 3: Verify Comments Load
Once on issue page, scroll down to "Comments" section to verify comment features work.

---

## Advanced: Check Database Directly

If diagnostic page doesn't help, check database directly:

```sql
-- See all issues with their keys
SELECT issue_key, summary FROM issues LIMIT 20;

-- See all projects
SELECT id, key, name FROM projects;

-- Check specific issue
SELECT * FROM issues WHERE issue_key = 'TEST-1';
```

---

## Still Getting 404?

1. Run diagnostic: `http://localhost/DEBUG_ISSUE_NOT_FOUND.php`
2. Share the output
3. Check browser console for JavaScript errors
4. Check PHP error logs

---

## Testing Comments After Fix

Once issue page loads:

1. **Scroll to Comments section**
2. **Test Add Comment** - Type and submit
3. **Test Edit Comment** - Hover over comment, click pencil icon
4. **Test Delete Comment** - Hover over comment, click trash icon
5. **Test Collapse/Expand** - Click "Collapse All" button
6. **Test Load More** - If 5+ comments exist, click "Load More"

All features from the improvements should work!

---

## URL Examples

| What | URL |
|------|-----|
| View issue TEST-1 | `/issue/TEST-1` |
| View issue PROJ-42 | `/issue/PROJ-42` |
| Edit issue TEST-1 | `/issue/TEST-1/edit` |
| Create new issue | `/projects/TEST/issues/create` |

---

## Need More Help?

1. **Is database running?** Check XAMPP MySQL status
2. **Is app running?** Check `http://localhost/` loads
3. **Do issues exist?** Run diagnostic script
4. **Is URL correct?** Use format `/issue/KEY`

Run diagnostic and share output if still stuck! 📋
