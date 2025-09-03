<?php
// Quick verification that the database was updated correctly
require_once 'bootstrap.php';

$db = new Database();

echo "=== Verifying Database Update ===\n";

// Check the bulb purchase price
$db->query("SELECT product_id, product_name, purchase_price, selling_price FROM products WHERE product_id = 139");
$db->execute();
$product = $db->single();

if ($product) {
    echo "Product 139 (Bulb) current data:\n";
    echo "Name: {$product->product_name}\n";
    echo "Purchase Price: ₹{$product->purchase_price}\n";
    echo "Selling Price: ₹{$product->selling_price}\n\n";
    
    // Also check inventory
    $db->query("SELECT product_id, inventory_quantity FROM inventory WHERE product_id = 139");
    $db->execute();
    $inventory = $db->single();
    
    if ($inventory) {
        echo "Inventory quantity: {$inventory->inventory_quantity}\n\n";
        
        // Test the API calculation manually
        echo "=== Testing API Calculation ===\n";
        echo "Manual calculation:\n";
        echo "Current: {$inventory->inventory_quantity} × ₹{$product->purchase_price} = ₹" . ($inventory->inventory_quantity * $product->purchase_price) . "\n";
        echo "New: 1 × ₹70 = ₹70\n";
        echo "Total: " . ($inventory->inventory_quantity + 1) . " units = ₹" . (($inventory->inventory_quantity * $product->purchase_price) + 70) . "\n";
        echo "Average: ₹" . round((($inventory->inventory_quantity * $product->purchase_price) + 70) / ($inventory->inventory_quantity + 1), 2) . "\n\n";
        
        if ($product->purchase_price == 83.20) {
            echo "✅ Database is correct!\n";
            echo "Expected result for 1 bulb @ ₹70: ₹" . round(((16 * 83.20) + 70) / 17, 2) . "\n";
        } else {
            echo "❌ Database still has wrong purchase price!\n";
        }
    } else {
        echo "No inventory record found for product 139\n";
    }
} else {
    echo "❌ Product 139 not found\n";
}
?>
