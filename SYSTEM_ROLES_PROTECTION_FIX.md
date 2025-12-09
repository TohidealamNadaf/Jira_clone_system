# System Roles Protection Fix

## Issue
System roles (Administrator, Developer, Project Manager, QA Tester, Viewer) could be accessed for editing from the UI, even though the server-side validation would block changes. This created a confusing UX where users could load the edit form but get a 403 error only after trying to submit.

## Solution
Implemented comprehensive client-side protection for system roles, matching the approach used for administrator users:

### Changes Made

#### 1. Controller: `src/Controllers/AdminController.php`

**Before:**
```php
public function editRole(Request $request): string
{
    // ...
    // Prevent editing of system roles
    if ($role['is_system'] ?? false) {
        abort(403, 'System roles cannot be edited.');
    }
    // ...
}
```

**After:**
```php
public function editRole(Request $request): string
{
    // ...
    // Allow viewing system roles, but pass flag to disable editing in view
    $isSystemRole = $role['is_system'] ?? false;

    // ...
    
    return $this->view('admin.roles.form', [
        'role' => $role,
        'permissions' => $permissionIds,
        'allPermissions' => $allPermissions,
        'isSystemRole' => $isSystemRole,  // ← Pass flag
    ]);
}
```

**Rationale:** Users can view system role information without getting a 403 error, but the form is disabled for editing.

#### 2. View: `views/admin/roles/form.php`

**Added:**
- Warning alert: "System Role - Cannot be modified"
- Disabled form fields: name, description
- Disabled permission checkboxes
- Disabled "Select All/None" buttons
- Disabled submit button

```php
<?php if ($isSystemRole): ?>
<div class="alert alert-warning alert-dismissible fade show mb-4">
    <i class="bi bi-shield-lock me-2"></i>
    <strong>System Role</strong> - Cannot be modified.
</div>
<?php endif; ?>

<!-- All form fields disabled for system roles -->
<input ... <?= $isSystemRole ? 'disabled' : '' ?> >
<textarea ... <?= $isSystemRole ? 'disabled' : '' ?> ></textarea>
<button type="submit" <?= $isSystemRole ? 'disabled' : '' ?> >
```

#### 3. View: `views/admin/roles/show.php`

**Added:**
- Conditional edit button (hidden for system roles)
- "System Role" badge in header (for system roles)
- Warning alert: "This role cannot be modified"

```php
<?php if (!($role['is_system'] ?? false)): ?>
    <a href="<?= url('/admin/roles/' . $role['id'] . '/edit') ?>" class="btn btn-primary">
        <i class="bi bi-pencil me-1"></i> Edit Role
    </a>
<?php else: ?>
    <span class="badge bg-warning">
        <i class="bi bi-shield-lock me-1"></i> System Role
    </span>
<?php endif; ?>
```

#### 4. View: `views/admin/roles/index.php`

**Changes:**
- For system roles: Show only "View" button and "Protected" badge (instead of Edit/Delete)
- For custom roles: Show all buttons (View, Edit, Delete)
- Fixed documentation: "cannot be edited or deleted" (was "can be modified")

```php
<?php if ($role['is_system'] ?? false): ?>
    <div class="btn-group">
        <a href="<?= url('/admin/roles/' . $role['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View">
            <i class="bi bi-eye"></i>
        </a>
        <span class="badge bg-warning" title="System roles cannot be modified">
            <i class="bi bi-shield-lock me-1"></i> Protected
        </span>
    </div>
<?php else: ?>
    <!-- Show Edit and Delete buttons -->
<?php endif; ?>
```

### Protection Layers

| Layer | Method | Status |
|-------|--------|--------|
| **Controller** | Removed early 403 abort, allows page load with disabled flag | ✅ |
| **Client-Side (Form)** | Disabled form fields and submit button | ✅ |
| **Client-Side (Buttons)** | Hidden edit button in index and show views | ✅ |
| **Visual Feedback** | Warning alert + badge + tooltip | ✅ |
| **Server-Side** | updateRole() still validates and blocks changes | ✅ Existing |

