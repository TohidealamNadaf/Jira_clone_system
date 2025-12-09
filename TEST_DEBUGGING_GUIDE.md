# Testing & Debugging Guide

**I need specific information to diagnose the issue. Please follow these steps carefully and report EXACTLY what you see:**

## Step 1: Check Projects List

1. Go to: `http://localhost:8080/jira_clone_system/public/projects`
2. **Take a screenshot or tell me:**
   - How many projects are visible?
   - Do you see "Baramati Project" in the list?

## Step 2: Create Project

1. Click "Create Project"
2. Fill in:
   - Project Name: `Test_YYYY_HHMM` (use current time, e.g., `Test_2025_0735`)
   - Project Key: `TS` (short, unique key)
   - Description: `Testing` 
3. Click "Create"
4. **Tell me:**
   - Did you see a success message?
   - Were you redirected to a project page?
   - What is the URL shown in the browser address bar?

## Step 3: Immediately Check Project List (WITHOUT RELOAD)

1. **WITHOUT pressing F5**, go back to `http://localhost:8080/jira_clone_system/public/projects`
2. **Tell me:**
   - Is your new project visible in the list?
   - What does it say for issue count?

## Step 4: Create an Issue

1. Click on your project
2. Click "Create Issue"
3. Fill in:
   - Issue Type: Task
   - Summary: `Test Issue`
   - Description: `Testing issue creation`
4. Click "Create"
5. **Tell me:**
   - Did you see a success message?
   - What URL are you on now?

## Step 5: Check Database Directly

Open phpMyAdmin and check:

```sql
SELECT * FROM projects WHERE key LIKE 'TS%' OR name LIKE 'Test_%';
```

**Tell me:**
- How many rows are returned?
- What are the values for `id`, `key`, `name`, `issue_count`?

## Step 6: The Reload Test

1. In your browser, press **F5** to reload the page
2. **Tell me exactly what happens:**
   - Do you stay on the issue page?
   - Do you see a 404 error?
   - What does the URL show?

## Critical Questions:

**A) At what point does the project disappear?**
   - ☐ Immediately after creating (never shows in list)?
   - ☐ After creating issue (when you reload)?
   - ☐ After adding comment (when you reload)?
   - ☐ After editing comment (when you reload)?

**B) When you see the 404, are you on:**
   - ☐ An issue page (URL like `/issue/TS-1`)?
   - ☐ A project page (URL like `/projects/TS`)?
   - ☐ The projects list?

**C) Open browser console (F12) and tell me if there are any red errors.**

---

## Database Check Script

Run this command:

```bash
cd /d c:\xampp\htdocs\jira_clone_system
php -r "require 'bootstrap/app.php'; use App\Core\Database; $projects = Database::select('SELECT id, key, name, issue_count FROM projects ORDER BY id DESC LIMIT 5'); foreach ($projects as $p) { echo '[' . $p['id'] . '] ' . $p['key'] . ' - ' . $p['name'] . ' (Issues: ' . $p['issue_count'] . ')\n'; }"
```

**Tell me the output.**

---

Please provide these details and I can identify exactly where the problem is.
