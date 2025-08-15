# CSS Development Rules & Guidelines

## 🚫 **NEVER DO THIS:**

- ❌ Add `<style>` tags in PHP files
- ❌ Write inline CSS in HTML attributes
- ❌ Create new CSS files without approval
- ❌ Use hardcoded colors or values
- ❌ Copy CSS from external sources without theming

## ✅ **ALWAYS DO THIS:**

### 1. **Use Theme System Classes First**

Before writing ANY new CSS, check if these existing classes can be used:

#### **Component Classes:**

- `.kpi-card` - For dashboard cards with metrics
- `.enhanced-table` - For styled data tables
- `.quick-action-btn` - For action buttons with variants (.primary, .success, .warning, .danger)
- `.status-badge` - For status indicators (.status-active, .status-pending, .status-inactive, .status-processing)
- `.enhanced-search` - For search input containers
- `.responsive-grid` - For responsive grid layouts
- `.workflow-tabs`, `.workflow-nav`, `.workflow-section`, `.workflow-tab` - For workflow navigation
- `.activity-feed`, `.activity-item` - For activity lists
- `.metric-card` - For dashboard metrics
- `.product-result-card`, `.location-result-card` - For search results
- `.scanner-container` - For barcode scanner areas

#### **Utility Classes:**

- `.chart-container` - For chart/graph containers
- `.chart-title` - For chart headings
- `.suggestion-list`, `.priority-list` - For suggestion/priority lists
- `.priority-item.urgent/.high/.medium` - For priority indicators

### 2. **Use CSS Variables for All Values**

```css
/* ✅ CORRECT */
background: var(--card-bg) !important;
color: var(--text-primary) !important;
border: 1px solid var(--card-border) !important;

/* ❌ WRONG */
background: #ffffff;
color: #333333;
border: 1px solid #e0e0e0;
```

#### **Available CSS Variables:**

```css
/* Colors */
--primary, --success, --warning, --danger, --info
--text-primary, --text-muted
--card-bg, --card-border, --card-shadow, --card-header-bg
--bg-tertiary, --input-bg, --input-border

/* Transitions */
--theme-transition

/* Backgrounds */
--success-bg, --warning-bg, --danger-bg, --info-bg
--success-border, --warning-border, --danger-border, --info-border
--success-color, --warning-color, --danger-color, --info-color
```

### 3. **Add New Styles to theme-system.css**

When you MUST add new CSS:

1. **Open:** `c:\wamp64\www\public\css\theme-system.css`
2. **Find appropriate section** or create new one with proper heading
3. **Use theme variables** for all values
4. **Follow naming conventions**: `.component-name`, `.component-element`
5. **Add responsive rules** if needed
6. **Test in both light and dark themes**

#### **CSS Structure Template:**

```css
/* ==============================================
   [COMPONENT NAME] SYSTEM
   ============================================== */

.component-name {
  background: var(--card-bg) !important;
  border: 1px solid var(--card-border) !important;
  color: var(--text-primary) !important;
  transition: var(--theme-transition);
}

.component-name:hover {
  background: var(--bg-tertiary) !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px var(--card-shadow) !important;
}

/* Responsive */
@media (max-width: 768px) {
  .component-name {
    /* Mobile styles */
  }
}
```

### 4. **Remove Inline Styles**

If you find existing inline `<style>` tags:

1. **Extract** the CSS rules
2. **Convert** hardcoded values to theme variables
3. **Add** to appropriate section in theme-system.css
4. **Remove** the `<style>` block completely
5. **Test** functionality

## 📋 **Development Checklist**

Before committing any CSS changes:

- [ ] ✅ No `<style>` tags in PHP files
- [ ] ✅ All colors use CSS variables
- [ ] ✅ Used existing theme classes where possible
- [ ] ✅ Added new styles to theme-system.css
- [ ] ✅ Responsive design included
- [ ] ✅ Tested in light AND dark themes
- [ ] ✅ No hardcoded values
- [ ] ✅ Follows naming conventions

## 🎯 **Theme System Benefits**

By following these rules:

- **Consistent** appearance across all pages
- **Automatic** dark/light theme support
- **Maintainable** codebase
- **Responsive** design out of the box
- **Professional** appearance
- **Easy** future updates

## 🚨 **Emergency Override**

If you absolutely MUST add inline CSS (rare emergency):

1. **Add comment**: `<!-- TODO: Move to theme-system.css -->`
2. **Create GitHub issue** to track
3. **Fix within 24 hours**

## 📝 **Examples**

### ✅ **Good Implementation:**

```html
<div class="kpi-card">
  <div class="kpi-value">1,234</div>
  <div class="kpi-label">Total Products</div>
</div>
```

### ❌ **Bad Implementation:**

```html
<style>
  .my-card {
    background: #fff;
    border: 1px solid #ddd;
  }
</style>
<div class="my-card">Content</div>
```

---

**Remember: Theme consistency = Professional appearance = Happy users! 🎨**
