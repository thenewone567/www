<?php
require_once 'c:\wamp64\www\bootstrap.php';
$db = new Database();

echo "Checking products with selling prices..." . PHP_EOL;

$db->query('SELECT COUNT(*) as count FROM products WHERE selling_price > 0');
$count = $db->single();
echo "Products with selling price > 0: " . $count->count . PHP_EOL . PHP_EOL;

$db->query('SELECT product_id, product_name, selling_price, purchase_price, current_average_cost, profit_margin FROM products WHERE selling_price > 0 ORDER BY product_id LIMIT 5');
$products = $db->resultSet();

if ($products) {
    foreach ($products as $p) {
        echo $p->product_name . ' (ID: ' . $p->product_id . ')' . PHP_EOL;
        echo '  Selling: ₹' . $p->selling_price . ' | Cost: ₹' . ($p->current_average_cost ?: $p->purchase_price) . ' | Margin: ' . $p->profit_margin . '%' . PHP_EOL;
    }
} else {
    echo "No products found." . PHP_EOL;
}
