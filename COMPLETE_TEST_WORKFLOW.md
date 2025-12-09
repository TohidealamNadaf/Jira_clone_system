# Complete Test Workflow - After All Fixes

## Step 1: Create Baramati Project
1. Go to: `http://localhost:8080/jira_clone_system/public/projects`
2. Click "Create Project"
3. Enter:
   - Project Name: `Baramati Project`
   - Project Key: `BARA`
   - Description: `Test project`
4. Click "Create"
5. ✅ Verify: Project appears in the list

## Step 2: Create an Issue
1. Click on "Baramati Project" 
2. Click "Create Issue"
3. Fill in:
   - Issue Type: Any (e.g., Task)
   - Summary: `Test Issue with Comments`
   - Description: `Testing comment edit/delete functionality`
4. Click "Create"
5. ✅ Verify: Issue is created and page loads without 404

## Step 3: Add a Comment
1. Scroll to "Comments" section
2. In "Add a comment" field, type: `This is my first comment`
3. Click "Post Comment"
4. ✅ Verify: Comment appears below the textarea

## Step 4: Test Comment Edit
1. Hover over the comment
2. You should see **Edit (pencil)** and **Delete (trash)** icons fade in
3. Click the **Edit button** (pencil icon)
4. An edit form should appear with the comment text
5. Change the text to: `This is my edited comment`
6. Click **Save**
7. ✅ Verify: 
   - NO 404 error appears
   - Comment text updates
   - "(edited)" label appears next to timestamp

## Step 5: Test Comment Delete
1. Add another comment: `This comment will be deleted`
2. Click "Post Comment"
3. Hover over this new comment
4. Click the **Delete button** (trash icon)
5. A confirmation should appear
6. Confirm deletion
7. ✅ Verify:
   - NO 404 error appears
   - Comment is removed from the page

## Step 6: Reload and Verify Persistence
1. **CRITICAL TEST**: Press F5 to reload the page
2. ✅ Verify:
   - Issue still exists (no 404)
   - Project still exists
   - Edited comment shows your changes
   - Deleted comment is gone
   - Page loads without errors

## Step 7: Check Browser Console
1. Press **F12** to open Developer Tools
2. Click **Console** tab
3. ✅ Verify: No red errors appear (yellow warnings are OK)

## Expected Results

| Test | Expected | Status |
|------|----------|--------|
| Create project | ✅ Success | |
| Create issue | ✅ Success | |
| Add comment | ✅ Success | |
| Edit comment | ✅ Updates without 404 | |
| Delete comment | ✅ Removes without 404 | |
| Reload page | ✅ Everything persists | |
| No console errors | ✅ No red errors | |

---

## If Something Fails

### Comment buttons not visible on hover?
- Check browser console (F12 > Console tab)
- Look for JavaScript errors
- Make sure you're hovering directly over the comment text area

### Still getting 404 errors?
- Clear browser cache (Ctrl+Shift+Delete)
- Hard reload (Ctrl+Shift+R)
- Check if the route is correct: `/jira_clone_system/public/comments/{id}`

### Project still disappearing?
- The database fix may not have applied
- Verify in phpMyAdmin that the foreign key changed to `ON DELETE RESTRICT`
- Re-execute the SQL if needed

---

## After All Tests Pass

✅ Comment edit/delete feature is fully working
✅ Database constraints prevent data loss
✅ All fixes are complete

Report back with results!
