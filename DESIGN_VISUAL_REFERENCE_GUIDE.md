# Design Visual Reference Guide - Enterprise Jira-Like System

**Quick Visual Guide** | Reference your design system at a glance

---

## Color Palette Quick Reference

### Primary Colors
```
🔵 Jira Blue:        #0052CC  (Primary actions, links, active states)
🔵 Jira Blue Dark:   #003DA5  (Hover states, darker variant)
🔵 Jira Blue Light:  #DEEBFF  (Hover backgrounds, light variant)
```

### Neutral Colors
```
⚫ Dark Text:       #161B22  (Headings, primary text)
⚪ Gray Text:       #626F86  (Secondary text, labels)
⚪ Light Gray BG:   #F7F8FA  (Section backgrounds, secondary elements)
⚪ White:           #FFFFFF  (Cards, main backgrounds)
⚪ Border Gray:     #DFE1E6  (Borders, dividers)
```

### Status Colors
```
🔴 Red/Danger:      #ED3C32  (Delete, destructive actions)
🟢 Green/Success:   #216E4E  (Confirmed, success states)
🟠 Orange/Warning:  #974F0C  (Warnings, alerts)
🔵 Teal/Info:       #216E4E  (Information, secondary)
```

---

## Typography Scale

```
Page Title:        28px | 700  | #161B22 | -0.3px letter-spacing
Section Header:    20px | 600  | #161B22
Subsection:        15px | 600  | #161B22
Body Text:         14px | 400  | #161B22 | 1.43 line-height
Small Text:        12px | 400  | #626F86 | 1.5 line-height
Label/Badge:       13px | 600  | #626F86 (uppercase)
Button Text:       14px | 500
```

**Visual Hierarchy**:
```
H1 Title ──────────────────────────────────── 28px, Bold
H2 Section Header ────────────────────────── 20px, Semibold
H3 Subsection ────────────────────────────── 15px, Semibold
Paragraph Text ───────────────────────────── 14px, Normal
Small/Label ───────────────────────────────── 12px, Normal
```

---

## Spacing System (4px Base)

```
4px   ·  (fine spacing)
8px   ··  (buttons, gaps)
12px  ···  (elements)
16px  ····  (padding)
20px  █  (padding)
24px  ███  (generous padding)
32px  ████  (large gaps)
40px  █████  (extra large)
48px  ██████  (section gaps)
```

**Common Usage**:
- Button padding: 8px vertical, 16px horizontal
- Card padding: 16px, 20px, or 24px
- Gap between items: 8px, 12px, or 16px
- Section padding: 24px (desktop), 16px (mobile)

---

## Component Library

### Buttons

```
PRIMARY BUTTON (Blue)
┌──────────────────────┐
│  ✐ Create           │  Background: #0052CC
└──────────────────────┘  Hover: #003DA5
Size: 10px vertical × 20px horizontal
Radius: 4px

SECONDARY BUTTON (White)
┌──────────────────────┐
│  ⊙ Filter           │  Background: White
└──────────────────────┘  Border: #DFE1E6
                          Hover: #F7F8FA
Size: 10px vertical × 20px horizontal
Radius: 4px

DANGER BUTTON (Red)
┌──────────────────────┐
│  🗑 Delete           │  Background: #ED3C32
└──────────────────────┘  Hover: darker red
Size: 10px vertical × 20px horizontal
Radius: 4px
```

### Cards

```
┌─────────────────────────────────────────┐
│ 🔷 Title                      [Badges]  │  ─ Header
├─────────────────────────────────────────┤
│ Description or content text              │  ─ Body
│ Multiple lines of text...                │
├─────────────────────────────────────────┤
│ Updated 2 days ago  [Action] [Menu]     │  ─ Footer
└─────────────────────────────────────────┘

Border: 1px #DFE1E6
Radius: 6px or 8px
Padding: 16px, 20px, or 24px
Shadow: 0 1px 3px rgba(9,30,66,0.13)
Hover: translateY(-3px), enhance shadow
```

