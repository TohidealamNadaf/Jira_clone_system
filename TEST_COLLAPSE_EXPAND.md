# Test Guide - Collapse/Expand Buttons

## 🧪 Quick Test (2 minutes)

### Step 1: Create Test Data
```
1. Open your Jira Clone
2. Create an issue or open existing one
3. Add 10+ comments
4. Perform 5+ actions (change status, assign, etc.)
```

### Step 2: Test Comments Collapse/Expand

**Initial State:**
- Should show first 5 comments
- "Collapse All" button visible in header
- Comments list has scrollbar

**Click "Collapse All":**
```
Expected:
✓ Comments list reduces to 600px height
✓ Scrollbar appears (if comments > 600px)
✓ Button text changes to "Expand All"
✓ Icon changes to ⬇️
```

**Click "Expand All":**
```
Expected:
✓ Comments list expands to full height
✓ All comments visible without scrolling
✓ Button text changes to "Collapse All"
✓ Icon changes to ⬆️
```

**Repeat 5 times:**
- Verify smooth transitions
- Verify no console errors (F12)

### Step 3: Test Activity Collapse/Expand

**Initial State:**
- Activity section visible (expanded)
- Icon shows ⬆️

**Click Activity Header:**
```
Expected:
✓ Activity section collapses (height becomes 0)
✓ Content hidden
✓ Icon changes to ⬇️
✓ Smooth 0.3s animation
```

**Click Activity Header Again:**
```
Expected:
✓ Activity section expands (height becomes 400px)
✓ Timeline visible with scrollbar
✓ Icon changes to ⬆️
✓ Smooth 0.3s animation
```

**Repeat 5 times:**
- Verify smooth transitions
- Verify no console errors

---

## 🔍 Detailed Test

### Test 1: Comments with Many Items

**Setup:**
- Issue with 50+ comments

**Test Steps:**
1. Load issue - should show 5 comments
2. Click "Load More Comments" - should show all
3. Click "Collapse All" - should collapse to 600px
4. Verify scrollbar appears
5. Scroll through comments
6. Click "Expand All" - should expand fully

**Expected Result:** ✅ All working smoothly

### Test 2: Activity with Many Entries

**Setup:**
- Issue with 100+ activity entries

**Test Steps:**
1. Load issue - Activity expanded
2. Click Activity header - should collapse
3. Verify height = 0 (no content visible)
4. Click Activity header again - should expand
5. Verify height = 400px
6. Scroll through activity

**Expected Result:** ✅ All working smoothly

### Test 3: Browser Console Check

**Steps:**
1. Open Developer Tools (F12)
2. Go to Console tab
3. Reload page
4. No errors should appear
5. Click buttons - should see messages:
   ```
   Activity toggled. Collapsed: true
   Activity toggled. Collapsed: false
   ```

**Expected Result:** ✅ No errors, messages appear

### Test 4: Mobile Device Test

**Setup:**
- Test on phone or tablet

**Test Steps:**
1. Open issue on mobile
2. Try "Collapse All" button
3. Try "Expand All" button
4. Try Activity collapse/expand
5. Verify responsive layout

**Expected Result:** ✅ Touch-friendly, works well

---

## 📋 Test Checklist

### Comments Functionality
- [ ] Collapse All button visible
- [ ] Click Collapse All works
- [ ] Comments reduce to 600px
- [ ] Button text changes
- [ ] Icon changes to down arrow
- [ ] Click Expand All works
- [ ] Comments expand to full height
- [ ] Button text changes back
- [ ] Icon changes to up arrow
- [ ] Smooth animation (no jumping)

### Activity Functionality
- [ ] Activity header clickable
- [ ] Click to collapse works
- [ ] Height becomes 0 (hidden)
- [ ] Icon changes to down arrow
- [ ] Click to expand works
- [ ] Height becomes 400px
- [ ] Icon changes to up arrow
- [ ] Smooth animation (0.3s)
- [ ] Content visible/hidden correctly

### General Quality
- [ ] No JavaScript errors (F12)
- [ ] No console warnings
- [ ] Smooth animations
- [ ] Works in Chrome
- [ ] Works in Firefox
- [ ] Works in Safari
- [ ] Works on mobile
- [ ] Works on tablet
- [ ] Responsive layout maintained
- [ ] No text overflow

---

## 🐛 If Something Breaks

### Problem: Button doesn't respond

**Solution:**
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh (Ctrl+Shift+R)
3. Check F12 Console for errors
4. Check that show.php file is updated
5. Verify PHP syntax: `php -l views/issues/show.php`

### Problem: Animation is choppy

**Solution:**
1. Close other browser tabs
2. Disable browser extensions
3. Check browser CPU usage
4. Try different browser
5. Check for JavaScript errors (F12)

### Problem: Elements not found

**Solution:**
1. Make sure you're on issue detail page
2. Verify page has comments or activity
3. Check HTML in browser (F12 → Elements)
4. Look for `comments-container` ID
5. Look for `activity-body` ID

### Problem: Styles not applying

**Solution:**
1. Verify CSS file loaded: Check Network tab (F12)
2. Clear CSS cache: Ctrl+Shift+Delete
3. Hard refresh page: Ctrl+Shift+R
4. Check for CSS errors in app.css
5. Verify max-height values in CSS

---

## ✅ Verification Commands

### Check if elements exist
```javascript
// In browser console (F12)
document.getElementById('comments-container') // Should not be null
document.getElementById('activity-body') // Should not be null
document.getElementById('toggle-all-comments') // Should not be null
```

### Check CSS values
```javascript
// Check comments container height
getComputedStyle(document.getElementById('comments-container')).maxHeight
// Should be "600px" or "100vh"

// Check activity body state
document.getElementById('activity-body').classList
// Should contain "collapsed" or not
```

### Check event listeners
```javascript
// Click buttons and check console
// Should see "Activity toggled" message
// Look in F12 → Console tab
```

---

## 📊 Expected Behavior

### Comments Section
| Action | Before | After | Timing |
|--------|--------|-------|--------|
| Load page | Show 5 | Show 5 | Instant |
| Click "Load More" | - | Show all | Instant |
| Click "Collapse All" | - | Height=600px | 0.2s |
| Click "Expand All" | - | Height=100vh | 0.2s |

### Activity Section
| Action | Before | After | Timing |
|--------|--------|-------|--------|
| Load page | Expanded | Expanded | Instant |
| Click header | - | Collapsed | 0.3s |
| Click header | - | Expanded | 0.3s |

---

## 🎯 Success Criteria

**All of these must be true:**

✅ Collapse All button works  
✅ Expand All button works  
✅ Activity collapse works  
✅ Activity expand works  
✅ No JavaScript errors  
✅ Smooth animations (no jumping)  
✅ Visual feedback visible  
✅ Mobile responsive  
✅ Works in all major browsers  

---

## 📞 Need Help?

If tests fail:
1. Check `COLLAPSE_EXPAND_BUG_FIX.md` for details
2. Review the F12 Console for errors
3. Verify `views/issues/show.php` was updated
4. Try hard refresh (Ctrl+Shift+R)
5. Clear all cache (Ctrl+Shift+Delete)

---

**Test Guide Created**: 2025-12-06  
**Status**: Ready to test  
**Time Required**: ~5-10 minutes
