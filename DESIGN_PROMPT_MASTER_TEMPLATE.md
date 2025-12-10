# Design Prompt Master Template - Enterprise Jira-Like Design System

**Purpose**: Use this prompt template to design new pages consistent with the board, navbar, and issue pages  
**Status**: Reference Document | **Version**: 1.0

---

## Copy-Paste Design Prompt Template

Use this prompt with AI design tools or as a specification document for new pages:

```
DESIGN BRIEF: [PAGE_NAME] Page

DESIGN SYSTEM: Enterprise Jira-Like UI (Reference: Board Page, Issue Detail Page, Navbar)

OBJECTIVE: Design a [PAGE_NAME] page that maintains enterprise-grade consistency with existing Jira-like application design.

DESIGN CONSTRAINTS:
✓ Match existing color palette, typography, and spacing from board/issue pages
✓ Maintain professional enterprise aesthetic
✓ Ensure responsive design (mobile, tablet, desktop)
✓ Preserve all functionality (no feature loss)
✓ Follow accessibility standards (WCAG AA)

COLOR PALETTE:
- Primary Blue: #0052CC (actions, hover states, links)
- Dark Blue: #003DA5 (hover darker variant)
- Light Blue: #DEEBFF (hover background, light variant)
- Black/Dark Text: #161B22 (primary text, headings)
- Gray Text: #626F86 (secondary text, labels)
- Light Gray: #F7F8FA (backgrounds, secondary elements)
- Borders: #DFE1E6 (dividers, card borders)
- White: #FFFFFF (main background)
- Red: #ED3C32 (danger, destructive actions)
- Green: #216E4E (success states)
- Orange: #974F0C (warnings)
- Teal: #216E4E (info, additional info)

TYPOGRAPHY:
- Font Family: System fonts (default browser stack)
- Page Title: 28px, font-weight 700, color #161B22, letter-spacing -0.3px
- Section Header: 20px, font-weight 600, color #161B22
- Subsection: 15px, font-weight 600, color #161B22
- Body Text: 14px, font-weight 400, color #161B22
- Small Text: 12px, font-weight 400, color #626F86
- Label: 13px, font-weight 600, color #626F86
- Button Text: 14px, font-weight 500
- Line Height: 1.43 for body text, 1.5 for headings

SPACING SCALE (multiples of 4px):
4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 48px

LAYOUT STRUCTURE:
1. BREADCRUMB NAVIGATION (12px top)
   - Items: Home > Section > Current Page
   - Icons on left of first item
   - Gray separators between items
   - Hover effect on links
   - Current page: bold, not a link

2. PAGE HEADER (24px padding, white background)
   - Left: Title + Subtitle
   - Right: Action buttons
   - Bottom border: 1px #DFE1E6
   - Box shadow: 0 1px 1px rgba(9,30,66,0.13)
   - Flexbox with space-between

3. MAIN CONTENT AREA
   - Background: #F7F8FA
   - Padding: 24px 32px (desktop), 16px 20px (mobile)
   - Flexible layout (grid or flex)
   - Responsive columns

COMPONENT PATTERNS:

A. CARDS
   - Background: #FFFFFF
   - Border: 1px solid #DFE1E6
   - Border Radius: 6px or 8px
   - Padding: 16px, 20px, or 24px (context-dependent)
   - Box Shadow: 0 1px 3px rgba(9,30,66,0.13), 0 0 1px rgba(9,30,66,0.13)
   - Hover: translateY(-3px), shadow-lg
   - Transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1)

B. BUTTONS
   - Primary: #0052CC background, white text
   - Primary Hover: #003DA5 background
   - Secondary: white background, #DFE1E6 border, #161B22 text
   - Secondary Hover: #F7F8FA background
   - Danger: #ED3C32 background, white text
   - Padding: 8px 16px (small), 10px 20px (medium), 12px 24px (large)
   - Border Radius: 4px or 6px
   - Font: 14px, font-weight 500
   - Transition: 0.2s ease

C. BADGES & LABELS
   - Background: colored (type-specific)
   - Color: white text
   - Padding: 4px 8px (inline), 6px 12px (standalone)
   - Border Radius: 4px or rounded pill (12px)
   - Font: 11px, font-weight 700, uppercase
   - Letter Spacing: 0.3-0.5px

D. INPUT FIELDS
   - Background: #FFFFFF
   - Border: 1px solid #DFE1E6
   - Border Radius: 6px
   - Padding: 8px 12px (small), 12px 16px (large)
   - Focus: blue border (#0052CC) + shadow (0 0 0 2px rgba(0,82,204,0.1))
   - Font: 14px, color #161B22
   - Transition: 0.2s ease

E. DROPDOWNS & PANELS
   - Background: #FFFFFF
   - Border: 1px solid #DFE1E6
   - Border Radius: 6px
   - Box Shadow: 0 4px 12px rgba(9,30,66,0.15)
   - Margin Top: 8px
   - Min Width: 280px or context-dependent
   - Overflow: hidden, flex column

F. TABLE STYLING
   - Header Background: #F7F8FA
   - Header Font: 12px, font-weight 600, #626F86, uppercase
   - Row Background: #FFFFFF
   - Row Border: 1px solid #DFE1E6
   - Cell Padding: 12px 16px
   - Row Hover: #F7F8FA background
   - Alternating Rows: optional light gray
   - Transition: 0.2s ease

G. LISTS
   - Item Padding: 12px 16px (compact), 16px 20px (spacious)
   - Item Border: 1px solid #DFE1E6
   - Hover: #F7F8FA background
   - Icon + Text: gap 12px, icon flex-shrink 0
   - Divider: 1px #DFE1E6

H. EMPTY STATES
   - Icon: 56-72px emoji or SVG, opacity 0.5
   - Text: 14px, font-weight 500, color #626F86
   - Centered: text-align center, flex column, justify center
   - Padding: 60px 20px (generous vertical)

RESPONSIVE DESIGN:

Desktop (1400px+):
- Sidebar: 300-350px width (if needed)
- Main: flex 1
- Padding: 24px 32px
- Columns: 3-4 grid columns

Laptop (1024px - 1399px):
- Padding: 20px 24px
- Columns: 2-3 grid columns
- Adjusted sizing

Tablet (768px - 1023px):
- Padding: 16px 20px
- Columns: 2 grid columns
- Stacked sidebar if present
- Smaller gap between items

Mobile (< 768px):
- Padding: 16px 16px or 12px
- Columns: 1 (full width)
- Touch-friendly: min height 44px buttons
- Larger gaps: 16px or 20px
- No sidebar (stack vertically)

ANIMATIONS & TRANSITIONS:
- Base Duration: 0.2s
- Timing Function: cubic-bezier(0.4, 0, 0.2, 1) or ease
- Hover Effects:
  - Cards: lift + shadow (translateY -3px)
  - Buttons: background + color shift (no transform for navbar)
  - Links: color shift + underline
  - Icons: scale or color change
- Loading States: opacity fade, spinner animation

HOVER EFFECTS PATTERN:
1. Color Shift (primary element + text)
2. Background Change (light variant)
3. Shadow Enhancement (card elevate)
4. Lift Animation (translateY -2px to -3px for cards)
5. Timing: 0.2s transition

ICONS:
- Library: Bootstrap Icons (bi-*)
- Size: 16px (inline), 20px (buttons), 24px (headers), 56px+ (empty states)
- Color: Inherit from text color or specific palette
- Alignment: center vertically with text, gap 6-12px

ACCESSIBILITY:
✓ Semantic HTML: nav, main, section, article, button, a, form
✓ Color Contrast: WCAG AA (4.5:1 for text, 3:1 for graphics)
✓ Focus States: Visible outline or highlight
✓ ARIA: role, aria-label, aria-expanded, aria-hidden
✓ Keyboard Nav: Tab order logical, Enter activates buttons
✓ Screen Reader: Meaningful text, icon descriptions
✓ Touch: Min 44px height for interactive elements

BEST PRACTICES:
1. Use CSS Variables for colors: var(--jira-blue)
2. No hardcoded colors in components
3. Flexbox for alignment (gap instead of margin)
4. CSS Grid for complex layouts
5. Mobile-first: Default styles for mobile, @media for larger screens
6. No unnecessary DOM nesting
7. Reuse component classes
8. Consistent spacing (multiples of 4)
9. Smooth transitions (0.2s)
10. Performance: CSS > JavaScript for animations

SECTION PATTERNS:

Pattern A: Title + Subtitle + Content
├─ .page-header
│  ├─ .header-left
│  │  ├─ h1.page-title
│  │  └─ p.page-subtitle
│  └─ .header-right
│     └─ button.btn (actions)
└─ .content-area
   └─ [Grid/Flex content]

Pattern B: Sidebar + Main Content
├─ .main-container (flex)
│  ├─ .sidebar (300px)
│  │  └─ [Navigation/Filters]
│  └─ .main-content (flex: 1)
│     └─ [Primary content]

Pattern C: Tabs + Content
├─ .tabs-container
│  ├─ .tab-nav
│  │  ├─ .tab-item.active
│  │  ├─ .tab-item
│  │  └─ .tab-item
│  └─ .tab-content
│     └─ [Tab-specific content]

Pattern D: Grid of Cards
├─ .cards-grid (grid: repeat(auto-fit, minmax(240px, 1fr)))
│  ├─ .card
│  │  ├─ .card-header
│  │  ├─ .card-body
│  │  └─ .card-footer
│  ├─ .card
│  └─ .card

EXAMPLE PAGE STRUCTURE:
```html
<div class="page-wrapper">
  <!-- Breadcrumb -->
  <div class="breadcrumb-nav">
    <a href="/home">Home</a> / <span>Current Page</span>
  </div>
  
  <!-- Header -->
  <div class="page-header">
    <div class="header-left">
      <h1>Page Title</h1>
      <p>Subtitle or description</p>
    </div>
    <div class="header-right">
      <button class="btn btn-secondary">Filter</button>
      <button class="btn btn-primary">Action</button>
    </div>
  </div>
  
  <!-- Content -->
  <div class="content-area">
    <!-- Grid/Cards/Tables/etc -->
  </div>
