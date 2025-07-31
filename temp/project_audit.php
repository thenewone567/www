<?php
/**
 * Comprehensive Project Audit Script
 * Checks for bugs, inconsistencies, and structural issues
 */

echo "<h1>🔍 Hardware Store Project - Comprehensive Audit</h1>";

// 1. Check project structure
echo "<h2>📁 Project Structure Analysis</h2>";

$rootFiles = scandir('.');
$importantFiles = ['index.php', 'composer.json', 'README.md', '.htaccess'];
$tempFiles = [];
$setupFiles = [];

foreach ($rootFiles as $file) {
    if ($file === '.' || $file === '..') continue;
    
    if (is_file($file)) {
        if (strpos($file, 'test_') === 0 || strpos($file, 'check_') === 0 || 
            strpos($file, 'setup_') === 0 || strpos($file, 'debug_') === 0 ||
            strpos($file, 'fix_') === 0 || strpos($file, 'final_') === 0) {
            $tempFiles[] = $file;
        }
        
        if (strpos($file, '.sql') !== false || strpos($file, '.php') !== false) {
            $setupFiles[] = $file;
        }
    }
}

echo "🗂️ <strong>Root Directory Files:</strong><br>";
echo "- Total files in root: " . count(array_filter($rootFiles, 'is_file')) . "<br>";
echo "- Temporary/Debug files: " . count($tempFiles) . "<br>";

if (count($tempFiles) > 0) {
    echo "<details><summary>📋 Temporary files found (should be cleaned):</summary>";
    foreach ($tempFiles as $file) {
        echo "- $file<br>";
    }
    echo "</details>";
}

// 2. Check app structure
echo "<h2>🏗️ Application Structure</h2>";

$appDirs = ['controllers', 'models', 'views'];
foreach ($appDirs as $dir) {
    $path = "app/$dir";
    if (is_dir($path)) {
        $files = array_filter(scandir($path), function($f) { return $f !== '.' && $f !== '..'; });
        echo "✅ $dir: " . count($files) . " files<br>";
    } else {
        echo "❌ Missing: $path<br>";
    }
}

// 3. Check for PHP syntax errors
echo "<h2>🐛 PHP Syntax Check</h2>";

function checkPHPSyntax($file) {
    $output = [];
    $return = 0;
    exec("php -l \"$file\" 2>&1", $output, $return);
    return $return === 0;
}

$phpFiles = [];
$directories = ['app/controllers', 'app/models', 'app/views'];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $files = glob("$dir/*.php");
        $phpFiles = array_merge($phpFiles, $files);
    }
}

// Add root PHP files
$rootPhpFiles = glob("*.php");
$phpFiles = array_merge($phpFiles, $rootPhpFiles);

$syntaxErrors = [];
$checkedCount = 0;

foreach ($phpFiles as $file) {
    if (is_file($file)) {
        $checkedCount++;
        if (!checkPHPSyntax($file)) {
            $syntaxErrors[] = $file;
        }
    }
}

echo "📊 Checked $checkedCount PHP files<br>";
if (count($syntaxErrors) > 0) {
    echo "❌ Files with syntax errors:<br>";
    foreach ($syntaxErrors as $file) {
        echo "- $file<br>";
    }
} else {
    echo "✅ No syntax errors found<br>";
}

// 4. Check for deprecated PHP features
echo "<h2>⚠️ Deprecated Code Check</h2>";

$deprecatedPatterns = [
    'FILTER_SANITIZE_STRING' => 'Should use FILTER_SANITIZE_FULL_SPECIAL_CHARS',
    'mysql_' => 'Should use PDO or MySQLi',
    'each(' => 'Deprecated in PHP 7.2+',
    'create_function' => 'Use anonymous functions',
    '__autoload' => 'Use spl_autoload_register'
];

$deprecatedFound = [];

foreach ($phpFiles as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        foreach ($deprecatedPatterns as $pattern => $suggestion) {
            if (strpos($content, $pattern) !== false) {
                $deprecatedFound[$pattern][] = $file;
            }
        }
    }
}

