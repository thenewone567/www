<?php
require_once 'bootstrap.php';

echo "📊 Fresh Database Inventory Check\n";
echo "=================================\n\n";

$db = new Database();

try {
    // Check products
    $db->query("SELECT COUNT(*) as count FROM products WHERE deleted_at IS NULL");
    $productCount = $db->single();
    echo "✅ Total products: " . ($productCount ? $productCount->count : 0) . "\n";

    // Check inventory
    $db->query("SELECT COUNT(*) as count FROM inventory");
    $inventoryCount = $db->single();
    echo "✅ Inventory records: " . ($inventoryCount ? $inventoryCount->count : 0) . "\n";

    // Check for low/out of stock
    $db->query("SELECT 
        COUNT(*) as count 
        FROM products p 
        LEFT JOIN inventory i ON p.id = i.product_id 
        WHERE p.deleted_at IS NULL 
        AND (i.stock_level IS NULL OR i.stock_level <= 0)");
    $outOfStockCount = $db->single();
    echo "📦 Out of stock items: " . ($outOfStockCount ? $outOfStockCount->count : 0) . "\n";

    // Check for low stock (1-10)
    $db->query("SELECT 
        COUNT(*) as count 
        FROM products p 
        LEFT JOIN inventory i ON p.id = i.product_id 
        WHERE p.deleted_at IS NULL 
        AND i.stock_level > 0 
        AND i.stock_level <= 10");
    $lowStockCount = $db->single();
    echo "⚠️  Low stock items (1-10): " . ($lowStockCount ? $lowStockCount->count : 0) . "\n";

    // Check purchases
    $db->query("SELECT COUNT(*) as count FROM purchases");
    $purchaseCount = $db->single();
    echo "🛒 Purchase orders: " . ($purchaseCount ? $purchaseCount->count : 0) . "\n";

    // Sample some products if any exist
    if ($productCount && $productCount->count > 0) {
        echo "\n📋 Sample Products:\n";
        $db->query("SELECT p.id, p.name, COALESCE(i.stock_level, 0) as stock 
                    FROM products p 
                    LEFT JOIN inventory i ON p.id = i.product_id 
                    WHERE p.deleted_at IS NULL 
                    ORDER BY stock ASC 
                    LIMIT 5");
        $samples = $db->resultSet();

        if ($samples) {
            foreach ($samples as $product) {
                echo "• {$product->name} (ID: {$product->id}) - Stock: {$product->stock}\n";
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>