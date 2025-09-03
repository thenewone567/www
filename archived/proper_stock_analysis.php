<?php
require_once 'bootstrap.php';

echo "=== STOCK ANALYSIS WITH PROPER QUERIES ===\n\n";

$database = new Database();

// 1. Check stock distribution using single() instead of resultSet()
echo "1. Stock Distribution:\n";
$database->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0");
$database->execute();
$zeroStock = $database->single();
echo "   Zero stock products: " . ($zeroStock ? $zeroStock->count : 'ERROR') . "\n";

$database->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity BETWEEN 1 AND 5");
$database->execute();
$lowStock = $database->single();
echo "   Low stock (1-5): " . ($lowStock ? $lowStock->count : 'ERROR') . "\n";

$database->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity > 5");
$database->execute();
$goodStock = $database->single();
echo "   Good stock (6+): " . ($goodStock ? $goodStock->count : 'ERROR') . "\n\n";

// 2. Get individual 0-stock products
echo "2. Zero Stock Products (individual queries):\n";
$database->query("SELECT product_id FROM products WHERE stock_quantity = 0 LIMIT 10");
$database->execute();
if ($database->rowCount() > 0) {
    // Get each product individually
    for ($i = 0; $i < min($database->rowCount(), 10); $i++) {
        $database->query("SELECT product_id, product_name, stock_quantity, reorder_level FROM products WHERE stock_quantity = 0 LIMIT 1 OFFSET $i");
        $database->execute();
        $product = $database->single();
        if ($product) {
            echo "   ID: {$product->product_id} | {$product->product_name} | Stock: {$product->stock_quantity} | Reorder: {$product->reorder_level}\n";
        }
    }
} else {
    echo "   No zero stock products found\n";
}

// 3. Check recent purchases individually
echo "\n3. Recent Purchases:\n";
$database->query("SELECT COUNT(*) as count FROM purchases WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$database->execute();
$recentCount = $database->single();
echo "   Recent purchases (7 days): " . ($recentCount ? $recentCount->count : 'ERROR') . "\n";

if ($recentCount && $recentCount->count > 0) {
    $database->query("SELECT id, supplier_id, status, total_amount, created_at FROM purchases WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY created_at DESC LIMIT 1");
    $database->execute();
    $purchase = $database->single();
    if ($purchase) {
        echo "   Most recent: Purchase #{$purchase->id} | Supplier: {$purchase->supplier_id} | Status: {$purchase->status} | Amount: {$purchase->total_amount} | Date: {$purchase->created_at}\n";
    }
}

// 4. Check for any purchases today
echo "\n4. Today's Activity:\n";
$database->query("SELECT COUNT(*) as count FROM purchases WHERE DATE(created_at) = CURDATE()");
$database->execute();
$todayPurchases = $database->single();
echo "   Purchases today: " . ($todayPurchases ? $todayPurchases->count : 'ERROR') . "\n";

$database->query("SELECT COUNT(*) as count FROM receiving WHERE DATE(received_date) = CURDATE()");
$database->execute();
$todayReceiving = $database->single();
echo "   Receivings today: " . ($todayReceiving ? $todayReceiving->count : 'ERROR') . "\n";

// 5. Check if there are products that recently received stock
echo "\n5. Products that might have received stock recently:\n";
$database->query("
    SELECT COUNT(DISTINCT r.product_id) as count 
    FROM receiving r 
    WHERE r.received_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$database->execute();
$recentlyReceived = $database->single();
echo "   Products with recent receiving: " . ($recentlyReceived ? $recentlyReceived->count : 'ERROR') . "\n";

if ($recentlyReceived && $recentlyReceived->count > 0) {
    // Get one example
    $database->query("
        SELECT r.product_id, p.product_name, p.stock_quantity, r.quantity_received, r.received_date
        FROM receiving r
        JOIN products p ON r.product_id = p.product_id
        WHERE r.received_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY r.received_date DESC
        LIMIT 1
    ");
    $database->execute();
    $example = $database->single();
    if ($example) {
        echo "   Example: {$example->product_name} | Current Stock: {$example->stock_quantity} | Recently Received: {$example->quantity_received} | Date: {$example->received_date}\n";
    }
}

echo "\n=== ANALYSIS COMPLETE ===\n";
