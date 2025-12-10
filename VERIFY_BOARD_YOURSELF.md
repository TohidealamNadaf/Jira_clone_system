# Verify Board Drag-and-Drop Yourself

This guide helps you confirm that board drag-and-drop actually changes the database.

---

## Quick Test (5 minutes)

### Step 1: Open the Board

Go to: `http://localhost/jira_clone_system/public/projects/BP/board`

You should see a Kanban board with columns like:
- To Do
- In Progress
- In Review
- Done

Each column has issue cards.

### Step 2: Open Browser DevTools

Press `F12` to open DevTools.

Go to: **Console** tab

### Step 3: Check Initialization

Look for this message in the console:

```
📊 Board status: {cards: N, columns: 4, projectKey: "BP", ready: true}
```

If you see this, the drag-and-drop is initialized and ready.

### Step 4: Drag an Issue

1. Click on an issue card (e.g., "BP-1")
2. Drag it to a different column
3. Drop it

Watch the console. You should see:

```
✓ Drag started for BP-1
📡 API Call: {
    url: "/jira_clone_system/public/api/v1/issues/BP-1/transitions",
    method: "POST",
    body: {status_id: 2}
}
📦 API Response: {success: true, issue: {...}}
✓ Issue transitioned successfully
```

### Step 5: Reload the Page

Press `F5` to reload the page.

**Check**: Does the issue stay in the NEW column?

- **YES** → Database change is real ✅
- **NO** → Something is wrong ❌

If YES, the drag-and-drop made a **real database change**.

---

## Detailed Test (15 minutes)

### Method 1: Browser DevTools + Network Tab

#### Step 1: Open Board and DevTools

```
URL: http://localhost/jira_clone_system/public/projects/BP/board
Press: F12 → Network tab
```

#### Step 2: Take Note of Current State

Look at an issue, e.g., "BP-5" in the "To Do" column.

#### Step 3: Drag and Watch Network

1. Drag "BP-5" from "To Do" to "In Progress"
2. In Network tab, look for a request to: `/api/v1/issues/BP-5/transitions`
3. Click on that request to see details:

**Request Headers**:
```
POST /jira_clone_system/public/api/v1/issues/BP-5/transitions
Content-Type: application/json
```

**Request Body**:
```json
{
    "status_id": 2
}
```

**Response**:
```json
{
    "success": true,
    "issue": {
        "id": 5,
        "issue_key": "BP-5",
        "status_id": 2,
        "status_name": "In Progress",
        ...
    }
}
```

#### Step 4: Verify Status Code

The response should be **200 OK** (green).

If it's:
- **200** → Success ✅
- **404** → Issue not found ❌
- **422** → Transition not allowed ❌
- **500** → Server error ❌

#### Step 5: Reload and Confirm

Press F5 to reload the page.

Check: Is "BP-5" now in "In Progress" column?

- **YES** → Database change confirmed ✅
- **NO** → UI-only change ❌

---

### Method 2: Database Query

#### Option A: Via phpMyAdmin

1. Open: `http://localhost/phpmyadmin`
2. Select database: `jira_clone` (or your database name)
3. Click: **SQL** tab
4. Run this query:

```sql
SELECT issue_key, status_id, s.name as status_name 
FROM issues i 
JOIN statuses s ON i.status_id = s.id 
WHERE issue_key = 'BP-1';
```

**Result Before Drag**:
```
| BP-1 | 1 | To Do |
```

**After Dragging to "In Progress"**:
```
| BP-1 | 2 | In Progress |
```

If the status_id changed, the database was updated ✅

#### Option B: Via Command Line

```bash
cd c:\xampp\htdocs\jira_clone_system

mysql -h localhost -u root -p jira_clone << EOF
SELECT issue_key, status_id, s.name as status_name 
FROM issues i 
JOIN statuses s ON i.status_id = s.id 
WHERE issue_key = 'BP-1';
EOF
```

Check if `status_id` changed after dragging.

---

### Method 3: PHP Verification Script

Run this command:

```bash
cd c:\xampp\htdocs\jira_clone_system
php verify_board_production.php
```

This script will:
- ✓ Check database connection
- ✓ Verify projects exist
- ✓ Verify issues exist
- ✓ Test a transition
- ✓ Confirm database was updated
- ✓ Show the exact SQL being run

---

## What to Look For

### ✅ Signs It's Working Correctly

1. **Console messages** appear during drag
   - "✓ Drag started for [ISSUE-KEY]"
   - "📡 API Call" message
   - "📦 API Response" message

2. **Network request** is made to `/api/v1/issues/.../transitions`
   - Status: **200 OK**
   - Method: **POST**
   - Response includes: `"success": true`

3. **Issue stays in new column** after page reload
   - Hard proof the database changed

4. **Database query shows new status_id**
   - Absolute confirmation of persistence

