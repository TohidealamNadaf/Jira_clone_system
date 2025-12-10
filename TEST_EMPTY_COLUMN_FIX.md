# Test: Empty Column Drag & Drop Fix

**Issue**: When dragging to an empty column, "No issues" text remained visible and card disappeared on refresh

**Fix Applied**: 
1. Enhanced `updateStatusCounts()` to hide/show "No issues" message
2. Better error handling in drop listener
3. Update counts after both success and failure

---

## Test Steps

### Setup
1. Navigate to: `/projects/BP/board`
2. Find a column with "No issues" message
3. Open browser Console: F12 → Console tab

### Test 1: Drag to Empty Column
```
1. Click and drag an issue card from a non-empty column
2. Drop it in the empty column (one showing "No issues")
3. Expected:
   - Card moves to the column
   - "No issues" text DISAPPEARS (hidden)
   - Status count badge updates
   - Console shows: ✓ Issue transitioned successfully
   - Console shows: ✓ UI updated
```

### Test 2: Verify Persistence
```
1. After successful drop, reload page (F5)
2. Expected:
   - Issue is still in the same column
   - Column count badge shows correct number
   - "No issues" message is HIDDEN
```

### Test 3: Multiple Cards to Empty Column
```
1. Drag another issue to the same column
2. Expected:
   - Both cards visible
   - "No issues" text HIDDEN
   - Count badge shows: 2
3. Reload page
4. Both cards should still be there
```

### Test 4: Drag Back to Original
```
1. Drag a card from the populated column
2. Drop it back in original empty column
3. Expected:
   - "No issues" message REAPPEARS (if column is now empty)
   - Count badge shows: 0
4. Reload and verify
```

---

## Expected Console Output

When dragging successfully to empty column:
```
✓ Drag started for BP-3
📡 API Call: {
    url: "/jira_clone_system/public/api/v1/issues/BP-3/transitions",
    method: "POST",
    body: {status_id: 2}
}
✓ Moved card to column 2
📦 API Response: {success: true, issue: {...}}
✓ Issue transitioned successfully
✓ UI updated
```

---

## What Changed

### File: `views/projects/board.php`

#### Change 1: Enhanced `updateStatusCounts()`
```javascript
function updateStatusCounts() {
    // Hide "No issues" message if cards exist
    const noIssuesMsg = column.querySelector('p.text-muted');
    if (noIssuesMsg) {
        if (count > 0) {
            noIssuesMsg.style.display = 'none';
        } else {
            noIssuesMsg.style.display = 'block';
        }
    }
}
```

#### Change 2: Better Error Handling
- Added check for `data.success` even if HTTP 200
- Call `updateStatusCounts()` on both success AND failure
- Better error messages

#### Change 3: Store Original Card
- Preserve original card HTML for restoration
- Safer rollback on errors

---

## Troubleshooting

### "No issues" text still visible after drop
**Cause**: `updateStatusCounts()` not called or not working

**Fix**:
1. Check console for errors
2. Verify card was added to column: `document.querySelectorAll('.board-card').length`
3. Manually trigger: `document.querySelectorAll('p.text-muted').forEach(p => p.style.display = 'none')`

### Card still disappears on refresh
**Cause**: API call failed but error not shown

**Fix**:
1. Check Network tab (F12 → Network)
2. Look for POST to `/api/v1/issues/.../transitions`
3. Check response status and body
4. Check server logs in `storage/logs/`

### Status count badge wrong
**Cause**: `updateStatusCounts()` not counting correctly

**Fix**:
1. Check: `document.querySelectorAll('.board-column')[0].querySelectorAll('.board-card').length`
2. Should match the badge number
3. If not, refresh page

---

## Verification Checklist

- [ ] Can drag issue to empty column
- [ ] Card appears in column
- [ ] "No issues" text disappears
- [ ] Status count updates
- [ ] Console shows success messages
- [ ] Reload page - card still there
- [ ] Can drag card back out
- [ ] "No issues" reappears when column empty

---

## If Tests Pass ✅
Drag and drop to empty columns works perfectly. No further fixes needed.

## If Tests Fail ❌
Check the troubleshooting section and console messages for specific issues.
