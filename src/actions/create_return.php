<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $return_type = $_POST['return_type'];
    $sale_id = !empty($_POST['sale_id']) ? $_POST['sale_id'] : null;
    $purchase_id = !empty($_POST['purchase_id']) ? $_POST['purchase_id'] : null;
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    $pdo->beginTransaction();

    try {
        // Create the return record
        $stmt = $pdo->prepare("INSERT INTO returns (return_type, sale_id, purchase_id, reason, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$return_type, $sale_id, $purchase_id, $reason, $status]);
        $return_id = $pdo->lastInsertId();

        // If the item is restocked, update the stock quantity
        if ($status === 'restocked') {
            if ($return_type === 'sale' && $sale_id) {
                // Get items from the sale
                $stmt = $pdo->prepare("SELECT product_id, quantity FROM sale_items WHERE sale_id = ?");
                $stmt->execute([$sale_id]);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
            } elseif ($return_type === 'purchase' && $purchase_id) {
                // Get items from the purchase
                $stmt = $pdo->prepare("SELECT product_id, quantity FROM purchase_items WHERE purchase_id = ?");
                $stmt->execute([$purchase_id]);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
            }
        }

        $pdo->commit();
        header("Location: ../../index.php?page=returns&success=return_processed");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: ../../index.php?page=returns&action=new&error=" . urlencode($e->getMessage()));
    }

    exit();
}
?>
