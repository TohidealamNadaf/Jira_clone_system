# Jira Clone - Enterprise Design System & Application Guide

**Author**: Amp AI Assistant  
**Date**: December 9, 2025  
**Status**: Complete & Ready for Production  
**Purpose**: Apply consistent Jira-like design across all pages

---

## Table of Contents

1. [Design Principles](#design-principles)
2. [Color System](#color-system)
3. [Typography System](#typography-system)
4. [Spacing & Layout](#spacing--layout)
5. [Component Patterns](#component-patterns)
6. [Responsive Design](#responsive-design)
7. [Animation & Interactions](#animation--interactions)
8. [Page Structure Template](#page-structure-template)
9. [Implementation Checklist](#implementation-checklist)
10. [Code Examples](#code-examples)

---

## Design Principles

### 1. Enterprise-Grade Quality
- Professional appearance matching real Jira
- Clear visual hierarchy
- Proper information grouping
- Accessible and semantic HTML

### 2. Consistency
- Same color palette across all pages
- Unified component styling
- Consistent spacing rhythm
- Standard font sizes and weights

### 3. Usability
- Clear navigation patterns
- Intuitive interactions
- Proper visual feedback
- Mobile-responsive design

### 4. Performance
- CSS-based styling (no frameworks)
- No unnecessary JavaScript
- Self-contained page styles
- Lightweight design

### 5. Accessibility
- WCAG AA compliant contrast
- Semantic HTML structure
- Keyboard navigation support
- Screen reader friendly

---

## Color System

### CSS Variables (Use These!)

```css
:root {
    /* Brand Colors */
    --jira-blue: #0052CC;
    --jira-blue-dark: #003DA5;
    --jira-blue-light: #2684FF;
    --jira-blue-lighter: #DEEBFF;
    
    /* Text Colors */
    --text-primary: #161B22;
    --text-secondary: #57606A;
    --text-tertiary: #738496;
    --text-muted: #97A0AF;
    --text-white: #FFFFFF;
    
    /* Background Colors */
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --bg-tertiary: #ECEDF0;
    
    /* Borders & Dividers */
    --border-color: #DFE1E6;
    --border-light: #EBECF0;
    --divider-color: #F1F2F4;
    
    /* Functional Colors */
    --color-success: #36B37E;
    --color-warning: #FFAB00;
    --color-error: #FF5630;
    --color-info: #00B8D9;
}
```

### Color Usage Rules

| Element | Color | Hex |
|---------|-------|-----|
| Primary Actions | Jira Blue | #0052CC |
| Hover Actions | Dark Blue | #003DA5 |
| Text Primary | Dark | #161B22 |
| Text Secondary | Gray | #626F86 |
| Background Main | White | #FFFFFF |
| Background Secondary | Light Gray | #F7F8FA |
| Borders | Border Gray | #DFE1E6 |
| Success | Green | #36B37E |
| Warning | Orange | #FFAB00 |
| Error | Red | #FF5630 |

### Never Use
❌ Bootstrap classes (btn-primary, bg-success, etc)  
❌ Tailwind utilities  
❌ Hardcoded colors without variables  
❌ Inconsistent color shades  

---

## Typography System

### Font Family
```css
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
```

### Type Scale

| Element | Size | Weight | Letter-spacing | Usage |
|---------|------|--------|-----------------|-------|
| Page Title | 32px | 700 | -0.3px | Main page headings |
| Section Title | 24px | 700 | -0.3px | Major sections |
| Card Title | 15px | 700 | -0.2px | Card headers |
| Body | 14-15px | 400-500 | -0.2px | Main content |
| Label | 12-13px | 600-700 | 0.3-0.5px | Form labels, badges |
| Small | 11-12px | 500-600 | 0px | Secondary info |

### Line Heights
- Headers: 1.2
- Body: 1.5-1.6
- Compact: 1.4

### Font Weight Rules
```
Regular:  400 (body text)
Medium:   500 (slightly emphasized)
Semibold: 600 (label, secondary headings)
Bold:     700 (headings, badges)
```

### Examples

```css
/* Page Title */
.page-title {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -0.3px;
    color: var(--text-primary);
}

/* Card Title */
.card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
}

/* Body Text */
.body-text {
    font-size: 14px;
    font-weight: 400;
    color: var(--text-primary);
    line-height: 1.6;
}

/* Label */
.label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
```

---

## Spacing & Layout

### Spacing Scale

```css
4px  → micro spacing (tight)
8px  → extra small
12px → small
16px → medium
20px → large
24px → extra large (section gaps)
32px → page padding
```

### Padding Rules

| Component | Padding |
|-----------|---------|
| Page container | 32px (desktop), 20px (tablet), 16px (mobile) |
| Card | 20px (standard), 24px (spacious) |
| List item | 16px vertical, 20px horizontal |
| Button | 10px vertical, 16px horizontal |
| Badge | 4px vertical, 8-12px horizontal |

### Margin Rules

| Element | Margin |
|---------|--------|
| Section spacing | 24px (gap between major sections) |
| Card spacing | 16px (gap between cards) |
| Element spacing | 8-12px (spacing within components) |

### Gap Rules (Flexbox)

```css
.container { gap: 24px; }           /* Section gaps */
.card { gap: 16px; }                /* Card element gaps */
.row { gap: 8-12px; }               /* Row element gaps */
```

### Example Layout

```css
/* Page wrapper */
.page-wrapper {
    padding: 32px;
    background: var(--bg-secondary);
}

/* Section */
.section {
    margin-bottom: 24px;
}

/* Cards in section */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

/* Card */
.card {
    background: var(--bg-primary);
    padding: 20px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

/* Card content */
.card-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
```

---

## Component Patterns

### 1. Breadcrumb Navigation

```html
<div class="breadcrumb">
    <a href="..." class="breadcrumb-link">
        <i class="bi bi-house-door"></i> Home
    </a>
    <span class="breadcrumb-separator">/</span>
    <a href="..." class="breadcrumb-link">Section</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Current</span>
</div>
```

```css
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    font-size: 13px;
    flex-shrink: 0;
}

.breadcrumb-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.breadcrumb-link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}

.breadcrumb-separator {
    color: var(--text-secondary);
}

.breadcrumb-current {
    color: var(--text-primary);
    font-weight: 600;
}
```

### 2. Card Components

```html
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Title</h2>
        <a href="..." class="card-action">View All</a>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

```css
.card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
}

.card-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
}

.card-action {
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.2s ease;
}

.card-action:hover {
    text-decoration: underline;
}

.card-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}
```

### 3. List Items

```html
<a href="..." class="list-item">
    <span class="item-key">BP-123</span>
    <span class="item-title">Issue title</span>
    <span class="item-badge">Done</span>
</a>
```

```css
.list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.15s ease;
    gap: 12px;
}

.list-item:hover {
    background: var(--bg-secondary);
}

.item-key {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    white-space: nowrap;
}

.item-title {
    font-size: 14px;
    font-weight: 500;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.item-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    background: var(--bg-secondary);
    border-radius: 12px;
    white-space: nowrap;
    flex-shrink: 0;
}
```

### 4. Buttons

```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-small">Small</button>
```

```css
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-primary {
    background: var(--jira-blue);
    color: white;
}

.btn-primary:hover {
    background: var(--jira-blue-dark);
    box-shadow: 0 4px 12px rgba(0, 82, 204, 0.3);
}

.btn-secondary {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.btn-secondary:hover {
    background: var(--bg-secondary);
    border-color: #B6C2CF;
}

.btn-small {
    padding: 6px 12px;
    font-size: 13px;
}
```

### 5. Badges & Labels

```html
<span class="badge">Label</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-error">Error</span>
```

```css
.badge {
    display: inline-block;
    padding: 4px 12px;
    background: var(--bg-secondary);
    color: var(--text-primary);
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
}

.badge-success {
    background: #E3F2FD;
    color: var(--jira-blue);
}

.badge-error {
    background: #FFECEB;
    color: #FF5630;
}
```

### 6. Form Controls

```html
<input type="text" class="form-input" placeholder="Search...">
<textarea class="form-textarea"></textarea>
<select class="form-select">...</select>
```

```css
.form-input,
.form-textarea,
.form-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s ease;
    background: var(--bg-primary);
    color: var(--text-primary);
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 4px rgba(0, 82, 204, 0.15);
}

.form-input::placeholder {
    color: var(--text-tertiary);
}
```

---

## Responsive Design

### Breakpoints

```css
/* Mobile-first approach */

/* Mobile: < 576px */
/* Default styles for mobile */

/* Tablet: 576px - 1024px */
@media (min-width: 576px) {
    /* Tablet-specific styles */
}

/* Laptop: 1024px - 1400px */
@media (min-width: 1024px) {
    /* Laptop-specific styles */
}

/* Desktop: > 1400px */
@media (min-width: 1400px) {
    /* Desktop-specific styles */
}
```

### Responsive Layout Example

```css
/* Mobile first */
.main-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Tablet */
@media (min-width: 1024px) {
    .main-wrapper {
        flex-direction: row;
    }
}

.main-content {
    flex: 1;
    min-width: 0;
}

.sidebar {
    flex: 0 0 100%;
    /* On mobile, sidebar is full width */
}

@media (min-width: 1024px) {
    .sidebar {
        flex: 0 0 320px;
        /* On desktop, sidebar is fixed width */
    }
}
```

### Padding Responsiveness

```css
/* Mobile */
.container {
    padding: 16px;
}

/* Tablet */
@media (min-width: 768px) {
    .container {
        padding: 20px;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .container {
        padding: 32px;
    }
}
```

---

## Animation & Interactions

### Transitions

```css
/* Standard transitions */
--transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-base: 200ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);

/* Apply to interactive elements */
transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
```

### Hover Effects

**Lift Effect:**
```css
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}
```

**Highlight Effect:**
```css
.item:hover {
    background: var(--bg-secondary);
}
```

**Scale Effect:**
```css
.badge:hover {
    transform: scale(1.05);
}
```

**Color Change:**
```css
.link:hover {
    color: var(--jira-blue-dark);
    text-decoration: underline;
}
```

### Shadows

```css
/* Subtle */
box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);

/* Elevated */
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);

/* Strong */
box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);

/* Brand color shadow */
box-shadow: 0 4px 12px rgba(0, 82, 204, 0.3);
```

---

## Page Structure Template

### Standard Page Layout

```html
<?php \App\Core\View::extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <!-- breadcrumb items -->
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <!-- title, actions, etc -->
    </div>

    <!-- Quick Actions (optional) -->
    <div class="quick-actions">
        <!-- action buttons -->
    </div>

    <!-- Main Content -->
    <div class="page-content">
        <!-- Left Column -->
        <div class="content-main">
            <!-- cards, lists, etc -->
        </div>

        <!-- Right Sidebar (optional) -->
        <div class="content-sidebar">
            <!-- sidebar cards -->
        </div>
    </div>
</div>

<style>
/* Page styles here */
</style>

<?php \App\Core\View::endSection(); ?>
```

### CSS Structure

```css
/* 1. Root Variables */
:root {
    --jira-blue: #0052CC;
    /* ... other variables */
}

/* 2. Main Wrapper */
.page-wrapper {
    /* ... */
}

/* 3. Sections (breadcrumb, header, content) */
.breadcrumb { /* ... */ }
.page-header { /* ... */ }
.page-content { /* ... */ }

/* 4. Components (cards, buttons, etc) */
.card { /* ... */ }
.btn { /* ... */ }
.badge { /* ... */ }

/* 5. Responsive (media queries) */
@media (max-width: 1024px) { /* ... */ }
@media (max-width: 768px) { /* ... */ }
```

---

## Implementation Checklist

### For Each Page:

- [ ] **Structure**
  - [ ] Add breadcrumb navigation
  - [ ] Create page header with title
  - [ ] Layout main content (left) + sidebar (right)
  - [ ] Use flexbox for layouts

- [ ] **Styling**
  - [ ] Use CSS variables for colors
  - [ ] Apply consistent spacing (24px gaps)
  - [ ] Use proper typography scale
  - [ ] Add hover effects with transitions
  - [ ] Add shadows for depth

- [ ] **Components**
  - [ ] Replace Bootstrap cards with custom styling
  - [ ] Use custom buttons (not Bootstrap)
  - [ ] Style badges and labels
  - [ ] Style form controls
  - [ ] Style list items

- [ ] **Responsive**
  - [ ] Mobile-first approach
  - [ ] Test at 3 breakpoints
  - [ ] Adjust padding/gaps for mobile
  - [ ] Single column on mobile
  - [ ] Multi-column on desktop

- [ ] **Interactions**
  - [ ] Add hover effects
  - [ ] Add smooth transitions
  - [ ] Add focus states
  - [ ] Test keyboard navigation
  - [ ] Check animations

- [ ] **Accessibility**
  - [ ] Semantic HTML structure
  - [ ] Proper heading hierarchy
  - [ ] Color contrast (WCAG AA)
  - [ ] Link underlines
  - [ ] Alt text on images

- [ ] **Testing**
  - [ ] Test all browsers
  - [ ] Test all screen sizes
  - [ ] Check console for errors
  - [ ] Verify all links work
  - [ ] Check data display

- [ ] **Documentation**
  - [ ] Create completion doc
  - [ ] Document any changes
  - [ ] Update AGENTS.md if needed
  - [ ] Note any special cases

---

## Code Examples

### Complete Component Example

```html
<!-- Issue Card -->
<div class="issue-card">
    <div class="card-header">
        <h3 class="card-title">BP-123</h3>
        <span class="priority-badge">High</span>
    </div>
    <div class="card-body">
        <p class="issue-summary">This is the issue summary text</p>
        <div class="issue-meta">
            <span class="status">In Progress</span>
            <span class="assignee">John Doe</span>
        </div>
    </div>
</div>
```

```css
.issue-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: all 0.2s ease;
}

.issue-card:hover {
    border-color: #B6C2CF;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
}

.priority-badge {
    padding: 4px 12px;
    background: #FFECEB;
    color: #FF5630;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.issue-summary {
    margin: 0;
    font-size: 14px;
    color: var(--text-primary);
}

.issue-meta {
    display: flex;
    gap: 12px;
    font-size: 13px;
    color: var(--text-secondary);
}

.status,
.assignee {
    padding: 4px 8px;
    background: var(--bg-secondary);
    border-radius: 4px;
}
```

---

## Pages to Redesign (Next Thread)

List of pages that should be redesigned with this system:

1. **Issues List** (`views/issues/index.php`)
   - Filter sidebar
   - Issue table/grid
   - Pagination
   - Bulk actions

2. **Issue Detail** (`views/issues/show.php`)
   - Issue header
   - Description panel
   - Comments section
   - Activity feed
   - Sidebar (assignee, priority, etc)

3. **Backlog** (`views/projects/backlog.php`)
   - Backlog items list
   - Sprint planning
   - Drag and drop
   - Issue cards

4. **Sprints** (`views/projects/sprints.php`)
   - Sprint list
   - Sprint details
   - Sprint settings
   - Progress bars

5. **Reports** (`views/reports/*.php`)
   - Report header
   - Filters
   - Charts
   - Metrics cards

6. **Admin Pages** (`views/admin/*.php`)
   - User management
   - Role management
   - Project settings
   - System settings

7. **Settings** (`views/projects/settings.php`)
   - Project settings form
   - Team management
   - Integrations
   - Advanced options

8. **Activity** (`views/projects/activity.php`)
   - Activity timeline
   - Filters
   - Search
   - Pagination

---

## Quick Reference Card

### Colors
```
Primary: #0052CC (blue)
Dark: #161B22 (text)
Gray: #626F86 (secondary)
Light: #F7F8FA (background)
Border: #DFE1E6 (dividers)
```

### Spacing
```
4px, 8px, 12px, 16px, 20px, 24px, 32px
Use multiples of 4px
```

### Typography
```
Title: 32px 700 weight
Section: 24px 700 weight
Card: 15px 700 weight
Body: 14px 400 weight
Label: 12px 600 weight
```

### Shadows
```
Subtle: 0 1px 3px rgba(0,0,0,0.08)
Elevated: 0 4px 12px rgba(0,0,0,0.08)
Strong: 0 8px 24px rgba(0,0,0,0.12)
```

### Transitions
```
Fast: 150ms
Base: 200ms
Slow: 300ms
Cubic: cubic-bezier(0.4, 0, 0.2, 1)
```

### Breakpoints
```
Mobile: < 576px
Tablet: 576px - 1024px
Laptop: 1024px - 1400px
Desktop: > 1400px
```

---

## Deployment Checklist

Before deploying redesigned pages:

- [ ] All pages follow this design system
- [ ] Colors use CSS variables
- [ ] Typography follows scale
- [ ] Spacing is consistent (multiples of 4)
- [ ] Components match patterns
- [ ] Responsive design tested
- [ ] Hover/animations working
- [ ] Accessibility verified
- [ ] No Bootstrap classes used
- [ ] No hardcoded colors
- [ ] All links working
- [ ] No console errors
- [ ] Documented in AGENTS.md

---

## Support & Reference

### Files to Reference
- `views/projects/board.php` - Kanban board (complete redesign example)
- `views/projects/show.php` - Project overview (complete redesign example)
- `BOARD_CARD_UPGRADE_COMPLETE.md` - Board card details
- `PROJECT_OVERVIEW_REDESIGN_COMPLETE.md` - Project page details
- `AGENTS.md` - Development standards

### Key Principles
1. **Consistency** - Use same design everywhere
2. **Simplicity** - Clean, minimal design
3. **Hierarchy** - Clear visual structure
4. **Feedback** - Interactive feedback on hover
5. **Accessibility** - Works for everyone
6. **Performance** - Fast, lightweight
7. **Maintenance** - Easy to update

---

## Questions & Answers

**Q: Can I use Bootstrap?**  
A: No. Use custom CSS following this system instead. Bootstrap contradicts our design goals.

**Q: What about dark mode?**  
A: CSS variables are ready for it. Can be added later as a theme toggle.

**Q: How do I handle custom colors?**  
A: Use CSS variables. Define them in :root and reference throughout.

**Q: What about animations?**  
A: Use transitions for smooth effects. Keep it subtle (0.2s) and professional.

**Q: Mobile-first or desktop-first?**  
A: Mobile-first. Start with mobile styles, then add desktop in @media queries.

**Q: How do I know if my design is correct?**  
A: Follow the checklist. Check against board.php and show.php examples.

---

## Summary

This design system provides:

✅ **Consistency** - Same look across all pages  
✅ **Professional** - Enterprise-grade Jira appearance  
✅ **Maintainable** - Easy to update and extend  
✅ **Accessible** - WCAG AA compliant  
✅ **Responsive** - Works on all devices  
✅ **Performant** - Lightweight CSS, no frameworks  
✅ **Flexible** - Reusable components and patterns  

**Use this guide for all future page redesigns to maintain consistency and quality.**

---

**End of Design System Guide**  
Ready for production deployment and team reference.
