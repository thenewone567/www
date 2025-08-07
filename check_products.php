<?php
require_once 'bootstrap.php';

echo "<h3>Recent Products Check</h3>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check recent products (last 10)
    $stmt = $pdo->query("
        SELECT 
            product_id, 
            product_name, 
            sku, 
            category_id, 
            supplier_id, 
            brand_id,
            is_active,
            created_at
        FROM products 
        ORDER BY product_id DESC 
        LIMIT 10
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo "<p>❌ No products found in database</p>";
    } else {
        echo "<p>✅ Found " . count($products) . " recent products:</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>SKU</th><th>Category ID</th><th>Supplier ID</th><th>Brand ID</th><th>Active</th><th>Created</th></tr>";
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['product_id'] . "</td>";
            echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
            echo "<td>" . htmlspecialchars($product['sku']) . "</td>";
            echo "<td>" . $product['category_id'] . "</td>";
            echo "<td>" . $product['supplier_id'] . "</td>";
            echo "<td>" . $product['brand_id'] . "</td>";
            echo "<td>" . $product['is_active'] . "</td>";
            echo "<td>" . ($product['created_at'] ?? 'No timestamp') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check if products table has is_active column
    echo "<h4>Products Table Schema:</h4>";
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasIsActive = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'is_active') {
            $hasIsActive = true;
            echo "<p>✅ is_active column found: " . $col['Type'] . " (Default: " . $col['Default'] . ")</p>";
            break;
        }
    }
    
    if (!$hasIsActive) {
        echo "<p>❌ is_active column not found - this might be the issue!</p>";
    }
    
    // Check inventory table for recent entries
    echo "<h4>Recent Inventory Entries:</h4>";
    $stmt = $pdo->query("
        SELECT 
            inventory_id,
            product_id, 
            quantity, 
            created_at
        FROM Inventory 
        ORDER BY inventory_id DESC 
        LIMIT 5
    ");
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($inventory)) {
        echo "<p>No inventory entries found</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>Inventory ID</th><th>Product ID</th><th>Quantity</th><th>Created</th></tr>";
        foreach ($inventory as $inv) {
            echo "<tr>";
            echo "<td>" . $inv['inventory_id'] . "</td>";
            echo "<td>" . $inv['product_id'] . "</td>";
            echo "<td>" . $inv['quantity'] . "</td>";
            echo "<td>" . ($inv['created_at'] ?? 'No timestamp') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