</div>
```

EXAMPLE CSS STRUCTURE:
```css
:root {
  --jira-blue: #0052CC;
  --text-primary: #161B22;
  --text-secondary: #626F86;
  --bg-primary: #FFFFFF;
  --bg-secondary: #F7F8FA;
  --border-color: #DFE1E6;
  --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
  --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13);
  --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
  --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  background: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
}

.card {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  padding: 20px;
  box-shadow: var(--shadow-sm);
  transition: all var(--transition);
}

.card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-3px);
}

.btn-primary {
  background: var(--jira-blue);
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  transition: background var(--transition);
}

.btn-primary:hover {
  background: #003DA5;
}
```

REFERENCES:
- Board Page: views/projects/board.php
- Issue Detail: views/issues/show.php
- Navbar: views/layouts/app.php
- Design System: JIRA_DESIGN_SYSTEM_COMPLETE.md
- Quick Reference: DESIGN_SYSTEM_QUICK_REFERENCE.md

DELIVERABLE CHECKLIST:
✓ Page header with breadcrumb
✓ Professional typography hierarchy
✓ Consistent color palette
✓ Proper spacing (4px multiples)
✓ Hover effects on interactive elements
✓ Responsive design (3+ breakpoints)
✓ Accessibility compliant
✓ No console errors
✓ Mobile touch-friendly
✓ CSS variables used for colors
```

