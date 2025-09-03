<?php
require_once 'bootstrap.php';

echo "🗄️  Database Tables Check\n";
echo "=========================\n\n";

try {
    $database = new Database();

    $database->query("SHOW TABLES");
    $database->execute();
    $tables = $database->resultSet();

    echo "📋 Available Tables:\n";
    $purchaseRelated = [];
    $receivingRelated = [];

    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "   - {$tableName}\n";

        if (stripos($tableName, 'purchase') !== false) {
            $purchaseRelated[] = $tableName;
        }
        if (stripos($tableName, 'receiv') !== false || stripos($tableName, 'inventory') !== false) {
            $receivingRelated[] = $tableName;
        }
    }

    echo "\n🛒 Purchase-Related Tables:\n";
    if (count($purchaseRelated) > 0) {
        foreach ($purchaseRelated as $table) {
            echo "   ✅ {$table}\n";
        }
    } else {
        echo "   ❌ No purchase-related tables found\n";
    }

    echo "\n📦 Receiving/Inventory Tables:\n";
    if (count($receivingRelated) > 0) {
        foreach ($receivingRelated as $table) {
            echo "   ✅ {$table}\n";
        }
    } else {
        echo "   ❌ No receiving-related tables found\n";
    }

    echo "\n🎯 CONCLUSION:\n";
    if (count($purchaseRelated) == 0) {
        echo "❌ MAJOR ISSUE: No purchase order tables exist!\n";
        echo "   This explains why receiving bot uses random products\n";
        echo "   The system has no purchase order workflow to begin with\n";
        echo "\n🔧 SOLUTION NEEDED:\n";
        echo "   1. Create purchase_orders table\n";
        echo "   2. Create purchase_order_items table\n";
        echo "   3. Update receiving bot to process actual orders\n";
    } else {
        echo "✅ Purchase tables exist, checking structure...\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>