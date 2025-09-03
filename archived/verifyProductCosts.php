<?php
require_once '../app/config.php';
require_once '../app/Database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getDbh();

    echo json_encode(["message" => "Checking product costs"]) . "\n";

    // Get products with all cost columns
    $stmt = $pdo->prepare("
        SELECT 
            product_name,
            purchase_price,
            current_average_cost,
            selling_price,
            (selling_price - current_average_cost) as profit_margin
        FROM products 
        WHERE purchase_price > 0 OR current_average_cost > 0 OR selling_price > 0
        ORDER BY product_name 
        LIMIT 10
    ");

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "product_count" => count($products),
        "sample_products" => $products
    ]) . "\n";

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]) . "\n";
}
?>