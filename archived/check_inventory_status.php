<?php
// Check current inventory levels
echo "🔍 Current Inventory Status Check\n";
echo "==================================\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');

    // Check total inventory count
    echo "1. 📊 Total inventory from inventory table:\n";
    $stmt = $pdo->query("SELECT SUM(quantity) as total_inventory FROM inventory");
    $totalInv = $stmt->fetchColumn();
    echo "   Total: " . number_format($totalInv) . "\n\n";

    // Check recent sales activity
    echo "2. 🛒 Recent sales activity:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as today_sales FROM sales WHERE DATE(sale_date) = CURDATE()");
    $todaySales = $stmt->fetchColumn();
    echo "   Today's sales: {$todaySales}\n";

    $stmt = $pdo->query("
        SELECT sale_id, customer_id, total_amount, sale_date 
        FROM sales 
        WHERE DATE(sale_date) = CURDATE() 
        ORDER BY sale_date DESC 
        LIMIT 5
    ");
    $recentSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($recentSales as $sale) {
        echo "   Sale #{$sale['sale_id']}: Customer {$sale['customer_id']}, ₹{$sale['total_amount']}, {$sale['sale_date']}\n";
    }

    // Check inventory movements today
    echo "\n3. 📦 Recent inventory movements:\n";
    $stmt = $pdo->query("
        SELECT movement_type, product_id, quantity_change, reference_id, created_at
        FROM inventory_movements 
        WHERE DATE(created_at) = CURDATE() 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($movements)) {
        echo "   No inventory movements found today\n";
    } else {
        foreach ($movements as $move) {
            echo "   {$move['movement_type']}: Product {$move['product_id']}, Change: {$move['quantity_change']}, Ref: {$move['reference_id']}, {$move['created_at']}\n";
        }
    }

    // Check if products have current_inventory calculated correctly
    echo "\n4. 🔍 Sample product inventory check:\n";
    $stmt = $pdo->query("
        SELECT 
            p.product_id,
            p.product_name,
            COALESCE(SUM(i.quantity), 0) as inventory_total,
            p.selling_price
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        GROUP BY p.product_id, p.product_name, p.selling_price
        ORDER BY p.product_name
        LIMIT 5
    ");
    $sampleProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($sampleProducts as $prod) {
        echo "   Product #{$prod['product_id']}: {$prod['product_name']}, Stock: {$prod['inventory_total']}, Price: ₹{$prod['selling_price']}\n";
    }

    // Check if Sales Bot is actually reducing inventory
    echo "\n5. 💡 Analysis:\n";
    if ($todaySales > 0 && empty($movements)) {
        echo "❌ ISSUE FOUND: Sales exist but no inventory movements recorded!\n";
        echo "   The Sales Bot is creating sales but not updating inventory movements table.\n";
        echo "   This might be why the inventory count isn't changing.\n";
    } elseif ($todaySales > 0 && !empty($movements)) {
        echo "✅ Sales and inventory movements both exist.\n";
        echo "   The issue might be caching or calculation in the Products page.\n";
    } elseif ($todaySales == 0) {
        echo "ℹ️  No sales today - run the Sales Bot to test inventory updates.\n";
    }

} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🏁 Inventory Check Complete!\n";
?>