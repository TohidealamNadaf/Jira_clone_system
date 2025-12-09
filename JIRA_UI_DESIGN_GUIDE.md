# Jira Clone UI Design Guide

## Design Philosophy
Clean, spacious, and professional like real Jira. Follows Atlassian design system principles.

## Key Design Principles

### 1. Spacing & Breathing Room
- Generous padding: 16px-24px
- Gap between elements: 12px-24px (use `gap-3` or `mb-5`)
- Container padding: `px-4 py-3`
- Card padding: `p-4`

### 2. Typography
- Page titles: 28px, fw-600, color #161B22
- Subtitles: 14px, text-muted
- Card labels: 12px, UPPERCASE, letter-spacing 0.5px, fw-600
- Numbers: 32px, fw-600, color #161B22
- Regular text: 14px

### 3. Colors
- Primary: #0052CC (Jira blue)
- Text primary: #161B22 (dark gray)
- Text muted: #626F86 (medium gray)
- Success: #22c55e (green)
- Danger: #ff7875 (red)
- Warning: #fa8c16 (orange)

### 4. Borders & Shadows
- Card shadow: `0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)`
- Border radius: 4px (compact, Jira-like)
- No border on cards: `border-0`

### 5. Form Elements
- Select/input height: 36px
- Border: 1px solid #ddd
- Border-radius: 4px
- Min-width for selects: 220px
- Font-size: 14px

## Report Layout Template

```html
<!-- Page Header -->
<div class="mb-5">
    <h1 class="h2 fw-600 mb-2" style="font-size: 28px; color: #161B22;">
        Report Title
    </h1>
    <p class="text-muted mb-0" style="font-size: 14px;">
        Description
    </p>
</div>

<!-- Filters Section -->
<div class="d-flex gap-3 mb-5 align-items-end" style="flex-wrap: wrap;">
    <div>
        <label class="form-label text-muted mb-2" 
               style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
            Filter Label
        </label>
        <select class="form-select" style="min-width: 220px; height: 36px; border-radius: 4px; border: 1px solid #ddd; font-size: 14px;">
            <option>Option</option>
        </select>
    </div>
</div>

<!-- Main Chart/Content Card -->
<div class="card border-0" style="box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13); margin-bottom: 24px;">
    <div class="card-body p-4">
        <h6 class="text-muted mb-4" style="font-size: 13px; font-weight: 600; letter-spacing: 0.5px;">
            SECTION LABEL
        </h6>
        <!-- Content here -->
    </div>
</div>

<!-- Metrics Grid -->
<div class="row g-3">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 h-100" style="box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <div class="card-body p-4">
                <p class="text-muted mb-3" style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="bi bi-icon me-2" style="color: #0052CC;"></i>Metric Label
                </p>
                <div style="display: flex; align-items: baseline; gap: 8px;">
                    <h2 class="mb-0" style="font-size: 32px; font-weight: 600; color: #161B22;">
                        Value
                    </h2>
                    <span class="text-muted" style="font-size: 12px;">unit</span>
                </div>
                <p class="text-muted mb-0 mt-3" style="font-size: 12px;">Supporting text</p>
            </div>
        </div>
    </div>
</div>
```

## Color Scheme by Icon

### Metric Icons
- Created: bi-plus-circle #0052CC (blue)
- Resolved: bi-check-circle #22c55e (green)
- Net Change: bi-arrow-left-right #ff7875 (red)
- Rate: bi-percent #fa8c16 (orange)
- Workload: bi-people #3b82f6 (info blue)
- Time: bi-stopwatch #8b5cf6 (purple)
- Priority: bi-exclamation-triangle #fa8c16 (warning)

## Responsive Grid System

### Report Layout Breakpoints
```
- Desktop (lg > 1200px):     4-column grid (col-lg-3)
- Tablet (md 768-1200px):    2-column grid (col-md-6)
- Mobile (< 768px):          1-column grid (full width)
```

### Container Padding
- Desktop: px-4 py-3 (normal padding)
- Tablet: px-3 py-2 (slightly compressed)
- Mobile: px-2 py-2 (minimal padding)

## Filter Design

