# Fix: Projects Filter Dropdowns Not Working

## Problem
The filter dropdowns on the projects page (`/projects`) were not working:
- Category dropdown showed no options
- Status dropdown was not filtering properly
- Filters weren't being applied to project listing

## Root Causes
1. **Missing categories variable**: The `ProjectController::index()` method did not fetch and pass `$categories` to the view, even though the view expected it on line 50
2. **Filter name mismatch**: The controller was passing filter names that didn't match what the view expected
3. **Service filter logic**: The `ProjectService::getAllProjects()` was checking for old filter names

## Solution

### 1. Updated ProjectController::index() 
**File**: `src/Controllers/ProjectController.php`

**Changes**:
- Changed filter names from `is_archived`, `category_id` to `category`, `status` (matching view form inputs)
- Added query to fetch all categories: `Database::select("SELECT * FROM project_categories ORDER BY name ASC")`
- Pass `categories` to the view so dropdown can render options

**Before**:
```php
$filters = [
    'search' => $request->input('search'),
    'is_archived' => ...,
    'category_id' => $request->input('category_id'),
    'lead_id' => $request->input('lead_id'),
];
// No categories passed to view
return $this->view('projects.index', [
    'projects' => $projects,
    'filters' => $filters,
]);
```

**After**:
```php
$filters = [
    'search' => $request->input('search'),
    'category' => $request->input('category'),
    'status' => $request->input('status'),
];

$categories = Database::select("SELECT * FROM project_categories ORDER BY name ASC");

return $this->view('projects.index', [
    'projects' => $projects,
    'filters' => $filters,
    'categories' => $categories,
]);
```

### 2. Updated ProjectService::getAllProjects()
**File**: `src/Services/ProjectService.php`

**Changes**:
- Added logic to handle `status` filter (converts `archived`/`active` to `is_archived` boolean)
- Updated category filter to accept both `category` and `category_id` (backward compatible)
- Improved filter handling for status field

**Before**:
```php
if (isset($filters['is_archived'])) {
    $where[] = "p.is_archived = :is_archived";
    $params['is_archived'] = $filters['is_archived'] ? 1 : 0;
}

if (!empty($filters['category_id'])) {
    $where[] = "p.category_id = :category_id";
    $params['category_id'] = $filters['category_id'];
}
```

**After**:
```php
// Handle status filter (active/archived)
if (!empty($filters['status'])) {
    $where[] = "p.is_archived = :is_archived";
    $params['is_archived'] = $filters['status'] === 'archived' ? 1 : 0;
}

// Handle category filter (support both 'category' and 'category_id')
$categoryId = $filters['category'] ?? $filters['category_id'] ?? null;
if (!empty($categoryId)) {
    $where[] = "p.category_id = :category_id";
    $params['category_id'] = $categoryId;
}
```

## Result
✅ Category dropdown now shows all available project categories from the database
✅ Status dropdown filters projects by Active/Archived status
✅ Search filter works correctly
✅ All filters can be combined for advanced filtering
✅ Filter state is preserved in URLs (e.g., `?search=test&category=1&status=active`)

## Testing
1. Navigate to `/projects`
2. Category dropdown now has options instead of being empty
3. Select a category → projects are filtered
4. Select a status (Active/Archived) → projects are filtered
5. Search + filters work together

## Files Modified
1. `src/Controllers/ProjectController.php` - Fixed index() method
2. `src/Services/ProjectService.php` - Fixed filter handling in getAllProjects()

## View File (No Changes Needed)
- `views/projects/index.php` - Already had correct filter structure, just needed data
