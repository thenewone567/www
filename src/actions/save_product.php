<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $sku = $_POST['sku'];
    $stock_quantity = $_POST['stock_quantity'];

    // Handle file upload
    $product_image = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../../public/uploads/";
        $product_image = basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $product_image;
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
    }

    if (empty($product_id)) {
        // Insert new product
        $sql = "INSERT INTO products (product_name, category_id, sku, stock_quantity, product_image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_name, $category_id, $sku, $stock_quantity, $product_image]);
    } else {
        // Update existing product
        $sql = "UPDATE products SET product_name = ?, category_id = ?, sku = ?, stock_quantity = ?";
        $params = [$product_name, $category_id, $sku, $stock_quantity];

        if ($product_image) {
            $sql .= ", product_image = ?";
            $params[] = $product_image;
        }

        $sql .= " WHERE product_id = ?";
        $params[] = $product_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    header("Location: ../../index.php?page=products");
    exit();
}
?>
