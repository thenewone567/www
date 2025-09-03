<?php
// Fix the bulb purchase price in the database
require_once 'bootstrap.php';

$db = new Database();

echo "=== Fixing Bulb Purchase Price ===\n";

// First, let's see what the current data is
$db->query("SELECT product_id, product_name, purchase_price, selling_price FROM products WHERE product_id = 139");
$db->execute();
$product = $db->single();

if ($product) {
    echo "Current data for product 139:\n";
    echo "Name: {$product->product_name}\n";
    echo "Purchase Price: ₹{$product->purchase_price}\n";
    echo "Selling Price: ₹{$product->selling_price}\n\n";
    
    // Update the purchase price to the correct value
    echo "Updating purchase price to ₹83.20...\n";
    
    $db->query("UPDATE products SET purchase_price = 83.20 WHERE product_id = 139");
    $db->execute();
    
    // Verify the update
    $db->query("SELECT product_id, product_name, purchase_price, selling_price FROM products WHERE product_id = 139");
    $db->execute();
    $updatedProduct = $db->single();
    
    echo "Updated data:\n";
    echo "Name: {$updatedProduct->product_name}\n";
    echo "Purchase Price: ₹{$updatedProduct->purchase_price}\n";
    echo "Selling Price: ₹{$updatedProduct->selling_price}\n\n";
    
    echo "✅ SUCCESS: Bulb purchase price updated to ₹83.20\n";
    echo "Now try adding the bulb to cart - it should show ₹80.56!\n";
    
} else {
    echo "❌ Product 139 not found in database\n";
}
?>
