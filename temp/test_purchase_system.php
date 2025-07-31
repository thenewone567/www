<?php
// Test purchase order functionality
require_once 'app/config.php';
require_once 'app/Database.php';

echo "<h2>Purchase Order System Test</h2>";

try {
    $db = new Database();
    echo "✅ Database connection successful<br>";

    // Test if purchase_orders table exists
    $db->query("SHOW TABLES LIKE 'purchase_orders'");
    $result = $db->single();
    if ($result) {
        echo "✅ purchase_orders table exists<br>";

        // Check table structure
        $db->query("DESCRIBE purchase_orders");
        $columns = $db->resultSet();
        echo "<h3>purchase_orders table structure:</h3>";
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>{$column->Field} - {$column->Type}</li>";
        }
        echo "</ul>";

        // Check if there's any data
        $db->query("SELECT COUNT(*) as count FROM purchase_orders");
        $count = $db->single();
        echo "📊 Total purchase orders: {$count->count}<br>";

    } else {
        echo "❌ purchase_orders table does not exist<br>";
    }

    // Test if suppliers table exists
    $db->query("SHOW TABLES LIKE 'suppliers'");
    $result = $db->single();
    if ($result) {
        echo "✅ suppliers table exists<br>";

        $db->query("SELECT COUNT(*) as count FROM suppliers");
        $count = $db->single();
        echo "📊 Total suppliers: {$count->count}<br>";
    } else {
        echo "❌ suppliers table does not exist<br>";
    }

    // Test Purchase model
    require_once 'app/models/Purchase.php';
    $purchaseModel = new Purchase();
    echo "✅ Purchase model loaded successfully<br>";

    $purchases = $purchaseModel->getPurchases();
    if ($purchases !== false) {
        echo "✅ getPurchases() method works - found " . count($purchases) . " purchases<br>";

        if (!empty($purchases)) {
            echo "<h3>Sample purchase data:</h3>";
            echo "<pre>" . print_r($purchases[0], true) . "</pre>";
        }
    } else {
        echo "❌ getPurchases() method failed<br>";
    }

    // Test PurchaseOrder model
    require_once 'app/models/PurchaseOrder.php';
    $poModel = new PurchaseOrder();
    echo "✅ PurchaseOrder model loaded successfully<br>";

    $orders = $poModel->getPurchaseOrders();
    if ($orders !== false) {
        echo "✅ getPurchaseOrders() method works - found " . count($orders) . " orders<br>";
    } else {
        echo "❌ getPurchaseOrders() method failed<br>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><a href='" . URLROOT . "/purchases'>→ Go to Purchases Page</a>";
?>