---
applyTo: "*www*"
---

# UNIFIED CSS SYSTEM INSTRUCTIONS

**CRITICAL:** We use a single unified CSS file architecture. NO separate CSS files should be created.

## **DOCUMENTATION ORGANIZATION - MANDATORY**

**CRITICAL RULE:** All new `.md` files MUST be created in the `MD files/` folder.

- ✅ **Location:** `c:\wamp64\www\MD files\`
- ✅ **All documentation** - Technical docs, guides, summaries, reports
- ✅ **No exceptions** - Every new .md file goes in this folder
- ❌ **DO NOT create .md files** in root, docs/, or any other directory

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

## **Temporary Files Management:**

**CRITICAL:** For any test-related temporary files, use the designated `temp/` folder.

- ✅ **USE TEMP FOLDER:** Create test files in `c:\wamp64\www\temp\` (already in `.gitignore`)
- ✅ **Clean up after testing** - Remove temporary files when done
- ❌ **DO NOT leave test files** in root, app/, or other production directories
- ❌ **DO NOT commit temporary files** to the repository
