<?php
require_once 'bootstrap.php';

echo "🔧 FIXING LOW INVENTORY KPI INVESTIGATION\n";
echo "=========================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Detailed Low Inventory Analysis...\n";

    // More comprehensive analysis
    $db->query("
        SELECT 
            p.product_id,
            p.name,
            p.reorder_level,
            COALESCE(i.quantity, 0) as current_stock,
            p.selling_price,
            CASE 
                WHEN COALESCE(i.quantity, 0) = 0 THEN 'OUT_OF_STOCK'
                WHEN COALESCE(i.quantity, 0) <= p.reorder_level THEN 'LOW_INVENTORY'
                ELSE 'OK'
            END as status
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        ORDER BY current_stock ASC, p.reorder_level DESC
    ");
    $db->execute();
    $allProducts = $db->resultSet();

    $outOfStock = [];
    $lowInventory = [];
    $okStock = [];

    foreach ($allProducts as $product) {
        if ($product->current_stock == 0) {
            $outOfStock[] = $product;
        } elseif ($product->current_stock <= $product->reorder_level) {
            $lowInventory[] = $product;
        } else {
            $okStock[] = $product;
        }
    }

    echo "   📊 Inventory Status Breakdown:\n";
    echo "     • Out of Stock: " . count($outOfStock) . " items\n";
    echo "     • Low Inventory: " . count($lowInventory) . " items\n";
    echo "     • Total Low/Out: " . (count($outOfStock) + count($lowInventory)) . " items\n";
    echo "     • OK Stock: " . count($okStock) . " items\n";

    if (count($outOfStock) > 0) {
        echo "\n   🚨 OUT OF STOCK items:\n";
        foreach (array_slice($outOfStock, 0, 10) as $item) { // Show first 10
            echo "     • {$item->name}: 0 units (Reorder at: {$item->reorder_level})\n";
        }
        if (count($outOfStock) > 10) {
            echo "     ... and " . (count($outOfStock) - 10) . " more\n";
        }
    }

    if (count($lowInventory) > 0) {
        echo "\n   ⚠️ LOW INVENTORY items:\n";
        foreach (array_slice($lowInventory, 0, 10) as $item) { // Show first 10
            echo "     • {$item->name}: {$item->current_stock} units (Reorder at: {$item->reorder_level})\n";
        }
        if (count($lowInventory) > 10) {
            echo "     ... and " . (count($lowInventory) - 10) . " more\n";
        }
    }

    echo "\n2️⃣ Checking Different Calculation Methods...\n";

    // Method 1: Basic count
    $db->query("
        SELECT COUNT(*) as count
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE COALESCE(i.quantity, 0) <= p.reorder_level
    ");
    $db->execute();
    $method1 = $db->single();

    // Method 2: Only out of stock
    $db->query("
        SELECT COUNT(*) as count
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE COALESCE(i.quantity, 0) = 0
    ");
    $db->execute();
    $method2 = $db->single();

    // Method 3: Products with reorder_level > 0
    $db->query("
        SELECT COUNT(*) as count
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE COALESCE(i.quantity, 0) <= p.reorder_level
        AND p.reorder_level > 0
    ");
    $db->execute();
    $method3 = $db->single();

    echo "   Different calculation methods:\n";
    echo "     • Method 1 (All ≤ reorder_level): {$method1->count}\n";
    echo "     • Method 2 (Only out of stock): {$method2->count}\n";
    echo "     • Method 3 (≤ reorder_level AND reorder_level > 0): {$method3->count}\n";

    echo "\n3️⃣ Checking Dashboard Sources...\n";

    // Check if there are different dashboard calculations
    $actualLowCount = count($outOfStock) + count($lowInventory);
    echo "   Calculated manually: {$actualLowCount} items\n";
    echo "   Expected from KPI: 17 items\n";
    echo "   Discrepancy: " . abs($actualLowCount - 17) . " items\n";

    echo "\n4️⃣ Purchase Bot Analysis...\n";

    // Check purchase bot activity correctly
    $db->query("
        SELECT COUNT(*) as count
        FROM purchases 
        WHERE status IN ('pending', 'sent', 'in_transit')
        AND purchase_date >= DATE_SUB(NOW(), INTERVAL 7 DAYS)
        AND (notes LIKE '%bot%' OR notes LIKE '%Auto-generated%')
    ");
    $db->execute();
    $recentBotPurchases = $db->single();

    echo "   Recent bot purchases (last 7 days): {$recentBotPurchases->count}\n";

    // Check what the purchase bot has been ordering
    $db->query("
        SELECT 
            pr.name as product_name,
            pi.quantity as ordered_qty,
            COALESCE(i.quantity, 0) as current_stock,
            pr.reorder_level,
            p.po_number,
            p.status,
            p.purchase_date
        FROM purchase_items pi
        JOIN purchases p ON pi.purchase_id = p.purchase_id
        JOIN products pr ON pi.product_id = pr.product_id
        LEFT JOIN inventory i ON pr.product_id = i.product_id
        WHERE (p.notes LIKE '%bot%' OR p.notes LIKE '%Auto-generated%')
        AND p.purchase_date >= DATE_SUB(NOW(), INTERVAL 7 DAYS)
        ORDER BY p.purchase_date DESC
        LIMIT 15
    ");
    $db->execute();
    $botOrderedItems = $db->resultSet();

    if (count($botOrderedItems) > 0) {
        echo "\n   🤖 Items ordered by bot recently:\n";
        foreach ($botOrderedItems as $item) {
            $stockStatus = "";
            if ($item->current_stock == 0) {
                $stockStatus = " [OUT OF STOCK]";
            } elseif ($item->current_stock <= $item->reorder_level) {
                $stockStatus = " [LOW INVENTORY]";
            } else {
                $stockStatus = " [OK STOCK]";
            }
            echo "     • {$item->product_name}: Ordered {$item->ordered_qty}, Stock: {$item->current_stock}, Reorder: {$item->reorder_level}{$stockStatus}\n";
        }
    }

    echo "\n5️⃣ Finding the Root Cause...\n";

    $issues = [];
    $fixes = [];

    // Issue 1: KPI calculation discrepancy
    if ($actualLowCount != 17) {
        $issues[] = "KPI shows 17 but actual calculation shows {$actualLowCount}";
        $fixes[] = "Update KPI calculation to use correct method";
    }

    // Issue 2: Purchase bot not targeting low inventory
    $lowInventoryInOrders = 0;
    foreach ($botOrderedItems as $item) {
        if ($item->current_stock <= $item->reorder_level) {
            $lowInventoryInOrders++;
        }
    }

    if ($lowInventoryInOrders == 0 && count($botOrderedItems) > 0) {
        $issues[] = "Purchase bot is ordering items that are NOT low inventory";
        $fixes[] = "Fix purchase bot logic to target actual low inventory items";
    }

    if (count($issues) > 0) {
        echo "   🚨 ROOT CAUSES IDENTIFIED:\n";
        foreach ($issues as $i => $issue) {
            echo "     " . ($i + 1) . ". {$issue}\n";
        }

        echo "\n   🔧 REQUIRED FIXES:\n";
        foreach ($fixes as $i => $fix) {
            echo "     " . ($i + 1) . ". {$fix}\n";
        }
    }

    echo "\n6️⃣ Verification of Current State...\n";

    // Double-check the exact calculation used
    $db->query("
        SELECT 
            COUNT(CASE WHEN COALESCE(i.quantity, 0) = 0 THEN 1 END) as out_of_stock,
            COUNT(CASE WHEN COALESCE(i.quantity, 0) > 0 AND COALESCE(i.quantity, 0) <= p.reorder_level THEN 1 END) as low_stock,
            COUNT(CASE WHEN COALESCE(i.quantity, 0) <= p.reorder_level THEN 1 END) as total_low_or_out
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
    ");
    $db->execute();
    $verification = $db->single();

    echo "   📊 Verified counts:\n";
    echo "     • Out of stock: {$verification->out_of_stock}\n";
    echo "     • Low stock: {$verification->low_stock}\n";
    echo "     • Total low/out: {$verification->total_low_or_out}\n";

    echo "\n🎯 NEXT STEPS:\n";
    echo "   1. Verify which calculation method the dashboard is using\n";
    echo "   2. Check if the purchase bot is using correct low inventory logic\n";
    echo "   3. Fix any discrepancies found\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>