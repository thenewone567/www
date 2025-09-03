<?php
require_once 'bootstrap.php';

$productModel = new Product();
$products = $productModel->getProductsForPriceManagement();

echo "Total products: " . count($products) . PHP_EOL;

if (count($products) > 0) {
    $firstProduct = $products[0];
    echo "Available fields in first product:" . PHP_EOL;
    foreach (get_object_vars($firstProduct) as $key => $value) {
        echo "  $key: $value" . PHP_EOL;
    }
}
?>
