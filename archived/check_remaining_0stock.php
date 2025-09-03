<?php
require_once 'bootstrap.php';

echo "🔍 CHECKING REMAINING OUT-OF-STOCK PRODUCTS\n";
echo "===========================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Finding products still out of stock...\n";

    $db->query("
        SELECT
            p.product_id,
            p.product_name,
            p.reorder_level,
            COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as current_stock
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND p.deleted_at IS NULL
        AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 0
        ORDER BY p.product_name
    ");
    $db->execute();
    $outOfStockProducts = $db->resultSet();

    echo "   📊 Found " . count($outOfStockProducts) . " products still out of stock:\n\n";

    foreach ($outOfStockProducts as $product) {
        echo "   📦 {$product->product_name} (ID: {$product->product_id})\n";
        echo "      Stock: {$product->current_stock}, Reorder Level: {$product->reorder_level}\n";

        // Check if this product has suppliers
        $db->query("
            SELECT COUNT(*) as supplier_count
            FROM supplier_products sp
            WHERE sp.product_id = ?
        ");
        $db->bind(1, $product->product_id);
        $db->execute();
        $supplierResult = $db->single();
        $supplierCount = $supplierResult->supplier_count ?? 0;

        echo "      Suppliers: {$supplierCount}\n";

        if ($supplierCount == 0) {
            echo "      ❌ NO SUPPLIERS - This is why it can't be ordered!\n";
        } else {
            // Check if suppliers have valid pricing
            $db->query("
                SELECT sp.purchase_price, s.supplier_name
                FROM supplier_products sp
                JOIN suppliers s ON sp.supplier_id = s.supplier_id
                WHERE sp.product_id = ?
                ORDER BY sp.purchase_price ASC
                LIMIT 1
            ");
            $db->bind(1, $product->product_id);
            $db->execute();
            $cheapestSupplier = $db->single();

            if ($cheapestSupplier && $cheapestSupplier->purchase_price > 0) {
                echo "      ✅ Has suppliers with valid pricing (Cheapest: {$cheapestSupplier->supplier_name} - $" . number_format($cheapestSupplier->purchase_price, 2) . ")\n";
            } else {
                echo "      ❌ Has suppliers but INVALID PRICING!\n";
            }
        }

        // Check if recently ordered
        $db->query("
            SELECT COUNT(*) as recent_orders
            FROM purchases p
            JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
            WHERE pi.product_id = ?
            AND DATE(p.purchase_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ");
        $db->bind(1, $product->product_id);
        $db->execute();
        $recentOrderResult = $db->single();
        $recentOrders = $recentOrderResult->recent_orders ?? 0;

        if ($recentOrders > 0) {
            echo "      ⚠️  Recently ordered {$recentOrders} times (within 7 days)\n";
        }

        echo "\n";
    }

    echo "2️⃣ Summary of issues:\n";

    $noSuppliers = 0;
    $invalidPricing = 0;
    $recentlyOrdered = 0;
    $shouldBeOrderable = 0;

    foreach ($outOfStockProducts as $product) {
        // Check suppliers
        $db->query("SELECT COUNT(*) as supplier_count FROM supplier_products sp WHERE sp.product_id = ?");
        $db->bind(1, $product->product_id);
        $db->execute();
        $supplierResult = $db->single();
        $supplierCount = $supplierResult->supplier_count ?? 0;

        if ($supplierCount == 0) {
            $noSuppliers++;
        } else {
            // Check pricing
            $db->query("
                SELECT sp.purchase_price
                FROM supplier_products sp
                WHERE sp.product_id = ?
                ORDER BY sp.purchase_price ASC
                LIMIT 1
            ");
            $db->bind(1, $product->product_id);
            $db->execute();
            $cheapestSupplier = $db->single();

            if (!$cheapestSupplier || $cheapestSupplier->purchase_price <= 0) {
                $invalidPricing++;
            } else {
                // Check recent orders
                $db->query("
                    SELECT COUNT(*) as recent_orders
                    FROM purchases p
                    JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
                    WHERE pi.product_id = ?
                    AND DATE(p.purchase_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                ");
                $db->bind(1, $product->product_id);
                $db->execute();
                $recentOrderResult = $db->single();
                $recentOrders = $recentOrderResult->recent_orders ?? 0;

                if ($recentOrders > 0) {
                    $recentlyOrdered++;
                } else {
                    $shouldBeOrderable++;
                }
            }
        }
    }

    echo "   📊 Analysis:\n";
    echo "      • No suppliers: {$noSuppliers} products\n";
    echo "      • Invalid pricing: {$invalidPricing} products\n";
    echo "      • Recently ordered: {$recentlyOrdered} products\n";
    echo "      • Should be orderable: {$shouldBeOrderable} products\n\n";

    if ($shouldBeOrderable > 0) {
        echo "   💡 There are {$shouldBeOrderable} products that should be orderable but aren't being ordered.\n";
        echo "   🔧 This might be due to the 'recently ordered' logic in the purchase bot.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>