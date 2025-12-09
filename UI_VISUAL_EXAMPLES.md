# UI Visual Examples & Patterns

Real-world examples of how components look with the new design system.

## Page Layouts

### Dashboard Layout
```html
<!-- Navbar (gradient blue background) -->
<nav class="navbar">
    <a class="navbar-brand">🎯 Jira Clone</a>
    <ul class="navbar-nav">
        <li><a class="nav-link">Projects</a></li>
        <li><a class="nav-link">Issues</a></li>
    </ul>
    <input class="form-control" placeholder="Search...">
    <button class="btn btn-light">Create</button>
</nav>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Stats Cards Row -->
    <div class="row g-4">
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-1">12</h3>
                    <span class="text-muted">Assigned to you</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects & Activity Row -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Your Projects</h5>
                </div>
                <div class="card-body">
                    <!-- List items -->
                </div>
            </div>
        </div>
    </div>
</div>
```

**Visual Result**:
- Clean white navbar with blue gradient background
- White cards with subtle shadows
- Proper spacing between sections
- Dark text on light backgrounds (high contrast)

### Projects Page

```html
<div class="container-fluid">
    <!-- Header with Create Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Projects</h1>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Project
        </button>
    </div>

    <!-- Projects Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <div class="col">
            <div class="card card-interactive">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="#">Project Name</a>
                    </h5>
                    <p class="card-text text-muted">
                        Project description...
                    </p>
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-list-task"></i> 8 issues</span>
                        <span><i class="bi bi-people"></i> 4 members</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

**Visual Result**:
- Large heading with action button on same line
- 3-column grid of project cards
- Cards lift on hover (subtle animation)
- Icons with text labels

### Issue Detail View

```html
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Projects</a></li>
            <li class="breadcrumb-item active">PROJ-123</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="badge" style="background-color: #FF5630;">
                            <i class="bi bi-bug-fill"></i>
                        </span>
                        <h4 class="ms-3 mb-0">PROJ-123</h4>
                        <span class="badge ms-2" style="background-color: #DEEBFF;">
                            In Progress
                        </span>
                    </div>
                    <div class="btn-group">
                        <a href="#" class="btn btn-outline-primary btn-sm">Edit</a>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title mb-3">Bug: Login page not responding</h5>
                    
                    <div class="mb-4">
                        <h6>Description</h6>
                        <p>User reports that the login page hangs...</p>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6>Assignee</h6>
                            <div class="d-flex align-items-center">
                                <img src="avatar.jpg" class="rounded-circle me-2" 
                                     width="32" height="32">
                                <span>John Doe</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Priority</h6>
                            <span class="badge" style="background-color: #FF5630;">
                                Highest
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6>Details</h6>
                </div>
                <div class="card-body">
                    <!-- Issue metadata -->
                </div>
            </div>
        </div>
    </div>
</div>
```

**Visual Result**:
- Bold issue key with badge showing type
- Large title for issue summary
- Structured details section
- Avatar with assignee name
- Color-coded priority badge

### Kanban Board

```html
<div class="board-container">
    <!-- To Do Column -->
    <div class="board-column">
        <div class="board-column-header">
            <span>To Do</span>
            <span class="board-column-count">5</span>
        </div>
        <div class="board-column-content">
            <div class="board-card">
                <div class="board-card-key">PROJ-125</div>
                <div class="board-card-summary">
                    Implement user authentication
                </div>
                <div class="board-card-footer">
                    <img src="avatar.jpg" class="board-card-avatar">
                    <span class="badge badge-warning">High</span>
                </div>
            </div>
            <div class="board-card">
                <div class="board-card-key">PROJ-124</div>
                <div class="board-card-summary">
                    Design new dashboard layout
                </div>
                <div class="board-card-footer">
                    <img src="avatar.jpg" class="board-card-avatar">
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress Column -->
    <div class="board-column">
        <div class="board-column-header">
            <span>In Progress</span>
            <span class="board-column-count">3</span>
        </div>
        <div class="board-column-content">
            <div class="board-card">
                <div class="board-card-key">PROJ-123</div>
                <div class="board-card-summary">
                    Fix login page responsiveness
                </div>
                <div class="board-card-footer">
                    <img src="avatar.jpg" class="board-card-avatar">
                </div>
            </div>
        </div>
    </div>

    <!-- Done Column -->
    <div class="board-column">
        <div class="board-column-header">
            <span>Done</span>
            <span class="board-column-count">12</span>
        </div>
        <div class="board-column-content">
            <!-- Done cards -->
        </div>
    </div>
