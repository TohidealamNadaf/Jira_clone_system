# Design System Quick Reference

Fast lookup for design tokens and component styling.

## Color Palette

### Primary Brand
```
--jira-blue:        #0052CC  (Brand Blue)
--jira-blue-dark:   #003DA5  (Darker variant)
--jira-blue-light:  #2684FF  (Lighter variant)
--jira-blue-lighter: #DEEBFF (Very light, for backgrounds)
```

### Functional Status
```
--color-success:      #36B37E   (Green)
--color-success-light: #DFFCF0  (Light green bg)
--color-warning:      #FFAB00   (Amber)
--color-warning-light: #FFF3C1  (Light amber bg)
--color-error:        #FF5630   (Red)
--color-error-light:  #FFECEB  (Light red bg)
--color-info:         #00B8D9   (Teal)
```

### Text
```
--text-primary:   #161B22  (Darkest, headlines)
--text-secondary: #57606A  (Dark, body text)
--text-tertiary:  #738496  (Medium, secondary info)
--text-muted:     #97A0AF  (Light, disabled/hints)
```

### Backgrounds
```
--bg-primary:   #FFFFFF   (Main white)
--bg-secondary: #F7F8FA   (Light gray)
--bg-tertiary:  #ECEDF0   (Medium gray)
--border-color: #DFE1E6   (Border lines)
```

## Sizing & Radius

### Border Radius
```
--radius-sm: 3px   (Inputs, badges, small elements)
--radius-md: 6px   (Buttons, form elements)
--radius-lg: 8px   (Cards, larger containers)
--radius-xl: 12px  (Modals, dialogs)
```

### Dimensions
```
--sidebar-width: 256px
--navbar-height: 56px
--container-max-width: 1400px
```

## Shadows

### Box Shadows
```
--shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06)
--shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08)
--shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12)
--shadow-xl: 0 12px 32px rgba(0, 0, 0, 0.15)
```

## Animations & Transitions

```
--transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1)
--transition-base: 200ms cubic-bezier(0.4, 0, 0.2, 1)
--transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1)
```

## Status Colors (Issue Tracking)

| Status | Background | Text Color | Badge Class |
|--------|-----------|-----------|-------------|
| To Do | #DFE1E6 | #42526E | `.status-todo` |
| In Progress | #DEEBFF | #0052CC | `.status-in-progress` |
| In Review | #FCE2B6 | #7F5F01 | `.status-in-review` |
| Done | #E3FCEF | #006644 | `.status-done` |

## Issue Type Colors

| Type | Color | Badge Class |
|------|-------|-------------|
| Story | #6554C0 (Purple) | `.issue-type-story` |
| Bug | #FF5630 (Red) | `.issue-type-bug` |
| Task | #0052CC (Blue) | `.issue-type-task` |
| Epic | #6554C0 (Purple) | `.issue-type-epic` |
| Subtask | #00875A (Green) | `.issue-type-subtask` |

## Priority Colors

| Priority | Color | Icon Class |
|----------|-------|-----------|
| Highest | #FF5630 (Red) | `.priority-highest` |
| High | #FF8B00 (Orange) | `.priority-high` |
| Medium | #FFAB00 (Amber) | `.priority-medium` |
| Low | #0052CC (Blue) | `.priority-low` |
| Lowest | #626F86 (Gray) | `.priority-lowest` |

## Typography Sizes

| Element | Size | Weight | Letter-Spacing |
|---------|------|--------|-----------------|
| H1 | 2rem (32px) | 700 | -0.3px |
| H2 | 1.75rem (28px) | 700 | -0.3px |
| H3 | 1.5rem (24px) | 600 | -0.3px |
| H4 | 1.25rem (20px) | 600 | -0.3px |
| H5 | 1.125rem (18px) | 600 | -0.3px |
| H6 | 1rem (16px) | 600 | +0.5px |
| Body | 0.9375rem (15px) | 400 | -0.2px |
| Small | 0.875rem (14px) | 400 | -0.2px |
| XSmall | 0.8125rem (13px) | 400 | -0.2px |

## Component Height/Padding Quick Chart

### Buttons
| Size | Height | Padding | Font Size |
|------|--------|---------|-----------|
| SM | 32px | 0.5rem 0.75rem | 0.8125rem |
| Regular | 40px | 0.65rem 1rem | 0.9375rem |
| LG | 48px | 0.875rem 1.5rem | 1rem |

### Form Controls
| Type | Height | Padding |
|------|--------|---------|
| Regular | 40px | 0.75rem 1rem |
| Large | 48px | 0.875rem 1.25rem |