---

## How to Use This Prompt

### For New Pages:

1. **Copy the prompt above** into your AI design tool or keep as reference
2. **Fill in placeholders**:
   - `[PAGE_NAME]` = "Projects List" or "Users Dashboard"
   - `[SPECIFIC_COMPONENTS]` = cards, tables, forms, etc.

3. **Add page-specific details**:
   ```
   ADDITIONAL REQUIREMENTS FOR [PAGE_NAME]:
   - Custom color for [element]: [color]
   - Special spacing for [section]: [value]
   - Mobile breakpoint change: [value]
   - Custom component: [description]
   ```

4. **Reference existing pages**:
   - Need cards? → Look at board.php
   - Need tables? → Look at issues/index.php  
   - Need forms? → Look at profile settings or create page
   - Need dropdowns? → Look at navbar.php

### Example: Designing Projects List Page

Copy prompt and customize:
```
DESIGN BRIEF: Projects List Page

[Copy template above]

ADDITIONAL REQUIREMENTS FOR PROJECTS LIST:
- Table layout with columns: Key, Name, Lead, Issues, Updated
- Column widths responsive:
  - Desktop: 80px, auto, 200px, 80px, 150px
  - Mobile: Key, Name, Status (hide others)
- Hover row highlight: #F7F8FA background
- Action column: Edit, Archive, Delete buttons
- Pagination: 25 items per page
- Search: Top of page with filters
```

