# TEMP FOLDER USAGE GUIDELINES

## **CRITICAL RULE: USE TEMP FOLDER FOR TEST FILES**

All test-related temporary files MUST be created in the designated `temp/` folder.

### **Location:**

```
c:\wamp64\www\temp\
```

### **Purpose:**

- ✅ **Test scripts** - Any debugging or testing PHP/HTML/JS files
- ✅ **Temporary data** - Test output, logs, analysis results
- ✅ **Debug files** - Diagnostic scripts and debug output
- ✅ **Experimental code** - Proof-of-concept files
- ✅ **Analysis artifacts** - Data dumps, reports, investigations

### **Benefits:**

- **Git Ignored** - Files in `temp/` are automatically excluded from version control
- **Clean Repository** - Keeps root directory and app folders clean
- **Easy Cleanup** - Can safely delete entire `temp/` folder contents
- **Organized Testing** - Centralized location for all temporary files
- **Production Safety** - Prevents accidental deployment of test files

## **USAGE EXAMPLES:**

### **✅ CORRECT Usage:**

```
temp/test_customer_analysis.php
temp/debug_database_connection.php
temp/sample_data_export.csv
temp/api_test_results.json
temp/performance_analysis.log
```

### **❌ INCORRECT Usage:**

```
root/test_something.php           ❌ Don't put in root
app/debug_controller.php          ❌ Don't put in app folders
public/temp_styles.css            ❌ Don't put in public
scripts/one_time_fix.php          ❌ Don't put in scripts
```

## **WORKFLOW:**

### **When Creating Test Files:**

1. **Always use temp folder:** `c:\wamp64\www\temp\filename.php`
2. **Use descriptive names:** Include purpose and date if needed
3. **Clean up when done:** Remove files after testing is complete

### **When Debugging:**

1. Create test script in `temp/debug_issue_name.php`
2. Run tests and analyze results
3. Move any useful code to proper app structure
4. Delete temporary files

### **When Experimenting:**

1. Create proof-of-concept in `temp/poc_feature_name.php`
2. Test functionality thoroughly
3. Integrate successful code into app architecture
4. Remove experimental files

## **GITIGNORE PROTECTION:**

The `temp/` folder is protected by `.gitignore`:

```gitignore
# Temporary test files
/temp/
```

This ensures that:

- ✅ Test files are never accidentally committed
- ✅ Repository stays clean and professional
- ✅ No sensitive debugging data is exposed
- ✅ Collaborators don't see temporary artifacts

## **CLEANUP GUIDELINES:**

### **Regular Maintenance:**

- **Weekly:** Review `temp/` folder contents
- **Monthly:** Clean up old experimental files
- **Before Commits:** Ensure no temporary logic is in main app

### **Safe Cleanup Commands:**

```powershell
# List temp folder contents
Get-ChildItem temp\

# Remove all files in temp (safe operation)
Remove-Item temp\* -Force -Recurse

# Keep folder structure, remove only files
Get-ChildItem temp\ -File | Remove-Item -Force
```

## **ENFORCEMENT:**

This rule is enforced through:

- **GitHub Copilot Instructions** - AI assistant follows these guidelines
- **Git Ignore** - Prevents accidental commits
- **Code Review Process** - Manual verification during reviews
- **Documentation Standards** - Clear guidelines for all developers

## **EXAMPLES OF PROPER TEMP USAGE:**

### **Database Testing:**

```php
// temp/test_database_connection.php
<?php
require_once '../bootstrap.php';
// Test database operations safely
?>
```

### **API Testing:**

```php
// temp/test_api_endpoints.php
<?php
// Test API calls and responses
?>
```

### **Performance Analysis:**

```php
// temp/analyze_query_performance.php
<?php
// Measure and analyze database query performance
?>
```

**Remember:** The `temp/` folder is your sandbox for safe experimentation without cluttering the main codebase!
