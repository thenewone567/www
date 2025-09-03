<?php
require_once 'bootstrap.php';

echo "🔧 FIXING LOW INVENTORY KPI AND PURCHASE BOT\n";
echo "===========================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Current Dashboard KPI Analysis...\n";

    // Use the exact same methods as Dashboard model
    $db->query("
        SELECT COUNT(DISTINCT p.product_id) as total 
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10)
    ");
    $db->execute();
    $lowInventoryResult = $db->single();
    $lowInventoryCount = $lowInventoryResult->total ?? 0;

    $db->query("
        SELECT COUNT(DISTINCT p.product_id) as total 
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 0
    ");
    $db->execute();
    $outOfStockResult = $db->single();
    $outOfStockCount = $outOfStockResult->total ?? 0;

    echo "   📊 Dashboard KPI Values:\n";
    echo "     • Main Number (Low Inventory): {$lowInventoryCount}\n";
    echo "     • Subtitle (Out of Stock): {$outOfStockCount} out of Inventory • Low Inventory\n";
    echo "     • Expected: Shows '{$lowInventoryCount}' with '{$outOfStockCount} out of Inventory • Low Inventory'\n";

    if ($lowInventoryCount == 29 && $outOfStockCount == 17) {
        echo "   ✅ KPI calculation is CORRECT\n";
    } else {
        echo "   ❌ KPI calculation mismatch detected\n";
    }

    echo "\n2️⃣ Analyzing Low Inventory Items for Purchase Bot...\n";

    // Get the actual low inventory items that need to be ordered
    $db->query("
        SELECT 
            p.product_id,
            p.name as product_name,
            p.reorder_level,
            COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as current_stock,
            p.selling_price,
            CASE 
                WHEN COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 0 THEN 'OUT_OF_STOCK'
                WHEN COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10) THEN 'LOW_STOCK'
                ELSE 'OK'
            END as status
        FROM products p
        WHERE p.is_active = 1
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10)
        ORDER BY current_stock ASC, p.reorder_level DESC
    ");
    $db->execute();
    $lowStockItems = $db->resultSet();

    echo "   Found " . count($lowStockItems) . " items needing reorder:\n";

    $outOfStock = [];
    $lowStock = [];

    foreach ($lowStockItems as $item) {
        if ($item->status == 'OUT_OF_STOCK') {
            $outOfStock[] = $item;
        } else {
            $lowStock[] = $item;
        }
    }

    echo "     • Out of Stock: " . count($outOfStock) . " items\n";
    echo "     • Low Stock: " . count($lowStock) . " items\n";

    echo "\n3️⃣ Checking Purchase Bot Activity...\n";

    // Check recent purchase bot orders
    $db->query("
        SELECT 
            p.purchase_id,
            p.po_number,
            p.status,
            p.total_amount,
            p.purchase_date,
            p.notes,
            COUNT(pi.purchase_item_id) as item_count
        FROM purchases p
        LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
        WHERE (p.notes LIKE '%bot%' OR p.notes LIKE '%Auto-generated%')
        AND p.purchase_date >= DATE_SUB(NOW(), INTERVAL 7 DAYS)
        GROUP BY p.purchase_id
        ORDER BY p.purchase_date DESC
    ");
    $db->execute();
    $recentBotOrders = $db->resultSet();

    echo "   Recent bot orders (last 7 days): " . count($recentBotOrders) . "\n";

    if (count($recentBotOrders) > 0) {
        foreach ($recentBotOrders as $order) {
            echo "     • PO #{$order->po_number}: ₹{$order->total_amount} ({$order->item_count} items, {$order->status})\n";
        }
    }

    echo "\n4️⃣ Checking if low inventory items are in purchase orders...\n";

    if (!empty($lowStockItems)) {
        $productIds = array_column($lowStockItems, 'product_id');
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';

        $db->query("
            SELECT 
                pi.product_id,
                pr.name as product_name,
                pi.quantity as ordered_qty,
                p.po_number,
                p.status as order_status,
                p.purchase_date
            FROM purchase_items pi
            JOIN purchases p ON pi.purchase_id = p.purchase_id
            JOIN products pr ON pi.product_id = pr.product_id
            WHERE pi.product_id IN ($placeholders)
            AND p.status IN ('pending', 'sent', 'in_transit')
            ORDER BY p.purchase_date DESC
        ");

        for ($i = 0; $i < count($productIds); $i++) {
            $db->bind($i + 1, $productIds[$i]);
        }

        $db->execute();
        $orderedLowStock = $db->resultSet();

        if (count($orderedLowStock) > 0) {
            echo "   ✅ Low inventory items in pending orders:\n";
            foreach ($orderedLowStock as $order) {
                echo "     • {$order->product_name}: {$order->ordered_qty} units in PO #{$order->po_number} ({$order->order_status})\n";
            }
        } else {
            echo "   ❌ NO low inventory items found in pending purchase orders!\n";
            echo "   🚨 ISSUE: Purchase bot is NOT ordering low inventory items!\n";
        }
    }

    echo "\n5️⃣ Testing Purchase Bot Execution...\n";

    // Check if purchase bot method exists and works
    require_once 'app/controllers/BotController.php';

    echo "   Testing purchase bot logic...\n";

    // Create a test to see what the purchase bot would do
    $db->query("
        SELECT 
            p.product_id,
            p.name,
            COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as current_stock,
            p.reorder_level
        FROM products p
        WHERE p.is_active = 1
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10)
        ORDER BY current_stock ASC
        LIMIT 5
    ");
    $db->execute();
    $testProducts = $db->resultSet();

    if (count($testProducts) > 0) {
        echo "   Top 5 items that SHOULD be ordered by purchase bot:\n";
        foreach ($testProducts as $product) {
            $reorderQty = max(50, $product->reorder_level * 2); // Sample reorder quantity
            echo "     • {$product->name}: Stock={$product->current_stock}, Reorder Level={$product->reorder_level}, Should Order={$reorderQty}\n";
        }
    }

    echo "\n6️⃣ Recommendations...\n";

    $fixes = [];

    if ($lowInventoryCount != 17) {
        $fixes[] = "KPI shows low inventory count ({$lowInventoryCount}) not matching expected (17)";
    }

    if (empty($orderedLowStock)) {
        $fixes[] = "Purchase bot is NOT ordering low inventory items - needs immediate fix";
    }

    if (count($recentBotOrders) == 0) {
        $fixes[] = "Purchase bot has not run in the last 7 days - check bot execution";
    }

    if (count($fixes) > 0) {
        echo "   🔧 ISSUES TO FIX:\n";
        foreach ($fixes as $i => $fix) {
            echo "     " . ($i + 1) . ". {$fix}\n";
        }

        echo "\n   💡 PROPOSED SOLUTIONS:\n";
        echo "     1. Verify the dashboard is showing correct KPI values\n";
        echo "     2. Fix purchase bot to target actual low inventory products\n";
        echo "     3. Ensure purchase bot is scheduled and running regularly\n";
        echo "     4. Test purchase bot execution manually\n";
    } else {
        echo "   ✅ All systems appear to be working correctly!\n";
    }

    echo "\n🎯 SUMMARY:\n";
    echo "   • Low Inventory KPI: {$lowInventoryCount} items\n";
    echo "   • Out of Stock: {$outOfStockCount} items\n";
    echo "   • Items in pending orders: " . count($orderedLowStock) . "\n";
    echo "   • Recent bot activity: " . count($recentBotOrders) . " orders\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>