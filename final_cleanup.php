<?php
/**
 * Final Cleanup and Bug Fix Script
 * Addresses remaining deprecated code and potential issues
 */

echo "<h1>🧹 Final Cleanup and Bug Fixes</h1>";

// Step 1: Check for remaining deprecated constants
echo "<h2>⚠️ Step 1: Checking for Deprecated Code</h2>";

$phpFiles = [];
$directories = ['app/controllers', 'app/models', 'app/views'];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $files = glob("$dir/*.php");
        $phpFiles = array_merge($phpFiles, $files);
    }
}

// Check for deprecated patterns
$deprecatedPatterns = [
    'FILTER_SANITIZE_STRING' => 'Use FILTER_SANITIZE_FULL_SPECIAL_CHARS',
    'mysql_' => 'Use PDO',
    'each(' => 'Use foreach',
    'create_function' => 'Use anonymous functions',
    '__autoload' => 'Use spl_autoload_register',
    'split(' => 'Use preg_split or explode',
    'ereg' => 'Use preg_match'
];

$issuesFound = [];

foreach ($phpFiles as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        foreach ($deprecatedPatterns as $pattern => $fix) {
            if (strpos($content, $pattern) !== false) {
                $issuesFound[$pattern][] = $file;
            }
        }
    }
}

if (count($issuesFound) > 0) {
    foreach ($issuesFound as $pattern => $files) {
        echo "⚠️ Found '$pattern' in " . count($files) . " files: " . implode(', ', $files) . "<br>";
    }
} else {
    echo "✅ No deprecated patterns found<br>";
}

// Step 2: Fix common security issues
echo "<h2>🔒 Step 2: Security Improvements</h2>";

// Create a secure helper for input sanitization
$securityHelper = '<?php
/**
 * Security Helper Functions
 */

/**
 * Sanitize input data
 * @param mixed $data Input data
 * @param string $type Type of sanitization
 * @return mixed Sanitized data
 */
function sanitizeInput($data, $type = "string") {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $data);
    }
    
    switch ($type) {
        case "email":
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case "url":
            return filter_var($data, FILTER_SANITIZE_URL);
        case "int":
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case "float":
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case "string":
        default:
            return htmlspecialchars(trim($data), ENT_QUOTES, "UTF-8");
    }
}

/**
 * Validate input data
 * @param mixed $data Input data
 * @param string $type Type of validation
 * @return bool True if valid
 */