### Avatars
| Size | Dimension |
|------|-----------|
| SM | 24px × 24px |
| Regular | 32px × 32px |
| LG | 48px × 48px |
| XL | 64px × 64px |

## Spacing Scale

```
0.25rem = 4px
0.5rem = 8px
0.75rem = 12px
1rem = 16px
1.25rem = 20px
1.5rem = 24px
2rem = 32px
2.5rem = 40px
3rem = 48px
```

## Responsive Breakpoints

| Screen | Width | Use Case |
|--------|-------|----------|
| Mobile | < 576px | Phones |
| Tablet | 576px - 768px | Small tablets |
| Desktop | > 768px | Laptops/desktops |
| Large | > 1200px | Wide screens |

## Hover Effects

### Standard Hover
```css
transform: translateY(-2px);
box-shadow: var(--shadow-md);
```

### Link Hover
```css
color: var(--jira-blue-dark);
text-decoration: underline;
```

### Button Hover
```css
background: darker shade;
transform: translateY(-1px);
box-shadow: var(--shadow-md);
```

### Card Hover
```css
box-shadow: var(--shadow-md);
border-color: var(--border-color-light);
```

## Common CSS Patterns

### Flex Center
```html
<div class="d-flex justify-content-center align-items-center"></div>
```

### Space Between
```html
<div class="d-flex justify-content-between align-items-center"></div>
```

### Text Truncate (1 line)
```html
<span class="text-truncate-1"></span>
```

### Text Truncate (2 lines)
```html
<span class="text-truncate-2"></span>
```

### Gap Between Items
```html
<div class="d-flex gap-1"><!-- 8px gap --></div>
<div class="d-flex gap-2"><!-- 16px gap --></div>
<div class="d-flex gap-3"><!-- 24px gap --></div>
```

## Modal Layout

```html
<div class="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">Header</div>
            <div class="modal-body">Content</div>
            <div class="modal-footer">Footer</div>
        </div>
    </div>
</div>
```

## Alert Variants

```html
<div class="alert alert-info">Info</div>
<div class="alert alert-success">Success</div>
<div class="alert alert-warning">Warning</div>
<div class="alert alert-danger">Error</div>
```

## Badge System

### Status Badges
```html
<span class="status-badge status-todo">To Do</span>
<span class="status-badge status-in-progress">In Progress</span>
<span class="status-badge status-done">Done</span>
```

### Type Badges
```html
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-warning">Warning</span>
<span class="badge badge-danger">Danger</span>
```

## Issue Card Example

```html
<div class="board-card">
    <div class="board-card-key">PROJ-123</div>
    <div class="board-card-summary">Issue summary here</div>
    <div class="board-card-footer">
        <img class="board-card-avatar" src="avatar.jpg">
        <span class="badge badge-primary">Priority</span>
    </div>
</div>
```

## Focus States

All interactive elements have:
```css
outline: 2px solid var(--jira-blue);
outline-offset: 2px;
```

## Accessibility Checklist

- ✅ Color contrast ≥ 4.5:1 for text
- ✅ Focus states visible on all interactive elements
- ✅ ARIA labels on icons
- ✅ Form labels associated with inputs
- ✅ Semantic HTML structure
- ✅ Keyboard navigation support

## Performance Tips

1. Use CSS variables for theme changes (no recompile)
2. Utilize transitions (avoid animations on large elements)
3. Hardware-accelerated transforms (translateY, translateX)
4. Minimal shadow depth (max 4 levels)
5. Defer non-critical animations

## Browser Support

| Browser | Min Version |
|---------|------------|
| Chrome | Latest |
| Firefox | Latest |
| Safari | 13+ |
| Edge | Latest |
| IE 11 | Not supported |

## File Organization

```
public/assets/css/
├── app.css (1100+ lines, all styles)
└── (future) theme variations
```

## Custom Property Override Example

```css
:root {
    --jira-blue: #0052CC;       /* Change primary color */
    --text-primary: #161B22;    /* Change text color */
    --radius-lg: 12px;          /* Increase border radius */
}
```

## Common Issues & Solutions

### Modal appearing behind navbar
✓ **Already fixed**: Z-index: modal 2050, backdrop 2040, navbar 2000

### Button text misaligned
✓ Use `d-flex align-items-center justify-content-center` on buttons

### Dropdown cut off
✓ Use `dropdown-menu-end` for right-aligned dropdowns

### Mobile sidebar overlapping
✓ Already handled with `position: fixed` and `z-index: 1050`

---

**Last Updated**: December 2025  
**Version**: 1.0.0
