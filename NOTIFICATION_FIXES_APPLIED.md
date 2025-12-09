# Notification System Fixes - Applied Changes

**Status**: ✅ Applied  
**Date**: December 7, 2025  
**Summary**: Fixed authentication and routing issues with notification action buttons

---

## Changes Applied (4 Files)

### 1. src/Middleware/ApiMiddleware.php

**Added session-based authentication support**

```diff
+ use App\Core\Session;

  private function authenticate(Request $request): ?array
  {
      // ... existing JWT and PAT auth checks ...
+     // Try session-based authentication (for AJAX calls from web pages)
+     $user = $this->authenticateSession();
+     if ($user) {
+         return $user;
+     }
      return null;
  }

+ /**
+  * Authenticate with session (for AJAX calls from web pages)
+  */
+ private function authenticateSession(): ?array
+ {
+     $user = Session::user();
+     if (!$user || !($user['is_active'] ?? true)) {
+         return null;
+     }
+     return [
+         'id' => $user['id'],
+         'email' => $user['email'],
+         'first_name' => $user['first_name'],
+         'last_name' => $user['last_name'],
+         'token_type' => 'session',
+     ];
+ }
```

---

### 2. src/Core/Request.php

**Updated user() method to check API context**

```diff
- /**
-  * Get authenticated user from session
-  */
+ /**
+  * Get authenticated user from session or API context
+  */
  public function user(): ?array
  {
+     // Check if user was authenticated by API middleware
+     if (isset($GLOBALS['api_user'])) {
+         return $GLOBALS['api_user'];
+     }
+     
+     // Fall back to session user
      return Session::user();
  }
```

---

### 3. routes/api.php

**Fixed route order for notification endpoints**

```diff
  // Notifications
  $router->get('/notifications', [NotificationController::class, 'apiIndex']);
- $router->patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
  $router->patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
+ $router->patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
  $router->delete('/notifications/{id}', [NotificationController::class, 'delete']);
  $router->get('/notifications/preferences', [NotificationController::class, 'getPreferences']);
  $router->post('/notifications/preferences', [NotificationController::class, 'updatePreferences']);
  $router->get('/notifications/stats', [NotificationController::class, 'getStats']);
```

---

### 4. views/notifications/index.php

**Updated JavaScript to use configured base URL and fixed all API calls**

```diff
  <script>
+ // Get base URL from app configuration
+ const APP_BASE_URL = '<?= rtrim(config('app.url', ''), '/') ?>';
+ console.log('APP_BASE_URL:', APP_BASE_URL);
+
  document.addEventListener('DOMContentLoaded', function() {
      // ... filter functionality ...

      // Mark single notification as read
      document.querySelectorAll('.mark-read-btn').forEach(btn => {
          btn.addEventListener('click', async (e) => {
              e.preventDefault();
              e.stopPropagation();
              const notificationId = btn.dataset.notificationId;
              
              console.log('Marking notification as read:', notificationId);
              
              try {
                  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
-                 const url = `/jira_clone_system/public/api/v1/notifications/${notificationId}/read`;
+                 const url = `${APP_BASE_URL}/api/v1/notifications/${notificationId}/read`;
                  
                  console.log('Marking notification as read:', notificationId);
                  console.log('Fetching:', url);
                  
                  const response = await fetch(url, {
                      method: 'PATCH',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-Token': csrfToken || ''
                      }
                  });
                  // ... rest of handler ...
              }
          });
      });

      // Delete notification
      document.querySelectorAll('.delete-btn').forEach(btn => {
          btn.addEventListener('click', async (e) => {
              e.preventDefault();
              e.stopPropagation();
              if (!confirm('Delete this notification?')) return;

              const notificationId = btn.dataset.notificationId;
              console.log('Deleting notification:', notificationId);

              try {
                  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
-                 const url = `/jira_clone_system/public/api/v1/notifications/${notificationId}`;
+                 const url = `${APP_BASE_URL}/api/v1/notifications/${notificationId}`;
                  
                  const response = await fetch(url, {
                      method: 'DELETE',
                      headers: {
                          'X-CSRF-Token': csrfToken || ''
                      }
                  });
                  // ... rest of handler ...
              }
          });
      });

      // Mark all as read
      document.getElementById('markAllReadBtn')?.addEventListener('click', async (e) => {
          e.preventDefault();
          console.log('Marking all notifications as read');

          try {
              const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
-             const url = `/jira_clone_system/public/api/v1/notifications/read-all`;
+             const url = `${APP_BASE_URL}/api/v1/notifications/read-all`;
              
              console.log('Fetching:', url);
              
              const response = await fetch(url, {
                  method: 'PATCH',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-Token': csrfToken || ''
                  }
              });
              // ... rest of handler ...
          }
      });
  });
  </script>
```

---

## What These Changes Do

### 1. ApiMiddleware - Session Auth Support
- ✅ Adds fallback authentication method for session-based requests
- ✅ Allows AJAX calls from web pages to authenticate without JWT
- ✅ Maintains backward compatibility with JWT/PAT/API Key auth

