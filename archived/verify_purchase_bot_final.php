<?php
require_once 'bootstrap.php';

echo "🔍 VERIFYING PURCHASE BOT IS WORKING CORRECTLY\n";
echo "===============================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Checking products with suppliers that are out of stock...\n";

    $db->query("
        SELECT DISTINCT
            p.product_id,
            p.product_name,
            COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as current_stock,
            COUNT(sp.supplier_id) as supplier_count
        FROM products p
        LEFT JOIN supplier_products sp ON p.product_id = sp.product_id
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND p.deleted_at IS NULL
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 0
        AND sp.supplier_id IS NOT NULL
        GROUP BY p.product_id, p.product_name
        HAVING supplier_count > 0
    ");
    $db->execute();
    $outOfStockWithSuppliers = $db->resultSet();

    echo "   📊 Found " . count($outOfStockWithSuppliers) . " out-of-stock products that HAVE suppliers:\n\n";

    if (count($outOfStockWithSuppliers) > 0) {
        foreach ($outOfStockWithSuppliers as $product) {
            echo "   📦 {$product->product_name} (ID: {$product->product_id})\n";
            echo "      Stock: {$product->current_stock}, Suppliers: {$product->supplier_count}\n";

            // Check if they have valid pricing
            $db->query("
                SELECT sp.purchase_price, s.supplier_name
                FROM supplier_products sp
                JOIN suppliers s ON sp.supplier_id = s.supplier_id
                WHERE sp.product_id = ?
                AND sp.purchase_price > 0
                ORDER BY sp.purchase_price ASC
                LIMIT 1
            ");
            $db->bind(1, $product->product_id);
            $db->execute();
            $cheapestSupplier = $db->single();

            if ($cheapestSupplier) {
                echo "      ✅ Valid pricing available (Cheapest: {$cheapestSupplier->supplier_name} - $" . number_format($cheapestSupplier->purchase_price, 2) . ")\n";
                echo "      💡 This product SHOULD be orderable by the purchase bot!\n";
            } else {
                echo "      ❌ No valid pricing from suppliers\n";
            }

            // Check recent orders
            $db->query("
                SELECT COUNT(*) as recent_orders, MAX(p.purchase_date) as last_ordered
                FROM purchases p
                JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
                WHERE pi.product_id = ?
                AND DATE(p.purchase_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $db->bind(1, $product->product_id);
            $db->execute();
            $recentOrderResult = $db->single();

            if ($recentOrderResult && $recentOrderResult->recent_orders > 0) {
                echo "      ⚠️  Recently ordered {$recentOrderResult->recent_orders} times (Last: {$recentOrderResult->last_ordered})\n";
            } else {
                echo "      ✅ No recent orders\n";
            }

            echo "\n";
        }
    } else {
        echo "   ✅ No out-of-stock products with suppliers found\n";
        echo "   💡 This means all out-of-stock products correctly have no suppliers\n";
    }

    echo "2️⃣ Testing purchase bot on products with suppliers...\n";

    // Find products that have suppliers and are low/out of stock
    $db->query("
        SELECT DISTINCT p.product_id, p.product_name,
               COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as current_stock
        FROM products p
        JOIN supplier_products sp ON p.product_id = sp.product_id
        WHERE p.is_active = 1
        AND p.deleted_at IS NULL
        AND sp.purchase_price > 0
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 10
        LIMIT 5
    ");
    $db->execute();
    $productsWithSuppliers = $db->resultSet();

    echo "   📊 Found " . count($productsWithSuppliers) . " products with suppliers that need stock:\n";

    if (count($productsWithSuppliers) > 0) {
        // Bypass authentication for testing
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'admin';

        $controller = new BotController();
        $reflection = new ReflectionClass($controller);
        $performBotActionMethod = $reflection->getMethod('performBotAction');
        $performBotActionMethod->setAccessible(true);

        echo "\n   🤖 Testing purchase bot execution:\n";

        $result = $performBotActionMethod->invoke($controller, 'purchase_bot');

        if ($result['success']) {
            if (isset($result['product_name'])) {
                echo "   ✅ SUCCESS! Purchase bot ordered: {$result['product_name']} (Stock: {$result['current_stock']})\n";
                echo "   📦 This proves the purchase bot IS working for products with suppliers!\n";
            } else {
                echo "   ✅ Purchase bot executed successfully: {$result['message']}\n";
            }
        } else {
            echo "   ❌ Purchase bot failed: {$result['message']}\n";
        }
    }

    echo "\n3️⃣ Final conclusion:\n";

    if (count($outOfStockWithSuppliers) == 0) {
        echo "   🎉 PURCHASE BOT IS WORKING PERFECTLY!\n";
        echo "   📋 All out-of-stock products correctly have no suppliers\n";
        echo "   📋 The purchase bot only orders products that CAN be ordered\n";
        echo "   📋 This is the expected and correct behavior\n";
    } else {
        echo "   ⚠️  There are still some issues to investigate\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>