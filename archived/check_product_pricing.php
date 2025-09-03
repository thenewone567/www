<?php
require_once 'bootstrap.php';

echo "🔍 Product Pricing Analysis\n";
echo "===========================\n\n";

try {
    $db = new Database();

    // Check out-of-stock products and their pricing
    echo "📦 Out-of-stock products with pricing:\n";
    echo "-------------------------------------\n";

    $db->query("
        SELECT 
            p.product_id, 
            p.product_name, 
            p.purchase_price,
            p.selling_price,
            p.reorder_level,
            COALESCE(inv.total_qty, 0) as stock
        FROM products p
        LEFT JOIN (
            SELECT product_id, SUM(quantity) as total_qty 
            FROM inventory 
            GROUP BY product_id
        ) inv ON p.product_id = inv.product_id
        WHERE p.is_active = 1
        AND p.deleted_at IS NULL
        AND COALESCE(inv.total_qty, 0) <= 0
        LIMIT 10
    ");
    $products = $db->executeResultSet();

    if (!empty($products)) {
        foreach ($products as $product) {
            echo "• {$product->product_name} (ID: {$product->product_id})\n";
            echo "  Purchase Price: \$" . ($product->purchase_price ?? 'NULL') . "\n";
            echo "  Selling Price: \$" . ($product->selling_price ?? 'NULL') . "\n";
            echo "  Reorder Level: " . ($product->reorder_level ?? 'NULL') . "\n";
            echo "  Stock: {$product->stock}\n\n";
        }

        // Test the fallback pricing calculation
        echo "🧮 Testing dynamic pricing calculation:\n";
        echo "======================================\n";

        foreach (array_slice($products, 0, 3) as $product) {
            if ($product->purchase_price > 0) {
                $basePrice = $product->purchase_price;
                echo "\n🏷️  {$product->product_name}\n";
                echo "   Base Price: \${$basePrice}\n";
                echo "   Budget Supplier (15% off): \$" . round($basePrice * 0.85, 2) . "\n";
                echo "   Quality Supplier (5% off): \$" . round($basePrice * 0.95, 2) . "\n";
                echo "   Premium Supplier (5% markup): \$" . round($basePrice * 1.05, 2) . "\n";

                // Test quantity calculation
                $sellingPrice = $product->selling_price ?? 50;
                if ($sellingPrice <= 20) {
                    $monthlyDemand = "15-25 (High Volume)";
                } elseif ($sellingPrice <= 50) {
                    $monthlyDemand = "8-15 (Medium Volume)";
                } else {
                    $monthlyDemand = "3-8 (Low Volume)";
                }
                echo "   Selling Price: \${$sellingPrice} → Monthly Demand: {$monthlyDemand}\n";
            }
        }

    } else {
        echo "❌ No out-of-stock products found\n";
    }

    // Check if supplier tables exist
    echo "\n🏪 Checking supplier data:\n";
    echo "=========================\n";

    $db->query("SELECT COUNT(*) as count FROM suppliers");
    $db->execute();
    $supplierCount = $db->single();
    echo "Suppliers in database: " . ($supplierCount ? $supplierCount->count : 0) . "\n";

    $db->query("SELECT COUNT(*) as count FROM product_suppliers");
    $db->execute();
    $productSupplierCount = $db->single();
    echo "Product-supplier relationships: " . ($productSupplierCount ? $productSupplierCount->count : 0) . "\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
?>