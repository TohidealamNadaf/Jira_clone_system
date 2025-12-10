# Design System - Quick Reference Card

**One-page reference for applying Jira design to all pages**

---

## Colors (Use These Variables!)

```css
--jira-blue: #0052CC          (primary/links)
--jira-blue-dark: #003DA5     (hover state)
--text-primary: #161B22       (main text)
--text-secondary: #626F86     (secondary text)
--bg-primary: #FFFFFF         (cards/white)
--bg-secondary: #F7F8FA       (page background)
--border-color: #DFE1E6       (borders)
```

---

## Typography

| Purpose | Size | Weight | Usage |
|---------|------|--------|-------|
| Page Title | 32px | 700 | Main headings |
| Section | 24px | 700 | Major sections |
| Card Title | 15px | 700 | Card headers |
| Body | 14px | 400 | Main content |
| Label | 12px | 600 | Badges, labels |

```css
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
letter-spacing: -0.2px;
line-height: 1.5;
```

---

## Spacing (Use Multiples of 4)

```
4px   → micro
8px   → extra small
12px  → small
16px  → medium
20px  → large
24px  → extra large (section gaps)
32px  → page padding
```

**Key Rule**: `gap: 24px;` between major sections

---

## Components

### Card
```css
background: var(--bg-primary);
border: 1px solid var(--border-color);
border-radius: 8px;
padding: 20px;
box-shadow: 0 1px 3px rgba(0,0,0,0.08);
```

### Button
```css
padding: 10px 20px;
border-radius: 6px;
font-weight: 600;
transition: all 0.2s ease;
```

### Badge
```css
padding: 4px 12px;
border-radius: 12px;
font-size: 11px;
font-weight: 600;
text-transform: uppercase;
```

### List Item
```css
padding: 16px 20px;
border-bottom: 1px solid var(--border-color);
transition: all 0.15s ease;
```
On hover: `background: var(--bg-secondary);`

---

## Responsive Breakpoints

```css
Mobile:   < 576px
Tablet:   576px - 1024px
Laptop:   1024px - 1400px
Desktop:  > 1400px
```

**Mobile-first**: Default styles = mobile, then use @media for larger screens

---

## Hover Effects

**Lift Effect** (cards, components):
```css
transform: translateY(-2px);
box-shadow: 0 4px 12px rgba(0,0,0,0.08);
```

**Highlight** (lists):
```css
background: var(--bg-secondary);
```

**Color Change** (links):
```css
color: var(--jira-blue-dark);
text-decoration: underline;
```

---

## Shadows

```css
Subtle:    0 1px 3px rgba(0,0,0,0.08);
Elevated:  0 4px 12px rgba(0,0,0,0.08);
Strong:    0 8px 24px rgba(0,0,0,0.12);
```

---

## Transitions

```css
Fast:  150ms cubic-bezier(0.4, 0, 0.2, 1)
Base:  200ms cubic-bezier(0.4, 0, 0.2, 1)
Slow:  300ms cubic-bezier(0.4, 0, 0.2, 1)

/* Use 0.2s for most interactions */
transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
```

---

## Page Structure Template

```html
<?php \App\Core\View::extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- 1. Breadcrumb -->
    <div class="breadcrumb">
        <a href="..." class="breadcrumb-link">Home</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Current</span>
    </div>

    <!-- 2. Page Header -->
    <div class="page-header">
        <h1 class="page-title">Page Title</h1>
        <div class="header-actions"><!-- buttons --></div>
    </div>

    <!-- 3. Main Content -->
    <div class="page-content">
        <div class="content-main">
            <!-- cards, lists -->
        </div>
        <div class="content-sidebar">
            <!-- sidebar cards -->
        </div>
    </div>
</div>

<style>
/* Embedded CSS */
</style>

<?php \App\Core\View::endSection(); ?>
```

---

## CSS Template

```css
/* 1. Variables */
:root {
    --jira-blue: #0052CC;
    --text-primary: #161B22;
    /* ... */
}

/* 2. Main Layout */
.page-wrapper { }
.page-header { }
.page-content { }

/* 3. Components */
.card { }
.btn { }
.badge { }

/* 4. Responsive */
@media (max-width: 1024px) { }
@media (max-width: 768px) { }
```

---

## Do's & Don'ts

### ✅ DO

- Use CSS variables for colors
- Apply 24px gaps between sections
- Add hover effects (lift + shadow)
- Use flexbox/grid layouts
- Mobile-first responsive design
- Follow typography scale
- Add breadcrumbs for navigation
- Use semantic HTML

### ❌ DON'T

- Use Bootstrap classes
- Hardcode colors
- Use pixel-based spacing inconsistently
- Skip hover effects
- Forget responsive design
- Mix fonts/sizes randomly
- Use nested tables for layout
- Skip accessibility

---

## Implementation Checklist

For each page:

- [ ] Add breadcrumb navigation
- [ ] Use CSS variables for colors
- [ ] Apply consistent spacing
- [ ] Add hover effects
- [ ] Test responsive design
- [ ] Semantic HTML structure
- [ ] Proper heading hierarchy
- [ ] No console errors
- [ ] All links working
- [ ] Mobile tested

---

## Example: Complete Card Component

```html
<div class="stat-card">
    <div class="stat-value">42</div>
    <div class="stat-label">Total Issues</div>
</div>
```

```css
.stat-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 24px;
    transition: all 0.2s ease;
}

.stat-card:hover {
    border-color: #B6C2CF;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
}
```

---

## Common Patterns

### Breadcrumb
Class: `.breadcrumb`, `.breadcrumb-link`, `.breadcrumb-current`

### Cards
Class: `.card`, `.card-header`, `.card-title`, `.card-body`

### Lists
Class: `.list-item`, `.item-key`, `.item-title`, `.item-badge`

### Buttons
Class: `.btn`, `.btn-primary`, `.btn-secondary`

### Forms
Class: `.form-input`, `.form-textarea`, `.form-select`

### Badges
Class: `.badge`, `.badge-success`, `.badge-error`

---

## Files to Reference

- `views/projects/board.php` - Board design example
- `views/projects/show.php` - Project page example
- `JIRA_DESIGN_SYSTEM_COMPLETE.md` - Full guide
- `AGENTS.md` - Development standards

---

## Quick Commands

Create new page with design:

1. Copy page template
2. Replace Bootstrap with custom CSS
3. Use color variables
4. Apply spacing rules
5. Add hover effects
6. Test responsive
7. Verify accessibility

---

**Use this card as your daily reference when designing pages!**

Full guide: `JIRA_DESIGN_SYSTEM_COMPLETE.md`
