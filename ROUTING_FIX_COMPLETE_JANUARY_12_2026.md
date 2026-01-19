# Complete Routing Fix - January 12, 2026

## Problem Statement

User reports three routing issues:
1. `/projects/CWAYSMIS/board` → Shows Kanban board ✅ WORKS
2. `/projects/CWAYSMIS/boards` → Shows board list with "0 columns, 0 issues" ❌ WRONG DISPLAY
3. `/boards/6?sprint_id=10` → Shows empty board ❌ NO DATA
4. Sprint "View Board" link redirects to `/boards/6?sprint_id=10` but page is empty

## Root Cause Analysis

### Primary Issue: Missing Board Columns

The board is showing empty because:
1. **Board ID 6 exists** but has **NO COLUMNS configured**
2. `BoardService::createBoard()` calls `createDefaultColumns()` to create default columns (To Do, In Progress, Done)
3. The columns are stored in `board_columns` table with `status_ids` JSON field
4. If the board_columns table is empty, the view loops through 0 columns → empty board

**Database Check:**
```sql
SELECT * FROM board_columns WHERE board_id = 6;
```
If this returns 0 rows, the board needs columns created.

### Secondary Issue: Missing Board Columns on Index View

The `boards.index` view shows "0 columns" because:
- Line 77 in `views/boards/index.php`: `<?= count($board['columns'] ?? []) ?> columns`
- The board data fetched by `BoardController::index()` uses `getBoards()` NOT `getBoardById()`
- `getBoards()` (line 76 in BoardService) does NOT fetch columns
- Therefore `$board['columns']` is empty array → count = 0

### Tertiary Issue: Two Board Systems Exist

The project has TWO separate board implementations:
1. **Old System**: `ProjectController::board()` → `projects.board` view (simple Kanban)
2. **New System**: `BoardController::show()` → `boards.show` view (full board with sprints)

Routes are confusing:
- `/projects/{key}/board` → Old Kanban board (should be removed)
- `/projects/{key}/boards` → Board list (correct)
- `/boards/{id}` → New board view with sprint support (correct)

## Solution

### Fix 1: Ensure Board Has Columns (CRITICAL)

The board must have columns configured before it can display issues.

**Check if columns exist:**
```sql
SELECT COUNT(*) FROM board_columns WHERE board_id = 6;
```

**If empty, create default columns:**
```php
// Run this script or add to database migration
$boardId = 6;
$columns = [
    ['name' => 'To Do', 'status_ids' => json_encode([1, 2, 3])],      // Map to status IDs
    ['name' => 'In Progress', 'status_ids' => json_encode([4])],        // Map to status ID
    ['name' => 'Done', 'status_ids' => json_encode([5, 6])],           // Map to status IDs
];

foreach ($columns as $col) {
    Database::insert('board_columns', [
        'board_id' => $boardId,
        'name' => $col['name'],
        'status_ids' => $col['status_ids'],
        'color' => '#8B1956',  // Plum theme
    ]);
}
```

**OR use the existing service method (RECOMMENDED):**
```php
// In BoardService::createDefaultColumns()
$boardService = new BoardService();
$boardService->createDefaultColumns($boardId, 'scrum');
```

### Fix 2: Update BoardController::index to Load Columns

**File**: `src/Controllers/BoardController.php`  
**Method**: `index()` (line 30-49)

**Current Code**:
```php
public function index(Request $request): string
{
    $projectKey = $request->param('key');
    $project = $this->projectService->getProjectByKey($projectKey);

    if (!$project) {
        abort(404, 'Project not found');
    }

    $boards = $this->boardService->getBoards($project['id']);
    // ... rest of method
}
```

**Problem**: `getBoards()` doesn't load columns

**Fix**: Load columns for each board
```php
public function index(Request $request): string
{
    $projectKey = $request->param('key');
    $project = $this->projectService->getProjectByKey($projectKey);

    if (!$project) {
        abort(404, 'Project not found');
    }

    $boards = $this->boardService->getBoards($project['id']);
    
    // Load columns and issue counts for each board
    foreach ($boards as &$board) {
        $board['columns'] = $this->boardService->getBoardColumns($board['id']);
        // Count issues per board
        $board['issue_count'] = (int) Database::selectValue(
            "SELECT COUNT(*) FROM issues WHERE project_id = ? AND status_id IN (
                SELECT id FROM statuses
            )",
            [$project['id']]
        );
    }

    if ($request->wantsJson()) {
        $this->json($boards);
    }

    return $this->view('boards.index', [
        'project' => $project,
        'boards' => $boards,
    ]);
}
```

