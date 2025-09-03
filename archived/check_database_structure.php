<?php
require_once 'bootstrap.php';

echo "🗄️  Database Structure Check\n";
echo "============================\n\n";

$db = new Database();

try {
    // Check if purchases table exists
    echo "1. Checking purchases table:\n";
    $db->query("SHOW TABLES LIKE 'purchases'");
    $result = $db->single();

    if ($result) {
        echo "✅ Purchases table exists\n";

        // Check table structure
        $db->query("DESCRIBE purchases");
        $structure = $db->resultSet();
        echo "   Table structure:\n";
        foreach ($structure as $field) {
            echo "   - {$field->Field} ({$field->Type})\n";
        }

        // Count total records
        $db->query("SELECT COUNT(*) as count FROM purchases");
        $count = $db->single();
        echo "   Total records: {$count->count}\n\n";

    } else {
        echo "❌ Purchases table does not exist\n\n";
    }

    // Check purchase_items table
    echo "2. Checking purchase_items table:\n";
    $db->query("SHOW TABLES LIKE 'purchase_items'");
    $result = $db->single();

    if ($result) {
        echo "✅ Purchase_items table exists\n";

        // Count total records
        $db->query("SELECT COUNT(*) as count FROM purchase_items");
        $count = $db->single();
        echo "   Total records: {$count->count}\n\n";
    } else {
        echo "❌ Purchase_items table does not exist\n\n";
    }

    // List all tables to see what we have
    echo "3. All tables in database:\n";
    $db->query("SHOW TABLES");
    $tables = $db->resultSet();
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "   - {$tableName}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>