### Badges

```
ISSUE TYPE BADGE          PRIORITY BADGE      STATUS BADGE
┌─────────────┐          ┌────┐              ┌─────────┐
│ 🔷 STORY    │          │ P1 │              │ OPEN    │
└─────────────┘          └────┘              └─────────┘
Color: Type-specific     Color: Red/Orange   Color: Blue
Text: White              Text: White         Text: White
Padding: 4px × 8px       Padding: varies     Padding: varies
Font: 11px, bold         Size: 26px × 26px   Font: 11px, bold
```

### Input Fields

```
FOCUS STATE              NORMAL STATE         ERROR STATE
┌─────────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│ Search... │         │ │ Search... │     │ │ Search... │     │
└─────────────────────┘ └─────────────────┘ └─────────────────┘
Border: #0052CC        Border: #DFE1E6    Border: #ED3C32
Shadow: blue glow      No shadow          No shadow
0 0 0 2px rgba(...)    

Padding: 12px 16px (large), 8px 12px (small)
Radius: 6px
Font: 14px
Transition: 0.2s
```

### Tables

```
┌──────┬──────────────┬─────────┬──────────┐
│ KEY  │ SUMMARY      │ TYPE    │ STATUS   │  ─ Header (#F7F8FA)
├──────┼──────────────┼─────────┼──────────┤
│ BP-1 │ Fix login... │ 🐛 Bug  │ 🔵 OPEN  │  ─ Row (white)
├──────┼──────────────┼─────────┼──────────┤
│ BP-2 │ Add cache... │ ⚙️ Task │ 🟢 DONE  │
├──────┼──────────────┼─────────┼──────────┤
│ BP-3 │ Design API..│ 📋 Epic │ 🟡 IN-PROGRESS
└──────┴──────────────┴─────────┴──────────┘

Header Font: 12px, 600, uppercase, #626F86
Row Padding: 12px 16px
Border: 1px #DFE1E6
Hover: #F7F8FA background
```

### Dropdowns/Panels

```
┌─ Projects ─────────────────────┐
├────────────────────────────────┤
│ 🔵 View All Projects          │  Header bg: #F7F8FA
├────────────────────────────────┤
│ 🎯 Project A                   │  Item padding: 10px 16px
│    5 issues, 3 members         │
├────────────────────────────────┤
│ + Create Project               │  Item hover: #F7F8FA
└────────────────────────────────┘

Min Width: 280px
Radius: 6px
Shadow: 0 4px 12px rgba(9,30,66,0.15)
Border: 1px #DFE1E6
Margin-top: 8px
```

### Navigation Breadcrumb

```
🏠 Home  /  Projects  /  Current Page
 ▲         ▲           ▲
Link      Link        Active (bold)
Color:    Color:      Color:
#0052CC   #0052CC     #161B22

Separator: gray, font-weight 300
Font: 13px
Hover: underline + darken
Padding: 12px 32px
Background: white
Border-bottom: 1px #DFE1E6
```

---

## Layout Patterns

### Pattern 1: Page with Header

```
┌────────────────────────────────────────────────┐
│ 🏠 Home / Section / Current                     │  Breadcrumb
├────────────────────────────────────────────────┤
│ Page Title                        [Filter] [+]  │  Header
│ Subtitle text                                   │
├────────────────────────────────────────────────┤
│                                                 │
│  Content Area                                   │  Main (bg: #F7F8FA)
│  Grid / Cards / Table / etc                     │
│                                                 │
└────────────────────────────────────────────────┘
```

### Pattern 2: With Sidebar

```
┌────────────────────────────────────────────────┐
│ [Header]                                        │
├─────────────────┬──────────────────────────────┤
│ Sidebar (300px) │ Main Content (flex: 1)       │
│                 │                              │
│ [Nav Items]     │ [Grid / Cards / etc]        │
│                 │                              │
│                 │                              │
└─────────────────┴──────────────────────────────┘
```

