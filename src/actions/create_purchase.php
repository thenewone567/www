<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $unit_prices = $_POST['unit_price'];
    $total_amount = 0;

    // Handle invoice attachment upload
    $invoice_attachment = '';
    if (isset($_FILES['invoice_attachment']) && $_FILES['invoice_attachment']['error'] == 0) {
        $target_dir = "../../public/uploads/invoices/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $invoice_attachment = basename($_FILES["invoice_attachment"]["name"]);
        $target_file = $target_dir . $invoice_attachment;
        move_uploaded_file($_FILES["invoice_attachment"]["tmp_name"], $target_file);
    }

    $pdo->beginTransaction();

    try {
        // Create the purchase
        $stmt = $pdo->prepare("INSERT INTO purchases (supplier_id, total_amount, invoice_attachment) VALUES (?, ?, ?)");
        $stmt->execute([$supplier_id, 0, $invoice_attachment]); // total_amount will be updated later
        $purchase_id = $pdo->lastInsertId();

        // Add purchase items and calculate total amount
        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = $product_ids[$i];
            $quantity = $quantities[$i];
            $unit_price = $unit_prices[$i];
            $item_total = $unit_price * $quantity;
            $total_amount += $item_total;

            // Insert purchase item
            $stmt = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$purchase_id, $product_id, $quantity, $unit_price]);

            // Update stock quantity
            $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
            $stmt->execute([$quantity, $product_id]);
        }

        // Update the total amount in the purchases table
        $stmt = $pdo->prepare("UPDATE purchases SET total_amount = ? WHERE purchase_id = ?");
        $stmt->execute([$total_amount, $purchase_id]);

        $pdo->commit();
        header("Location: ../../index.php?page=purchases&success=purchase_created");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: ../../index.php?page=purchases&action=new&error=" . urlencode($e->getMessage()));
    }

    exit();
}
?>
