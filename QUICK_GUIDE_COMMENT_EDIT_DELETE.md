# Quick Guide - Comment Edit & Delete

## What's New

✅ **Edit Comments** - Pencil icon (✏️) in blue  
✅ **Delete Comments** - Trash icon (🗑️) in red  
✅ **Hover to Show** - Buttons appear when you hover over comment  
✅ **Own Comments Only** - Can edit/delete your own comments (admins can do all)  

---

## How to Use

### Edit Your Comment

```
1. Go to issue detail page
2. Find your comment
3. Hover over the comment
   ↓
   See [✏️] and [🗑️] icons appear
   ↓
4. Click the blue pencil [✏️]
   ↓
   Edit form appears
   ↓
5. Modify the text
6. Click "Save"
   ↓
   Comment updates
   ↓
7. See "Comment updated successfully" notification
```

### Delete Your Comment

```
1. Go to issue detail page
2. Find your comment
3. Hover over the comment
   ↓
   See [✏️] and [🗑️] icons appear
   ↓
4. Click the red trash [🗑️]
   ↓
   Confirmation dialog appears
   ↓
5. Click "OK" to confirm
   ↓
   Comment fades out and disappears
   ↓
6. See "Comment deleted successfully" notification
```

### Cancel Edit

```
1. Click edit button
2. Edit form opens
3. Change your mind?
4. Click "Cancel"
   ↓
   Form closes
   ↓
   Nothing changes
```

### Cancel Delete

```
1. Click delete button
2. Confirmation appears
3. Change your mind?
4. Click "Cancel"
   ↓
   Dialog closes
   ↓
   Comment stays
```

---

## Visual Guide

### Hovering Over Comment
```
Before Hover:
┌─────────────────────────────┐
│ John Doe          just now  │
│ This is my comment          │
└─────────────────────────────┘

On Hover:
┌─────────────────────────────────────┐
│ John Doe    just now    [✏️] [🗑️]   │
│ This is my comment                  │
└─────────────────────────────────────┘
```

### Edit Form
```
Click edit button
     ↓
┌───────────────────────────────────┐
│ [Textarea with your comment text] │
│                                   │
│ [Save] [Cancel]                   │
└───────────────────────────────────┘
```

### Delete Confirmation
```
Click delete button
     ↓
┌──────────────────────────────────┐
│ Delete Comment?                  │
│                                  │
│ Are you sure you want to delete  │
│ this comment? This action cannot │
│ be undone.                       │
│                                  │
│         [OK] [Cancel]            │
└──────────────────────────────────┘
```

---

## Permissions

### Regular Users
- ✅ Edit own comments
- ✅ Delete own comments
- ❌ Cannot edit/delete others' comments

### Admins
- ✅ Edit any comment
- ✅ Delete any comment
- ✅ Override any user's comments

---

## Tips & Tricks

### Tip 1: Hover to See Buttons
Buttons are hidden until you hover - less cluttered look!

### Tip 2: Cancel is Always Available
Changed your mind? Click Cancel to undo.

### Tip 3: Confirmation on Delete
Delete requires confirmation - prevents accidents.

### Tip 4: Notifications Appear
Look for success messages at top of page.

### Tip 5: Changes Persist
Refresh the page - your edits are saved!

---

## FAQ

**Q: Can I edit other people's comments?**  
A: No, unless you're an admin.

**Q: Can I recover a deleted comment?**  
A: No, deletion is permanent.

**Q: Will people see when I edit?**  
A: No, edits are silent.

**Q: Can I edit comment after a long time?**  
A: Yes, anytime (unless deleted).

**Q: What's the character limit?**  
A: 50,000 characters max.

**Q: Can I undo a delete?**  
A: No, but there's a confirmation dialog.

---

## Troubleshooting

### Buttons Not Showing?
→ Hover over the comment  
→ Try refreshing the page  
→ Clear browser cache  

### Edit Form Not Opening?
→ Check you have permission  
→ Try refreshing the page  
→ Check browser console (F12)  

### Changes Not Saving?
→ Check internet connection  
→ Look for error message  
→ Try again  

### Delete Not Working?
→ Confirm the dialog appears  
→ Check you have permission  
→ Try refreshing and trying again  

---

## Keyboard Shortcuts

Currently no keyboard shortcuts, but future versions might add:
- `Escape` to cancel edit
- `Ctrl+Enter` to save edit

---

## Mobile Use

On mobile devices:
- Buttons might not appear on hover
- Tap/touch to interact with comment
- Edit form works on all devices
- Delete confirmation is easy to use

---

## What Gets Updated

When you edit:
- ✅ Comment text updates
- ✅ "updated" marker shows
- ✅ Timestamp stays same
- ❌ Edit history not saved (currently)

When you delete:
- ✅ Comment completely removed
- ✅ Comment count decreases
- ❌ No trash/recovery option

---

## Notifications

### Edit Success
```
✓ Comment updated successfully
(appears at top, auto-dismisses in 3 seconds)
```

### Delete Success
```
✓ Comment deleted successfully
(appears at top, auto-dismisses in 3 seconds)
```

### Error Messages
```
Error: Comment cannot be empty
Error: [error details]
(appears as alert dialog)
```

---

## Best Practices

1. **Re-read before saving**
   - Review edits before clicking Save

2. **Be careful with delete**
   - Deletion is permanent
   - Confirmation is required

3. **Use edit for corrections**
   - Typos
   - Formatting
   - Clarity

4. **Ask before deleting**
   - If others replied to it
   - Might lose context

5. **Check permissions**
   - Only your comments show buttons
   - Admins can override

---

## Support

### Need Help?
1. Check this quick guide
2. Read full feature guide: `COMMENT_EDIT_DELETE_FEATURE.md`
3. Check browser console (F12)
4. Contact administrator

### Reporting Issues
Include:
- What you clicked
- What happened
- Browser type
- Error message (if any)

---

## Summary

**Edit**: Pencil icon (✏️)  
**Delete**: Trash icon (🗑️)  
**Show**: On hover  
**Access**: Your comments (all if admin)  
**Confirm**: Delete requires confirmation  
**Feedback**: Notifications appear  

---

**Version**: 1.0  
**Date**: 2025-12-06  
**Status**: Ready to Use ✅
