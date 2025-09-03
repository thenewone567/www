<?php
require_once 'bootstrap.php';

echo "=== Database Connection Test ===\n";

try {
    $db = new Database();
    echo "✅ Database object created successfully\n";

    // Test basic query
    $db->query('SHOW TABLES');
    $tables = $db->resultSet();

    echo "Tables found:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "  - " . $tableName . "\n";

        if ($tableName === 'products') {
            echo "    ✅ Products table found!\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>