### Pattern 3: Tabs

```
┌────────────────────────────────────────────────┐
│ Details │ Activity │ Comments │ Files          │  Tabs
├────────────────────────────────────────────────┤
│                                                 │
│  Tab Content                                    │  Active tab content
│                                                 │
└────────────────────────────────────────────────┘
```

---

## Responsive Breakpoints

```
DESKTOP (1400px+)        LAPTOP (1024px)         TABLET (768px)         MOBILE (< 768px)
├─ Full width            ├─ 90% width            ├─ Full width           ├─ Full width
├─ 3-4 columns           ├─ 2-3 columns          ├─ 2 columns            ├─ 1 column
├─ 24px padding          ├─ 20px padding         ├─ 16px padding         ├─ 12px-16px padding
├─ Sidebar visible       ├─ Sidebar visible      ├─ Sidebar: stack       ├─ Sidebar: stack
├─ 360px cards           ├─ 300px cards          ├─ Full width cards     ├─ Full width cards
└─ Gap: 24px             └─ Gap: 20px            └─ Gap: 16px            └─ Gap: 12px
```

**Breakpoint CSS**:
```css
/* Mobile first (default for < 768px) */
padding: 16px;
columns: 1;

/* Tablet (768px+) */
@media (min-width: 768px) {
  padding: 16px 20px;
  columns: 2;
}

/* Laptop (1024px+) */
@media (min-width: 1024px) {
  padding: 20px 24px;
  columns: 3;
}

/* Desktop (1400px+) */
@media (min-width: 1400px) {
  padding: 24px 32px;
  columns: 4;
}
```

---

## Animation & Interaction

### Hover Effects

```
CARD HOVER                  BUTTON HOVER            LINK HOVER
┌─────────────────┐       ┌──────────────┐        Underline
│ ↑ translateY(-3px)       │ darker color │        Color: #0052CC
│ Shadow enhance │       │ same padding │        Transition: 0.2s
│ Scale stay 1.0  │       └──────────────┘
└─────────────────┘

Transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1)
```

### Loading States

```
BUTTON LOADING             INPUT LOADING          CARD SKELETON
┌──────────────┐         ┌─────────────────┐    ┌──────────────┐
│ ⟳ Loading... │         │ ⟳ Loading...    │    │ ████████████ │
└──────────────┘         └─────────────────┘    │ ████  ████   │
                                                 │ ██████████   │
Opacity: 0.6              Opacity: 0.6           └──────────────┘
Cursor: not-allowed       Disabled: true         Pulse animation
Disabled: true                                   
```

---

## Typography Examples

```
TITLE (Page Title)
The Projects Page
28px, 700, -0.3px letter-spacing

SECTION HEADER
Main Projects
20px, 600

SUBSECTION
Team Projects
15px, 600

BODY TEXT
This is the main content of the page. It uses a standard 14px font size
with a line height of 1.43 for optimal readability. Regular text content.
14px, 400, 1.43 line-height

SMALL TEXT / LABEL
Updated 2 hours ago
12px, 400, #626F86
```

---

## Color Usage Examples

```
PRIMARY ACTION                HOVER STATE             SECONDARY
┌─────────────┐             ┌─────────────┐        ┌──────────┐
│ Create      │             │ Create      │        │ Cancel   │
│ #0052CC     │  ──────→    │ #003DA5     │        │ White    │
│ White text  │             │ White text  │        │ Border   │
└─────────────┘             └─────────────┘        └──────────┘

DANGER                       DISABLED                LOADING
┌─────────────┐             ┌─────────────┐        ┌──────────┐
│ Delete      │             │ Submit      │        │ ⟳ Wait.. │
│ #ED3C32     │             │ Gray        │        │ Gray     │
│ White text  │             │ Cursor: not-│        │ Opacity  │
└─────────────┘             └─────────────┘        └──────────┘
```

---

## Shadow System

