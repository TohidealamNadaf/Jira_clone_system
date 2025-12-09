# Fix: Board Kanban Drag and Drop

**Status**: ✅ FIXED  
**Date**: December 9, 2025  
**Files Modified**: 1  
**Feature**: Full HTML5 Drag-and-Drop Implementation

## Problem

The Kanban board at `/projects/{key}/board` displayed issue cards but had no drag-and-drop functionality. Users could only click cards to view details, not move them between status columns.

## Solution

Implemented full HTML5 drag-and-drop functionality with visual feedback and server synchronization.

## Files Modified

### views/projects/board.php
- Added `draggable="true"` attribute to issue cards
- Added `data-status-id` to status columns
- Added `data-issue-id` and `data-issue-key` to issue cards
- Wrapped issue title in proper link structure
- Added CSS classes for drag states
- Implemented complete drag-and-drop JavaScript

## Features Implemented

### 1. Visual Drag Indicators
- **Dragging State**: Card becomes semi-transparent (opacity: 0.5)
- **Drag Over**: Column highlights with light blue background
- **Cursor**: Changes to "move" cursor during drag

### 2. Drag and Drop Logic
- Cards are `draggable="true"`
- Columns are drop targets
- Prevents dropping to the same status (no-op)
- Prevents dropping on empty columns (auto-accept)

### 3. Server Synchronization
- Uses `/api/v1/issues/{key}/transitions` endpoint
- Sends `status_id` in request body
- Optimistic UI update (moves card immediately)
- Reloads if server returns error
- Includes CSRF token in headers

### 4. CSS Styling
```css
.board-card {
    cursor: move;
}

.board-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.board-card.dragging {
    opacity: 0.5;
}

.board-column {
    transition: background-color 0.2s;
}

.board-column.drag-over {
    background-color: rgba(0, 123, 255, 0.05);
}
```

### 5. JavaScript Implementation
- Event listeners for `dragstart`, `dragend`, `dragover`, `dragleave`, `drop`
- Maintains `draggedCard` state
- Handles column drop zones
- API communication with error handling
- Graceful fallback to page reload on error

## How It Works

1. **User drags card**: `dragstart` event fires, card becomes semi-transparent
2. **User moves over column**: `dragover` prevents default, adds "drag-over" class
3. **User leaves column**: `dragleave` removes "drag-over" class
4. **User drops card**: 
   - Validates same-status check
   - Moves card in UI (optimistic)
   - Sends API request to `/api/v1/issues/{key}/transitions`
   - Server updates database
   - On error: reloads page to refresh state

## API Endpoint Used

**POST** `/api/v1/issues/{key}/transitions`

Request body:
```json
{
    "status_id": 2
}
```

Response:
```json
{
    "success": true,
    "issue": { ... }
}
```

## Browser Compatibility

✓ Chrome 4+  
✓ Firefox 3.6+  
✓ Safari 5+  
✓ Edge (all versions)  
✓ IE 10+ (basic support)

## Testing

1. Navigate to `/projects/BP/board`
2. Locate an issue card
3. Click and drag card to another column
4. Card should move smoothly
5. Card should stay in new column on page reload
6. Check browser console for any errors

### Test Scenarios

1. **Drag to different status**: Move card from "To Do" to "In Progress"
2. **Drag to same status**: Card should snap back (no API call)
3. **Drag to empty column**: Card should move and show in count
4. **Network error**: Should alert and reload page
5. **Multiple fast drags**: Should handle queue correctly

## Security

✓ CSRF token validation (auto-included)  
✓ Authorization via API middleware  
✓ Status ID validation on server  
✓ Issue ownership validation  
✓ Audit logging on transition

## Performance

- Optimistic UI update (instant visual feedback)
- Single API call per drag
- No polling or websockets
- Efficient event delegation

## Accessibility

- Cards remain keyboard-accessible (can still click to open)
- Links still functional within cards
- Error messages in alert boxes (accessible)

## Future Enhancements

- [ ] Reorder issues within same column
- [ ] Keyboard support (arrow keys to move)
- [ ] Multi-select drag
- [ ] Sprint assignment during drag
- [ ] Assignee change during drag
- [ ] Undo/Redo functionality

## Known Limitations

- Cannot reorder within same column (maintains issue creation order)
- Cannot drag multiple cards at once
- No keyboard alternative for drag (must use mouse/touch)
- No visual progress indicator for API call

## Troubleshooting

**Cards not dragging?**
- Check if draggable="true" is present
- Verify JavaScript is loaded (check console)
- Ensure you have `issues.transition` permission

**Card moves but doesn't persist?**
- Check API response in Network tab
- Verify status_id is correct
- Check server error logs

**Page reloads unexpectedly?**
- API call failed (check Network tab)
- Permission denied (check user roles)
- Database error (check server logs)

## References

- [MDN: HTML Drag and Drop API](https://developer.mozilla.org/en-US/docs/Web/API/HTML_Drag_and_Drop_API)
- [MDN: Drag Events](https://developer.mozilla.org/en-US/docs/Web/API/DragEvent)
- [IssueApiController::transition()](src/Controllers/Api/IssueApiController.php)
- [ProjectController::board()](src/Controllers/ProjectController.php)
