<?php
require_once 'c:\wamp64\www\bootstrap.php';

$productModel = new Product();
$product = $productModel->getProductById(110);

if ($product) {
    echo "Product: " . $product->product_name . PHP_EOL;
    echo "Purchase Price: ₹" . number_format($product->purchase_price, 2) . PHP_EOL;
    echo "Current Avg Cost: ₹" . number_format($product->current_average_cost, 2) . PHP_EOL;
    echo "Selling Price: ₹" . number_format($product->selling_price, 2) . PHP_EOL;
    echo "DB Profit Margin: " . $product->profit_margin . "%" . PHP_EOL;
    echo "Calculated Profit Margin: " . ($product->calculated_profit_margin ?? 'N/A') . "%" . PHP_EOL;

    // Manual calculation for verification
    $cost = $product->current_average_cost > 0 ? $product->current_average_cost : $product->purchase_price;
    $margin = (($product->selling_price - $cost) / $product->selling_price) * 100;
    echo "Manual Calculation: " . number_format($margin, 2) . "%" . PHP_EOL;
    echo "Cost used: ₹" . number_format($cost, 2) . " (" . ($product->current_average_cost > 0 ? 'avg cost' : 'purchase price') . ")" . PHP_EOL;
} else {
    echo "Product not found" . PHP_EOL;
}