### User Experience

#### Viewing System Role Information
**Path:** `/admin/roles/{id}`

```
✅ Edit button hidden
✅ Warning alert visible: "System Role - Cannot be modified"
✅ Can view permissions and users
```

#### Attempting to Edit System Role
**Path:** `/admin/roles/{id}/edit`

```
✅ Page loads successfully (no 403)
✅ All fields are visibly disabled (grayed out)
✅ Submit button is disabled
✅ Warning alert explains why
✅ Cannot submit form
```

#### System Roles List
**Path:** `/admin/roles`

```
✅ System roles show "Protected" badge
✅ Edit button is hidden
✅ Delete button is hidden
✅ Only "View" button is available
✅ Custom roles show all action buttons
```

### Database
- No schema changes required
- Uses existing `is_system` flag on `roles` table

### Security

**Multi-layer protection:**
1. **UI prevents access** - Edit/Delete buttons hidden
2. **Form is disabled** - Cannot submit changes
3. **Server validates** - Blocks via HTTP 403 even if form is bypassed
4. **Clear feedback** - Users understand why action is unavailable

**Non-bypassable:** Even if user removes `disabled` attributes with DevTools, server-side validation in `updateRole()` will reject the request.

### Testing

#### Test 1: View System Role (Success Case)
```
1. Navigate to /admin/roles
2. Click "View" on "Administrator" role
3. Verify:
   ✅ Page loads without error
   ✅ Warning alert displays
   ✅ Can see permissions and users
```

#### Test 2: Try to Edit System Role (Blocked Case)
```
1. Navigate to /admin/roles
2. Verify Edit button is hidden for system roles
3. Attempt direct URL: /admin/roles/1/edit
4. Verify:
   ✅ Page loads
   ✅ Form fields are disabled (grayed out)
   ✅ Submit button is disabled
   ✅ Cannot submit form
```

#### Test 3: Edit Custom Role (Works)
```
1. Create a custom role
2. Click Edit on custom role
3. Verify:
   ✅ All fields are enabled
   ✅ Submit button is enabled
   ✅ Can make changes
```

#### Test 4: Server-Side Validation
```
1. Load /admin/roles/1/edit (system role)
2. Open DevTools → Console
3. Run: document.querySelectorAll('[disabled]').forEach(el => el.removeAttribute('disabled'))
4. Submit form
5. Verify:
   ✅ Form submission blocked by server
   ✅ 403 Forbidden error displayed
```

## Files Modified

1. **src/Controllers/AdminController.php**
   - Changed: `editRole()` - removes 403 abort, passes `isSystemRole` flag

2. **views/admin/roles/form.php**
   - Added: Warning alert for system roles
   - Updated: All form fields with `disabled` attribute for system roles
   - Updated: Submit button disabled for system roles

3. **views/admin/roles/show.php**
   - Added: Conditional edit button (hidden for system roles)
   - Added: System Role badge in header
   - Added: Warning alert for system roles

4. **views/admin/roles/index.php**
   - Updated: Action buttons - show "Protected" badge instead of Edit/Delete
   - Fixed: Documentation text to indicate system roles cannot be edited

## Backward Compatibility

✅ **No breaking changes**
- Existing code continues to work
- Server-side protection already exists
- Only adds UI-level protection
- No database changes required

## Documentation

Existing protection documentation covers system roles:
- `ADMIN_PROTECTION_QUICK_REFERENCE.md` - Lists system roles and their protection
- `ADMINISTRATOR_PROTECTION.md` - Technical implementation details

## Summary

This fix completes the administrator protection system by extending it to system roles. Users can now **view** system role information but **cannot modify** them through the UI, with clear visual feedback explaining why.

The implementation follows the same pattern as administrator user protection:
- Server-side blocking (existing)
- Client-side disabling (new)
- Visual warnings and badges (new)
- Hidden action buttons (new)

**Status: COMPLETE** ✅
