<?php
require_once 'bootstrap.php';

echo "📋 Inventory Table Structure Analysis\n";
echo "====================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Checking inventory table structure:\n";
    $db->query("DESCRIBE inventory");
    $db->execute();
    $columns = $db->resultSet();

    echo "Columns in inventory table:\n";
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type}) " . ($col->Null == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }

    echo "\n2️⃣ Sample inventory records:\n";
    $db->query("SELECT * FROM inventory LIMIT 5");
    $db->execute();
    $samples = $db->resultSet();

    foreach ($samples as $sample) {
        echo "   Inventory ID: {$sample->inventory_id}, Product: {$sample->product_id}, Qty: {$sample->quantity}, Location: {$sample->location_id}\n";
    }

    echo "\n3️⃣ Testing correct INSERT syntax:\n";

    // Try inserting without created_at
    $testProduct = 999; // Use a test product that doesn't exist
    $testQty = 100;

    echo "   Inserting product {$testProduct} with quantity {$testQty} (no created_at)...\n";
    $db->query("INSERT INTO inventory (product_id, quantity, location_id) VALUES (?, ?, 1)");
    $db->bind(1, $testProduct);
    $db->bind(2, $testQty);
    $result = $db->execute();

    if ($result) {
        echo "   ✅ INSERT successful without created_at!\n";

        // Clean up test record
        $db->query("DELETE FROM inventory WHERE product_id = ?");
        $db->bind(1, $testProduct);
        $db->execute();
        echo "   🧹 Test record cleaned up\n";
    } else {
        echo "   ❌ INSERT still failed: " . $db->getLastError() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>