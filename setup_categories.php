<?php
require_once 'bootstrap.php';

$db = new Database();

echo "<h2>Setting Up Categories for Products</h2>";

// First, let's create some basic categories if they don't exist
$categories = [
    'Power Tools',
    'Hand Tools', 
    'Hardware',
    'Electrical',
    'Plumbing',
    'Safety Equipment'
];

echo "<h3>1. Creating Categories:</h3>";
foreach ($categories as $categoryName) {
    // Check if category exists
    $db->query("SELECT category_id FROM categories WHERE category_name = ?");
    $db->bind(1, $categoryName);
    $db->execute();
    $existing = $db->single();
    
    if (!$existing) {
        // Create category
        $db->query("INSERT INTO categories (category_name, created_at) VALUES (?, NOW())");
        $db->bind(1, $categoryName);
        $db->execute();
        echo "✓ Created category: {$categoryName}<br>";
    } else {
        echo "- Category already exists: {$categoryName}<br>";
    }
}

// Get all categories
$db->query("SELECT category_id, category_name FROM categories");
$db->execute();
$allCategories = $db->resultSet();

// Get products that don't have categories
$db->query("
    SELECT p.product_id, p.product_name 
    FROM products p 
    INNER JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1
    WHERE p.is_active = 1 AND (p.category_id IS NULL OR p.category_id = 0)
    LIMIT 10
");
$db->execute();
$productsWithoutCategories = $db->resultSet();

echo "<h3>2. Assigning Categories to Products:</h3>";
if (count($productsWithoutCategories) > 0) {
    foreach ($productsWithoutCategories as $index => $product) {
        // Assign categories in a cycle
        $category = $allCategories[$index % count($allCategories)];
        
        $db->query("UPDATE products SET category_id = ? WHERE product_id = ?");
        $db->bind(1, $category->category_id);
        $db->bind(2, $product->product_id);
        $db->execute();
        
        echo "✓ Assigned '{$category->category_name}' to '{$product->product_name}'<br>";
    }
    echo "<p><strong>Categories assigned successfully!</strong></p>";
    echo "<p><a href='/www/purchases/add'>Go back to Purchase Order page</a></p>";
} else {
    echo "<p>All products already have categories assigned.</p>";
}
?>