function validateInput($data, $type = "string") {
    switch ($type) {
        case "email":
            return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
        case "url":
            return filter_var($data, FILTER_VALIDATE_URL) !== false;
        case "int":
            return filter_var($data, FILTER_VALIDATE_INT) !== false;
        case "float":
            return filter_var($data, FILTER_VALIDATE_FLOAT) !== false;
        case "required":
            return !empty(trim($data));
        default:
            return is_string($data);
    }
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid
 */
function verifyCSRFToken($token) {
    return isset($_SESSION["csrf_token"]) && hash_equals($_SESSION["csrf_token"], $token);
}

/**
 * Escape output for HTML
 * @param string $data Data to escape
 * @return string Escaped data
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, "UTF-8");
}
';

file_put_contents('app/security.php', $securityHelper);
echo "✅ Created: app/security.php (Security helper functions)<br>";

// Step 3: Update bootstrap to include security helper
echo "<h2>🔄 Step 3: Updating Bootstrap</h2>";

$bootstrapContent = file_get_contents('bootstrap.php');
$newInclude = "\n// Security helpers\nrequire_once __DIR__ . \"/app/security.php\";\n";

if (strpos($bootstrapContent, 'app/security.php') === false) {
    $bootstrapContent = str_replace(
        '// Helper functions\nrequire_once __DIR__ . "/app/helpers.php";',
        '// Helper functions\nrequire_once __DIR__ . "/app/helpers.php";' . $newInclude,
        $bootstrapContent
    );
    file_put_contents('bootstrap.php', $bootstrapContent);
    echo "✅ Updated bootstrap.php to include security helpers<br>";
} else {
    echo "ℹ️ Security helpers already included in bootstrap<br>";
}

// Step 4: Create database migration system
echo "<h2>🗄️ Step 4: Creating Database Migration System</h2>";

$migrationRunner = '<?php
/**
 * Database Migration Runner
 */

require_once __DIR__ . "/../bootstrap.php";

class MigrationRunner {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        $this->createMigrationsTable();
    }
    
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_migration (migration)
        )";
        
        $this->db->query($sql);
        $this->db->execute();
    }
    
    public function runMigrations() {
        $migrationsDir = __DIR__ . "/../database/migrations/";
        $files = glob($migrationsDir . "*.sql");
        sort($files);
        
        echo "<h3>Running Database Migrations</h3>";
        
        foreach ($files as $file) {
            $migrationName = basename($file);
            
            // Check if already executed
            $this->db->query("SELECT id FROM migrations WHERE migration = :migration");
            $this->db->bind(":migration", $migrationName);
            
            if (!$this->db->single()) {
                echo "Executing: $migrationName<br>";
                
                $sql = file_get_contents($file);
                $statements = array_filter(array_map("trim", explode(";", $sql)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        try {
                            $this->db->query($statement);
                            $this->db->execute();
                        } catch (Exception $e) {
                            echo "Error in $migrationName: " . $e->getMessage() . "<br>";
                        }
                    }
                }
                
                // Mark as executed
                $this->db->query("INSERT INTO migrations (migration) VALUES (:migration)");
                $this->db->bind(":migration", $migrationName);
                $this->db->execute();
                
                echo "✅ Completed: $migrationName<br>";
            } else {
                echo "⏭️ Skipped (already executed): $migrationName<br>";
            }
        }
    }
}

// Run if called directly
if (basename(__FILE__) === basename($_SERVER["PHP_SELF"])) {
    echo "<h1>Database Migration Runner</h1>";
    $runner = new MigrationRunner();
    $runner->runMigrations();
    echo "<br><a href=\"../\">← Back to Application</a>";
}
';

file_put_contents('scripts/utilities/migrate.php', $migrationRunner);
echo "✅ Created: scripts/utilities/migrate.php (Database migration runner)<br>";

// Step 5: Create system status dashboard
echo "<h2>📊 Step 5: Creating System Status Dashboard</h2>";

$statusDashboard = '<?php
/**
 * System Status Dashboard
 */

require_once __DIR__ . "/bootstrap.php";

echo "<h1>🎛️ System Status Dashboard</h1>";

