# GitHub Copilot Instructions

## CSS Architecture Guidelines

### **UNIFIED CSS SYSTEM - CRITICAL RULES**

Our application uses a **single unified CSS file**: `public/css/app-unified.css` (37.4KB)

**MANDATORY CSS WORKFLOW:**

1. **ALWAYS check existing styles first** - Search `app-unified.css` for existing classes before creating new ones
2. **DO NOT create new CSS files** - Everything goes in the unified file
3. **USE existing classes** - The file contains comprehensive styles for all components
4. **MINIMAL additions only** - Only add new styles if absolutely necessary
5. **MAINTAIN file size** - Keep the unified file lean and efficient

### **Available CSS Components in app-unified.css:**

- ✅ **Buttons:** `.btn-primary`, `.btn-secondary`, `.btn-success`, etc.
- ✅ **Cards:** `.card`, `.card-body`, `.card-header` with theme support
- ✅ **Forms:** `.form-control`, `.form-group`, input styling
- ✅ **Tables:** `.table`, `.table-striped`, `.table-hover`
- ✅ **Scanners:** `.scanner-container`, `.scanner-video`, `.scanner-overlay`
- ✅ **Navigation:** `.nav`, `.navbar`, `.sidebar` components
- ✅ **Modals:** `.modal`, `.modal-dialog`, `.modal-content`
- ✅ **Alerts:** `.alert`, `.alert-success`, `.alert-danger`
- ✅ **Layout:** Grid, flexbox, spacing utilities
- ✅ **Theme system:** CSS variables for light/dark mode
- ✅ **Sidebar:** `.theme-sidebar`, `.theme-sidebar-item` with hover effects
- ✅ **Navbar:** `.theme-navbar` with dropdown styling

### **Before Adding New Styles:**

1. Search the unified CSS file for similar patterns
2. Use existing utility classes and CSS variables
3. Check if Bootstrap classes can handle the requirement
4. Only add new styles if no existing solution works

### **CSS Variables Available:**

- `--primary`, `--secondary`, `--success`, `--info`, `--warning`, `--danger`
- `--bg-primary`, `--bg-secondary`, `--card-bg`, `--text-primary`
- `--spacing-xs`, `--spacing-sm`, `--spacing-md`, `--spacing-lg`
- `--border-radius`, `--theme-transition`

## Submission Process Rules

Whenever you are generating or modifying code related to a data submission process (e.g., forms, API calls, database saves), you must include clear alerts or console logs at every major step.

- **Before starting the submission:** Add a log or alert that states "Initiating submission..."
- **Upon successful completion:** Add an alert or log that clearly states "Submission successful!" with any relevant data.
- **For any errors:** Use a `try...catch` block. In the `catch` block, log the error and display an alert that says "Submission failed! Details: [error message]".

## Global Impact Assessment Rules

**CRITICAL:** Before making any changes anywhere in the codebase, always assess the global impact.

- **Check Global Scale Effects:** If a change affects multiple files, components, or system-wide functionality, identify ALL affected items
- **Impact Analysis Required:** For changes to:
  - CSS classes or variables (affects all files using them)
  - Database models or schema (affects all controllers and views)
  - Shared functions or utilities (affects all calling code)
  - API endpoints or routes (affects all frontend integrations)
  - Configuration files (affects entire application)
- **Protection Protocol:** When global impact is detected, take care of ALL affected items:
  - Update all references consistently
  - Test all affected functionality
  - Document breaking changes
  - Ensure backward compatibility where possible

## Development Rules

- Do not use commands which give an error: "Parse error: syntax error, unexpected token "\", expecting end of file in Command line code"
- Always reference the unified CSS file: `public/css/app-unified.css`
- Keep CSS architecture clean and maintainable
