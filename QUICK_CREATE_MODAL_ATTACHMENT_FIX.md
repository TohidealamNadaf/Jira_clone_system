# Quick Create Modal - Attachment Not Being Created FIX

## Problem Report
User reported that when creating an issue from the Quick Create Modal:
1. Modal appears to close successfully (looks like form submitted)
2. But the issue is NOT created in the database
3. Issue occurs when adding attachments in the description or PDF attachments

## Root Cause Analysis

### Issue #1: Form Data Serialization Bug (PRIMARY)
**Location**: `views/layouts/app.php` lines 2472-2474

**Problem**:
```javascript
// This converts FormData to plain JSON object
const data = Object.fromEntries(
    Array.from(formData.entries()).filter(([key]) => key !== 'create_another')
);
```

**Why it fails**:
- FormData can contain File objects
- `Object.fromEntries()` converts FormData to a plain object
- When you serialize File objects to JSON, they become `[object Object]`
- The API rejects the request or silently fails because files are missing/invalid
- Modal closes (due to catch block) but issue is never created

**Evidence**:
- Console log at line 2475: `console.log('[SUBMIT] Creating issue with data:', data);` will show File objects as `[object Object]`
- The server never receives valid file data

### Issue #2: Content-Type Mismatch (SECONDARY)
**Location**: `views/layouts/app.php` lines 2488-2489

**Problem**:
```javascript
headers: {
    'Content-Type': 'application/json',
    // ...
}
body: JSON.stringify(data),  // Line 2493 - Trying to JSON serialize File objects!
```

**Why it fails**:
- Sending File objects as JSON is impossible
- File objects cannot be serialized to JSON
- When JSON.stringify encounters a File, it converts it to `{}`
- Server receives invalid/empty file data

### Issue #3: Attachment Field Not in Validation (TERTIARY)
**Location**: `src/Controllers/IssueController.php` lines 120-136

**Problem**:
- Validation schema doesn't include attachment fields
- Even if files reached the server, they would be rejected by validation
- No validation rule for attachments or files

## Solution

### Fix #1: Use FormData for File Submission (PRIMARY)
**Change**: Keep FormData instead of converting to JSON

```javascript
// BEFORE: Converts files to [object Object]
const data = Object.fromEntries(
    Array.from(formData.entries()).filter(([key]) => key !== 'create_another')
);

// AFTER: Keep FormData to preserve File objects
const formDataToSend = new FormData(form);
// Remove create_another checkbox
formDataToSend.delete('create_another');
```

### Fix #2: Set Correct Content-Type
**Change**: Don't set Content-Type header when using FormData

```javascript
// BEFORE: Sets JSON content type
headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    'X-Requested-With': 'XMLHttpRequest',
},
body: JSON.stringify(data),

// AFTER: Let browser set multipart/form-data
// Don't set Content-Type - browser will set it with boundary
const response = await fetch(webUrl, {
    method: 'POST',
    credentials: 'include',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        // Remove Content-Type - let browser set it!
    },
    body: formDataToSend,  // Use FormData directly
});
```

### Fix #3: Add Attachment Validation
**File**: `src/Controllers/IssueController.php` lines 120-136

```php
$data = $request->validate([
    'project_id' => 'required|integer',
    'issue_type_id' => 'required|integer',
    'summary' => 'required|max:500',
    'description' => 'nullable|max:50000',
    // ... other fields ...
    'attachments' => 'nullable|array',  // ADD THIS
    'attachments.*' => 'file|max:10240',  // Max 10MB per file
]);
```

## Files to Modify

1. **views/layouts/app.php** (lines 2461-2494)
   - Change FormData handling
   - Remove JSON serialization
   - Remove Content-Type header
   - Keep FormData as-is for submission

2. **src/Controllers/IssueController.php** (lines 120-136)
   - Add attachment validation rules
   - Validate file array (optional, but if present, validate each file)

3. **src/Services/IssueService.php** (if attachment handling needed)
   - Ensure createIssue() can handle FormData with files
   - Or handle attachments in separate API call after issue creation

## Testing Checklist

- [ ] Create issue via Quick Modal without attachments - should work
- [ ] Create issue via Quick Modal with description attachment - should work
- [ ] Create issue via Quick Modal with PDF attachment - should work
- [ ] Create issue via Quick Modal with multiple attachments - should work
- [ ] Check DevTools Network tab - should see multipart/form-data request
- [ ] Check database - issue should be created with files
- [ ] Check `/public/uploads/` - files should be stored there

## Browser Network Inspection

When fixed, Network tab should show:
```
POST /projects/BP/issues
Request Headers:
  Content-Type: multipart/form-data; boundary=----WebKitFormBoundary...
  X-CSRF-TOKEN: [token]
  X-Requested-With: XMLHttpRequest

Request Body:
  ------WebKitFormBoundary...
  Content-Disposition: form-data; name="project_id"
  
  1
  ------WebKitFormBoundary...
  Content-Disposition: form-data; name="attachments"; filename="document.pdf"
  Content-Type: application/pdf
  
  [binary file data]
  ------WebKitFormBoundary...--
```

## Impact

- **Risk Level**: LOW - Only affects attachment submission
- **Breaking Changes**: NONE
- **Backward Compatible**: YES - Non-attachment issues still work
- **Performance**: No impact
- **Database**: No schema changes needed

## Status
🔴 **NOT YET FIXED** - Awaiting implementation

This is a CRITICAL bug preventing issue creation when attachments are included.