</div>
```

**Visual Result**:
- 3 columns side-by-side (scrollable on mobile)
- Column headers with count badges
- White cards with blue borders on hover
- Issue key in blue, summary in dark text
- Avatar thumbnails in footer

## Component Variations

### Status Badge Examples

```html
<!-- To Do - Light Gray -->
<span class="status-badge status-todo">To Do</span>

<!-- In Progress - Light Blue -->
<span class="status-badge status-in-progress">In Progress</span>

<!-- In Review - Light Amber -->
<span class="status-badge status-in-review">In Review</span>

<!-- Done - Light Green -->
<span class="status-badge status-done">Done</span>
```

**Visual Result**:
```
[To Do]           - Gray background with dark text
[In Progress]     - Blue background with darker blue text
[In Review]       - Amber background with dark amber text
[Done]            - Green background with dark green text
```

### Issue Type Indicators

```html
<span class="issue-type-badge issue-type-story">📖 Story</span>
<span class="issue-type-badge issue-type-bug">🐛 Bug</span>
<span class="issue-type-badge issue-type-task">✓ Task</span>
<span class="issue-type-badge issue-type-epic">⚡ Epic</span>
<span class="issue-type-badge issue-type-subtask">↳ Subtask</span>
```

**Visual Result**:
```
Story    - Purple background, white text
Bug      - Red background, white text
Task     - Blue background, white text
Epic     - Purple background, white text
Subtask  - Green background, white text
```

### Priority Indicators

```html
<div class="d-flex gap-2 align-items-center">
    <i class="bi bi-arrow-up priority-highest"></i>
    <span>Highest</span>
</div>

<div class="d-flex gap-2 align-items-center">
    <i class="bi bi-arrow-up priority-high"></i>
    <span>High</span>
</div>

<div class="d-flex gap-2 align-items-center">
    <i class="bi bi-minus priority-medium"></i>
    <span>Medium</span>
</div>

<div class="d-flex gap-2 align-items-center">
    <i class="bi bi-arrow-down priority-low"></i>
    <span>Low</span>
</div>

<div class="d-flex gap-2 align-items-center">
    <i class="bi bi-arrow-down priority-lowest"></i>
    <span>Lowest</span>
</div>
```

**Visual Result**:
```
↑ Highest - Red text
↑ High    - Orange text
— Medium  - Amber text
↓ Low     - Blue text
↓ Lowest  - Gray text
```

### Form Layouts

#### Login Form
```html
<div class="card" style="max-width: 400px; margin: auto; margin-top: 10rem;">
    <div class="card-header">
        <h5>Sign In</h5>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-control form-control-lg" 
                   placeholder="you@example.com">
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" class="form-control form-control-lg">
        </div>
        <button class="btn btn-primary w-100">Sign In</button>
    </div>
</div>
```

**Visual Result**:
- Centered card (400px max width)
- Large form controls
- Blue submit button
- 10rem top margin

#### Create Issue Form (Modal)
```html
<div class="modal fade" id="createModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Project</label>
                    <select class="form-select form-select-lg">
                        <option>Select project...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Issue Type</label>
                    <select class="form-select form-select-lg">
                        <option>Select type...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <input type="text" class="form-control form-control-lg" 
                           placeholder="Brief description">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</div>
```

**Visual Result**:
- Centered modal with rounded corners
- Large form controls
- Clear header with close button
- Action buttons in footer

### Table Examples

#### Issues Table
```html
<div class="table-issues">
    <table class="table">
        <thead>
            <tr>
                <th>Key</th>
                <th>Summary</th>
                <th>Status</th>
                <th>Assignee</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
            <tr class="clickable">
                <td><span class="issue-key">PROJ-125</span></td>
                <td>Implement authentication</td>
                <td>
                    <span class="status-badge status-in-progress">
                        In Progress
                    </span>
                </td>
                <td>
                    <img src="avatar.jpg" class="avatar avatar-sm me-2">
                    John Doe
                </td>
                <td>
                    <span class="badge badge-warning">High</span>
                </td>
            </tr>
            <tr class="clickable">
                <td><span class="issue-key">PROJ-124</span></td>
                <td>Design dashboard</td>
                <td>
                    <span class="status-badge status-todo">To Do</span>
                </td>
                <td>
                    <img src="avatar.jpg" class="avatar avatar-sm me-2">
                    Jane Smith
                </td>
                <td>
                    <span class="badge badge-primary">Medium</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

