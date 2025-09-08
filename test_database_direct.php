<?php
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/config/database.php";

// Test constants
echo "Configuration check:\n";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS === '' ? 'EMPTY' : 'SET') : 'NOT DEFINED') . "\n";

// Load Database class manually
require_once __DIR__ . "/app/Database.php";

echo "\nTesting Database class directly...\n";

try {
    $db = new Database();
    
    // Check connection
    $pdo = $db->getDbh();
    if ($pdo) {
        echo "Database connection established\n";
        
        // Test a simple query
        $db->query('SELECT DATABASE() as db_name');
        $result = $db->single();
        
        if ($result) {
            echo "Connected to: " . $result->db_name . "\n";
        } else {
            echo "Query failed or returned null\n";
        }
        
    } else {
        echo "Failed to get database handle\n";
    }
    
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
