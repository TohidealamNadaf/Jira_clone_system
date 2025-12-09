# Report UI Standards - Professional Jira-like Design

## Overview

All report pages follow a consistent, professional design that matches Atlassian Jira's enterprise-grade UI.

## Page Structure

### Layout Spacing
```php
<div class="container-fluid px-5 py-4">
    <!-- Header -->
    <!-- Filters -->
    <!-- Main Content -->
</div>
```

- **Padding**: `px-5 py-4` (20px horizontal, 16px vertical)
- **Container**: `container-fluid` for full width

### Header Section
```php
<div class="mb-6">
    <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">
        Report Title
    </h1>
    <p style="font-size: 15px; color: #626F86; margin: 0;">
        Report description
    </p>
</div>
```

- **Title**: 32px, font-weight 700, color #161B22
- **Spacing**: 8px bottom margin
- **Description**: 15px, color #626F86, muted text

## Filters Section

```php
<div style="display: flex; gap: 24px; margin-bottom: 32px; align-items: flex-end; flex-wrap: wrap;">
    <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
            Filter Label
        </label>
        <select class="form-select" style="width: 240px; height: 40px; border-radius: 4px; border: 1px solid #DFE1E6; font-size: 14px; color: #161B22; padding: 8px 12px; background-color: white; cursor: pointer;">
            <option value="">Option</option>
        </select>
    </div>
</div>
```

### Dropdown Styling
- **Width**: `240px` (fixed, prevents text cutoff)
- **Height**: `40px` (better touch targets)
- **Border**: `1px solid #DFE1E6`
- **Border Radius**: `4px`
- **Padding**: `8px 12px`
- **Font Size**: `14px`
- **Text Color**: `#161B22`
- **Background**: `white`
- **Cursor**: `pointer`

**Important**: Use fixed `width` instead of `min-width` to prevent text overflow

### Label Styling
- **Font Size**: `12px`
- **Font Weight**: `600`
- **Color**: `#626F86`
- **Text Transform**: `uppercase`
- **Letter Spacing**: `0.5px`
- **Margin Bottom**: `8px`

## Cards & Containers

### Chart Card
```php
<div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 24px; margin-bottom: 32px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
    <h6 style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">
        Chart Section Title
    </h6>
    <canvas id="chartElement" style="max-height: 400px;"></canvas>
</div>
```

### Metric Card (Grid)
```php
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
            <span style="color: #0052CC; margin-right: 8px;">●</span>Metric Name
        </p>
        <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
            <h2 id="metricValue" style="font-size: 36px; font-weight: 700; color: #161B22; margin: 0;">0</h2>
            <span style="font-size: 14px; color: #626F86;">unit</span>
        </div>
        <p style="font-size: 12px; color: #626F86; margin: 0;">Metric description</p>
    </div>
</div>
```

### Card Properties
- **Background**: `white`
- **Border**: `1px solid #DFE1E6`
- **Border Radius**: `8px`
- **Padding**: `20px` (metric cards) or `24px` (section cards)
- **Margin Bottom**: `32px`
- **Shadow**: `0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)`

### Grid Layout
- **Display**: `grid`
- **Template Columns**: `repeat(auto-fit, minmax(240px, 1fr))`
- **Gap**: `20px`
- Responsive: Automatically adjusts from 1-4 columns based on screen size

## Typography

### Heading (H1 - Page Title)
```
font-size: 32px;
font-weight: 700;
color: #161B22;
margin-bottom: 8px;
```

### Heading (H6 - Section Header)
```
font-size: 12px;
font-weight: 600;
color: #626F86;
text-transform: uppercase;
letter-spacing: 0.5px;
margin-bottom: 20px;
```

### Metric Value
```
font-size: 36px;
font-weight: 700;
color: #161B22;
margin: 0;
```

### Description Text
```
font-size: 15px;
color: #626F86;
margin: 0;
```

### Secondary Text
```
font-size: 14px;
color: #626F86;
```

### Muted Text
```
font-size: 12px;
color: #626F86;
```

## Color Palette

| Usage | Color | Hex |
|-------|-------|-----|
| Primary Text | Gray | #161B22 |
| Secondary Text | Gray | #626F86 |
| Muted Text | Gray | #97A0AF |
| Borders | Light Gray | #DFE1E6 |
| Background | White | #FFFFFF |
| Blue Accent | Jira Blue | #0052CC |
| Green Accent | Success | #22c55e |
| Red Accent | Error | #FF5630 |
| Orange Accent | Warning | #FFAB00 |
| Purple Accent | Info | #8b5cf6 |
| Light Blue Accent | Info | #3b82f6 |

## Empty State

```php
<div style="grid-column: 1 / -1; text-align: center; color: #626F86; padding: 40px 20px;">
    <p style="font-size: 48px; margin: 0 0 16px 0;">📭</p>
    <p style="margin: 0;">No data available</p>
</div>
```

- **Emoji**: 48px
- **Message**: 14px, gray text
- **Padding**: 40px vertical, 20px horizontal
- **Text Align**: center

## Table Styling

```php
<div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
    <div style="padding: 24px;">
        <h6 style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">
            Table Title
        </h6>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <!-- Table content -->
            </table>
        </div>
    </div>
</div>
```

## Progress Bar

```php
<div style="height: 8px; background-color: #EBECF0; border-radius: 4px; overflow: hidden;">
    <div style="height: 100%; width: 75%; background-color: #0052CC;"></div>
</div>
```

- **Height**: `8px`
- **Background**: `#EBECF0` (light gray)
- **Border Radius**: `4px`
- **Overflow**: `hidden`

## Icon Indicator

Use colored dots instead of icons:

```php
<span style="color: #0052CC; margin-right: 8px;">●</span>Metric Name
```

- **Small dot**: `●`
- **Color**: Matches metric type
- **Spacing**: 8px right margin

## Spacing Standards

| Element | Value |
|---------|-------|
| Page padding | px-5 py-4 |
| Header margin | mb-6 |
| Filter section margin | margin-bottom: 32px |
| Card padding | 20-24px |
| Card margin bottom | margin-bottom: 32px |
| Grid gap | gap: 20px |
| Flex gap | gap: 24px |
| Element margin | margin-bottom: 12-16px |
| Text margin | margin: 0 |

## Implementation Checklist

When creating a new report page:

- [ ] Use `px-5 py-4` padding on container
- [ ] Header: 32px title + 15px description
- [ ] All dropdowns: 240px width (not min-width)
- [ ] Dropdown height: 40px
- [ ] All cards: white background + #DFE1E6 border + rounded corners + shadow
- [ ] Grid layout with `repeat(auto-fit, minmax(240px, 1fr))`
- [ ] Use 36px for metric values
- [ ] Use uppercase labels with 12px font
- [ ] Empty state with emoji + centered text
- [ ] Consistent color usage from palette
- [ ] All text properly styled with correct font sizes

## Files Updated

- `views/reports/created-vs-resolved.php`
- `views/reports/resolution-time.php`
- `views/reports/priority-breakdown.php`

## Future Reports

Apply these standards to remaining report files:
- `views/reports/time-logged.php`
- `views/reports/estimate-accuracy.php`
- `views/reports/version-progress.php`
- `views/reports/release-burndown.php`
- `views/reports/velocity.php`
- `views/reports/cumulative-flow.php`
- `views/reports/sprint.php`
- `views/reports/burndown.php`
