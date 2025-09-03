<?php
/**
 * Quick test to verify supplier products are working
 */

require_once 'bootstrap.php';

echo "<h3>Supplier Products Debug Test</h3>";

// Test with supplier ID 1 which we know has products
$productModel = new Product();
$supplierProducts = $productModel->getProductsBySupplier(1);

echo "<p>Testing Supplier ID 1 (Tool World Distributors)</p>";
echo "<p>Products found: " . count($supplierProducts) . "</p>";

if (!empty($supplierProducts)) {
    echo "<h4>First 5 products:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Product ID</th><th>Name</th><th>SKU</th><th>Supplier Price</th><th>Status</th><th>Is Primary</th></tr>";
    
    for ($i = 0; $i < min(5, count($supplierProducts)); $i++) {
        $product = $supplierProducts[$i];
        echo "<tr>";
        echo "<td>" . ($product->product_id ?? 'N/A') . "</td>";
        echo "<td>" . ($product->product_name ?? 'N/A') . "</td>";
        echo "<td>" . ($product->sku ?? 'N/A') . "</td>";
        echo "<td>" . ($product->supplier_price ?? 'N/A') . "</td>";
        echo "<td>" . ($product->status ?? 'N/A') . "</td>";
        echo "<td>" . ($product->is_primary ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p><strong>ERROR: No products found!</strong></p>";
    
    // Test controller data
    echo "<h4>Testing Controller Logic:</h4>";
    $supplierModel = new Supplier();
    $supplier = $supplierModel->getSupplierById(1);
    
    if ($supplier) {
        echo "<p>Supplier found: " . $supplier->supplier_name . "</p>";
        
        // Test the controller logic
        $supplierProducts = $productModel->getProductsBySupplier(1);
        echo "<p>Products from controller logic: " . count($supplierProducts) . "</p>";
        
        $data = [
            'supplier' => $supplier,
            'supplier_products' => $supplierProducts ?: []
        ];
        
        echo "<p>Data array supplier_products count: " . count($data['supplier_products']) . "</p>";
        echo "<p>Is supplier_products empty? " . (empty($data['supplier_products']) ? 'YES' : 'NO') . "</p>";
    } else {
        echo "<p>Supplier not found!</p>";
    }
}

// Test what's actually happening in the database
echo "<h4>Direct Database Check:</h4>";
$db = new Database();
$db->query("SELECT COUNT(*) as count FROM product_suppliers WHERE supplier_id = 1 AND is_active = 1");
$db->execute();
$result = $db->single();
echo "<p>Active product links in database: " . ($result->count ?? 'Error') . "</p>";
?>