### ❌ Signs Something Is Wrong

1. **No console messages** during drag
   - JavaScript initialization failed
   - Fix: Check browser console for errors

2. **No network request** appears
   - JavaScript isn't calling the API
   - Fix: Check JavaScript for errors

3. **Error status code** (404, 422, 500)
   - Check error message in response
   - 404 = Issue not found
   - 422 = Transition not allowed
   - 500 = Server error

4. **Issue returns to original column** after page reload
   - Database didn't change
   - Something is broken

---

## Troubleshooting

### Issue: Drag doesn't work at all

**Check 1**: Open console (F12). Do you see:
```
📊 Board status: {cards: N, columns: 4, ready: true}
```

If NO:
- Board elements might not be rendering
- Check if you're logged in
- Check if project has issues
- Reload the page

If YES:
- Try dragging a different issue

**Check 2**: Are you dragging correctly?
- Click and hold the issue card
- Move mouse to another column
- Release the mouse button
- The card should move in the UI

### Issue: Drag works but change doesn't persist

**Check 1**: Open Network tab during drag

Do you see a request to `/api/v1/issues/.../transitions`?

If NO:
- JavaScript isn't calling the API
- Check console for JavaScript errors

If YES:
- What's the response status?
- **200** → Server says success, check database
- **422** → Transition not allowed, check workflow rules
- **500** → Server error, check server logs

**Check 2**: Is the issue moving in the UI?

If YES but not persisting:
- API response says success, but database didn't change
- Likely a backend bug
- Check `src/Services/IssueService.php` line 430

If NO:
- API request failed
- Check error response

### Issue: Column shows wrong count

After dragging, the count badge on columns might be wrong.

**This is a UI bug, not a database issue.**

The database is correct. The UI just needs to refresh.

Solution:
- Reload the page (F5)
- Issue count will show correctly

---

## What the Database Query Should Show

### Before Dragging

```bash
mysql> SELECT issue_key, status_id FROM issues WHERE issue_key = 'BP-1';

+----------+-----------+
| issue_key | status_id |
+----------+-----------+
| BP-1     |         1 |
+----------+-----------+
```

Status ID = 1 = "To Do"

### After Dragging to "In Progress"

```bash
mysql> SELECT issue_key, status_id FROM issues WHERE issue_key = 'BP-1';

+----------+-----------+
| issue_key | status_id |
+----------+-----------+
| BP-1     |         2 |
+----------+-----------+
```

Status ID = 2 = "In Progress"

The `updated_at` timestamp will also change automatically.

---

## Complete Flow Diagram

```
┌─────────────────────────────┐
│  User Drags Issue Card      │
│  (Click + Drag + Drop)      │
└────────────┬────────────────┘
             ↓
┌─────────────────────────────────────┐
│  JavaScript detects drop event      │
│  Gets issue_key and status_id       │
└────────────┬────────────────────────┘
             ↓
┌─────────────────────────────────────┐
│  POST /api/v1/issues/{key}/trans... │
│  Body: { "status_id": 2 }           │
└────────────┬────────────────────────┘
             ↓
┌─────────────────────────────────────┐
│  Server receives request            │
│  IssueApiController::transition()   │
└────────────┬────────────────────────┘
             ↓
┌─────────────────────────────────────┐
│  Validate input                     │
│  Check if issue exists              │
│  Check if transition allowed        │
└────────────┬────────────────────────┘
             ↓
┌──────────────────────────────────────┐
│  Execute SQL UPDATE query            │
│  UPDATE issues SET status_id = 2     │
│  WHERE id = 1                        │
└────────────┬─────────────────────────┘
             ↓
     *** DATABASE CHANGED ***
     (NEW STATUS ID IN DATABASE)
             ↓
┌──────────────────────────────────────┐
│  Record history entry                │
│  Log audit trail                     │
│  Fetch updated issue                 │
└────────────┬─────────────────────────┘
             ↓
┌──────────────────────────────────────┐
│  Return API response                 │
│  { "success": true, "issue": {...} } │
│  Status: 200 OK                      │
└────────────┬─────────────────────────┘
             ↓
┌──────────────────────────────────────┐
│  JavaScript receives response        │
│  Moves card in UI                    │
│  Updates column counts               │
│  Shows success                       │
└────────────┬─────────────────────────┘
             ↓
┌──────────────────────────────────────┐
│  User reloads page (F5)              │
│  Board re-renders from database      │
│  Issue appears in NEW column         │
│  CONFIRMS PERSISTENCE                │
└──────────────────────────────────────┘
```

---

## Conclusion

If you can:
1. Drag an issue to a new column ✓
2. See it move in the UI ✓
3. Reload the page and it stays there ✓
4. Query the database and see the new status_id ✓

Then the board drag-and-drop is **working correctly** and making **real, persistent database changes**.

You can confidently deploy this to production.
