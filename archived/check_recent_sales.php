<?php
require_once 'bootstrap.php';

echo "🔍 Recent Sales Check\n";
echo "====================\n\n";

$db = new Database();
$db->query("SELECT s.sale_id, s.total_amount, s.sale_date, c.name as customer_name 
            FROM sales s 
            LEFT JOIN customers c ON s.customer_id = c.customer_id 
            ORDER BY s.sale_date DESC 
            LIMIT 10");
$db->execute();
$sales = $db->resultSet();

echo "📋 Last 10 sales:\n";
foreach ($sales as $sale) {
    echo "   Sale #{$sale->sale_id}: \${$sale->total_amount} to {$sale->customer_name} on {$sale->sale_date}\n";
}

// Check for bot-generated customers (walk-in customers created by bot)
$db->query("SELECT COUNT(*) as count FROM customers WHERE name LIKE 'Walk-in%'");
$db->execute();
$walkInCount = $db->single()->count;

echo "\n🚶 Walk-in customers (Bot generated): {$walkInCount}\n";

// Check today's sales count
$db->query("SELECT COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE()");
$db->execute();
$todayCount = $db->single()->count;

echo "📊 Sales today: {$todayCount}\n";
?>