### Filter Labels
- Always above input
- Font size: 12px
- Font weight: 600
- Text transform: UPPERCASE
- Letter spacing: 0.5px
- Color: text-muted
- Margin bottom: mb-2

### Filter Inputs
- Type: form-select
- Min width: 220px (allows comfortable selection)
- Height: 36px (tall enough for touch)
- Border: 1px solid #ddd
- Border radius: 4px
- Font size: 14px

### Filter Container
- Gap: gap-3 (good spacing between filters)
- Alignment: align-items-end (aligns with labels)
- Flex wrap: flex-wrap (allows stacking on mobile)
- Margin bottom: mb-5 (breathing room before content)

## Card Design

### Card Properties
- Border: border-0 (no border)
- Shadow: `0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)`
- Padding: p-4 (16px)
- Margin bottom: mb-4 (between sections)

### Card Headers
- Use h6 tags
- Font size: 13px
- Font weight: 600
- Text color: text-muted
- Margin bottom: mb-4
- Letter spacing: 0.5px

## Charts

### Chart Container
- Canvas max-height: 450px (good for reports)
- No fixed height (responsive)
- Margin bottom: 24px (spacing after chart)

### Chart Legend
- Position: 'top'
- Labels use point style
- Padding: 15px
- Font size: 12px

### Chart Grid
- X-axis: no grid (cleaner)
- Y-axis: rgba(0, 0, 0, 0.05) (subtle grid)

## Tables

### Table Design
- Class: `table table-hover`
- Header: `table-light` background
- Font size: 14px
- Responsive: wrap in `table-responsive`

### Table Spacing
- Row padding: 12px
- Column gap: 16px
- Hover effect: light background shift

## Validation States

### Form Validation
- Invalid: border-danger, error text in red
- Valid: border-success
- Loading: opacity-50, disabled state

## Example Components

### Icon with Label
```html
<i class="bi bi-icon me-2" style="color: #0052CC;"></i>Label Text
```

### Number + Unit
```html
<div style="display: flex; align-items: baseline; gap: 8px;">
    <h2 class="mb-0" style="font-size: 32px; font-weight: 600; color: #161B22;">
        1,234
    </h2>
    <span class="text-muted" style="font-size: 12px;">issues</span>
</div>
```

### Support Text
```html
<p class="text-muted mb-0 mt-3" style="font-size: 12px;">
    Supporting information text
</p>
```

## Accessibility

### Color Contrast
- Text on white: #161B22 (contrast ratio 15:1) ✅
- Muted text: #626F86 (contrast ratio 6:1) ✅
- Interactive elements: #0052CC with white background ✅

### Focus States
- Inputs: focus-visible outline
- Links: underline on hover
- Buttons: background shift on focus

### ARIA Labels
```html
<label for="projectFilter">Project</label>
<select id="projectFilter">
```

## Loading States

### Skeleton Loading
```css
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
```

### Empty States
```html
<div class="text-center text-muted py-5">
    <p><i class="bi bi-inbox fs-1"></i></p>
    <p>No data available</p>
</div>
```

## Best Practices

1. **Consistent Spacing**: Always use mb-5 between major sections
2. **Card Grouping**: Group related metrics in rows
3. **Readability**: Large fonts for important numbers (32px)
4. **Visual Hierarchy**: Use colors and sizes to guide attention
5. **Mobile First**: Design mobile layout first, then enhance
6. **Whitespace**: Don't crowd the page - embrace empty space
7. **Typography**: Consistent font sizes and weights
8. **Shadows**: Subtle shadows for depth, not drama

---

## Implementation Checklist

When creating a new report:

- [ ] Page header with title and description
- [ ] Filter section with proper labels above selects
- [ ] Main content card with section label
- [ ] Metrics grid with 4 cards (or fewer if needed)
- [ ] Proper spacing (mb-5 between sections)
- [ ] Icons in metric labels with appropriate colors
- [ ] Responsive grid (col-lg-3 col-md-6)
- [ ] Touch-friendly inputs (36px height)
- [ ] Proper shadows and borders
- [ ] Mobile responsive design
- [ ] Accessible labels and ARIA attributes
