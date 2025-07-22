<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $from_location = $_POST['from_location'];
    $to_location = $_POST['to_location'];
    $quantity = $_POST['quantity'];

    // For this basic implementation, we just log the movement.
    // A more advanced implementation would update stock levels at different locations.
    $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, from_location, to_location, quantity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_id, $from_location, $to_location, $quantity]);

    header("Location: ../../index.php?page=stock_movements");
    exit();
}
?>