```
SHADOW-SM (Cards default)
0 1px 1px rgba(9, 30, 66, 0.13)

SHADOW-MD (Cards, inputs)
0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)

SHADOW-LG (Dropdowns, hover cards)
0 4px 12px rgba(9, 30, 66, 0.15)

SHADOW-XL (Modals, large overlays)
0 8px 16px rgba(9, 30, 66, 0.15)
```

---

## Icon Usage

```
ICONS IN TEXT              ICONS IN BUTTONS      ICONS STANDALONE
────────────────────      ─────────────────    ──────────────────
🔵 Search results         ┌──────────────┐     Icon Size: 56-72px
14px icon                 │ ✐ Create     │     Opacity: 0.5
Gap: 6px                  └──────────────┘     Color: #626F86
                          16px icon
                          Gap: 6px

ICON IN HEADER           ICON IN BADGE         ICON IN EMPTY STATE
────────────────        ──────────────       ──────────────────
🎯 Projects             🔷 STORY             📋 (72px)
24px icon               11px icon            "No items"
                        Gap: 4px             14px text
```

---

## Empty States

```
┌─────────────────────────────────┐
│                                 │
│          📋                      │
│      (56-72px emoji)             │
│                                 │
│      No issues found             │  Title: 14px, 500, #626F86
│                                 │  Text: 14px, 400, #626F86
│   Create your first issue to     │
│        get started.              │  Centered, flex column
│                                 │  Padding: 60px 20px
│       [+ Create Issue]           │  Button: Primary color
│                                 │
└─────────────────────────────────┘
```

---

## Component Size Reference

```
SMALL         MEDIUM        LARGE         EXTRA LARGE
─────────────────────────────────────────────────────
Button
32px height   36px height   40px height   48px height
12px pad      14px pad      16px pad      20px pad

Card
240px         300px         360px         450px
(width)       (width)       (width)       (width)

Avatar
24px          32px          40px          56px
(diameter)    (diameter)    (diameter)    (diameter)

Icon
12px          16px          20px          24px
(inline)      (buttons)     (headers)     (titles)
```

---

## Quick CSS Copy-Paste

### Card Component
```css
.card {
  background: #FFFFFF;
  border: 1px solid #DFE1E6;
  border-radius: 6px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover {
  box-shadow: 0 4px 12px rgba(9, 30, 66, 0.15);
  transform: translateY(-3px);
}
```

### Primary Button
```css
.btn-primary {
  background: #0052CC;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-primary:hover {
  background: #003DA5;
}
```

### Header Component
```css
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  background: #FFFFFF;
  border-bottom: 1px solid #DFE1E6;
  box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #161B22;
  margin: 0;
  letter-spacing: -0.3px;
}
```

---

## Design Checklist for New Pages

- [ ] Color palette matches (use CSS variables)
- [ ] Typography hierarchy correct (sizes and weights)
- [ ] Spacing multiples of 4px (4, 8, 12, 16, 20, 24, 32)
- [ ] Breadcrumb navigation at top
- [ ] Page header with title + actions
- [ ] Proper hover effects (0.2s transition)
- [ ] Cards/components match style
- [ ] Responsive design (3+ breakpoints)
- [ ] Focus states visible (blue outline or highlight)
- [ ] No hardcoded colors (use variables)
- [ ] No console errors
- [ ] Mobile touch-friendly (44px min height)
- [ ] Consistent icon sizing
- [ ] Proper button styling
- [ ] Accessible contrast (4.5:1 for text)

---

## Reference Pages Quick Links

| Page | Location | Use For |
|------|----------|---------|
| Navbar | views/layouts/app.php | Dropdown structure, icons, responsive |
| Board | views/projects/board.php | Cards, breadcrumb, header, grid |
| Issue | views/issues/show.php | Tabs, modals, complex layout |
| Projects | views/projects/index.php | Table, pagination, filters |
| Issues List | views/issues/index.php | Table styling, badges |

---

**Use this visual guide alongside DESIGN_PROMPT_MASTER_TEMPLATE.md for complete design consistency across all pages.**
