<?php
require_once '../app/config.php';
require_once '../app/Database.php';

header('Content-Type: text/html');

try {
    $db = new Database();
    $pdo = $db->getDbh();

    echo "<h3>Product Cost Analysis</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>
            <th>Product Name</th>
            <th>Purchase Price</th>
            <th>Current Average Cost</th>
            <th>Selling Price</th>
            <th>Profit Margin</th>
          </tr>";

    // Get products with all cost columns
    $stmt = $pdo->prepare("
        SELECT 
            product_name,
            purchase_price,
            current_average_cost,
            selling_price,
            (selling_price - COALESCE(current_average_cost, purchase_price, 0)) as profit_margin
        FROM products 
        WHERE purchase_price > 0 OR current_average_cost > 0 OR selling_price > 0
        ORDER BY product_name 
        LIMIT 10
    ");

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
        echo "<td>₹" . number_format($product['purchase_price'], 2) . "</td>";
        echo "<td>₹" . number_format($product['current_average_cost'] ?? 0, 2) . "</td>";
        echo "<td>₹" . number_format($product['selling_price'], 2) . "</td>";
        echo "<td>₹" . number_format($product['profit_margin'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<p><strong>Total products with cost data:</strong> " . count($products) . "</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>