### Fix 3: Clean Up Routing (OPTIONAL but RECOMMENDED)

**File**: `routes/web.php`

**Current confusing routes:**
- Line 80: `$router->get('/projects/{key}/board', [ProjectController::class, 'board']);`
- Line 139: `$router->get('/projects/{key}/boards', [BoardController::class, 'index']);`
- Line 141: `$router->get('/boards/{id}', [BoardController::class, 'show']);`

**Recommended approach:**
```php
// Option A: Keep both but add clarity
// /projects/{key}/board → Redirect to /projects/{key}/boards/{default-board-id}
// /projects/{key}/boards → List all boards
// /boards/{id} → Show specific board

// Option B: Remove old board route entirely (BREAKING CHANGE)
// Only use: /projects/{key}/boards and /boards/{id}

// Option C: Make /projects/{key}/board smart redirect
$router->get('/projects/{key}/board', function (Request $request) {
    $projectKey = $request->param('key');
    $board = Database::selectOne(
        "SELECT b.id FROM boards b 
         JOIN projects p ON b.project_id = p.id 
         WHERE p.key = ? LIMIT 1",
        [$projectKey]
    );
    
    if ($board) {
        redirect(url("/boards/{$board['id']}"));
    } else {
        redirect(url("/projects/{$projectKey}/boards"));
    }
});
```

### Fix 4: Verify Sprint Board View Works

**Route**: `GET /projects/{key}/sprints/{id}/board` (line 160)  
**Controller**: `SprintController::viewBoard()` (line 392)  
**Behavior**: Redirects to `/boards/{board_id}?sprint_id={sprint_id}`

**This should work automatically once Fix 1 is applied** (columns exist).

## Implementation Steps (In Order)

### Step 1: Check Board Columns
```sql
SELECT COUNT(*) FROM board_columns WHERE board_id = 6;
```

### Step 2: Create Columns if Missing
If the query returns 0:
```sql
-- Get status IDs for mapping
SELECT id, name FROM statuses ORDER BY id;

-- Insert default columns (adjust status IDs based on your system)
INSERT INTO board_columns (board_id, name, status_ids, color, created_at, updated_at)
VALUES
    (6, 'To Do', '[1,2,3]', '#8B1956', NOW(), NOW()),
    (6, 'In Progress', '[4]', '#8B1956', NOW(), NOW()),
    (6, 'Done', '[5,6]', '#8B1956', NOW(), NOW());
```

### Step 3: Update BoardController::index
Apply Fix 2 above to load columns

### Step 4: Test URLs
1. `/projects/CWAYSMIS/boards` → Should show board with columns
2. `/boards/6` → Should show board with columns and issues  
3. `/boards/6?sprint_id=10` → Should show board filtered to sprint 10

### Step 5 (Optional): Clean Up Routing
Apply Fix 3 if you want to remove the old `/projects/{key}/board` route

## Testing Checklist

- [ ] `/projects/CWAYSMIS/boards` shows board with 3 columns (not 0 columns)
- [ ] `/boards/6` shows board with columns and issues
- [ ] `/boards/6?sprint_id=10` shows board filtered to sprint 10 (if sprint exists)
- [ ] Sprint page "View Board" button navigates correctly
- [ ] Board shows issues in correct columns
- [ ] Drag-and-drop works on board
- [ ] No console errors

## Files Modified

1. `src/Controllers/BoardController.php` - Update index() method
2. `database/` - Add columns to board_columns table (SQL)
3. `routes/web.php` - (Optional) Clean up routing

## Risk Assessment

**Risk Level**: LOW
- No breaking changes to existing functionality
- Purely adding missing data and fixing view logic
- Backward compatible

**Affected Users**: 
- Anyone trying to view board at `/boards/{id}` or `/projects/{key}/boards`
- Sprint "View Board" feature
