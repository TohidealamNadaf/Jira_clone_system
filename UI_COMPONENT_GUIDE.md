# UI Component Guide - Modern Jira Clone

Quick reference for using the redesigned UI components.

## Color Classes

### Text Colors
```html
<!-- Primary text -->
<p class="text-primary"></p>

<!-- Secondary text -->
<p class="text-secondary"></p>

<!-- Muted text -->
<p class="text-muted"></p>
```

### Background Colors
```html
<!-- Light background -->
<div class="bg-light"></div>

<!-- White background (primary) -->
<div class="bg-white"></div>

<!-- Info background -->
<div class="alert alert-info"></div>
```

### Status Badges
```html
<!-- To Do -->
<span class="status-badge status-todo">To Do</span>

<!-- In Progress -->
<span class="status-badge status-in-progress">In Progress</span>

<!-- Done -->
<span class="status-badge status-done">Done</span>
```

## Cards

### Basic Card
```html
<div class="card">
    <div class="card-header">
        <h5>Card Title</h5>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

### Interactive Card
```html
<div class="card card-interactive">
    <!-- Content -->
</div>
```

### Card with Footer
```html
<div class="card">
    <div class="card-header">Title</div>
    <div class="card-body">Content</div>
    <div class="card-footer">Footer</div>
</div>
```

## Buttons

### Primary Button
```html
<button class="btn btn-primary">
    <i class="bi bi-plus-lg"></i> Action
</button>
```

### Secondary Button
```html
<button class="btn btn-secondary">Cancel</button>
```

### Outline Button
```html
<button class="btn btn-outline-primary">Edit</button>
```

### Button Sizes
```html
<button class="btn btn-sm btn-primary">Small</button>
<button class="btn btn-primary">Regular</button>
<button class="btn btn-lg btn-primary">Large</button>
```

### Disabled Button
```html
<button class="btn btn-primary" disabled>Disabled</button>
```

## Forms

### Form Group
```html
<div class="form-group">
    <label class="form-label">Field Label</label>
    <input type="text" class="form-control" placeholder="Enter value">
    <small class="form-text">Help text</small>
</div>
```

### Large Form Controls
```html
<input type="text" class="form-control form-control-lg">
<select class="form-select form-select-lg"></select>
```

## Issue Components

### Issue Type Badge
```html
<span class="issue-type-badge issue-type-story">Story</span>
<span class="issue-type-badge issue-type-bug">Bug</span>
<span class="issue-type-badge issue-type-task">Task</span>
```

### Issue Key
```html
<span class="issue-key">PROJ-123</span>
```

### Priority Indicator
```html
<i class="bi bi-arrow-up priority-highest"></i>
<i class="bi bi-arrow-up priority-high"></i>
<i class="bi bi-minus priority-medium"></i>
<i class="bi bi-arrow-down priority-low"></i>
<i class="bi bi-arrow-down priority-lowest"></i>
```

## Badges & Labels

### Basic Badge
```html
<span class="badge">Default</span>
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-warning">Warning</span>
<span class="badge badge-danger">Danger</span>
```

### Badge with Icon
```html
<span class="badge badge-primary">
    <i class="bi bi-check-circle"></i> Approved
</span>
```

## Avatars

### Standard Avatar
```html
<img src="user.jpg" class="avatar" alt="User">
```

### Avatar Sizes
```html
<img src="user.jpg" class="avatar avatar-sm">
<img src="user.jpg" class="avatar avatar-lg">
<img src="user.jpg" class="avatar avatar-xl">
```

### Avatar Placeholder
```html
<div class="avatar avatar-sm avatar-placeholder">AB</div>
```

## Alerts

### Alert Types
```html
<div class="alert alert-info">Info message</div>
<div class="alert alert-success">Success message</div>
<div class="alert alert-warning">Warning message</div>
<div class="alert alert-danger">Error message</div>
```

### Dismissible Alert
```html
<div class="alert alert-info alert-dismissible fade show">
    Message
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

## Tables

### Issue Table
```html
<div class="table-issues">
    <table class="table">
        <thead>
            <tr>
                <th>Key</th>
                <th>Summary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="issue-key">PROJ-1</span></td>
                <td>Issue summary</td>
                <td><span class="status-badge status-todo">To Do</span></td>
            </tr>
        </tbody>
    </table>
</div>
```

### Clickable Row Table
```html
<tbody>
    <tr class="clickable">
        <td>Data</td>
    </tr>
</tbody>
```

## Board Components

### Board Column
```html
<div class="board-column">
    <div class="board-column-header">
        <span>To Do</span>
        <span class="board-column-count">5</span>
    </div>
    <div class="board-column-content">
        <div class="board-card">
            <div class="board-card-key">PROJ-1</div>
            <div class="board-card-summary">Card summary</div>
            <div class="board-card-footer">
                <img src="avatar.jpg" class="board-card-avatar">
            </div>
        </div>
    </div>
</div>
```

