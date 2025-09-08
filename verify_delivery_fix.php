<?php
require 'bootstrap.php';

$db = new Database();

echo "=== DELIVERY TIME CONSISTENCY VERIFICATION ===\n\n";

echo "1. CHECKING DHARAMVIR PVT LTD:\n";
echo "==============================\n";

$db->query("
    SELECT 
        s.supplier_name,
        s.default_delivery_days,
        p.product_name,
        ps.lead_time_days
    FROM suppliers s
    INNER JOIN product_suppliers ps ON s.supplier_id = ps.supplier_id
    INNER JOIN products p ON ps.product_id = p.product_id
    WHERE s.supplier_name = 'Dharamvir Pvt Ltd'
    ORDER BY p.product_name
");

$db->execute();
$dharamvirProducts = $db->resultSet();

foreach ($dharamvirProducts as $product) {
    $status = ($product->lead_time_days == $product->default_delivery_days) ? "✅ CONSISTENT" : "❌ INCONSISTENT";
    echo "• {$product->product_name}: {$product->lead_time_days} days (Default: {$product->default_delivery_days}) {$status}\n";
}

echo "\n2. CHECKING ISHAAN ELECTRICAL SUPPLIERS:\n";
echo "=========================================\n";

$db->query("
    SELECT 
        s.supplier_name,
        s.default_delivery_days,
        p.product_name,
        ps.lead_time_days
    FROM suppliers s
    INNER JOIN product_suppliers ps ON s.supplier_id = ps.supplier_id
    INNER JOIN products p ON ps.product_id = p.product_id
    WHERE s.supplier_name = 'Ishaan Electrical Suppliers'
    ORDER BY p.product_name
");

$db->execute();
$ishaanProducts = $db->resultSet();

foreach ($ishaanProducts as $product) {
    $status = ($product->lead_time_days == $product->default_delivery_days) ? "✅ CONSISTENT" : "❌ INCONSISTENT";
    echo "• {$product->product_name}: {$product->lead_time_days} days (Default: {$product->default_delivery_days}) {$status}\n";
}

echo "\n3. OVERALL CONSISTENCY CHECK:\n";
echo "=============================\n";

$db->query("
    SELECT 
        COUNT(*) as total_relationships,
        SUM(CASE WHEN ps.lead_time_days = s.default_delivery_days THEN 1 ELSE 0 END) as consistent_count,
        SUM(CASE WHEN ps.lead_time_days != s.default_delivery_days THEN 1 ELSE 0 END) as inconsistent_count
    FROM product_suppliers ps
    INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
    WHERE s.default_delivery_days IS NOT NULL AND s.default_delivery_days > 0
");

$db->execute();
$stats = $db->single();

echo "Total relationships with default delivery days: {$stats->total_relationships}\n";
echo "Consistent: {$stats->consistent_count}\n";
echo "Inconsistent: {$stats->inconsistent_count}\n";
echo "Consistency Rate: " . round(($stats->consistent_count / $stats->total_relationships) * 100, 1) . "%\n\n";

if ($stats->inconsistent_count == 0) {
    echo "🎉 ALL DELIVERY TIMES ARE NOW CONSISTENT!\n";
} else {
    echo "⚠️  Some inconsistencies remain. May need manual review.\n";
}

echo "\n4. TESTING COMPETITION REPORT QUERY:\n";
echo "====================================\n";

// Test the simplified query for a product with Dharamvir
$db->query("
    SELECT p.product_id, p.product_name
    FROM products p
    INNER JOIN product_suppliers ps ON p.product_id = ps.product_id
    INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
    WHERE s.supplier_name = 'Dharamvir Pvt Ltd'
    LIMIT 1
");

$db->execute();
$testProduct = $db->single();

if ($testProduct) {
    echo "Testing with product: {$testProduct->product_name}\n";
    
    $db->query("
        SELECT 
            s.supplier_name,
            ps.lead_time_days as delivery_time,
            ps.purchase_price
        FROM product_suppliers ps
        INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
        WHERE ps.product_id = :product_id
        AND ps.purchase_price > 0
        ORDER BY ps.purchase_price ASC
    ");
    
    $db->bind(':product_id', $testProduct->product_id);
    $db->execute();
    $suppliers = $db->resultSet();
    
    foreach ($suppliers as $supplier) {
        $highlight = ($supplier->supplier_name == 'Dharamvir Pvt Ltd') ? " ← 🎯" : "";
        echo "• {$supplier->supplier_name}: {$supplier->delivery_time} days, ₹{$supplier->purchase_price}{$highlight}\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
?>
