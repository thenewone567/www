---
applyTo: "*www*"
---

# UNIFIED CSS SYSTEM INSTRUCTIONS

**CRITICAL:** We use a single unified CSS file architecture. NO separate CSS files should be created.

## **MANDATORY CSS WORKFLOW:**

1. **ALWAYS search existing styles first** - Use `grep_search` or `read_file` to search `public/css/app-unified.css` for existing classes
2. **DO NOT create new CSS files** - Everything must go in the unified file
3. **USE existing utility classes** - The file contains comprehensive component styles
4. **MINIMAL additions only** - Only add new styles if absolutely necessary and no existing solution works
5. **MAINTAIN lean file size** - Current size is 27.8KB, keep it efficient

## **When Styling is Needed:**

1. **First:** Search `app-unified.css` for similar patterns or existing classes
2. **Second:** Check if Bootstrap classes can handle the requirement
3. **Third:** Use existing CSS variables (`--primary`, `--spacing-md`, etc.)
4. **Last Resort:** Add minimal new styles to the unified file

## **Available in app-unified.css:**

- Complete component library (buttons, cards, forms, tables, modals)
- Scanner interfaces (inventory & receiving)
- Theme system with CSS variables
- Responsive design utilities
- Animation classes
- Layout systems

## **CSS Variables to Use:**

- Colors: `--primary`, `--secondary`, `--success`, `--info`, `--warning`, `--danger`
- Backgrounds: `--bg-primary`, `--bg-secondary`, `--card-bg`
- Text: `--text-primary`, `--text-secondary`, `--text-muted`
- Spacing: `--spacing-xs`, `--spacing-sm`, `--spacing-md`, `--spacing-lg`
- Borders: `--border-radius`, `--border-radius-sm`
- Transitions: `--theme-transition`

## **File Reference:**

- **Main CSS:** `public/css/app-unified.css` (27.8KB)
- **Documentation:** `public/css/elementcssreffrence.md`
- **Load in views:** `<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">`
