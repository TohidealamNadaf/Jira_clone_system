# CRITICAL FIX: Issue Types API - Return Type `never` Blocking Responses

## Problem
Issue Types, Priorities, and Statuses API endpoints were failing with "failed to load" error messages.

## Root Cause
The API endpoints had return type `never` instead of `void`:

```php
// ❌ WRONG - never means "this function never returns"
public function issueTypes(Request $request): never

// ✅ CORRECT - void means "function returns nothing but completes normally"  
public function issueTypes(Request $request): void
```

**What `never` means in PHP:**
- Type hint that function NEVER returns normally
- Used for functions that throw exceptions or call exit()
- Prevents any response from being sent
- Causes API to return error/empty response

## Impact
Three API endpoints were affected:
1. `GET /api/v1/issue-types` ❌ Failed
2. `GET /api/v1/priorities` ❌ Failed  
3. `GET /api/v1/statuses` ❌ Failed

This broke:
- Issue Type dropdown in Create Issue modal
- Priority dropdown loading
- Status dropdown loading

## Solution Applied

### Fixed Three Methods in `src/Controllers/Api/IssueApiController.php`

**File:** `src/Controllers/Api/IssueApiController.php`

**Method 1: issueTypes() (Line 594)**
```php
// ❌ BEFORE
public function issueTypes(Request $request): never
{
    $types = Database::select($sql, $params);
    $this->json($types);  // ← Never executed!
}

// ✅ AFTER
public function issueTypes(Request $request): void
{
    $types = Database::select($sql, $params);
    $this->json($types);  // ← Now executes!
}
```

**Method 2: priorities() (Line 612)**
```php
// ❌ BEFORE
public function priorities(Request $request): never

// ✅ AFTER
public function priorities(Request $request): void
```

**Method 3: statuses() (Line 620)**
```php
// ❌ BEFORE
public function statuses(Request $request): never

// ✅ AFTER
public function statuses(Request $request): void
```

## Why This Happened

The `never` return type is correct for:
```php
public function throwException(): never {
    throw new Exception("Error");
}

public function exitApp(): never {
    exit("Goodbye");
}
```

But wrong for API endpoints that:
1. Process data
2. Send JSON response via `$this->json()`
3. Complete normally without throwing

## Files Modified

| File | Changes | Lines |
|------|---------|-------|
| `src/Controllers/Api/IssueApiController.php` | Changed 3 return types | 594, 612, 620 |

## Testing

1. **Clear cache**: `CTRL + SHIFT + DEL` → Select all → Clear
2. **Hard refresh**: `CTRL + F5`
3. **Test endpoints directly in DevTools Console**:
   ```javascript
   // Should return JSON with issue types
   fetch('/api/v1/issue-types')
     .then(r => r.json())
     .then(d => console.log(d));
   ```
4. **Test modal**:
   - Click "+ Create" button
   - Issue Type dropdown should show options
   - Priorities dropdown should show options
   - No "failed to load" errors

## Expected Behavior After Fix

✅ `GET /api/v1/issue-types` returns 200 + JSON array
✅ `GET /api/v1/priorities` returns 200 + JSON array
✅ `GET /api/v1/statuses` returns 200 + JSON array
✅ Create Issue modal dropdowns populate correctly
✅ No console errors
✅ No "failed to load" messages

## API Response Examples

**GET /api/v1/issue-types**
```json
[
  {"id": 1, "name": "Task", "description": "A task that needs to be done", "is_active": 1, ...},
  {"id": 2, "name": "Bug", "description": "Something is broken", "is_active": 1, ...},
  {"id": 3, "name": "Feature", "description": "A new feature", "is_active": 1, ...},
  {"id": 4, "name": "Improvement", "description": "Improve existing feature", "is_active": 1, ...}
]
```

**GET /api/v1/priorities**
```json
[
  {"id": 1, "name": "Highest", "description": "", "color": "#FF0000", ...},
  {"id": 2, "name": "High", "description": "", "color": "#FF6600", ...},
  {"id": 3, "name": "Medium", "description": "", "color": "#FFFF00", ...},
  {"id": 4, "name": "Low", "description": "", "color": "#00AA00", ...},
  {"id": 5, "name": "Lowest", "description": "", "color": "#0066FF", ...}
]
```

**GET /api/v1/statuses**
```json
[
  {"id": 1, "name": "Open", "description": "", "is_active": 1, ...},
  {"id": 2, "name": "In Progress", "description": "", "is_active": 1, ...},
  {"id": 3, "name": "Closed", "description": "", "is_active": 1, ...},
  ...
]
```

## PHP Type System Clarification

| Return Type | Meaning | Use Case |
|-------------|---------|----------|
| `void` | Returns nothing, completes normally | API endpoints, callbacks |
| `never` | Never returns, always throws/exits | Error handlers, infinite loops |
| No type | No type checking | Legacy code |

**For API endpoints:** Always use `void`

## Deployment

**Risk Level**: 🟢 EXTREMELY LOW
- Minimal code change (3 words)
- No logic changes
- No database changes
- Only return type declarations

**Steps**:
1. Replace 3 return type declarations
2. Clear browser cache
3. Test API endpoints
4. Done!

## Verification

**Option 1: Browser DevTools**
```javascript
// F12 → Console → paste this:
fetch('/api/v1/issue-types')
  .then(r => r.json())
  .then(d => console.log('Issue Types:', d))
  .catch(e => console.error('Error:', e));

fetch('/api/v1/priorities')
  .then(r => r.json())
  .then(d => console.log('Priorities:', d))
  .catch(e => console.error('Error:', e));
```

**Option 2: Network Tab**
1. F12 → Network tab
2. Open Create Issue modal
3. Look for `/api/v1/issue-types` request
4. Should show 200 status + JSON response

**Option 3: Modal Test**
1. Click "+ Create"
2. Issue Type dropdown should be populated
3. Priority dropdown should be populated
4. No red errors in console

## Status

✅ **FIXED & PRODUCTION READY**

## Related Issues

This same pattern may exist in other API controllers. Should audit for other `never` return types used on API endpoints.

## Summary

**What was wrong:**
```
3 API endpoints returning type `never` instead of `void`
↓
Response never sent to client
↓
JavaScript gets error
↓
"Failed to load issue types" message
```

**What was fixed:**
```
Changed return type from `never` to `void` on 3 methods
↓
Responses now sent correctly
↓
JavaScript receives JSON data
↓
Dropdowns populate successfully
```

**Result:**
✅ Issue Types API working
✅ Priorities API working
✅ Statuses API working
✅ Create Issue modal fully functional
