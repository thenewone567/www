<?php
require_once 'bootstrap.php';

echo "=== Checking unique_id column implementation ===\n\n";

// Create database connection
$db = new Database();

try {
    // Check users table
    echo "Users table structure:\n";
    $db->query("DESCRIBE users");
    $columns = $db->resultSet();
    $hasUniqueId = false;
    foreach ($columns as $column) {
        if ($column->Field === 'unique_id') {
            echo "  ✅ unique_id column found: {$column->Type}\n";
            $hasUniqueId = true;
            break;
        }
    }
    if (!$hasUniqueId) {
        echo "  ❌ unique_id column NOT found\n";
    }

    // Check customers table
    echo "\nCustomers table structure:\n";
    $db->query("DESCRIBE customers");
    $columns = $db->resultSet();
    $hasUniqueId = false;
    foreach ($columns as $column) {
        if ($column->Field === 'unique_id') {
            echo "  ✅ unique_id column found: {$column->Type}\n";
            $hasUniqueId = true;
            break;
        }
    }
    if (!$hasUniqueId) {
        echo "  ❌ unique_id column NOT found\n";
    }

    // Check contractors table
    echo "\nContractors table structure:\n";
    $db->query("DESCRIBE contractors");
    $columns = $db->resultSet();
    $hasUniqueId = false;
    foreach ($columns as $column) {
        if ($column->Field === 'unique_id') {
            echo "  ✅ unique_id column found: {$column->Type}\n";
            $hasUniqueId = true;
            break;
        }
    }
    if (!$hasUniqueId) {
        echo "  ❌ unique_id column NOT found\n";
    }

    // Check functions
    echo "\nChecking MySQL functions:\n";
    $db->query("SHOW FUNCTION STATUS WHERE Name = 'generate_unique_id'");
    $result = $db->single();
    if ($result) {
        echo "  ✅ generate_unique_id function exists\n";
    } else {
        echo "  ❌ generate_unique_id function NOT found\n";
    }

    // Test a simple count
    echo "\nTesting table counts:\n";
    $db->query("SELECT COUNT(*) as count FROM users");
    $userCount = $db->single();
    echo "  Users: " . ($userCount ? $userCount->count : 'ERROR') . "\n";

    $db->query("SELECT COUNT(*) as count FROM customers");
    $customerCount = $db->single();
    echo "  Customers: " . ($customerCount ? $customerCount->count : 'ERROR') . "\n";

    $db->query("SELECT COUNT(*) as count FROM contractors");
    $contractorCount = $db->single();
    echo "  Contractors: " . ($contractorCount ? $contractorCount->count : 'ERROR') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Check complete ===\n";
?>