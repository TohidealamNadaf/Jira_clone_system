# Quick Reference - Report Styling

## Copy-Paste Templates

### Container
```php
<div class="container-fluid px-5 py-4">
    <!-- content -->
</div>
```

### Page Header
```php
<div class="mb-6">
    <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">
        Page Title
    </h1>
    <p style="font-size: 15px; color: #626F86; margin: 0;">
        Description
    </p>
</div>
```

### Filter Dropdown
```php
<div style="display: flex; gap: 24px; margin-bottom: 32px; align-items: flex-end; flex-wrap: wrap;">
    <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
            Filter Label
        </label>
        <select class="form-select" style="width: 240px; height: 40px; border-radius: 4px; border: 1px solid #DFE1E6; font-size: 14px; color: #161B22; padding: 8px 12px; background-color: white; cursor: pointer;">
            <option value="">All Options</option>
        </select>
    </div>
</div>
```

### Chart Card
```php
<div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 24px; margin-bottom: 32px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
    <h6 style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">
        Section Title
    </h6>
    <canvas id="chartElement" style="max-height: 400px;"></canvas>
</div>
```

### Metric Card Grid
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
        <p style="font-size: 12px; color: #626F86; margin: 0;">Description text</p>
    </div>
</div>
```

### Data Table Card
```php
<div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
    <div style="padding: 24px;">
        <h6 style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">
            Table Title
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <!-- table content -->
        </table>
    </div>
</div>
```

### Progress Bar
```php
<div style="height: 8px; background-color: #EBECF0; border-radius: 4px; overflow: hidden;">
    <div style="height: 100%; width: <?= $percentage ?>%; background-color: #0052CC;"></div>
</div>
```

### Empty State
```php
<div style="grid-column: 1 / -1; text-align: center; color: #626F86; padding: 40px 20px;">
    <p style="font-size: 48px; margin: 0 0 16px 0;">📭</p>
    <p style="margin: 0;">No data available</p>
</div>
```

## Color Codes

```css
/* Neutrals */
#161B22  /* Primary text */
#626F86  /* Secondary text */
#97A0AF  /* Muted text */
#DFE1E6  /* Borders */
#EBECF0  /* Light backgrounds */
#FFFFFF  /* Card backgrounds */

/* Accents */
#0052CC  /* Jira blue */
#22c55e  /* Success green */
#FF5630  /* Error red */
#FFAB00  /* Warning orange */
#3b82f6  /* Light blue */
#8b5cf6  /* Purple */
```

## Spacing Values

```
Page padding:      px-5 py-4     (20px h, 16px v)
Section gap:       gap: 24px     (flex)
Grid gap:          gap: 20px     (grid)
Card padding:      padding: 20px or 24px
Section margin:    margin-bottom: 32px
Element margin:    margin-bottom: 12-16px
Label margin:      margin-bottom: 8px
Text margin:       margin: 0
```

## Font Sizes

```
Page title:        32px font-weight: 700
Card title:        14px font-weight: 600
Label:            12px font-weight: 600 (uppercase)
Metric value:     36px font-weight: 700
Description:      15px (secondary text)
Secondary:        14px
Muted:            12px
```

## Key Properties

### Dropdown (240px width)
```
width: 240px
height: 40px
border-radius: 4px
border: 1px solid #DFE1E6
padding: 8px 12px
```

### Card Shadow
```
box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 
            0 0 1px rgba(9, 30, 66, 0.13)
```

### Border Radius
```
Container: 8px
Dropdown: 4px
Progress: 4px
```

## Icon Dots

Instead of icon fonts, use colored dots:

```php
<span style="color: #0052CC; margin-right: 8px;">●</span>Label
<span style="color: #22c55e; margin-right: 8px;">●</span>Label
<span style="color: #FF5630; margin-right: 8px;">●</span>Label
<span style="color: #FFAB00; margin-right: 8px;">●</span>Label
<span style="color: #3b82f6; margin-right: 8px;">●</span>Label
```

## Responsive Grid

```php
<!-- 1-4 columns, automatically responsive -->
display: grid
grid-template-columns: repeat(auto-fit, minmax(240px, 1fr))
gap: 20px
```

## Files to Reference

- **REPORT_UI_STANDARDS.md** - Complete guide with all sections
- **AGENTS.md** - Developer standards (section: Report UI Standards)
- **QUICK_REFERENCE_REPORT_STYLING.md** - This file

## Example Report Page Structure

```php
<?php \App\Core\View::extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">Title</h1>
        <p style="font-size: 15px; color: #626F86; margin: 0;">Description</p>
    </div>
    
    <!-- Filters -->
    <div style="display: flex; gap: 24px; margin-bottom: 32px; align-items: flex-end; flex-wrap: wrap;">
        <!-- dropdowns -->
    </div>
    
    <!-- Chart/Data -->
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 24px; margin-bottom: 32px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <!-- content -->
    </div>
    
    <!-- Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        <!-- metric cards -->
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
```

---

For complete details, see **REPORT_UI_STANDARDS.md**
