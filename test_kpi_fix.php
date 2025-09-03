<?php
require_once 'bootstrap.php';

echo "🔧 TESTING PRODUCTS KPI FIX\n";
echo "===========================\n\n";

try {
    $productModel = new Product();

    echo "1️⃣ Testing new getAllProductsKpiStats method...\n";
    $kpiStats = $productModel->getAllProductsKpiStats();
    
    echo "   📊 All Products KPI Stats:\n";
    echo "     • Total Products: " . $kpiStats['total_products'] . "\n";
    echo "     • Total Inventory: " . number_format($kpiStats['total_inventory']) . "\n";
    echo "     • Avg Margin: " . ($kpiStats['avg_margin'] !== null ? number_format((float)$kpiStats['avg_margin'], 1) . '%' : '—') . "\n";
    echo "     • Need Reorder: " . $kpiStats['need_reorder'] . "\n";

    echo "\n2️⃣ Testing paginated method (how it was before)...\n";
    $paginatedProducts = $productModel->getProductsPaginated(0, 25, ''); // First 25 products
    
    echo "   📊 Paginated Products (first 25):\n";
    echo "     • Paginated Count: " . count($paginatedProducts) . "\n";
    
    // Calculate old way (from paginated data)
    $reorderCount = 0;
    if (!empty($paginatedProducts)) {
        foreach ($paginatedProducts as $p) {
            $ri = $p->reorder_level ?? 10;
            if (($p->current_inventory ?? 0) <= $ri)
                $reorderCount++;
        }
    }
    echo "     • Need Reorder (from paginated): " . $reorderCount . "\n";

    echo "\n3️⃣ Testing different page sizes...\n";
    
    // Test with 100 items per page
    $paginated100 = $productModel->getProductsPaginated(0, 100, '');
    echo "   📊 Paginated Products (first 100):\n";
    echo "     • Paginated Count: " . count($paginated100) . "\n";
    
    $reorderCount100 = 0;
    if (!empty($paginated100)) {
        foreach ($paginated100 as $p) {
            $ri = $p->reorder_level ?? 10;
            if (($p->current_inventory ?? 0) <= $ri)
                $reorderCount100++;
        }
    }
    echo "     • Need Reorder (from paginated): " . $reorderCount100 . "\n";

    echo "\n4️⃣ Verification...\n";
    
    if ($kpiStats['total_products'] > count($paginatedProducts)) {
        echo "   ✅ Total products (" . $kpiStats['total_products'] . ") is greater than paginated count (" . count($paginatedProducts) . ")\n";
    } else {
        echo "   ❌ Total products count seems incorrect\n";
    }
    
    if ($kpiStats['need_reorder'] !== $reorderCount) {
        echo "   ✅ Need reorder count differs between all products (" . $kpiStats['need_reorder'] . ") and paginated (" . $reorderCount . ") - this is the fix!\n";
    } else {
        echo "   ⚠️ Need reorder counts are the same - this might indicate all products fit in one page\n";
    }
    
    echo "\n🎯 SUMMARY:\n";
    echo "   Before fix: KPI cards showed data from paginated products (25, 50, 100, etc.)\n";
    echo "   After fix: KPI cards show data from ALL products regardless of pagination\n";
    echo "   This means the KPI cards will always show accurate totals even when filtering or changing page size.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
