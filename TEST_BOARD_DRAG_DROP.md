# Test Guide: Board Kanban Drag and Drop

**Date**: December 9, 2025  
**Feature**: Drag and drop issues on Kanban board  
**Status**: Ready for testing

## Pre-Test Setup

1. Navigate to `/projects/BP/board` (or any project with a board)
2. Ensure you have issues in at least 2 different status columns
3. Open browser developer tools (F12) → Console tab
4. Test environment: Chrome, Firefox, or Edge

## Test Cases

### Test 1: Basic Drag and Drop
**Objective**: Drag an issue from one column to another

**Steps**:
1. Locate an issue card in a column (e.g., "To Do")
2. Click and hold the card
3. Drag it to another column (e.g., "In Progress")
4. Release the mouse

**Expected Result**:
- Card becomes semi-transparent (opacity: 0.5) while dragging
- Target column shows light blue highlight
- Card moves to target column
- Card stays in target column after release
- Browser console shows no errors

**Pass/Fail**: ___

---

### Test 2: Same Status Drop (No-Op)
**Objective**: Verify card doesn't move if dropped in same column

**Steps**:
1. Locate an issue card
2. Drag it to the SAME column it's in
3. Release the mouse

**Expected Result**:
- Card does NOT move
- No API call is made (check Network tab)
- Card returns to original position
- No error message

**Pass/Fail**: ___

---

### Test 3: Empty Column Drop
**Objective**: Drag card to an empty column

**Steps**:
1. Find a column with NO issues (e.g., "Done")
2. Drag an issue from another column to this column
3. Release the mouse

**Expected Result**:
- Card moves to empty column
- Column count increases to 1
- Card persists after page reload

**Pass/Fail**: ___

---

### Test 4: Visual Feedback - Drag Start
**Objective**: Verify visual feedback during drag

**Steps**:
1. Locate a card
2. Start dragging
3. Observe the card appearance

**Expected Result**:
- Card opacity changes to 0.5 (semi-transparent)
- Cursor changes to "move" cursor
- Effect is visible during entire drag

**Pass/Fail**: ___

---

### Test 5: Visual Feedback - Drag Over
**Objective**: Verify column highlight when dragging over

**Steps**:
1. Start dragging a card
2. Move cursor over a different column
3. Observe column appearance

**Expected Result**:
- Column background turns light blue
- Effect disappears when cursor leaves column
- Smooth color transition

**Pass/Fail**: ___

---

### Test 6: Multiple Drags in Sequence
**Objective**: Verify multiple drag operations work correctly

**Steps**:
1. Drag card A from Column 1 to Column 2
2. Drag card B from Column 3 to Column 1
3. Drag card A back from Column 2 to Column 1

**Expected Result**:
- All drags complete successfully
- Final positions are correct
- All cards persist after reload

**Pass/Fail**: ___

---

### Test 7: Network Error Handling
**Objective**: Verify behavior when API call fails

**Steps**:
1. Open Network tab (DevTools)
2. Find the `/api/v1/issues/` POST request URL
3. Use DevTools to simulate offline mode (or block requests)
4. Try to drag a card

**Expected Result**:
- Drag completes visually
- API request fails (visible in Network tab)
- Alert appears: "Error moving issue. Please try again."
- Page reloads automatically
- Card returns to original position

**Pass/Fail**: ___

---

### Test 8: Keyboard Accessibility
**Objective**: Verify non-drag users can still access issue

**Steps**:
1. Locate a card in the board
2. Try clicking on the issue title (the link text)
3. Click away if it opens the issue

**Expected Result**:
- Card title is still clickable
- Links work as expected
- No interference from drag functionality

**Pass/Fail**: ___

---

### Test 9: Assignee Avatar Display
**Objective**: Verify assigned user avatar shows correctly

**Steps**:
1. Find a card with an assignee
2. Drag the card to another column
3. Release and observe

**Expected Result**:
- Avatar displays correctly before drag
- Avatar displays correctly after drag
- No broken image links

**Pass/Fail**: ___

---

### Test 10: Long Card Drag
**Objective**: Drag from first column all the way to last column

**Steps**:
1. Drag card from leftmost column
2. Move cursor across all intermediate columns
3. Drop in rightmost column

**Expected Result**:
- Drag works across multiple columns
- Visual feedback works throughout
- Card ends up in correct position

**Pass/Fail**: ___

---

## Browser Testing Matrix

| Browser | Version | Test Result | Notes |
|---------|---------|-------------|-------|
| Chrome | Latest | _____ | |
| Firefox | Latest | _____ | |
| Edge | Latest | _____ | |
| Safari | Latest | _____ | |

---

## Console Checks

While running tests, verify:

1. **No JavaScript Errors**
   - Console should be clean
   - No red error messages

2. **Network Requests**
   - Each drop should create ONE POST request
   - URL: `/api/v1/issues/{key}/transitions`
   - Status: 200 OK
   - Body: `{"status_id": X}`

3. **CSRF Token**
   - All requests should include `X-CSRF-Token` header
   - Token should be valid

---

## Edge Cases to Test

1. **Rapid Consecutive Drags**
   - Drag 5 cards quickly in succession
   - All should work without conflicts

2. **Drag While API Call in Progress**
   - Drag a card
   - Immediately try dragging another before first completes
   - Should handle gracefully

3. **Drag with Sidebar Open**
   - Open any sidebar/panel
   - Try dragging
   - Should still work

4. **Mobile/Touch Testing** (if applicable)
   - Touch and drag on mobile device
   - Should work with touch events

---

## Regression Testing

Verify existing functionality still works:

1. **Click to View Issue**: ✓___
2. **Issue Count Badge**: ✓___
3. **Status Color**: ✓___
4. **Priority Color**: ✓___
5. **Issue Type Badge**: ✓___
6. **Assignee Avatar**: ✓___
7. **Create New Issue Button**: ✓___
8. **Board Filters** (if present): ✓___

---

## Performance Testing

While dragging:

1. **Page responsiveness**: Smooth / Laggy / _____
2. **Drag smoothness**: Smooth / Stuttering / _____
3. **Memory usage**: Normal / High / _____
4. **CPU usage**: Low / High / _____

---

## Sign-off

**Tester Name**: ___________________  
**Date**: ___________________  
**Overall Result**: PASS / FAIL / PARTIAL  
**Comments**:  
```
[Space for comments]
```

---

## Issues Found

If any tests fail, document here:

| Test # | Issue | Severity | Status |
|--------|-------|----------|--------|
| | | | |
| | | | |

---

## Approval

**Reviewed by**: ___________________  
**Approved**: YES / NO  
**Date**: ___________________
