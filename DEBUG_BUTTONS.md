# Debug: Comment Edit/Delete Buttons Not Appearing

## Checklist to diagnose the issue:

### 1. **Check if buttons are being rendered in HTML**
   - Open browser DevTools (F12)
   - Go to Network tab
   - Load an issue page
   - In Elements tab, search for "comment-actions"
   - If found: Buttons are rendered but hidden by CSS/permissions
   - If NOT found: Buttons aren't being rendered (PHP condition failing)

### 2. **Check if `$canEditDelete` condition is true**
   - If buttons NOT in HTML, add debug output:
   ```php
   // After line 256 in show.php, add:
   <?php var_dump($comment['user_id'], $currentUserId, $canEditDelete); ?>
   ```

### 3. **Check if `$currentUserId` is set**
   - Add debug line after line 8 in show.php:
   ```php
   <!-- DEBUG: currentUserId = <?= $currentUserId ?> -->
   ```
   - View page source and look for this comment

### 4. **Check if `comment['user_id']` exists**
   - It should be in the array per IssueService.php line 229
   - Add debug output if buttons not showing

### 5. **Check CSS opacity**
   - If buttons ARE in HTML, open DevTools
   - Inspect the `.comment-actions` div
   - Hover over comment - opacity should change from 0 to 1
   - If NOT changing, CSS isn't working

### 6. **Check browser console for JS errors**
   - Open DevTools Console tab
   - Look for any JavaScript errors that might prevent the hover effects
   - Check if there are CORS or CSRF-related errors

## What to report back:
Please tell me:
1. Are the buttons visible in the HTML (DevTools Elements)?
2. What is `$currentUserId` value (check page source)?
3. Are there any JS errors in console?
4. Do the buttons appear if you manually inspect `.comment-actions` element in DevTools?
