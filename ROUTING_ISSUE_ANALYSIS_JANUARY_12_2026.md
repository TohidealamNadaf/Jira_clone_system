# Routing Issue Analysis - January 12, 2026

## Problem Summary

Three separate URLs are showing conflicting behavior:

1. `/projects/CWAYSMIS/board` (Kanban-style board) ✅ WORKS
2. `/projects/CWAYSMIS/boards` (Board list) ❌ Shows wrong content (board list should be here)
3. `/boards/6?sprint_id=10` (Sprint board view) ❌ EMPTY (should show sprint issues)

## Root Causes Identified

### Issue 1: `/projects/{key}/boards` Route Confusion
**Current Behavior**: Routes to `BoardController::index()` which shows a list of boards in the project  
**Problem**: The view `boards.index.php` is rendering incorrectly, showing 0 columns and 0 issues  

**Root Cause**: 
- Route line 139: `$router->get('/projects/{key}/boards', [BoardController::class, 'index']);`
- This route is correct, but it shows boards that have 0 columns/0 issues
- The board data itself appears to be missing column/issue information

### Issue 2: `/boards/{id}` Route Missing Sprint Filter
**Current Behavior**: Routes to `BoardController::show()` which loads the board view  
**Problem**: When `sprint_id` parameter is passed, the board shows empty

**Root Cause**:
- Route line 141: `$router->get('/boards/{id}', [BoardController::class, 'show']);`
- Controller line 70: `$sprintId = $request->input('sprint_id') ? (int) $request->input('sprint_id') : null;`
- The controller accepts the sprint_id parameter and passes it to `getBoardWithIssues()`
- If sprint filtering isn't working in the service, no issues display

### Issue 3: Missing Proper Board Routing
**Current Behavior**: 
- `/projects/{key}/board` → Shows old Kanban board (from `projects.board` view)
- `/projects/{key}/boards` → Shows board list (from `boards.index` view)

**Problem**: The system has TWO different board viewing systems and they're not properly integrated:
1. **Old System**: `ProjectController::board()` → `projects.board` view (simple Kanban)
2. **New System**: `BoardController::show()` → `boards.show` view (full board with sprints)

## Correct Routing Architecture

The system should have:

```
/projects/{key}/board          → Should NOT EXIST (deprecated)
/projects/{key}/boards         → List all boards for project (use BoardController::index)
/boards/{id}                   → Show specific board (use BoardController::show)
/boards/{id}?sprint_id=X      → Show board filtered to sprint X (use BoardController::show with sprint filter)
/projects/{key}/sprints/{id}/board → Redirect to /boards/{id}?sprint_id={id}
```

## Files Affected

1. **routes/web.php** (lines 80, 139-142, 160)
   - Line 80: ProjectController::board route should be removed or redirect
   - Lines 139-142: Board routes are correct but may need clarification
   - Line 160: Sprint board view route

2. **src/Controllers/ProjectController.php** (line 399-435)
   - `board()` method shows old Kanban board
   - Should redirect to `BoardController` or be removed

3. **src/Controllers/BoardController.php** (line 67-97)
   - `show()` method should properly filter by sprint_id

4. **src/Services/BoardService.php**
   - `getBoardWithIssues()` must properly filter by sprint_id

## Sprint Board View URL

When user clicks "View Board" from sprint page at `/projects/CWAYSMIS/sprints`, they should:
1. Land on `/boards/6?sprint_id=10`
2. See the board for board ID 6, filtered to show only issues in sprint 10
3. This should work through `SprintController::viewBoard()` route (line 160)

## Data Quality Issue

The boards showing "0 columns, 0 issues" suggests:
1. The board has no columns configured
2. Or the column data isn't being loaded in the view

### Verify:
```sql
SELECT * FROM board_columns WHERE board_id = 6;
SELECT COUNT(*) FROM issues WHERE board_id = 6;
```

If these return 0, the board is empty. If they have data, the service layer isn't retrieving it.