## List Groups

### Basic List
```html
<div class="list-group">
    <a href="#" class="list-group-item list-group-item-action">Item 1</a>
    <a href="#" class="list-group-item list-group-item-action">Item 2</a>
    <a href="#" class="list-group-item list-group-item-action active">Item 3</a>
</div>
```

## Typography

### Headings
```html
<h1>Page Title</h1>
<h2>Section Title</h2>
<h3>Subsection</h3>
<h4>Minor Heading</h4>
<h5>Secondary Title</h5>
<h6>UPPERCASE LABEL</h6>
```

### Text Classes
```html
<p class="lead">Large introductory text</p>
<p class="text-muted">Muted text</p>
<small>Small text</small>
<span class="text-truncate-1">Truncate to 1 line</span>
<span class="text-truncate-2">Truncate to 2 lines</span>
```

## Utility Classes

### Display & Layout
```html
<!-- Flexbox utilities (Bootstrap native) -->
<div class="d-flex justify-content-between align-items-center">
    <span>Left</span>
    <span>Right</span>
</div>
```

### Spacing
```html
<!-- Gap utilities -->
<div class="d-flex gap-1">Items</div>
<div class="d-flex gap-2">Items</div>
<div class="d-flex gap-3">Items</div>
```

### Opacity
```html
<div class="opacity-75">75% opacity</div>
<div class="opacity-50">50% opacity</div>
```

### Text Truncation
```html
<span class="text-truncate-1">Single line truncate</span>
<span class="text-truncate-2">Two line truncate</span>
<span class="text-truncate-3">Three line truncate</span>
```

### Hover Effects
```html
<div class="hover-lift">Lifts on hover</div>
```

## Modals

### Basic Modal
```html
<div class="modal fade" id="exampleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
```

### Centered Modal
```html
<div class="modal-dialog modal-dialog-centered">
    <!-- Content -->
</div>
```

## Dropdowns

### Dropdown Menu
```html
<div class="dropdown">
    <button class="btn dropdown-toggle" data-bs-toggle="dropdown">
        Actions
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Edit</a></li>
        <li><a class="dropdown-item" href="#">Delete</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item active" href="#">Active Item</a></li>
    </ul>
</div>
```

## Spinner & Loading

### Spinner
```html
<div class="spinner-border" role="status">
    <span class="visually-hidden">Loading...</span>
</div>
```

### Growing Spinner
```html
<div class="spinner-grow" role="status">
    <span class="visually-hidden">Loading...</span>
</div>
```

## Breadcrumbs

```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Projects</a></li>
        <li class="breadcrumb-item active">Current Page</li>
    </ol>
</nav>
```

## Responsive Layout

### Container Classes
```html
<!-- Full-width with padding -->
<div class="container-fluid"></div>

<!-- Max-width container -->
<div class="container"></div>
```

### Grid System
```html
<div class="row g-4">
    <div class="col-lg-6 col-md-12"><!-- Full on mobile, half on desktop --></div>
    <div class="col-lg-6 col-md-12"><!-- Full on mobile, half on desktop --></div>
</div>
```

## CSS Custom Properties (Variables)

Access via `:root` selector or anywhere:

```css
:root {
    /* Brand Colors */
    --jira-blue: #0052CC;
    --jira-blue-dark: #003DA5;
    
    /* Text Colors */
    --text-primary: #161B22;
    --text-secondary: #57606A;
    --text-muted: #97A0AF;
    
    /* Backgrounds */
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    
    /* Sizes */
    --radius-md: 6px;
    --radius-lg: 8px;
    
    /* Effects */
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
    --transition-base: 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
```

## Best Practices

1. **Use CSS Variables** for consistency
2. **Maintain Spacing** with Bootstrap grid and utility classes
3. **Follow Color Palette** for brand consistency
4. **Apply Shadows** for depth hierarchy
5. **Use Transitions** for smooth interactions
6. **Ensure Accessibility** with proper ARIA labels
7. **Responsive First** design mobile, then enhance for desktop
8. **Keep Typography** hierarchy clear with proper heading levels

## Theme Colors Quick Reference

```
Primary Action: --jira-blue (#0052CC)
Success: --color-success (#36B37E)
Warning: --color-warning (#FFAB00)
Error: --color-error (#FF5630)
Info: --color-info (#00B8D9)
```

## Common Patterns

### Action Bar
```html
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Title</h2>
    <button class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Action
    </button>
</div>
```

### Empty State
```html
<div class="text-center py-5 text-muted">
    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
    <p>No items found</p>
    <a href="#" class="btn btn-sm btn-outline-primary">Create One</a>
</div>
```

### Stats Cards
```html
<div class="row g-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-1">123</h3>
                <span class="text-muted">Metric Name</span>
            </div>
        </div>
    </div>
</div>
```

---

**Component Library Version**: 1.0.0  
**Last Updated**: December 2025