### 2. Request::user() - API Context Awareness
- ✅ Checks if user was authenticated by API middleware first
- ✅ Falls back to session user if no API auth
- ✅ Makes controllers work in both API and web contexts

### 3. Route Order - Specific Before Generic
- ✅ Prevents `/read-all` from matching as `{id}` parameter
- ✅ Router checks exact paths before parameterized routes
- ✅ Ensures correct endpoint is called

### 4. View URLs - App Configuration
- ✅ Uses configured base URL from `config/config.php`
- ✅ Works regardless of installation path
- ✅ Same approach as rest of app
- ✅ Logs URL to console for debugging

---

## Testing the Fixes

### Step 1: Check Browser Console
1. Open notification page: `/notifications`
2. Press F12 to open Developer Tools
3. Go to Console tab
4. You should see: `APP_BASE_URL: http://localhost:8080/jira_clone_system/public`
5. Click "Mark as Read" button
6. Check console for:
   ```
   Marking notification as read: [ID]
   Fetching: http://localhost:8080/jira_clone_system/public/api/v1/notifications/[ID]/read
   Response status: 200
   Response data: {status: "success", ...}
   ```

### Step 2: Check Network Tab
1. Open Developer Tools → Network tab
2. Clear previous requests
3. Click "Mark as Read" button
4. Find the PATCH request to `/api/v1/notifications/[ID]/read`
5. Verify:
   - Status: 200
   - URL: `http://localhost:8080/jira_clone_system/public/api/v1/notifications/[ID]/read`
   - Response: `{"status":"success",...}`

### Step 3: Test All Three Buttons
- ✅ Mark as Read - Should update UI without reload
- ✅ Delete - Should remove notification from list
- ✅ Mark All as Read - Should mark all unread as read

---

## Expected Console Output

### On Page Load
```
APP_BASE_URL: http://localhost:8080/jira_clone_system/public
```

### When Clicking Mark as Read
```
Marking notification as read: 123
Fetching: http://localhost:8080/jira_clone_system/public/api/v1/notifications/123/read
Response status: 200
Response data: {status: "success", unread_count: 5}
```

### When Clicking Delete
```
Deleting notification: 456
Fetching: http://localhost:8080/jira_clone_system/public/api/v1/notifications/456
Response status: 200
Response data: {status: "success"}
```

### When Clicking Mark All as Read
```
Marking all notifications as read
Fetching: http://localhost:8080/jira_clone_system/public/api/v1/notifications/read-all
Response status: 200
Response data: {status: "success", unread_count: 0}
```

---

## Troubleshooting

### Issue: APP_BASE_URL shows empty
```javascript
APP_BASE_URL: 
```
**Solution**: The config() function failed. Check:
- Is the view being rendered through the app?
- Are you logged in?
- Are there PHP errors in server logs?

### Issue: 404 Error
```
Response status: 404
Response data: {error: "Not found"}
```
**Solution**: The API route wasn't found. Check:
- Routes are in correct file: `routes/api.php`
- Route order is correct (read-all before {id}/read)
- Application was restarted after changes

### Issue: 401 Unauthorized
```
Response status: 401
Response data: {error: "Unauthenticated"}
```
**Solution**: Session auth didn't work. Check:
- You're actually logged in (check navbar)
- PHPSESSID cookie exists (check Application → Cookies)
- Session is valid and user is active
- ApiMiddleware has Session import

### Issue: HTML Response
```
Unexpected token '<', "<!DOCTYPE"...
```
**Solution**: App returned an error page instead of JSON. Check:
- Server error logs
- Browser console for PHP errors
- APP_BASE_URL is correctly set

---

## Verification Checklist

Before considering this complete, verify:

- [ ] File 1: `src/Middleware/ApiMiddleware.php`
  - [ ] Has `use App\Core\Session;` import on line 13
  - [ ] Has `authenticateSession()` method around line 116
  - [ ] Method calls Session::user() and returns proper array

- [ ] File 2: `src/Core/Request.php`
  - [ ] user() method checks `$GLOBALS['api_user']` first
  - [ ] Falls back to `Session::user()`
  - [ ] Proper comment about API context

- [ ] File 3: `routes/api.php`
  - [ ] `/notifications/read-all` route comes BEFORE `/notifications/{id}/read`
  - [ ] Other routes unchanged

- [ ] File 4: `views/notifications/index.php`
  - [ ] Has `APP_BASE_URL` variable at top of script
  - [ ] All three API calls use `${APP_BASE_URL}/api/v1/...`
  - [ ] Console.log shows the base URL

---

## Summary

All changes have been applied to fix the notification system:

1. ✅ ApiMiddleware now supports session-based authentication
2. ✅ Request::user() method properly handles API context
3. ✅ Routes are in the correct order
4. ✅ View uses app's configured base URL

The notification buttons should now work without authentication errors.

---

**Next Step**: Visit `/notifications` and test the buttons. Check browser console for the expected output shown above.

If you still see errors, share the console output and we can debug further.
