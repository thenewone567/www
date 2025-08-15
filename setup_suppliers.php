<?php
require_once 'bootstrap.php';

$db = new Database();

echo "<h2>Setting up Product-Supplier Links</h2>";

// Get first 5 products without suppliers
$db->query("
    SELECT product_id, product_name, sku
    FROM products p
    WHERE p.is_active = 1 
    AND p.product_id NOT IN (
        SELECT DISTINCT product_id 
        FROM product_suppliers 
        WHERE is_active = 1
    )
    LIMIT 5
");
$db->execute();
$products = $db->resultSet();

// Get first 3 suppliers
$db->query("SELECT supplier_id, supplier_name FROM suppliers WHERE deleted_at IS NULL LIMIT 3");
$db->execute();
$suppliers = $db->resultSet();

if (count($products) > 0 && count($suppliers) > 0) {
    echo "<h3>Creating Links:</h3>";
    
    foreach ($products as $index => $product) {
        // Cycle through suppliers for variety
        $supplier = $suppliers[$index % count($suppliers)];
        
        // Random price between $10-100
        $price = rand(1000, 10000) / 100;
        
        echo "Linking Product '{$product->product_name}' to Supplier '{$supplier->supplier_name}' at ${$price}<br>";
        
        // Insert the link
        $db->query("
            INSERT INTO product_suppliers 
            (product_id, supplier_id, purchase_price, lead_time_days, min_order_quantity, is_active, is_primary, created_at) 
            VALUES (?, ?, ?, ?, ?, 1, 1, NOW())
        ");
        $db->bind(1, $product->product_id);
        $db->bind(2, $supplier->supplier_id);
        $db->bind(3, $price);
        $db->bind(4, rand(1, 14)); // Lead time 1-14 days
        $db->bind(5, rand(1, 10)); // Min order 1-10
        $db->execute();
    }
    
    echo "<h3>✓ Links created successfully!</h3>";
    echo "<p><a href='/www/purchases/add'>Go to Purchase Order Page</a></p>";
    
} else {
    echo "No products or suppliers available to link.";
}
?>