if (count($deprecatedFound) > 0) {
    foreach ($deprecatedFound as $pattern => $files) {
        echo "⚠️ Found '$pattern' in " . count($files) . " files<br>";
    }
} else {
    echo "✅ No deprecated patterns found<br>";
}

// 5. Check database consistency
echo "<h2>🗄️ Database Consistency Check</h2>";

try {
    require_once 'app/config.php';
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful<br>";
    
    // Check tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Found " . count($tables) . " tables<br>";
    
    // Check for common table issues
    $tableIssues = [];
    
    foreach ($tables as $table) {
        // Check for tables without primary keys
        $primaryKeys = $pdo->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'")->fetchAll();
        if (count($primaryKeys) === 0) {
            $tableIssues['no_primary_key'][] = $table;
        }
        
        // Check for empty tables
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        if ($count == 0) {
            $tableIssues['empty_tables'][] = $table;
        }
    }
    
    if (isset($tableIssues['no_primary_key'])) {
        echo "⚠️ Tables without primary keys: " . implode(', ', $tableIssues['no_primary_key']) . "<br>";
    }
    
    if (isset($tableIssues['empty_tables'])) {
        echo "ℹ️ Empty tables: " . implode(', ', $tableIssues['empty_tables']) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database check failed: " . $e->getMessage() . "<br>";
}

// 6. Check for missing dependencies
echo "<h2>📦 Dependencies Check</h2>";

if (file_exists('composer.json')) {
    echo "✅ composer.json found<br>";
    $composer = json_decode(file_get_contents('composer.json'), true);
    if (isset($composer['require'])) {
        echo "📋 Dependencies: " . count($composer['require']) . "<br>";
    }
    
    if (!is_dir('vendor')) {
        echo "⚠️ vendor/ directory not found - run 'composer install'<br>";
    } else {
        echo "✅ vendor/ directory exists<br>";
    }
} else {
    echo "ℹ️ No composer.json found<br>";
}

// 7. Security checks
echo "<h2>🔒 Security Check</h2>";

$securityIssues = [];

foreach ($phpFiles as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        
        // Check for potential SQL injection
        if (preg_match('/\$_[GET|POST|REQUEST].*?mysql_query|query\s*\(/i', $content)) {
            $securityIssues['sql_injection'][] = $file;
        }
        
        // Check for eval usage
        if (strpos($content, 'eval(') !== false) {
            $securityIssues['eval_usage'][] = $file;
        }
        
        // Check for direct superglobal usage without sanitization
        if (preg_match('/echo\s+\$_[GET|POST|REQUEST]/i', $content)) {
            $securityIssues['xss_risk'][] = $file;
        }
    }
}

if (count($securityIssues) > 0) {
    foreach ($securityIssues as $issue => $files) {
        echo "⚠️ $issue found in " . count($files) . " files<br>";
    }
} else {
    echo "✅ No obvious security issues found<br>";
}

echo "<h2>📋 Audit Summary & Recommendations</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px;'>";
echo "<h3>🔧 Immediate Actions Needed:</h3>";
echo "<ol>";
echo "<li><strong>Clean up temporary files</strong> - Remove debug/test files from root</li>";
echo "<li><strong>Organize project structure</strong> - Move setup files to dedicated folder</li>";
echo "<li><strong>Fix any syntax errors</strong> - Ensure all PHP files are valid</li>";
echo "<li><strong>Update deprecated code</strong> - Replace old PHP patterns</li>";
echo "<li><strong>Database optimization</strong> - Add missing primary keys, optimize tables</li>";
echo "</ol>";

echo "<h3>🏗️ Restructuring Recommendations:</h3>";
echo "<ul>";
echo "<li>Create <code>database/</code> folder for SQL files and migrations</li>";
echo "<li>Create <code>scripts/</code> folder for setup and utility scripts</li>";
echo "<li>Create <code>docs/</code> folder for documentation</li>";
echo "<li>Create <code>config/</code> folder for configuration files</li>";
echo "<li>Implement proper routing with clean URLs</li>";
echo "<li>Add error handling and logging system</li>";
echo "</ul>";
echo "</div>";

?>
