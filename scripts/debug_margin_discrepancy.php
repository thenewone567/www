<?php
require_once 'c:\wamp64\www\bootstrap.php';
$db = new Database();

echo "Checking products table structure..." . PHP_EOL;
$db->query('DESCRIBE products');
$columns = $db->resultSet();

echo "Price/Cost related columns:" . PHP_EOL;
foreach ($columns as $col) {
    if (strpos($col->Field, 'price') !== false || strpos($col->Field, 'cost') !== false) {
        echo "  - " . $col->Field . ' (' . $col->Type . ')' . PHP_EOL;
    }
}

echo PHP_EOL . "Testing product ID 110 with correct columns..." . PHP_EOL;
$db->query('SELECT product_id, product_name, selling_price, purchase_price, current_average_cost FROM products WHERE product_id = 110');
$product = $db->single();

if ($product) {
    echo 'Product: ' . $product->product_name . PHP_EOL;
    echo 'Selling Price: ₹' . $product->selling_price . PHP_EOL;
    echo 'Purchase Price: ₹' . $product->purchase_price . PHP_EOL;
    echo 'Current Avg Cost: ₹' . $product->current_average_cost . PHP_EOL;
    echo PHP_EOL;

    // Calculate margins with different cost bases
    $selling = floatval($product->selling_price);
    $cost_details = $product->current_average_cost > 0 ? floatval($product->current_average_cost) : floatval($product->purchase_price);
    $cost_table = floatval($product->purchase_price); // This is what should be used in table

    $margin_details = (($selling - $cost_details) / $selling) * 100;
    $margin_table = (($selling - $cost_table) / $selling) * 100;

    echo 'Details Margin: ' . number_format($margin_details, 2) . '% (using ' . ($product->current_average_cost > 0 ? 'current_average_cost' : 'purchase_price') . ': ₹' . $cost_details . ')' . PHP_EOL;
    echo 'Table Margin (should be): ' . number_format($margin_table, 2) . '% (using purchase_price: ₹' . $cost_table . ')' . PHP_EOL;

    // Now test the fallback that makes table show 48.2%
    $cost_fallback_null = 0; // When both primary_purchase_price and unit_price are NULL
    if ($cost_fallback_null == 0) {
        echo 'Table Margin (current broken): Division by zero or using wrong cost' . PHP_EOL;
    }

    // Check if there's another cost that gives 48.2%
    // 48.2% margin means: cost = selling_price * (1 - 0.482) = 16.81 * 0.518 = 8.71
    $target_cost_for_48_percent = $selling * (1 - 0.482);
    echo 'Cost needed for 48.2% margin: ₹' . number_format($target_cost_for_48_percent, 2) . PHP_EOL;
}
