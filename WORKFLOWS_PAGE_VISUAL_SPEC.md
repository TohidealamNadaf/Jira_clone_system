# Workflows Page - Visual Specification Guide
**December 25, 2025 - Enterprise Design Reference**

---

## Page Layout Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│  Dashboard / Projects / CWAYS Project / Workflows                  │
│  [Breadcrumb Navigation]                                            │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│ Project Workflows                                    [Back Button]  │
│ Manage how issues progress from creation to resolution             │
│ [Page Header with Title & Subtitle]                                │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────────────┬──────────────────────────────────────────────┐
│                      │                                              │
│  Details             │  Active Workflows Card                       │
│  Access              │  ┌──────────────────────────────────────────┐
│  Components          │  │ Workflow 1  | Issue Types | Status | ... │
│  Versions            │  ├──────────────────────────────────────────┤
│  Workflows (active)  │  │ Workflow 2  | ...                        │
│                      │  │ Workflow 3  | ...                        │
│  [Sidebar Nav]       │  └──────────────────────────────────────────┘
│                      │
│                      │  Understanding Workflows Card
│                      │  ┌──────────────────────────────────────────┐
│                      │  │ [Icon] Shared Workflows    [Icon] Admin.. │
│                      │  └──────────────────────────────────────────┘
└──────────────────────┴──────────────────────────────────────────────┘
```

---

## Breadcrumb Navigation (Top)

### Structure
```
🏠 Dashboard / 📁 Projects / CWAYS / Workflows
```

### Specifications
| Property | Value |
|----------|-------|
| **Background** | White (#FFFFFF) |
| **Border** | 1px bottom, #DFE1E6 |
| **Padding** | 12px 32px |
| **Height** | ~40px |
| **Font Size** | 13px |
| **Font Weight** | 500 |
| **Gap Between Items** | 8px |
| **Link Color** | #8B1956 (plum) |
| **Link Hover** | #6F123F + underline |
| **Separator Color** | #626F86 (50% opacity) |
| **Current Text** | #161B22 (bold, non-clickable) |

### Elements
1. **Home Link**: 🏠 icon + "Dashboard"
2. **Projects Link**: 📁 icon + "Projects"
3. **Project Link**: [Project Name] (from data)
4. **Current Page**: "Workflows" (plain text)

---

## Page Header Section

### Layout
```
┌─────────────────────────────────────────────────────────────────┐
│  [Icon] Project Workflows                    [Back Button]      │
│         Manage how issues progress...                           │
└─────────────────────────────────────────────────────────────────┘
```

### Left Column
| Property | Value |
|----------|-------|
| **Title Font Size** | 32px |
| **Title Weight** | 700 |
| **Title Color** | #161B22 |
| **Title Letter Spacing** | -0.2px |
| **Subtitle Font Size** | 15px |
| **Subtitle Color** | #626F86 |
| **Subtitle Margin** | 8px top |

### Right Column
| Property | Value |
|----------|-------|
| **Button Background** | White |
| **Button Border** | 1px #DFE1E6 |
| **Button Color** | #161B22 |
| **Button Padding** | 10px 16px |
| **Button Font Size** | 13px |
| **Button Border Radius** | 6px |
| **Button Hover Background** | #F7F8FA |
| **Button Hover Border** | #8B1956 |
| **Button Hover Color** | #8B1956 |
| **Button Hover Transform** | translateY(-1px) |

### Overall
| Property | Value |
|----------|-------|
| **Background** | White |
| **Border Bottom** | 1px #DFE1E6 |
| **Padding** | 32px (desktop), 20px (tablet), 16px (mobile) |
| **Display** | Flex |
| **Justify Content** | space-between |
| **Align Items** | flex-start |
| **Gap** | 24px |

---

## Sidebar Navigation

### Container
| Property | Value |
|----------|-------|
| **Width** | 260px |
| **Background** | White |
| **Border** | 1px #DFE1E6 |
| **Border Radius** | 8px |
| **Box Shadow** | 0 1px 3px rgba(0,0,0,0.08) |

### Navigation Items
| Property | Value |
|----------|-------|
| **Padding** | 12px 16px |
| **Font Size** | 13px |
| **Font Weight** | 500 |
| **Color** | #626F86 |
| **Text Decoration** | none |
| **Border Bottom** | 1px #DFE1E6 |
| **Display** | flex |
| **Gap** | 12px |
| **Align Items** | center |

### Item Icon
| Property | Value |
|----------|-------|
| **Font Size** | 16px |
| **Color** | inherit |

### Item Hover
| Property | Value |
|----------|-------|
| **Background** | #F7F8FA |
| **Color** | #8B1956 |
| **Padding Left** | 18px |
| **Transition** | 0.2s cubic-bezier(0.4, 0, 0.2, 1) |

### Active Item
| Property | Value |
|----------|-------|
| **Background** | #F7F8FA |
| **Color** | #8B1956 |
| **Border Left** | 3px #8B1956 |
| **Padding Left** | 13px |

---

## Card Components

### Card Container
| Property | Value |
|----------|-------|
| **Background** | White |
| **Border** | 1px #DFE1E6 |
| **Border Radius** | 8px |
| **Box Shadow** | 0 1px 3px rgba(0,0,0,0.08) |
| **Overflow** | hidden |

### Card Hover
| Property | Value |
|----------|-------|
| **Border Color** | #8B1956 |
| **Box Shadow** | 0 4px 12px rgba(0,0,0,0.08) |
| **Transition** | 0.2s |

### Card Header
| Property | Value |
|----------|-------|
| **Background** | #F7F8FA |
| **Border Bottom** | 1px #DFE1E6 |
| **Padding** | 20px |
| **Display** | flex |

### Card Title (h2)
| Property | Value |
|----------|-------|
| **Font Size** | 16px |
| **Font Weight** | 700 |
| **Color** | #161B22 |
| **Margin** | 0 0 4px 0 |

### Card Subtitle
| Property | Value |
|----------|-------|
| **Font Size** | 13px |
| **Color** | #626F86 |
| **Margin** | 0 |

### Card Body
| Property | Value |
|----------|-------|
| **Padding** | 24px |

---

## Table Design

### Table Container
| Property | Value |
|----------|-------|
| **Overflow** | auto (for horizontal scroll on mobile) |

### Table Element
| Property | Value |
|----------|-------|
| **Width** | 100% |
| **Border Collapse** | collapse |
| **Font Size** | 13px |

### Table Head
| Property | Value |
|----------|-------|
| **Background** | #F7F8FA |
| **Border Bottom** | 2px #DFE1E6 |

### Table Headers (th)
| Property | Value |
|----------|-------|
| **Padding** | 12px 16px |
| **Text Align** | left |
| **Font Weight** | 600 |
| **Color** | #626F86 |
| **Font Size** | 12px |
| **Text Transform** | uppercase |
| **Letter Spacing** | 0.5px |

### Table Rows (tr)
| Property | Value |
|----------|-------|
| **Border Bottom** | 1px #DFE1E6 |
| **Transition** | background-color 0.2s |

### Table Row Hover
| Property | Value |
|----------|-------|
| **Background** | #F7F8FA |

### Table Data (td)
| Property | Value |
|----------|-------|
| **Padding** | 16px |
| **Vertical Align** | middle |

---

## Workflow Row Content

### Workflow Info Block
```
[40px Icon] Workflow Name
             Some description text
             [System Badge if default]
