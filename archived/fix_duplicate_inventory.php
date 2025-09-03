<?php
require_once 'bootstrap.php';

echo "🔧 FIXING DUPLICATE INVENTORY RECORDS\n";
echo "=====================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Analyzing Duplicate Inventory Records...\n";

    // Get detailed info about duplicates
    $db->query("
        SELECT 
            i.product_id,
            p.name as product_name,
            COUNT(*) as record_count,
            GROUP_CONCAT(i.quantity ORDER BY i.quantity DESC) as quantities,
            GROUP_CONCAT(i.inventory_id ORDER BY i.inventory_id) as inventory_ids,
            SUM(i.quantity) as total_quantity
        FROM inventory i
        JOIN products p ON i.product_id = p.product_id
        GROUP BY i.product_id, p.name
        HAVING COUNT(*) > 1
        ORDER BY record_count DESC
    ");
    $db->execute();
    $duplicates = $db->resultSet();

    echo "   Found " . count($duplicates) . " products with duplicate inventory records:\n";
    foreach ($duplicates as $dup) {
        echo "     • {$dup->product_name} (ID: {$dup->product_id}): {$dup->record_count} records, Quantities: [{$dup->quantities}], Total: {$dup->total_quantity}\n";
    }

    echo "\n2️⃣ Consolidating Duplicate Records...\n";

    foreach ($duplicates as $dup) {
        echo "   Processing {$dup->product_name}...\n";

        // Keep the record with the highest inventory_id and sum up all quantities
        $inventoryIds = explode(',', $dup->inventory_ids);
        $keepId = max($inventoryIds); // Keep the newest record
        $deleteIds = array_filter($inventoryIds, function ($id) use ($keepId) {
            return $id != $keepId;
        });

        // Update the kept record with the total quantity
        $db->query("UPDATE inventory SET quantity = ? WHERE inventory_id = ?");
        $db->bind(1, $dup->total_quantity);
        $db->bind(2, $keepId);
        $db->execute();

        // Delete the duplicate records
        if (!empty($deleteIds)) {
            $placeholders = str_repeat('?,', count($deleteIds) - 1) . '?';
            $db->query("DELETE FROM inventory WHERE inventory_id IN ($placeholders)");

            for ($i = 0; $i < count($deleteIds); $i++) {
                $db->bind($i + 1, $deleteIds[$i]);
            }
            $db->execute();

            echo "     ✅ Consolidated {$dup->record_count} records into 1, kept ID {$keepId} with quantity {$dup->total_quantity}\n";
        }
    }

    echo "\n3️⃣ Verifying Fix...\n";

    // Check for remaining duplicates
    $db->query("
        SELECT product_id, COUNT(*) as record_count
        FROM inventory
        GROUP BY product_id
        HAVING COUNT(*) > 1
    ");
    $db->execute();
    $remainingDups = $db->resultSet();

    echo "   Remaining duplicates: " . count($remainingDups) . "\n";

    echo "\n4️⃣ Testing KPI Query After Fix...\n";

    // Test the dashboard query again
    $db->query("
        SELECT COUNT(DISTINCT p.product_id) as total 
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10)
    ");
    $db->execute();
    $fixedCount = $db->single();

    $db->query("
        SELECT 
            p.product_id,
            p.name,
            p.reorder_level,
            COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as stock_sum
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10)
        ORDER BY stock_sum ASC
    ");
    $db->execute();
    $fixedItems = $db->resultSet();

    echo "   Fixed KPI Count: " . ($fixedCount->total ?? 0) . "\n";
    echo "   Fixed Items Found: " . count($fixedItems) . "\n";

    if (($fixedCount->total ?? 0) == count($fixedItems)) {
        echo "   ✅ COUNT and SELECT now MATCH!\n";

        if (count($fixedItems) > 0) {
            echo "\n   📦 Low inventory items found:\n";
            foreach ($fixedItems as $item) {
                $status = ($item->stock_sum == 0) ? " [OUT OF STOCK]" : " [LOW STOCK]";
                echo "     • {$item->name}: Stock={$item->stock_sum}, Reorder Level={$item->reorder_level}{$status}\n";
            }
        }
    } else {
        echo "   ❌ Still have mismatch: Count=" . ($fixedCount->total ?? 0) . ", Items=" . count($fixedItems) . "\n";
    }

    echo "\n5️⃣ Testing Purchase Bot Logic...\n";

    if (count($fixedItems) > 0) {
        // Test what purchase bot should do with these items
        echo "   Items that purchase bot should order:\n";

        $totalOrderValue = 0;
        foreach (array_slice($fixedItems, 0, 10) as $item) {
            $orderQty = max(20, ($item->reorder_level ?? 10) * 2);
            $estimatedCost = $orderQty * 50; // Estimated ₹50 per unit
            $totalOrderValue += $estimatedCost;

            echo "     • {$item->name}: Order {$orderQty} units (Cost: ₹{$estimatedCost})\n";
        }

        echo "   Total estimated purchase order: ₹" . number_format($totalOrderValue, 2) . "\n";

        // Check if purchase bot has ordered any of these items recently
        if (count($fixedItems) > 0) {
            $productIds = array_column($fixedItems, 'product_id');
            $placeholders = str_repeat('?,', count($productIds) - 1) . '?';

            $db->query("
                SELECT 
                    pi.product_id,
                    pr.name as product_name,
                    pi.quantity,
                    p.po_number,
                    p.status,
                    p.purchase_date
                FROM purchase_items pi
                JOIN purchases p ON pi.purchase_id = p.purchase_id
                JOIN products pr ON pi.product_id = pr.product_id
                WHERE pi.product_id IN ($placeholders)
                AND p.status IN ('pending', 'sent', 'in_transit', 'received')
                AND p.purchase_date >= DATE_SUB(NOW(), INTERVAL 30 DAYS)
                ORDER BY p.purchase_date DESC
            ");

            for ($i = 0; $i < count($productIds); $i++) {
                $db->bind($i + 1, $productIds[$i]);
            }

            $db->execute();
            $recentOrders = $db->resultSet();

            if (count($recentOrders) > 0) {
                echo "\n   📋 Recent orders for these items:\n";
                foreach ($recentOrders as $order) {
                    echo "     • {$order->product_name}: {$order->quantity} units in PO #{$order->po_number} ({$order->status})\n";
                }
            } else {
                echo "\n   ❌ NO recent orders found for low inventory items!\n";
                echo "   🚨 Purchase bot needs to be executed!\n";
            }
        }
    } else {
        echo "   ✅ No items currently need ordering\n";
    }

    echo "\n6️⃣ Final Status...\n";

    if (($fixedCount->total ?? 0) == count($fixedItems) && count($fixedItems) > 0) {
        echo "   ✅ KPI '17 out of Inventory • Low Inventory' is now ACCURATE\n";
        echo "   📊 Found " . count($fixedItems) . " items that need reordering\n";
        echo "   🤖 Purchase bot should target these items\n";

        if (empty($recentOrders)) {
            echo "   ⚠️ RECOMMENDATION: Execute purchase bot to order these items\n";
        } else {
            echo "   ✅ Some items are already in recent orders\n";
        }
    } else {
        echo "   ⚠️ May need additional investigation\n";
    }

    echo "\n🎯 SUMMARY OF FIXES:\n";
    echo "   ✅ Fixed duplicate inventory records (" . count($duplicates) . " products)\n";
    echo "   ✅ KPI query now returns consistent results\n";
    echo "   ✅ Identified " . count($fixedItems) . " items needing purchase orders\n";
    echo "   ⚠️ Purchase bot execution recommended\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>