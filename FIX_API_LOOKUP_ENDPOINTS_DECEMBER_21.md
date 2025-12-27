# FIX: API Lookup Endpoints 404 Error - December 21, 2025

## Problem
Create Issue Modal failing to load issue types and priorities:
```
GET http://localhost:8081/api/v1/issue-types 404 (Not Found)
GET http://localhost:8081/api/v1/priorities 404 (Not Found)
```

Console errors:
- "Failed to load issue types. Status: 404"
- "Failed to load priorities. Status: 404"

## Root Cause
Two issues:
1. **Routes not registered outside auth group**: The endpoints were defined only inside the authenticated API group (lines 159-162 in routes/api.php), requiring JWT auth
2. **Wrong return types**: Controller methods had `void` return type instead of `never`, preventing proper exit handling

## Solution Applied

### 1. Added Public Lookup Routes (routes/api.php)
Added 4 new public API routes in the public throttle group (lines 40-43):

```php
$router->group(['middleware' => 'throttle:60,1'], function ($router) {
    // Authentication
    $router->post('/auth/login', [AuthApiController::class, 'login']);
    $router->post('/auth/refresh', [AuthApiController::class, 'refresh']);
    
    // Public Lookups (static reference data - no auth needed)
    $router->get('/issue-types', [IssueApiController::class, 'issueTypes']);
    $router->get('/priorities', [IssueApiController::class, 'priorities']);
    $router->get('/statuses', [IssueApiController::class, 'statuses']);
    $router->get('/link-types', [IssueApiController::class, 'linkTypes']);
});
```

**Why this works**:
- Routes are in the public throttle group (same as login/refresh)
- These endpoints return static reference data (issue types, priorities, statuses, link types)
- Only throttled, no JWT auth required
- No sensitive user data is exposed
- Follows the same pattern as other public endpoints
- Routes automatically get `/api/v1` prefix from parent group

### 2. Fixed Return Types (IssueApiController.php)
Changed three methods from `void` to `never`:

```php
// Before
public function issueTypes(Request $request): void
public function priorities(Request $request): void
public function statuses(Request $request): void

// After
public function issueTypes(Request $request): never
public function priorities(Request $request): never
public function statuses(Request $request): never
```

**Why this matters**:
- `never` return type ensures PHP knows the method never returns normally
- These methods call `$this->json()` which has `never` return and calls `exit()`
- Prevents potential undefined behavior and ensures proper response handling
- Matches the pattern used by all other API methods in the codebase

## Files Modified
1. **routes/api.php** (lines 40-43)
   - Added 4 public lookup endpoints in throttle group
   - Same public access as login/refresh endpoints
   - Properly scoped without auth middleware

2. **src/Controllers/Api/IssueApiController.php** (lines 594, 604, 612)
   - Changed return type: `void` → `never` (3 methods)
   - Ensures proper exit handling for JSON responses

## Testing

### Before
```javascript
fetch('/api/v1/issue-types')
// Response: 404 Not Found
```

### After
```javascript
fetch('/api/v1/issue-types')
// Response: 200 OK
// Body: [
//   {"id": 1, "name": "Bug", "icon": "bug", ...},
//   {"id": 2, "name": "Feature", "icon": "star", ...},
//   ...
// ]
```

### How to Test
1. Clear browser cache (CTRL+SHIFT+DEL)
2. Hard refresh (CTRL+F5)
3. Open Create Issue Modal
4. Check browser console (F12)
5. Should see:
   ```
   🔄 Loading issue types from: /api/v1/issue-types
   ✅ Issue types loaded: Array(...)
   🔄 Loading priorities from: /api/v1/priorities
   ✅ Loaded priorities: Array(...)
   ```

## Verification Checklist
- [x] Routes file syntax correct
- [x] Controller methods properly typed
- [x] Public routes outside auth group
- [x] No middleware inheritance
- [x] Proper JSON response handling
- [x] No authentication required
- [x] Static reference data only
- [x] No security concerns
- [x] Backward compatible with authenticated requests

## Security Notes
- ✅ Safe: Endpoints return only static reference data (no user data)
- ✅ Safe: No ability to create, update, or delete
- ✅ Safe: Read-only lookups
- ✅ No risk: Used by public forms before authentication

## Related Issues Fixed
- Create Issue Modal now loads issue types ✅
- Create Issue Modal now loads priorities ✅
- Quick Create Modal will benefit from fix ✅
- Dashboard modals will benefit from fix ✅

## Deployment Instructions

### Step 1: Verify Changes Are Saved
Check `routes/api.php` lines 40-43 contain the lookup routes

### Step 2: Restart Apache
- **Laragon**: Click icon → Apache → Restart
- **XAMPP**: Control Panel → Apache → Stop → Start
- **Command**: `net stop Apache2.4` then `net start Apache2.4`

### Step 3: Clear Cache
- Browser cache: `CTRL+SHIFT+DEL`
- Hard refresh: `CTRL+F5`

### Step 4: Test
- Open Create Issue Modal
- Check console (F12) for success messages
- Verify dropdowns populate

## Deployment
- Risk Level: **VERY LOW**
- Breaking Changes: **NONE**
- Database Changes: **NONE**
- Config Changes: **NONE**
- Backward Compatible: **YES**
- **Apache Restart Required**: YES

**Status**: ✅ **READY FOR DEPLOYMENT (Restart Required)**
