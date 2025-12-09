# Fix: User Three-Dot Menu 404 Error

**Status**: ✅ FIXED  
**Date**: December 9, 2025  
**Files Modified**: 3  
**Methods Added**: 2  
**Routes Added**: 2

## Problem

When clicking the three-dot menu (⋯) on users in the Admin Users page:
1. **Warning**: PHP undefined array key warning when displaying the menu
2. **404 Error**: `/admin/users/{id}/deactivate` and `/admin/users/{id}/activate` routes not found

## Root Causes

### Issue 1: Undefined Array Key Warning
**File**: `views/admin/users.php` (line 171)  
**Cause**: Template was checking `$u['status'] === 'active'` but the database column is `is_active` (0 or 1), not `status`.

**Fix**: Changed condition from:
```php
<?php if ($u['status'] === 'active'): ?>
```
To:
```php
<?php if (!empty($u['is_active'])): ?>
```

This matches the actual database schema and how status is checked elsewhere in the template (line 133).

### Issue 2: Missing Routes
**Files**: 
- `routes/web.php`
- `src/Controllers/AdminController.php`

**Cause**: The view template called `deactivate` and `activate` endpoints that didn't exist.

**Fix**: 
1. Added `deactivateUser()` method to AdminController
2. Added `activateUser()` method to AdminController
3. Registered routes in web.php:
   - `POST /admin/users/{id}/deactivate`
   - `POST /admin/users/{id}/activate`

## Changes Made

### 1. views/admin/users.php
- **Line 171**: Fixed `$u['status'] === 'active'` → `!empty($u['is_active'])`
- This resolves the PHP warning about undefined array key

### 2. src/Controllers/AdminController.php
Added two new methods (after deleteUser method):

#### deactivateUser() - Lines 408-458
- Validates user exists
- Prevents deactivating own account
- Prevents deactivating admin users
- Sets `is_active = 0`
- Logs audit trail
- Returns JSON or redirects with success message

#### activateUser() - Lines 460-489
- Validates user exists
- Sets `is_active = 1`
- Logs audit trail
- Returns JSON or redirects with success message

### 3. routes/web.php
Added two new routes in the admin group (lines 185-186):
```php
$router->post('/users/{id}/deactivate', [AdminController::class, 'deactivateUser'])->name('admin.users.deactivate');
$router->post('/users/{id}/activate', [AdminController::class, 'activateUser'])->name('admin.users.activate');
```

## Features

✓ **Authorization checks**: Both methods verify `admin.manage-users` permission  
✓ **Admin protection**: Prevents deactivating administrator users  
✓ **Self-protection**: Prevents deactivating own account  
✓ **Soft deactivation**: Sets `is_active` flag instead of deleting  
✓ **Audit logging**: All actions logged to audit_logs table  
✓ **JSON support**: Both methods support JSON responses for API clients  
✓ **User feedback**: Success/error messages on redirect or JSON response

## Testing

1. Navigate to `/admin/users`
2. Click three-dot menu on any non-admin user
3. Click "Deactivate" → User should be deactivated
4. Click "Activate" → User should be reactivated
5. No 404 errors or PHP warnings should appear

## Database Impact

No schema changes required. Uses existing `is_active` column in `users` table.

## Backward Compatibility

✓ No breaking changes  
✓ Works with existing database  
✓ No migration required
