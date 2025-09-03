<?php
require_once 'bootstrap.php';

echo "DASHBOARD INVENTORY FIX VERIFICATION\n";
echo "====================================\n";

try {
    $db = new Database();

    echo "1. CHECKING ACTUAL PRODUCT DATA:\n";
    echo "--------------------------------\n";

    // Check products with 0 stock
    $db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1 AND COALESCE(stock_quantity, 0) <= 0");
    $result = $db->single();
    $actualOutOfStock = $result ? $result->count : 0;
    echo "Products with 0 stock (actual): " . $actualOutOfStock . "\n";

    // Check products with low stock (1-10 units)
    $db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1 AND COALESCE(stock_quantity, 0) > 0 AND COALESCE(stock_quantity, 0) <= COALESCE(reorder_level, 10)");
    $result = $db->single();
    $actualLowStock = $result ? $result->count : 0;
    echo "Products with low stock (actual): " . $actualLowStock . "\n\n";

    echo "2. TESTING DASHBOARD MODEL (AFTER FIX):\n";
    echo "---------------------------------------\n";

    // Test the fixed Dashboard model
    require_once APPROOT . DS . 'app' . DS . 'models' . DS . 'Dashboard.php';
    $dashboardModel = new Dashboard();

    $dashboardLowStock = $dashboardModel->getLowInventoryCount();
    $dashboardOutOfStock = $dashboardModel->getOutOfInventoryCount();

    echo "Dashboard Low Inventory Count: " . $dashboardLowStock . "\n";
    echo "Dashboard Out of Inventory Count: " . $dashboardOutOfStock . "\n\n";

    echo "3. COMPARISON RESULTS:\n";
    echo "---------------------\n";

    if ($actualOutOfStock == $dashboardOutOfStock) {
        echo "✅ OUT OF STOCK: FIXED! (" . $actualOutOfStock . " = " . $dashboardOutOfStock . ")\n";
    } else {
        echo "❌ OUT OF STOCK: Still mismatched (" . $actualOutOfStock . " ≠ " . $dashboardOutOfStock . ")\n";
    }

    if ($actualLowStock == $dashboardLowStock) {
        echo "✅ LOW STOCK: FIXED! (" . $actualLowStock . " = " . $dashboardLowStock . ")\n";
    } else {
        echo "❌ LOW STOCK: Still mismatched (" . $actualLowStock . " ≠ " . $dashboardLowStock . ")\n";
    }

    echo "\n4. SAMPLE PRODUCTS WITH 0 STOCK:\n";
    echo "--------------------------------\n";

    $db->query("SELECT product_name, stock_quantity FROM products WHERE is_active = 1 AND COALESCE(stock_quantity, 0) <= 0 LIMIT 5");
    $zeroStockProducts = $db->resultSet();

    if (count($zeroStockProducts) > 0) {
        foreach ($zeroStockProducts as $product) {
            echo "- " . $product->product_name . " (Stock: " . ($product->stock_quantity ?? 'NULL') . ")\n";
        }
    } else {
        echo "No products with 0 stock found.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>