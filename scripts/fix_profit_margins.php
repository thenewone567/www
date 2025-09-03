<?php
require_once 'c:\wamp64\www\bootstrap.php';

$db = new Database();

echo "Updating profit margins for all products..." . PHP_EOL;

// Update profit_margin field for all products using the same calculation logic
$query = "
UPDATE products 
SET profit_margin = CASE 
    WHEN selling_price > 0 AND COALESCE(NULLIF(current_average_cost, 0), purchase_price, 0) > 0
    THEN ROUND(((selling_price - COALESCE(NULLIF(current_average_cost, 0), purchase_price, 0)) / selling_price) * 100, 2)
    ELSE 0
END
WHERE is_active = 1
";

$db->query($query);
$db->execute();

echo "Profit margins updated successfully!" . PHP_EOL;

// Test a few products to verify the update
echo PHP_EOL . "Verifying updated margins:" . PHP_EOL;

$db->query("
SELECT product_id, product_name, purchase_price, current_average_cost, selling_price, profit_margin 
FROM products 
WHERE product_id IN (110, 1, 2, 3) 
ORDER BY product_id
");
$products = $db->resultSet();

foreach ($products as $p) {
    $cost = $p->current_average_cost > 0 ? $p->current_average_cost : $p->purchase_price;
    $manual_calc = $p->selling_price > 0 && $cost > 0 ?
        round((($p->selling_price - $cost) / $p->selling_price) * 100, 2) : 0;

    echo "Product ID {$p->product_id}: {$p->product_name}" . PHP_EOL;
    echo "  DB Margin: {$p->profit_margin}% | Manual Calc: {$manual_calc}% | Match: " .
        ($p->profit_margin == $manual_calc ? "✓" : "✗") . PHP_EOL;
}
