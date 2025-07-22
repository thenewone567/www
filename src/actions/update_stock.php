<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $aisle = $_POST['aisle'];
    $rack = $_POST['rack'];

    // Update stock quantity and location
    $sql = "UPDATE products SET stock_quantity = ?, aisle = ?, rack = ? WHERE product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$quantity, $aisle, $rack, $product_id]);

    // Log the stock movement
    // Note: You might want to create a more sophisticated logging system
    $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, to_location, quantity, reason) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_id, "$aisle-$rack", $quantity, 'Manual Update']);


    header("Location: ../../index.php?page=inventory");
    exit();
}
?>
