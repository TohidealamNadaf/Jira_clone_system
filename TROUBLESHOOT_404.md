# Troubleshooting: "Issue not found" 404 Error

## ⚡ Quick Fix (2 minutes)

### Step 1: Verify Issue Exists
Open this in your browser:
```
http://localhost/DEBUG_ISSUE_NOT_FOUND.php
```

### Step 2: Copy Issue Key
You should see something like:
```
✅ Found 2 issues:
   - TEST-1: My First Issue
   - TEST-2: Second Issue
```

### Step 3: Use Correct URL
Visit the issue using the key:
```
http://localhost/issue/TEST-1
```

**That's it!** Comments should work now.

---

## 🔍 Detailed Troubleshooting

### Issue 1: Diagnostic Page Shows ❌ NO ISSUES FOUND

**Cause:** You haven't created any issues yet

**Fix:**
1. Go to: `http://localhost/dashboard`
2. Click: "Create Issue" button
3. Fill in: Project, Title, Description
4. Click: "Create"
5. You'll be taken to the new issue page automatically

---

### Issue 2: Diagnostic Shows Issues, But Still Getting 404

**Cause:** Wrong URL format

**Compare:**
```
❌ Wrong:
- http://localhost/issue/123
- http://localhost/issue/my-issue
- http://localhost/issue/edit

✅ Correct:
- http://localhost/issue/TEST-1
- http://localhost/issue/PROJ-42
- http://localhost/issue/BUG-7
```

The key format is always: **PROJECT-NUMBER**

---

### Issue 3: Can't Access Diagnostic Page

**Possible causes:**

1. **PHP not working**
   - Check if `http://localhost/` loads
   - If not, restart XAMPP

2. **Wrong path**
   - Use: `http://localhost/DEBUG_ISSUE_NOT_FOUND.php`
   - Not: `file:///C:/xampp/htdocs/jira_clone_system/DEBUG_ISSUE_NOT_FOUND.php`

3. **File doesn't exist**
   - Check file exists at: `C:\xampp\htdocs\jira_clone_system\DEBUG_ISSUE_NOT_FOUND.php`

---

## 🛠️ Manual Diagnostic

If diagnostic page doesn't help, check manually:

### In Browser Console (F12)
```javascript
// Check what issue key is being requested
console.log('Current URL:', window.location.href);
// Look for pattern: /issue/PROJECT-NUMBER
```

### In MySQL
```sql
-- Check if your issue key exists
SELECT * FROM issues WHERE issue_key = 'TEST-1';

-- If empty result, issue doesn't exist. Create one first.
```

---

## 📋 Checklist

- [ ] XAMPP is running (PHP + MySQL both green)
- [ ] Browser can access `http://localhost/`
- [ ] You created at least 1 project
- [ ] You created at least 1 issue
- [ ] You're using URL format `/issue/PROJECT-NUMBER`
- [ ] You ran diagnostic script and saw ✅ marks

---

## 🎯 Testing Path

Once issue page loads:

```
1. Issue page loads (no 404)
   ↓
2. Scroll to "Comments" section
   ↓
3. Add a comment (test feature)
   ↓
4. Edit comment (hover and click pencil)
   ↓
5. Delete comment (hover and click trash)
   ↓
6. All working! ✅
```

---

## 💡 Most Common Mistake

**Trying to access issue before creating one:**

```
❌ Wrong:
1. Open http://localhost/issue/TEST-1  (before creating TEST-1)
2. Get 404

✅ Correct:
1. Create project "TEST"
2. Create issue in TEST (becomes TEST-1)
3. Open http://localhost/issue/TEST-1  (now it exists)
4. See issue page with comments
```

---

## 🚀 Once Working

Once you see the issue page:

### Test Comment Features:
- **Add**: Type comment, click "Post Comment"
- **Edit**: Hover comment, click pencil icon ✏️
- **Delete**: Hover comment, click trash icon 🗑️
- **Collapse**: Click "Collapse All" button
- **Expand**: Click "Expand All" button
- **Load More**: Click if 5+ comments exist
- **Activity**: Click activity header to collapse/expand

All of these were improved in the recent updates!

---

## ❌ Still Not Working?

1. **Run this command** (check database):
   ```sql
   SELECT issue_key, summary FROM issues LIMIT 1;
   ```
   
2. **Copy the issue_key** from result
   
3. **Visit** `http://localhost/issue/{KEY}`
   - Replace {KEY} with actual key from step 2

4. **If still 404:**
   - Check PHP error logs
   - Restart XAMPP
   - Clear browser cache (Ctrl+F5)

---

## 📞 Still Stuck?

Share:
- [ ] Output from `DEBUG_ISSUE_NOT_FOUND.php`
- [ ] The URL you're trying to access
- [ ] Screenshot of error message
- [ ] Results of database query above

That will help identify the exact issue!

---

**Key Takeaway:** 
The issue key format must match what's in your database. Use the diagnostic tool to find the correct key, then use that in your URL. 🎯
