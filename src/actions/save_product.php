<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $sku = $_POST['sku'];
    $stock_quantity = $_POST['stock_quantity'];
    $aisle = $_POST['aisle'];
    $rack = $_POST['rack'];

    // Handle file uploads
    $product_image = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../../public/uploads/";
        $product_image = basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $product_image;
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
    }

    $icon = '';
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] == 0) {
        $target_dir = "../../public/uploads/icons/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $icon = basename($_FILES["icon"]["name"]);
        $target_file = $target_dir . $icon;
        move_uploaded_file($_FILES["icon"]["tmp_name"], $target_file);
    }

    if (empty($product_id)) {
        // Insert new product
        $sql = "INSERT INTO products (product_name, category_id, sku, stock_quantity, aisle, rack, product_image, icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_name, $category_id, $sku, $stock_quantity, $aisle, $rack, $product_image, $icon]);
    } else {
        // Update existing product
        $sql = "UPDATE products SET product_name = ?, category_id = ?, sku = ?, stock_quantity = ?, aisle = ?, rack = ?";
        $params = [$product_name, $category_id, $sku, $stock_quantity, $aisle, $rack];

        if ($product_image) {
            $sql .= ", product_image = ?";
            $params[] = $product_image;
        }
        if ($icon) {
            $sql .= ", icon = ?";
            $params[] = $icon;
        }

        $sql .= " WHERE product_id = ?";
        $params[] = $product_id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    header("Location: ../../index.php?page=inventory");
    exit();
}
?>
