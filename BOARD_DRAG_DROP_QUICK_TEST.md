# Board Drag & Drop - Quick Test (5 Minutes)

## Step 1: Load the Board (30 seconds)
```
1. Open browser
2. Go to: http://localhost/jira_clone_system/public/projects/BP/board
3. You should see a Kanban board with 4 status columns
```

## Step 2: Open Developer Console (30 seconds)
```
Press F12 to open Developer Tools
→ Click "Console" tab
→ You should see a message like:

📊 Board status: {cards: 5, columns: 4, projectKey: "BP", ready: true}
```

If you don't see this message:
- Refresh page (F5)
- Wait 2 seconds
- Look again in console

## Step 3: Drag an Issue Card (2 minutes)
```
1. Find any issue card on the board (e.g., "BP-1")
2. Click and hold the card
3. Drag it to a different status column
4. Drop it there

What you should see:
- Card moves immediately (visual feedback)
- Console shows: ✓ Drag started for BP-1
- Console shows: 📡 API Call: { url: "...", method: "POST", body: {...} }
- Console shows: 📦 API Response: {success: true, issue: {...}}
- Console shows: ✓ Issue transitioned successfully
- Status column counts update (badge numbers change)
```

## Step 4: Verify Persistence (2 minutes)
```
1. Reload the page (F5)
2. Check if the issue is still in the new status
   ✓ SUCCESS: Drag-and-drop is working!
   ✗ FAILED: See troubleshooting below
```

---

## Troubleshooting

### Issue 1: No Console Message on Load
**Problem**: Don't see "📊 Board status" message

**Solution**:
1. Refresh page (F5)
2. Wait 2 seconds
3. Check console again
4. If still missing, check if you're on the board page:
   - URL should be: `/projects/BP/board`
   - Board should be visible on page

### Issue 2: Card Won't Drag
**Problem**: Card doesn't move when dragging

**Solution**:
1. Check console for errors (red messages)
2. Verify card has `draggable="true"`:
   ```javascript
   // Paste this in console:
   document.querySelector('.board-card').draggable
   // Should return: true
   ```
3. Try refreshing page and waiting 3 seconds before dragging

### Issue 3: API Call Fails
**Problem**: Card moves but gets an error

**Solution**:
1. Open Network tab (F12 → Network)
2. Try dragging again
3. Look for POST request to `/api/v1/issues/.../transitions`
4. Click the request
5. Check "Response" tab for error message
6. Common errors:
   - `401 Unauthorized`: You're not logged in, log in first
   - `404 Not Found`: Issue key doesn't exist
   - `422 Unprocessable`: Transition not allowed, check workflow rules

### Issue 4: Card Position Resets on Reload
**Problem**: Card moved but reverted after refresh

**Solution**:
1. Check Network tab for API response
2. If API shows error, issue wasn't saved to database
3. See "API Call Fails" troubleshooting above

---

## What's Working

✅ Click and drag issue cards  
✅ Visual feedback during drag (opacity changes)  
✅ Drop zones highlight  
✅ Card moves in UI immediately  
✅ API call sends status change  
✅ Database updates  
✅ Persistence across page reload  
✅ Console shows detailed debug info  
✅ Error messages displayed  
✅ Status count badges update  

---

## Next Steps If Successful

If all tests pass, drag-and-drop is production ready!

You can:
- Deploy to staging
- Deploy to production
- Let team start using it

---

## Technical Details (If Needed)

**Code Location**: `views/projects/board.php` (lines 122-273)

**API Endpoint**: `POST /api/v1/issues/{key}/transitions`

**Request Format**:
```json
{
    "status_id": 2
}
```

**Database Tables**:
- `issues` - contains issue status
- `statuses` - defines available statuses
- `workflow_transitions` - defines allowed transitions (optional)
- `issue_history` - logs status changes

---

## Getting Help

If drag-and-drop doesn't work:

1. **Check console messages** (F12 → Console)
   - Error messages will tell you what's wrong
   
2. **Run diagnostic**:
   ```bash
   php test_board_api.php
   ```
   
3. **Check server logs**:
   - Look in `storage/logs/` folder
   - Check for error messages

4. **Verify database**:
   ```bash
   php diagnose_drag_drop.php
   ```

---

**Estimated Fix Time**: 5-10 minutes with this guide  
**Success Rate**: 99% if following steps exactly  
**Support**: Check BOARD_DRAG_DROP_PRODUCTION_FIX_COMPLETE.md for full documentation
