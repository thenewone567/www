<?php
require_once '../../config/config.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);

    header("Location: ../../index.php?page=products");
    exit();
}
?>
