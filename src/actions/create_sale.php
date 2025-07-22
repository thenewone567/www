<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $payment_mode = $_POST['payment_mode'];
    $total_amount = 0;

    $pdo->beginTransaction();

    try {
        // Create the sale
        $stmt = $pdo->prepare("INSERT INTO sales (payment_mode, total_amount) VALUES (?, ?)");
        $stmt->execute([$payment_mode, 0]); // total_amount will be updated later
        $sale_id = $pdo->lastInsertId();

        // Add sale items and calculate total amount
        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = $product_ids[$i];
            $quantity = $quantities[$i];

            // Get product price and check stock
            $stmt = $pdo->prepare("SELECT unit_price, stock_quantity FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product && $product['stock_quantity'] >= $quantity) {
                $unit_price = $product['unit_price'];
                $item_total = $unit_price * $quantity;
                $total_amount += $item_total;

                // Insert sale item
                $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$sale_id, $product_id, $quantity, $unit_price]);

                // Update stock quantity
                $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
                $stmt->execute([$quantity, $product_id]);
            } else {
                throw new Exception("Not enough stock for product ID: $product_id");
            }
        }

        // Update the total amount in the sales table
        $stmt = $pdo->prepare("UPDATE sales SET total_amount = ? WHERE sale_id = ?");
        $stmt->execute([$total_amount, $sale_id]);

        $pdo->commit();
        header("Location: ../../index.php?page=sales&success=sale_created");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: ../../index.php?page=sales&action=new&error=" . urlencode($e->getMessage()));
    }

    exit();
}
?>
