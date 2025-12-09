# 403 Error Message Display - Updated

## Overview

The 403 Forbidden error page has been updated to display custom error messages when users attempt to perform restricted actions (like editing administrator users or system roles).

## What Changed

### File Updates

1. **views/errors/403.php**
   - Added `.error-message` styled container
   - Added conditional display of custom message
   - Updated styling with backdrop filter and glass-morphism effect
   - Made buttons responsive with flex-wrap

2. **src/Helpers/functions.php**
   - Updated `abort()` function to pass `$message` variable to error template
   - Message is now accessible in error views for display

## Error Message Display

### When User Tries to Edit Administrator User

**URL**: `/admin/users/1/edit`

**Response Code**: 403 Forbidden

**Display in UI**:
```
┌─────────────────────────────────────────┐
│                                         │
│            🔒 (Shield Lock Icon)        │
│                                         │
│                  403                    │
│                                         │
│         Access Forbidden                │
│                                         │
│  ┌───────────────────────────────────┐  │
│  │ ⚠️  Administrator users cannot be  │  │
│  │     edited.                        │  │
│  └───────────────────────────────────┘  │
│                                         │
│  [🏠 Go Home] [← Go Back] [Login]      │
│                                         │
└─────────────────────────────────────────┘
```

### Error Message Styling

The message is displayed in a semi-transparent container with:
- **Background**: Semi-transparent white (rgba(255, 255, 255, 0.1))
- **Padding**: 1.5rem
- **Border Radius**: 8px
- **Border**: 1px solid rgba(255, 255, 255, 0.2)
- **Font Size**: 1.1rem
- **Text Color**: White
- **Icon**: ⚠️ Exclamation Circle

## Messages Displayed

### Administrator User Restrictions

**When trying to edit an admin user:**
```
Administrator users cannot be edited.
```

**When trying to update an admin user:**
```
Administrator users cannot be edited.
```

**When trying to delete an admin user:**
```
Administrator users cannot be deleted.
```

### System Role Restrictions

**When trying to edit a system role:**
```
System roles cannot be edited.
```

**When trying to update a system role:**
```
System roles cannot be edited.
```

**When trying to delete a system role:**
```
System roles cannot be deleted.
```

## Technical Implementation

### How It Works

1. **Controller** calls `abort(403, 'Message here')`
2. **abort()** function sets HTTP status code to 403
3. **abort()** includes the 403.php error template
4. **Message variable** is available in the template scope
5. **Template** checks if message is set and displays it
6. **User sees** the formatted error message with styling

### Code Flow

```php
// In AdminController
if ($user['is_admin']) {
    abort(403, 'Administrator users cannot be edited.');
}

// In functions.php abort()
function abort(int $code, string $message = ''): never {
    http_response_code($code);
    // ...
    $errorView = views_path("errors/$code.php");
    if (file_exists($errorView)) {
        include $errorView;  // $message is now in scope
    }
    exit;
}

// In views/errors/403.php
<?php if (isset($message) && !empty($message)): ?>
<div class="error-message">
    <p>
        <i class="bi bi-exclamation-circle me-2"></i>
        <?= htmlspecialchars($message) ?>
    </p>
</div>
<?php endif; ?>
```

## Fallback Behavior

If no custom message is provided, the page displays:
```
You don't have permission to access this resource.
```

This ensures backward compatibility with other 403 errors that may not have a custom message.

## Security Considerations

### XSS Protection
- Message is escaped using `htmlspecialchars()` to prevent XSS attacks
- All user input is properly encoded before display

### HTML Encoding
- Special characters are converted to HTML entities
- Malicious scripts cannot be injected through error messages

## Browser Compatibility

The error page uses:
- Bootstrap 5.3.2 (CSS Framework)
- Bootstrap Icons 1.11.2 (Icon Set)
- CSS Grid and Flexbox (Layout)
- CSS backdrop-filter (Glass effect - requires modern browser)

**Tested on:**
- Chrome/Chromium 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Responsive Design

The error page is fully responsive:
- **Desktop** (>1024px): Full width with centered container
- **Tablet** (768px-1024px): Adjusted padding and button size
- **Mobile** (<768px): Buttons stack with flex-wrap

```css
@media (max-width: 768px) {
    .error-code {
        font-size: 5rem;
    }
    .error-container {
        padding: 2rem;
    }
}
```

## Testing the Error Display

### Manual Test Steps

1. Login to admin panel
2. Go to `/admin/users`
3. Try to edit the admin user (System Administrator)
4. Observe the 403 error page with message

**Expected Result:**
- 403 Forbidden page displays
- Custom message shows: "Administrator users cannot be edited."
- Message is properly styled and readable

### Automated Test

```php
// In your test file
$response = $client->get('/admin/users/1/edit');
$this->assertEquals(403, $response->getStatusCode());
$this->assertStringContainsString('Administrator users cannot be edited.', $response->getContent());
```

## Customization

To customize the error message appearance:

1. Edit `views/errors/403.php`
2. Modify `.error-message` styles
3. Change colors in the gradient background
4. Update button styles as needed

```css
/* Example customization */
.error-message {
    background: rgba(255, 255, 255, 0.15);  /* More opaque */
    padding: 2rem;                           /* More padding */
    border-radius: 12px;                     /* More rounded */
    border: 2px solid rgba(255, 255, 255, 0.3);  /* Thicker border */
}
```

## Related Files

- `src/Controllers/AdminController.php` - Calls abort() with messages
- `src/Helpers/functions.php` - Contains abort() function
- `views/errors/403.php` - Error template
- `views/errors/404.php` - 404 template (similar structure)
- `views/errors/500.php` - 500 template (similar structure)

## Changelog

- **v1.0** (Current)
  - Added custom message display to 403 error page
  - Added glass-morphism styling to error message container
  - Updated abort() function to make message available in templates
  - Added XSS protection with htmlspecialchars()
  - Made buttons responsive with flex-wrap

## Future Enhancements

1. Add error code and request ID for debugging
2. Implement error tracking and logging
3. Add dark/light theme support
4. Internationalization (i18n) support for error messages
5. Add contact support form for 403 errors