**Visual Result**:
- Clean table with rounded corners
- Uppercase gray headers
- Status badges with colors
- Clickable rows highlight on hover
- Avatars next to names

## Responsive Examples

### Mobile (< 576px)

```html
<!-- Single column layout -->
<div class="container">
    <h1 style="font-size: 1.5rem;">Issues</h1>
    
    <div class="d-flex flex-column gap-2">
        <a href="#" class="card card-interactive">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="issue-key">PROJ-1</span>
                    <span class="status-badge status-done">Done</span>
                </div>
                <p class="mb-0">Issue summary here</p>
            </div>
        </a>
    </div>
</div>

<!-- Bottom sheet modal -->
<div class="modal-dialog" style="max-width: 100%; margin: 0;">
    <div class="modal-content" style="border-radius: 12px 12px 0 0; max-height: 90vh;">
        <!-- Modal content -->
    </div>
</div>
```

**Visual Result**:
- Full-width cards
- Bottom sheet modals (rounded top corners)
- Single column layout
- Compact spacing

### Tablet (576px - 768px)

```html
<!-- Two column layout -->
<div class="row g-3">
    <div class="col-6">
        <div class="card"><!-- Content --></div>
    </div>
    <div class="col-6">
        <div class="card"><!-- Content --></div>
    </div>
</div>

<!-- Collapsible sidebar -->
<div class="project-sidebar">
    <!-- Hidden by default, toggle with hamburger -->
</div>
```

**Visual Result**:
- 2-column card layout
- Sidebar hidden, available via toggle
- Adjusted padding: 1.5rem

### Desktop (> 768px)

```html
<!-- Full sidebar visible -->
<div class="d-flex">
    <aside class="project-sidebar">
        <!-- Navigation -->
    </aside>
    <main class="flex-grow-1">
        <!-- Content -->
    </main>
</div>

<!-- Multi-column grids -->
<div class="row row-cols-4 g-4">
    <div class="col"><div class="card"></div></div>
    <div class="col"><div class="card"></div></div>
    <div class="col"><div class="card"></div></div>
    <div class="col"><div class="card"></div></div>
</div>
```

**Visual Result**:
- Sidebar always visible (256px)
- Multi-column layouts
- Full-width containers
- Generous spacing

## Color Palette Showcase

### Brand Colors
```
Primary Blue:       #0052CC  ●
Dark Blue:          #003DA5  ●
Light Blue:         #2684FF  ●
Very Light Blue:    #DEEBFF  ●
```

### Functional Colors
```
Success (Green):    #36B37E  ●
Warning (Amber):    #FFAB00  ●
Error (Red):        #FF5630  ●
Info (Teal):        #00B8D9  ●
```

### Text Colors
```
Primary Dark:       #161B22  ●
Secondary:          #57606A  ●
Tertiary:           #738496  ●
Muted Light:        #97A0AF  ●
```

### Background Colors
```
White Primary:      #FFFFFF  ●
Light Secondary:    #F7F8FA  ●
Medium Tertiary:    #ECEDF0  ●
Border Light:       #DFE1E6  ●
```

## Interactive Elements

### Hover Effects

```
Button:         Color changes + lift effect (translateY -1px)
Card:           Shadow increases, no transform
Link:           Color changes to darker shade
Sidebar Item:   Background changes to light gray
Board Card:     Shadow increases + border color changes
```

### Focus States

```
Interactive Elements:  2px solid #0052CC outline
Outline Offset:        2px (provides space)
Keyboard Navigation:   Fully supported
```

### Transitions

```
Standard Transition:    200ms cubic-bezier(0.4, 0, 0.2, 1)
Fast Transition:        150ms (quick feedback)
Slow Transition:        300ms (animations)
```

## Animation Examples

### Fade In
```css
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
```

### Slide Down
```css
@keyframes slideDown {
    from { 
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

### Hover Lift
```css
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
```

---

**These visual examples demonstrate the complete redesign across all major UI components and pages.**

**Status**: Ready for implementation  
**Last Updated**: December 2025