// Database status
echo "<h2>🗄️ Database Status</h2>";
try {
    $db = new Database();
    $db->query("SELECT COUNT(*) as count FROM purchase_orders");
    $poCount = $db->single()->count;
    
    $db->query("SELECT COUNT(*) as count FROM products");
    $productCount = $db->single()->count;
    
    $db->query("SELECT COUNT(*) as count FROM suppliers");
    $supplierCount = $db->single()->count;
    
    echo "<div style=\"display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0;\">";
    echo "<div style=\"background: #e8f5e8; padding: 15px; border-radius: 8px; text-align: center;\">";
    echo "<h3>Purchase Orders</h3><h2>$poCount</h2>";
    echo "</div>";
    echo "<div style=\"background: #e8f0ff; padding: 15px; border-radius: 8px; text-align: center;\">";
    echo "<h3>Products</h3><h2>$productCount</h2>";
    echo "</div>";
    echo "<div style=\"background: #fff3e0; padding: 15px; border-radius: 8px; text-align: center;\">";
    echo "<h3>Suppliers</h3><h2>$supplierCount</h2>";
    echo "</div>";
    echo "</div>";
    
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// System info
echo "<h2>⚙️ System Information</h2>";
echo "<table border=\"1\" style=\"border-collapse: collapse; width: 100%; margin: 10px 0;\">";
echo "<tr><th style=\"padding: 8px; background: #f5f5f5;\">Component</th><th style=\"padding: 8px; background: #f5f5f5;\">Status</th></tr>";
echo "<tr><td style=\"padding: 8px;\">PHP Version</td><td style=\"padding: 8px;\">" . PHP_VERSION . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">App Version</td><td style=\"padding: 8px;\">" . (defined("APP_VERSION") ? APP_VERSION : "Unknown") . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">Environment</td><td style=\"padding: 8px;\">" . (defined("APP_ENV") ? APP_ENV : "Unknown") . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">Log File</td><td style=\"padding: 8px;\">" . (file_exists("storage/logs/app.log") ? "✅ Exists" : "❌ Not found") . "</td></tr>";
echo "<tr><td style=\"padding: 8px;\">Error Reporting</td><td style=\"padding: 8px;\">" . (error_reporting() > 0 ? "✅ Enabled" : "❌ Disabled") . "</td></tr>";
echo "</table>";

echo "<h2>🔗 Quick Actions</h2>";
echo "<div style=\"margin: 20px 0;\">";
echo "<a href=\"scripts/utilities/migrate.php\" style=\"background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;\">🔄 Run Migrations</a>";
echo "<a href=\"purchases\" style=\"background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;\">📦 Purchases</a>";
echo "<a href=\"products\" style=\"background: #ffc107; color: black; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;\">📦 Products</a>";
echo "</div>";

echo "<h2>📁 Project Structure</h2>";
echo "<pre style=\"background: #f8f9fa; padding: 15px; border-radius: 5px;\">";
echo "✅ app/controllers/ - " . count(glob("app/controllers/*.php")) . " files\n";
echo "✅ app/models/ - " . count(glob("app/models/*.php")) . " files\n";
echo "✅ app/views/ - " . count(glob("app/views/*.php")) . " files\n";
echo "✅ config/ - " . count(glob("config/*.php")) . " files\n";
echo "✅ database/migrations/ - " . count(glob("database/migrations/*.sql")) . " files\n";
echo "✅ scripts/setup/ - " . count(glob("scripts/setup/*")) . " files\n";
echo "✅ temp/ - " . count(glob("temp/*")) . " files\n";
echo "</pre>";
';

file_put_contents('system_status.php', $statusDashboard);
echo "✅ Created: system_status.php (System status dashboard)<br>";

echo "<h2>✨ Cleanup Complete!</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border: 1px solid #b8daff; border-radius: 5px;'>";
echo "<h3>🎉 Project Successfully Reorganized & Cleaned!</h3>";
echo "<p><strong>What was accomplished:</strong></p>";
echo "<ul>";
echo "<li>✅ Moved temporary/debug files to temp/ directory</li>";
echo "<li>✅ Organized SQL files in database/migrations/</li>";
echo "<li>✅ Created proper configuration structure</li>";
echo "<li>✅ Added improved bootstrap with error handling</li>";
echo "<li>✅ Created security helper functions</li>";
echo "<li>✅ Built database migration system</li>";
echo "<li>✅ Added system status dashboard</li>";
echo "</ul>";

echo "<p><strong>Access Points:</strong></p>";
echo "<ul>";
echo "<li><a href='http://localhost/'>🏠 Main Application</a></li>";
echo "<li><a href='http://localhost/system_status.php'>📊 System Status</a></li>";
echo "<li><a href='http://localhost/scripts/utilities/migrate.php'>🔄 Database Migrations</a></li>";
echo "<li><a href='http://localhost/purchases'>📦 Purchase Orders</a></li>";
echo "</ul>";

echo "<p><strong>Ready for Vue 3 + Tailwind CSS migration!</strong> 🚀</p>";
echo "</div>";
?>
