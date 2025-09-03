<?php
require_once 'bootstrap.php';

echo "🔍 CHECKING FOR OUT-OF-STOCK PRODUCTS\n";
echo "=====================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Checking total products in database...\n";

    $db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1 AND deleted_at IS NULL");
    $db->execute();
    $totalProducts = $db->single()->total;

    echo "   📊 Total active products: {$totalProducts}\n";

    echo "\n2️⃣ Checking inventory levels...\n";

    $db->query("
        SELECT
            COUNT(*) as total_with_inventory,
            SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) as in_stock,
            MIN(quantity) as min_stock,
            MAX(quantity) as max_stock,
            AVG(quantity) as avg_stock
        FROM inventory
    ");
    $db->execute();
    $inventoryStats = $db->single();

    echo "   📊 Inventory statistics:\n";
    echo "      • Products with inventory records: {$inventoryStats->total_with_inventory}\n";
    echo "      • Out of stock (≤0): {$inventoryStats->out_of_stock}\n";
    echo "      • In stock (>0): {$inventoryStats->in_stock}\n";
    echo "      • Stock range: {$inventoryStats->min_stock} to {$inventoryStats->max_stock}\n";
    echo "      • Average stock: " . round($inventoryStats->avg_stock, 2) . "\n";

    echo "\n3️⃣ Checking out-of-stock products specifically...\n";

    $db->query("
        SELECT
            p.product_id,
            p.product_name,
            COALESCE(i.quantity, 0) as current_stock,
            p.created_at,
            p.updated_at
        FROM products p
        LEFT JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND p.deleted_at IS NULL
        AND COALESCE(i.quantity, 0) <= 0
        ORDER BY p.product_name
    ");
    $db->execute();
    $outOfStockProducts = $db->resultSet();

    echo "   📊 Found " . count($outOfStockProducts) . " out-of-stock products:\n";

    if (count($outOfStockProducts) > 0) {
        foreach ($outOfStockProducts as $product) {
            echo "      • {$product->product_name} (ID: {$product->product_id}) - Stock: {$product->current_stock}\n";
        }
    } else {
        echo "   ❌ No out-of-stock products found!\n";
        echo "   💡 This explains why the purchase bot has nothing to order\n";
    }

    echo "\n4️⃣ Checking if products have suppliers...\n";

    if (count($outOfStockProducts) > 0) {
        $productIds = array_column($outOfStockProducts, 'product_id');
        $inClause = str_repeat('?,', count($productIds) - 1) . '?';

        $db->query("
            SELECT DISTINCT ps.product_id, p.product_name, s.supplier_name
            FROM product_suppliers ps
            INNER JOIN products p ON ps.product_id = p.product_id
            INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id IN ({$inClause})
            ORDER BY p.product_name, s.supplier_name
        ");
        $db->execute();
        $productsWithSuppliers = $db->resultSet();

        echo "   📊 Out-of-stock products with suppliers: " . count($productsWithSuppliers) . "\n";

        $supplierCount = [];
        foreach ($productsWithSuppliers as $item) {
            if (!isset($supplierCount[$item->product_id])) {
                $supplierCount[$item->product_id] = 0;
            }
            $supplierCount[$item->product_id]++;
        }

        foreach ($outOfStockProducts as $product) {
            $supplierNum = $supplierCount[$product->product_id] ?? 0;
            $status = $supplierNum > 0 ? "✅ Has suppliers" : "❌ No suppliers";
            echo "      • {$product->product_name} - {$status} ({$supplierNum} suppliers)\n";
        }
    }

    echo "\n5️⃣ Summary:\n";

    if (count($outOfStockProducts) == 0) {
        echo "   📋 NO OUT-OF-STOCK PRODUCTS FOUND\n";
        echo "   💡 The purchase bot has nothing to work with\n";
        echo "   💡 Need to create some out-of-stock products or adjust inventory\n";
    } else {
        $withSuppliers = count(array_filter($outOfStockProducts, function ($p) use ($supplierCount) {
            return ($supplierCount[$p->product_id] ?? 0) > 0;
        }));

        echo "   📊 Found " . count($outOfStockProducts) . " out-of-stock products\n";
        echo "   📊 {$withSuppliers} have suppliers available\n";

        if ($withSuppliers > 0) {
            echo "   🎯 PURCHASE BOT SHOULD BE ABLE TO ORDER THESE PRODUCTS\n";
        } else {
            echo "   ❌ NO PRODUCTS HAVE SUPPLIERS - BOT CAN'T ORDER ANYTHING\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>