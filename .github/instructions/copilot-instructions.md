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

## Temporary / Test / Debug Files — Do not leave behind

We routinely find temporary, test, debug, and analysis artifacts left in the repository. These files increase repository noise, risk accidental commits of sensitive data, and complicate backups.

Rules:

- Do not commit temporary files (filename patterns like `*temp*`, `*tmp*`, `*debug*`, `*test*`, `*.log`, `*.zip` created for debugging/analysis) into source-controlled folders.
- If you must run local analysis or debugging that produces artifacts, keep them outside the repository workspace or in a clearly-named local-only folder that is listed in `.gitignore`.
- Use the provided cleanup script `scripts/archive_temp_files.ps1` to archive and remove temporary analysis artifacts when necessary. Review the archive before deleting it permanently.
- Prefer logging to the application `storage/logs` with rotation; avoid creating one-off logs in `scripts/` or repository root. If a debug log is created, move it to `storage/logs` or remove it when done.

Suggested `.gitignore` entries (add to the project root `.gitignore`):

```
# temporary / debug / analysis artifacts
*temp*
*tmp*
*debug*
*~debug*
*.log
*.zip
/.archived/
```

When removing temporary artifacts commit a short message explaining what was removed and why, e.g. "chore: remove archived temp-scripts.zip and debug logs".

If a change will affect other parts of the project (for example moving or deleting shared logs or archives), list the affected files and run a quick smoke test of related features.

## One-off maintenance scripts (check*/fix*/verify\*)

Scripts named with the `check*`, `fix*`, or `verify*` prefix are usually ad-hoc maintenance tools for debugging, diagnostics, or quick repairs. These should not remain in the main code tree as unreviewed, unmanaged artifacts.

Rules:

- Move these files to `archived/` for review before permanently deleting or committing them to the main branch.
- If the logic is reusable or required by the application, refactor the code into the application with tests, documentation, and appropriate access control. Do not keep production logic in standalone ad-hoc scripts.
- If the script is strictly one-time, keep it in `archived/` with a short note in `README_ARCHIVED_TEMP_FILES.md` and then delete it after review.
- Pull requests that introduce `check*/fix*/verify*` files must include a short justification and an assigned owner for maintenance.

## Database performance recommendations (dashboard)

The dashboard runs several aggregation queries over large tables (for example `sales`). For queries that compute the first sale per customer or that filter by `sale_date`, adding a composite index will significantly reduce execution time and avoid long-running PHP requests.

Recommended safe index to add (run on staging or locally, then deploy to production during a maintenance window):

```
ALTER TABLE sales ADD INDEX idx_customer_sale_date (customer_id, sale_date);
```

Notes:

- This index supports GROUP BY / MIN(sale_date) patterns and WHERE filtering by `sale_date` and `customer_id`.
- Create the index on a staging copy first to verify disk/time impact. For very large tables consider creating the index concurrently (MySQL 5.7+ supports online DDL with ALGORITHM=INPLACE) or using a maintenance window.
- After adding the index, re-run slow dashboard requests and remove any temporary long-execution workarounds.
