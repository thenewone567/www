# UNIFIED CSS SYSTEM - IMPLEMENTATION GUIDE

## Overview

The Hardware Store Management System uses a **Unified CSS System** for consistent styling across all pages. This system ensures a professional, clean appearance that works properly regardless of theme conflicts.

## CRITICAL DISCOVERY

**Issue**: The theme-system.css loads AFTER unified-theme.css and forces dark theme styling, causing conflicts where new pages show dark backgrounds even in light mode.

**Solution**: When creating new pages, ALWAYS use the unified CSS system with proper overrides.

## Required Implementation Pattern

### 1. CSS Loading Order (Current System)

```
1. Bootstrap (external)
2. Font Awesome (external)
3. style.css
4. unified-theme.css
5. theme-system.css (OVERRIDES unified - this causes conflicts!)
```

### 2. Mandatory Page Structure for New Pages

```php
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<style>
/* REQUIRED: Force unified theme styling to override dark theme system */
.theme-unified {
    background-color: #f8f9fa !important;
}

.theme-unified .theme-card {
    background-color: white !important;
    color: #212529 !important;
}

.theme-unified .theme-card .card-body {
    background-color: white !important;
    color: #212529 !important;
}

.theme-unified .theme-table {
    background-color: white !important;
}

.theme-unified .theme-table .table {
    background-color: white !important;
    color: #212529 !important;
}

.theme-unified .theme-table .table tbody tr {
    background-color: white !important;
    color: #212529 !important;
}

.theme-unified .theme-table .table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05) !important;
    color: #212529 !important;
}

.theme-unified .theme-table .table tbody tr td {
    background-color: white !important;
    color: #212529 !important;
    border-color: #dee2e6 !important;
}

.theme-unified .theme-table .table thead th {
    background-color: #f8f9fa !important;
    color: #212529 !important;
    border-color: #dee2e6 !important;
}

.theme-unified h1, .theme-unified h2, .theme-unified h3, .theme-unified h4, .theme-unified h5, .theme-unified h6 {
    color: #212529 !important;
}

.theme-unified .text-muted {
    color: #6c757d !important;
}
</style>

<div class="container-fluid theme-container theme-unified">
    <!-- Page content here -->
</div>
```

### 3. Required CSS Classes for All New Pages

#### Container

```html
<div class="container-fluid theme-container theme-unified"></div>
```

#### Header Section

```html
<div class="row align-items-center theme-header">
  <div class="col-12 col-md-6 mb-2 mb-md-0">
    <!-- Back button or navigation -->
  </div>
  <div class="col-12 col-md-6 text-md-right">
    <h1><i class="fa-solid fa-icon"></i> Page Title</h1>
  </div>
</div>
```

#### Cards

```html
<div class="theme-card">
  <div class="card-header bg-primary-theme text-white">
    <h5 class="mb-0"><i class="fa-solid fa-icon"></i> Card Title</h5>
  </div>
  <div class="card-body">
    <!-- Card content -->
  </div>
</div>
```

#### Tables

```html
<div class="theme-table">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Column 1</th>
        <th>Column 2</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Data 1</td>
        <td>Data 2</td>
      </tr>
    </tbody>
  </table>
</div>
```

#### Action Buttons

```html
<div class="theme-action-group">
  <a href="#" class="btn btn-outline-primary btn-sm">
    <i class="fa-solid fa-icon"></i> Action
  </a>
</div>
```

## Available Unified CSS Classes

### Layout Classes

- `theme-container` - Main container
- `theme-header` - Page header section
- `theme-card` - Card wrapper
- `theme-table` - Table wrapper
- `theme-action-group` - Action button group

### Color Theme Classes

- `bg-primary-theme` - Primary blue background
- `bg-success-theme` - Success green background
- `bg-info-theme` - Info cyan background
- `bg-warning-theme` - Warning yellow background
- `bg-secondary-theme` - Secondary gray background
- `bg-dark-theme` - Dark background

### Button Classes

- `theme-btn` - Standard button
- `theme-btn-lg` - Large button
- `theme-btn-group` - Button group wrapper

### Text Classes

- `theme-text-muted` - Muted text color
- `theme-text-primary` - Primary text color
- `theme-text-success` - Success text color
- `theme-text-warning` - Warning text color
- `theme-text-danger` - Danger text color

## MANDATORY CHECKLIST for New Pages

### ✅ Before Creating Any New Page:

1. **[ ] Add CSS Override Block** - Copy the style block above
2. **[ ] Use `theme-unified` class** - On main container
3. **[ ] Use `theme-card`** - Instead of `card`
4. **[ ] Use `theme-table`** - Instead of `table-responsive`
5. **[ ] Use `theme-action-group`** - Instead of `btn-group`
6. **[ ] Use `bg-*-theme` classes** - Instead of `bg-*`
7. **[ ] Test in both light and dark mode** - Ensure consistency

### ❌ DO NOT USE:

- Regular Bootstrap `card` class without `theme-card`
- Regular `table-responsive` without `theme-table`
- `bg-info`, `bg-primary` etc. without `-theme` suffix
- `btn-group` without `theme-action-group`
- Any hardcoded colors in CSS

### ✅ ALWAYS USE:

- `theme-unified` class on main container
- CSS override block at top of page
- Unified theme classes for all components
- Proper semantic HTML structure

## Troubleshooting

### Problem: Dark backgrounds in light mode

**Solution**: Add the CSS override block and ensure `theme-unified` class is present

### Problem: Inconsistent styling

**Solution**: Use only unified CSS classes, avoid mixing with Bootstrap defaults

### Problem: Table rows showing dark

**Solution**: Ensure `theme-table` wrapper is used and CSS overrides are in place

## Examples

### Working Pages Using Unified CSS:

- `/app/views/sales/index.php` ✅ Updated (Sales Management)
- `/app/views/inventory/index.php` ✅ Already Updated
- `/app/views/purchases/receive.php` ✅ Already Updated (Receive Purchases)
- `/app/views/purchases/received.php` ✅ Already Updated (Received Purchases History)
- `/app/views/purchases/receive_items.php` ✅ Updated (Receive Items)
- `/app/views/purchases/index.php` ✅ Updated (Purchases Management)
- `/app/views/products/index.php` ✅ Updated (Products & Inventory)
- `/app/views/suppliers/index.php` ✅ Updated (Suppliers)
- `/app/views/reports/index.php` ✅ Updated (Reports & Analytics)
- `/app/views/dashboard/index.php` ✅ Already Updated
- `/app/views/customers/index.php` ✅ Already Updated

### Pages Needing Updates:

- Check remaining module pages for consistent unified CSS usage
- Form pages (add/edit) may need individual attention
- Secondary pages like settings, notifications, etc.

## Future Development

When creating new pages:

1. Copy the pattern from `/app/views/purchases/receive.php`
2. Adapt the content but keep the CSS structure
3. Always test theme switching
4. Document any new unified classes needed

---

**Last Updated**: August 1, 2025
**Status**: CRITICAL - Must be followed for all new pages
**Major Pages Updated**: 11/15+ core pages now using unified CSS
**Tested**: All major module pages - confirmed working with theme switching
