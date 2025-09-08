<?php
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/app/Database.php";

echo "Testing Database class methods...\n";

try {
    $db = new Database();
    
    // Test method 1: query + execute + single
    echo "Method 1: query + execute + single\n";
    $db->query('SELECT DATABASE() as db_name');
    if ($db->execute()) {
        $result = $db->single();
        if ($result) {
            echo "Success! Database: " . $result->db_name . "\n";
        } else {
            echo "Execute succeeded but single() returned null\n";
        }
    } else {
        echo "Execute failed\n";
    }
    
    // Test method 2: executeSingle
    echo "\nMethod 2: executeSingle\n";
    $db->query('SELECT DATABASE() as db_name');
    $result2 = $db->executeSingle();
    if ($result2) {
        echo "Success! Database: " . $result2->db_name . "\n";
    } else {
        echo "executeSingle failed\n";
    }
    
    // Test with companies table
    echo "\nTesting companies table access...\n";
    $db->query('SELECT COUNT(*) as total FROM companies');
    if ($db->execute()) {
        $result3 = $db->single();
        if ($result3) {
            echo "Companies count: " . $result3->total . "\n";
        } else {
            echo "Query succeeded but no result\n";
        }
    } else {
        echo "Companies query failed - table might not exist\n";
        echo "Last error: " . $db->getLastError() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
