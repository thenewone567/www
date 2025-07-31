<?php
/**
 * Project Reorganization and Cleanup Script
 * This script will restructure the project and fix major issues
 */

echo "<h1>🔧 Hardware Store Project - Reorganization & Cleanup</h1>";

// Step 1: Create proper directory structure
echo "<h2>📁 Step 1: Creating Proper Directory Structure</h2>";

$directories = [
    'database/migrations',
    'database/seeds', 
    'scripts/setup',
    'scripts/utilities',
    'docs',
    'config',
    'storage/logs',
    'storage/uploads',
    'temp'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created: $dir<br>";
        } else {
            echo "❌ Failed to create: $dir<br>";
        }
    } else {
        echo "ℹ️ Exists: $dir<br>";
    }
}

// Step 2: Move temporary/debug files
echo "<h2>🧹 Step 2: Moving Temporary Files</h2>";

$tempFiles = [
    'check_poi_structure.php',
    'check_purchase_tables.php', 
    'debug_purchase_query.php',
    'final_purchase_test.php',
    'fix_deprecated_constants.php',
    'setup_purchase_orders.php',
    'test_exact_query.php',
    'test_purchase_model.php',
    'test_purchase_system.php',
    'project_audit.php'
];

foreach ($tempFiles as $file) {
    if (file_exists($file)) {
        $destination = "temp/$file";
        if (rename($file, $destination)) {
            echo "✅ Moved: $file → temp/<br>";
        } else {
            echo "❌ Failed to move: $file<br>";
        }
    }
}

// Step 3: Move SQL files to database directory
echo "<h2>🗄️ Step 3: Moving Database Files</h2>";

$sqlFiles = glob("*.sql");
foreach ($sqlFiles as $file) {
    $destination = "database/migrations/$file";
    if (copy($file, $destination)) {
        unlink($file);
        echo "✅ Moved: $file → database/migrations/<br>";
    } else {
        echo "❌ Failed to move: $file<br>";
    }
}

// Step 4: Move setup scripts
echo "<h2>⚙️ Step 4: Moving Setup Scripts</h2>";

$setupFiles = [
    'add_6months_sample_data.php',
    'add_more_units.php',
    'add_sample_products.php',
    'add_sample_purchase_orders.php',
    'add_sample_stock.php',
    'add_warehouse_reserves.php',
    'apply_enhancements.php',
    'assign_product_locations.php',
    'check_categories.php',
    'check_db_structure.php',
    'check_fk.php',
    'check_product_locations.php',
    'check_reference_tables.php',
    'check_warehouse_db.php',
    'check_warehouse_table.php',
    'cleanup_locations.php',
    'final_warehouse_setup.php',
    'fix_cycle_count_form.php',
    'fix_foreign_keys.php',
    'fix_sales_pricing.php',
    'insert_sample_data.php',
    'location_assignment_report.php',
    'quick_test.php',
    'setup_inventory_updates.php',
    'setup_warehouse_db.php',
    'setup_warehouse_locations.php',
    'simple_inventory_setup.php',
    'test_cycle_count_deps.php',
    'test_database_tables.php',
    'test_db.php',
    'test_inventory.php',
    'test_warehouse.php',
    'update_inventory_tables.sql',
    'update_units_table.php',
    'update_warehouse_system.php',
    'update_warehouse_tables.sql'
];

foreach ($setupFiles as $file) {
    if (file_exists($file)) {
        $destination = "scripts/setup/$file";
        if (rename($file, $destination)) {
            echo "✅ Moved: $file → scripts/setup/<br>";
        } else {
            echo "❌ Failed to move: $file<br>";
        }
    }
}

// Step 5: Move documentation
echo "<h2>📚 Step 5: Moving Documentation</h2>";

$docFiles = [
    'ENHANCED_SYSTEM_GUIDE.md',
    'INVENTORY_UPDATE_README.md', 
    'PRODUCT_EDIT_LOCATION_GUIDE.md',
    'README.md',
    'WAREHOUSE_SYSTEM_GUIDE.md'
];

foreach ($docFiles as $file) {
    if (file_exists($file)) {
        $destination = "docs/$file";
        if (copy($file, $destination)) {
            echo "✅ Copied: $file → docs/<br>";
        } else {
            echo "❌ Failed to copy: $file<br>";
        }
    }
}

// Step 6: Create configuration files
echo "<h2>⚙️ Step 6: Creating Configuration Structure</h2>";

// Move config.php to config directory
if (file_exists('app/config.php')) {
    copy('app/config.php', 'config/database.php');
    echo "✅ Created: config/database.php<br>";
}

// Create app config
$appConfig = '<?php
/**
 * Application Configuration
 */

define("APP_NAME", "Hardware Store Management System");
define("APP_VERSION", "2.0.0");
define("APP_ENV", "development"); // development, production