---

## Quick Copy Design Tokens

Use these exact values for consistency:

```css
/* Colors */
--jira-blue: #0052CC;
--jira-blue-dark: #003DA5;
--jira-blue-light: #DEEBFF;
--text-primary: #161B22;
--text-secondary: #626F86;
--bg-primary: #FFFFFF;
--bg-secondary: #F7F8FA;
--border-color: #DFE1E6;
--red: #ED3C32;
--green: #216E4E;
--orange: #974F0C;
--teal: #216E4E;

/* Spacing */
--space-4: 4px;
--space-8: 8px;
--space-12: 12px;
--space-16: 16px;
--space-20: 20px;
--space-24: 24px;
--space-32: 32px;

/* Typography */
--font-size-sm: 12px;
--font-size-base: 14px;
--font-size-lg: 15px;
--font-size-xl: 20px;
--font-size-2xl: 28px;
--font-weight-normal: 400;
--font-weight-medium: 500;
--font-weight-semibold: 600;
--font-weight-bold: 700;

/* Shadows */
--shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
--shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
--shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
--shadow-xl: 0 8px 16px rgba(9, 30, 66, 0.15);

/* Transitions */
--transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);

/* Border Radius */
--radius-sm: 4px;
--radius-md: 6px;
--radius-lg: 8px;
```

---

## Design System References

| Component | Reference File | Key Features |
|-----------|--------------|--------------|
| Navbar | views/layouts/app.php | Dropdowns, search, notifications |
| Board | views/projects/board.php | Kanban cards, columns, drag-drop |
| Issue Detail | views/issues/show.php | Tabs, comments, timeline, modals |
| Projects List | views/projects/index.php | Table, pagination, filters |
| Issues List | views/issues/index.php | Table, badges, multi-select |

---

## Common Page Patterns

### Pattern 1: List Page (Projects, Issues, Users)
```
[Breadcrumb]
[Header: Title + Create Button]
[Search/Filter Bar]
[Table or Cards Grid]
[Pagination]
```

### Pattern 2: Detail Page (Issue, Project, User)
```
[Breadcrumb]
[Header: Title + Actions Menu]
[Tabs or Sidebar Navigation]
[Main Content Area]
[Related/Activity Sidebar]
```

### Pattern 3: Form Page (Create, Edit)
```
[Breadcrumb]
[Header: Title + Back Button]
[Form Fields with Labels]
[Help Text under each field]
[Submit/Cancel Buttons]
[Success/Error Messages]
```

### Pattern 4: Dashboard/Overview
```
[Breadcrumb]
[Header: Title + Refresh Button]
[Metrics Cards (Grid)]
[Charts/Graphs]
[Activity Feed or Table]
```

---

## Color Usage Guide

```
Primary Actions → #0052CC (Create, Submit, Primary)
Hover States → #003DA5 or #DEEBFF
Text & Headings → #161B22
Secondary Text → #626F86
Backgrounds → #FFFFFF or #F7F8FA
Borders → #DFE1E6
Danger/Delete → #ED3C32
Success → #216E4E
Warning → #974F0C
Info/Secondary → #216E4E
```

---

## File Locations for Reference

**Core System Files**:
- Design System: `JIRA_DESIGN_SYSTEM_COMPLETE.md`
- Quick Reference: `DESIGN_SYSTEM_QUICK_REFERENCE.md`
- Navbar Design: `NAVBAR_REDESIGN_ENTERPRISE_COMPLETE.md`

**Example Pages**:
- Board: `views/projects/board.php`
- Issue: `views/issues/show.php`
- Navbar: `views/layouts/app.php`

**Guidelines**:
- Code Style: `AGENTS.md` (lines 31-65)
- URL Routing: `AGENTS.md` (lines 60-64)

---

## Quick Start

**To design a new page**:

1. Copy the prompt template above
2. Fill in `[PAGE_NAME]` and details
3. Reference board.php or issue detail for component examples
4. Use color tokens and spacing scale exactly as specified
5. Test responsive design at 3 breakpoints
6. Verify accessibility (focus states, WCAG AA contrast)
7. No console errors when viewing

**Result**: A professional, consistent page that matches your entire application design.

---

**This template maintains enterprise-grade design consistency across all pages in your Jira clone system.**