```

### Icon Container
| Property | Value |
|----------|-------|
| **Width** | 40px |
| **Height** | 40px |
| **Background** | #F7F8FA |
| **Border Radius** | 6px |
| **Display** | flex |
| **Align Items** | center |
| **Justify Content** | center |
| **Color** | #8B1956 |
| **Font Size** | 18px |

### Workflow Name
| Property | Value |
|----------|-------|
| **Font Weight** | 600 |
| **Color** | #161B22 |
| **Margin Bottom** | 4px |
| **Display** | flex |
| **Align Items** | center |
| **Gap** | 8px |

### Workflow Description
| Property | Value |
|----------|-------|
| **Font Size** | 12px |
| **Color** | #626F86 |
| **Margin** | 0 |

---

## Badge Styles

### Default Badge (System Workflow)
| Property | Value |
|----------|-------|
| **Background** | transparent |
| **Color** | #8B1956 |
| **Border** | 1px #8B1956 |
| **Padding** | 4px 12px |
| **Border Radius** | 12px |
| **Font Size** | 12px |
| **Font Weight** | 600 |

### Primary Badge (Issue Type)
| Property | Value |
|----------|-------|
| **Background** | #8B1956 |
| **Color** | white |
| **Padding** | 4px 12px |
| **Border Radius** | 12px |
| **Font Size** | 12px |
| **Font Weight** | 600 |

### Outline Badge (All Types)
| Property | Value |
|----------|-------|
| **Background** | transparent |
| **Color** | #626F86 |
| **Border** | 1px #DFE1E6 |
| **Padding** | 4px 12px |
| **Border Radius** | 12px |

---

## Status Badge

### Active Status
| Property | Value |
|----------|-------|
| **Background** | rgba(33, 110, 78, 0.1) |
| **Color** | #216E4E |
| **Display** | inline-flex |
| **Align Items** | center |
| **Gap** | 6px |
| **Padding** | 4px 12px |
| **Border Radius** | 4px |
| **Font Size** | 12px |
| **Font Weight** | 600 |
| **Dot Color** | #216E4E |
| **Dot Size** | 6px |

### Inactive Status
| Property | Value |
|----------|-------|
| **Background** | rgba(231, 120, 23, 0.1) |
| **Color** | #E77817 |
| **Display** | inline-flex |
| **Align Items** | center |
| **Gap** | 6px |
| **Padding** | 4px 12px |
| **Border Radius** | 4px |
| **Font Size** | 12px |
| **Font Weight** | 600 |
| **Dot Color** | #E77817 |
| **Dot Size** | 6px |

---

## Empty State

### Container
| Property | Value |
|----------|-------|
| **Text Align** | center |
| **Padding** | 60px 40px |

### Icon
| Property | Value |
|----------|-------|
| **Font Size** | 48px |
| **Margin Bottom** | 16px |
| **Opacity** | 0.5 |

### Title
| Property | Value |
|----------|-------|
| **Font Size** | 18px |
| **Font Weight** | 600 |
| **Color** | #161B22 |
| **Margin** | 0 0 8px 0 |

### Text
| Property | Value |
|----------|-------|
| **Font Size** | 14px |
| **Color** | #626F86 |
| **Margin** | 0 auto |
| **Max Width** | 400px |

---

## Info Grid (Understanding Workflows)

### Grid Container
| Property | Value |
|----------|-------|
| **Display** | grid |
| **Grid Template Columns** | 1fr 1fr |
| **Gap** | 20px |

### Info Block
| Property | Value |
|----------|-------|
| **Display** | flex |
| **Gap** | 16px |
| **Padding** | 20px |
| **Background** | #F7F8FA |
| **Border Radius** | 6px |
| **Border** | 1px #DFE1E6 |

### Info Icon
| Property | Value |
|----------|-------|
| **Width** | 44px |
| **Height** | 44px |
| **Background** | white |
| **Border Radius** | 6px |
| **Display** | flex |
| **Align Items** | center |
| **Justify Content** | center |
| **Color** | #8B1956 |
| **Font Size** | 20px |
| **Flex Shrink** | 0 |

### Info Content h3
| Property | Value |
|----------|-------|
| **Font Size** | 14px |
| **Font Weight** | 600 |
| **Color** | #161B22 |
| **Margin** | 0 0 8px 0 |

### Info Content p
| Property | Value |
|----------|-------|
| **Font Size** | 13px |
| **Color** | #626F86 |
| **Margin** | 0 |
| **Line Height** | 1.5 |

---

## Responsive Adjustments

### Tablet (768px - 1024px)
```
Sidebar: Full width, horizontal nav grid
Padding: 20px
Font sizes: -1px reduction
Info Grid: Still 2 columns
```

### Mobile (480px - 768px)
```
Sidebar: Single column nav
Padding: 16px
Table: Status column hidden (3 columns visible)
Info Grid: 1 column
Font sizes: -2px reduction
```

### Small Mobile (< 480px)
```
Sidebar: Single column nav
Padding: 12px
Table: Minimum 400px with horizontal scroll
Info Grid: Full width stacked
Font sizes: -2px reduction
Breadcrumb: -1px font size
```

---

## Spacing Scale (Used Throughout)

| Spacing | Value |
|---------|-------|
| **xs** | 4px |
| **sm** | 8px |
| **md** | 12px |
| **lg** | 16px |
| **xl** | 20px |
| **2xl** | 24px |
| **3xl** | 32px |

---

## Animation/Transitions

### Standard Transition
```css
transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
```

### Hover Effects
- **Links**: Color change + underline (0.2s)
- **Buttons**: Background + border + transform (0.2s)
- **Cards**: Border + shadow (0.2s)
- **Table Rows**: Background color (0.2s)

### Transform on Hover
```css
transform: translateY(-1px); /* Lift effect */
```

---

## Font Stack

```css
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
```

### Font Weights Used
- 400: Regular text, descriptions
- 500: Meta text, breadcrumb links
- 600: Headers, sidebar items, badges
- 700: Page title, card titles

---

## Shadow System

| Level | Value |
|-------|-------|
| **sm** | 0 1px 3px rgba(0,0,0,0.08) |
| **md** | 0 4px 12px rgba(0,0,0,0.08) |
| **lg** | 0 8px 24px rgba(0,0,0,0.12) |

---

## Accessibility Specs

| Aspect | Requirement |
|--------|-------------|
| **Color Contrast** | 7:1 (WCAG AAA) |
| **Touch Targets** | Minimum 44x44px |
| **Focus States** | Visible outline |
| **Semantic HTML** | Proper structure |
| **Keyboard Nav** | All interactive elements accessible |
| **ARIA Labels** | Where needed for assistive tech |

---

## Browser Rendering Notes

✅ **Flex Layout**: All major browsers support `display: flex`  
✅ **CSS Grid**: Used for info grid, supported in all modern browsers  
✅ **CSS Variables**: `:root` scoped variables supported  
✅ **Border Radius**: All values work across browsers  
✅ **Box Shadow**: Full support  
✅ **Transitions**: All modern browsers  
✅ **Transform**: translateY supported everywhere  
✅ **Opacity**: Standard support  

---

## Print Styles (Not Implemented)

For future enhancement:
- Hide buttons/actions
- Full width for tables
- Black text only
- Remove colors

---

**Visual Specification Complete**  
Use this guide for reference when making future adjustments  
All measurements in pixels unless otherwise noted