// Paths
define("APP_ROOT", dirname(__DIR__));
define("URLROOT", "http://localhost");
define("SITENAME", "Hardware Store");

// Session configuration
ini_set("session.cookie_httponly", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.cookie_secure", 0); // Set to 1 for HTTPS

// Error reporting
if (APP_ENV === "development") {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    error_reporting(0);
    ini_set("display_errors", 0);
}

// Logging
define("LOG_FILE", APP_ROOT . "/storage/logs/app.log");
';

file_put_contents('config/app.php', $appConfig);
echo "✅ Created: config/app.php<br>";

// Step 7: Create improved autoloader
echo "<h2>🔄 Step 7: Creating Improved Bootstrap</h2>";

$bootstrap = '<?php
/**
 * Application Bootstrap
 * Improved autoloading and initialization
 */

// Load configurations
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/config/database.php";

// Composer autoloader
if (file_exists(__DIR__ . "/vendor/autoload.php")) {
    require_once __DIR__ . "/vendor/autoload.php";
}

// Custom autoloader for app classes
spl_autoload_register(function ($className) {
    $directories = [
        __DIR__ . "/app/controllers/",
        __DIR__ . "/app/models/", 
        __DIR__ . "/app/",
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . ".php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helper functions
require_once __DIR__ . "/app/helpers.php";

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error logging function
function logError($message, $context = []) {
    $timestamp = date("Y-m-d H:i:s");
    $contextStr = !empty($context) ? " Context: " . json_encode($context) : "";
    $logMessage = "[$timestamp] ERROR: $message$contextStr" . PHP_EOL;
    
    if (defined("LOG_FILE")) {
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// Set error handler
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    logError("$message in $file on line $line", [
        "severity" => $severity,
        "file" => $file,
        "line" => $line
    ]);
    
    if (APP_ENV === "development") {
        echo "<div style=\"background: #fee; border: 1px solid #fcc; padding: 10px; margin: 5px;\">";
        echo "<strong>Error:</strong> $message<br>";
        echo "<strong>File:</strong> $file<br>";
        echo "<strong>Line:</strong> $line<br>";
        echo "</div>";
    }
    
    return true;
});

// Set exception handler  
set_exception_handler(function($exception) {
    logError($exception->getMessage(), [
        "file" => $exception->getFile(),
        "line" => $exception->getLine(),
        "trace" => $exception->getTraceAsString()
    ]);
    
    if (APP_ENV === "development") {
        echo "<div style=\"background: #fee; border: 1px solid #fcc; padding: 10px; margin: 5px;\">";
        echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
        echo "<details><summary>Stack Trace</summary><pre>" . $exception->getTraceAsString() . "</pre></details>";
        echo "</div>";
    } else {
        echo "<h2>Application Error</h2><p>An error occurred. Please contact support.</p>";
    }
});
';

file_put_contents('bootstrap.php', $bootstrap);
echo "✅ Created: bootstrap.php<br>";

// Step 8: Summary
echo "<h2>✅ Reorganization Complete!</h2>";
echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<h3>📁 New Project Structure:</h3>";
echo "<pre>";
echo "📦 Hardware Store Project
├── 📁 app/                    # Application core
│   ├── 📁 controllers/        # Controllers
│   ├── 📁 models/            # Models  
│   ├── 📁 views/             # Views
│   ├── 📄 Database.php       # Database class
│   └── 📄 helpers.php        # Helper functions
├── 📁 config/                # Configuration files
│   ├── 📄 app.php            # App configuration
│   └── 📄 database.php       # Database configuration
├── 📁 database/              # Database related files
│   ├── 📁 migrations/        # SQL migration files
│   └── 📁 seeds/             # Sample data
├── 📁 scripts/               # Utility scripts
│   ├── 📁 setup/             # Setup scripts
│   └── 📁 utilities/         # Utility scripts
├── 📁 docs/                  # Documentation
├── 📁 public/                # Public assets
│   ├── 📁 css/               # Stylesheets
│   ├── 📁 js/                # JavaScript
│   └── 📁 uploads/           # User uploads
├── 📁 storage/               # Storage directory
│   ├── 📁 logs/              # Log files
│   └── 📁 uploads/           # Private uploads
├── 📁 temp/                  # Temporary files
├── 📁 vendor/                # Composer dependencies
├── 📄 bootstrap.php          # Application bootstrap
├── 📄 index.php              # Entry point
└── 📄 composer.json          # Dependencies
</pre>";

echo "<h3>🎯 Next Steps:</h3>";
echo "<ol>";
echo "<li>Update index.php to use new bootstrap.php</li>";
echo "<li>Review and update routing system</li>";
echo "<li>Fix any remaining deprecated code</li>";
echo "<li>Set up proper error logging</li>";
echo "<li>Begin Vue 3 + Tailwind CSS migration</li>";
echo "</ol>";
echo "</div>";

echo "<br><a href='http://localhost/'>🔗 Test the application</a>";
?>
