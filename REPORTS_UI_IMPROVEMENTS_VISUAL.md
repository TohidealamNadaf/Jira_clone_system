# Reports Page UI Improvements - Visual Guide

## Before vs After

### 1. Stat Cards Design

**Before:**
```
[Icon Box] [Text]
- Bootstrap cards with bg-opacity classes
- Icon in separate background box
- Horizontal layout taking up less space
- Generic Bootstrap styling
```

**After:**
```
━━━━━━━━━━━━━━━━━━━━━━━━
│  📊 TOTAL ISSUES      │
│  110                  │
━━━━━━━━━━━━━━━━━━━━━━━━
```

**Improvements:**
- Jira-like clean card design
- Icon integrated into label
- Large, prominent metric values (36px)
- Professional color scheme with #DFE1E6 borders
- Subtle shadow for depth: `0 1px 1px rgba(9, 30, 66, 0.13)`
- Better visual hierarchy

### 2. Color Palette Used
```
Text Primary:        #161B22 (Dark gray)
Text Secondary:      #626F86 (Medium gray)
Border Color:        #DFE1E6 (Light gray)
Blue (Primary):      #0052CC
Green (Success):     #216E4E
Orange (Warning):    #974F0C
Icon Colors match the Jira design system
```

### 3. Typography Improvements
```
Page Title:          32px, font-weight: 700
Stat Value:          36px, font-weight: 700
Card Header:         15px, font-weight: 600
Label:               12px, font-weight: 600, UPPERCASE
Filter Label:        13px, font-weight: 600
```

### 4. Report Category Cards

**Before:**
- Bootstrap card header with bg-transparent
- Standard heading styling
- Generic appearance

**After:**
```
━━━━━━━━━━━━━━━━━━━━━━━━━
│ ⚡ AGILE REPORTS       │
├───────────────────────┤
│ 📉 Burndown Chart     │
│ 📊 Velocity Chart     │
│ 📈 Sprint Report      │
│ 📚 Cumulative Flow    │
━━━━━━━━━━━━━━━━━━━━━━━━
```

**Improvements:**
- Clean header with border-bottom
- Professional spacing (16px padding)
- Jira color-coded icons
- Consistent shadow styling

### 5. Filter Dropdown

**Before:**
- Generic Bootstrap select
- No label
- Width: auto

**After:**
```
Filter by Project: [◆ All Projects ▼]
                    ← Label (13px, #626F86)
                    ← Fixed width 240px, height 40px
                    ← Border #DFE1E6, radius 4px
```

**Improvements:**
- Clear label indicating purpose
- Fixed width prevents layout shift
- Proper spacing and alignment
- Consistent with other Jira UI elements

## Spacing & Layout

### Container
- Padding: 20px (px-4) horizontal, 16px (py-4) vertical
- Gap between stat cards: 12px (g-3)
- Gap between report sections: 16px (g-4)
- Bottom margins: 32px (mb-5) for major sections

### Cards
- Padding: 20px
- Border-radius: 8px
- Border: 1px solid #DFE1E6
- Background: white
- Shadow: Jira-standard (two-layer subtle shadow)

## Responsive Behavior
- Stat cards: 4 columns desktop → 2 columns tablet → 1 column mobile
- Report sections: 2 columns desktop → 1 column tablet/mobile
- Dropdown: Fixed width 240px, maintains alignment across all breakpoints
- Filter stays right-aligned on desktop, moves below title on mobile

## Font Family
- System fonts (inherited from Bootstrap/app.css)
- No custom web fonts needed
- Improves performance
- Matches Jira's modern appearance

## Consistency with Other Pages
- Stat cards style matches `/reports/workload` page
- Color scheme matches entire application
- Typography hierarchy consistent with `/admin/` pages
- Shadow system aligns with design system in `app.css`
