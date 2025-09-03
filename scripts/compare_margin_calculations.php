<?php
require_once 'c:\wamp64\www\bootstrap.php';
$productModel = new Product();

echo "Getting product 110 from the paginated method (same as table view)..." . PHP_EOL;
$products = $productModel->getProductsPaginated(0, 1000, ''); // Get all products

$bulb = null;
foreach ($products as $product) {
    if ($product->product_id == 110) {
        $bulb = $product;
        break;
    }
}

if ($bulb) {
    echo 'Product: ' . $bulb->product_name . PHP_EOL;
    echo 'Selling Price: ₹' . $bulb->selling_price . PHP_EOL;
    echo 'Purchase Price: ₹' . $bulb->purchase_price . PHP_EOL;
    echo 'Current Avg Cost: ₹' . $bulb->current_average_cost . PHP_EOL;
    echo 'Primary Purchase Price: ₹' . $bulb->primary_purchase_price . PHP_EOL;
    echo 'Unit Price: ₹' . $bulb->unit_price . PHP_EOL;
    echo 'Min Supplier Price: ₹' . $bulb->min_supplier_price . PHP_EOL;
    echo PHP_EOL;

    // Calculate margins as done in table view
    $selling = $bulb->selling_price ?? 0;
    $cost = $bulb->primary_purchase_price ?? $bulb->unit_price ?? 0;
    $margin = 0;
    if ($selling > 0 && $cost > 0) {
        $margin = (($selling - $cost) / $selling) * 100;
    }
    echo 'Table Margin Calculation (OLD): ' . number_format($margin, 1) . '% (using primary_purchase_price: ₹' . $cost . ')' . PHP_EOL;

    // Calculate margin with NEW method (same as details view)
    $cost_new = ($bulb->current_average_cost ?? 0) > 0 ? $bulb->current_average_cost : ($bulb->purchase_price ?? 0);
    $margin_new = 0;
    if ($selling > 0 && $cost_new > 0) {
        $margin_new = (($selling - $cost_new) / $selling) * 100;
    }
    echo 'Table Margin Calculation (NEW): ' . number_format($margin_new, 1) . '% (using current_avg_cost: ₹' . $cost_new . ')' . PHP_EOL;

    // Calculate margin as done in details view
    $cost_details = $bulb->current_average_cost > 0 ? $bulb->current_average_cost : $bulb->purchase_price;
    $margin_details = (($selling - $cost_details) / $selling) * 100;
    echo 'Details Margin Calculation: ' . number_format($margin_details, 2) . '% (using current_avg_cost: ₹' . $cost_details . ')' . PHP_EOL;
} else {
    echo "Product 110 not found in paginated results!" . PHP_EOL;
}
