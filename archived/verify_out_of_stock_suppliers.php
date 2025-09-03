<?php
require_once 'bootstrap.php';

echo "🔍 CHECKING SPECIFIC OUT-OF-STOCK PRODUCTS\n";
echo "==========================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Getting current out-of-stock products...\n";

    $db->query("
        SELECT
            p.product_id,
            p.product_name,
            COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as current_stock
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND p.deleted_at IS NULL
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 0
        ORDER BY p.product_name
        LIMIT 10
    ");
    $db->execute();
    $outOfStockProducts = $db->resultSet();

    echo "   📊 Found " . count($outOfStockProducts) . " out-of-stock products\n\n";

    echo "2️⃣ Checking each product's suppliers in product_suppliers table...\n";

    foreach ($outOfStockProducts as $product) {
        echo "   📦 {$product->product_name} (ID: {$product->product_id})\n";

        // Check product_suppliers table
        $db->query("
            SELECT ps.*, s.supplier_name
            FROM product_suppliers ps
            LEFT JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = ?
            AND ps.is_active = 1
        ");
        $db->bind(1, $product->product_id);
        $db->execute();
        $suppliers = $db->resultSet();

        if (count($suppliers) > 0) {
            echo "      ✅ HAS SUPPLIERS in product_suppliers table:\n";
            foreach ($suppliers as $supplier) {
                echo "         • {$supplier->supplier_name} (ID: {$supplier->supplier_id}) - Price: $" . number_format($supplier->purchase_price, 2) . "\n";
            }
            echo "      🎯 This product SHOULD be orderable by purchase bot!\n";
        } else {
            echo "      ❌ NO SUPPLIERS in product_suppliers table\n";
            echo "      💡 This explains why purchase bot can't order this product\n";
        }

        // Check if purchase bot has tried to order this recently
        $db->query("
            SELECT COUNT(*) as recent_orders
            FROM purchases p
            JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
            WHERE pi.product_id = ?
            AND DATE(p.purchase_date) >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
        ");
        $db->bind(1, $product->product_id);
        $db->execute();
        $recentResult = $db->single();
        $recentOrders = $recentResult->recent_orders ?? 0;

        if ($recentOrders > 0) {
            echo "      ⚠️  Has {$recentOrders} recent orders (last 24 hours)\n";
        }

        echo "\n";
    }

    echo "3️⃣ Testing purchase bot on a product that SHOULD have suppliers...\n";

    if (count($outOfStockProducts) > 0) {
        $testProduct = $outOfStockProducts[0];

        // Check if this product has suppliers
        $db->query("
            SELECT COUNT(*) as supplier_count
            FROM product_suppliers
            WHERE product_id = ?
            AND is_active = 1
        ");
        $db->bind(1, $testProduct->product_id);
        $db->execute();
        $supplierCount = $db->single();

        if (($supplierCount->supplier_count ?? 0) > 0) {
            echo "   🤖 Testing purchase bot on {$testProduct->product_name}...\n";

            $_SESSION['user_id'] = 1;
            $_SESSION['user_role'] = 'admin';

            $controller = new BotController();
            $reflection = new ReflectionClass($controller);
            $performBotActionMethod = $reflection->getMethod('performBotAction');
            $performBotActionMethod->setAccessible(true);

            $result = $performBotActionMethod->invoke($controller, 'purchase_bot');

            if ($result['success']) {
                if (isset($result['product_name'])) {
                    echo "   ✅ SUCCESS! Purchase bot ordered: {$result['product_name']}\n";
                } else {
                    echo "   ✅ Purchase bot executed successfully\n";
                }
            } else {
                echo "   ❌ Purchase bot failed: {$result['message']}\n";
            }
        } else {
            echo "   ❌ Test product has no suppliers - can't test purchase bot\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>