<?php
require_once 'bootstrap.php';

echo "🔍 Sales Bot Dashboard Impact Analysis\n";
echo "======================================\n\n";

// Get dashboard data
$dashboardController = new DashboardController();
$dashboardModel = $dashboardController->dashboardModel;

echo "📊 Current Dashboard Metrics (30 days):\n";
echo "   Total Sales: \$" . number_format($dashboardModel->getTotalSales(30), 2) . "\n";
echo "   Total Transactions: " . $dashboardModel->getTotalTransactions(30) . "\n";
echo "   Average Transaction: \$" . number_format($dashboardModel->getAverageTransactionValue(30), 2) . "\n";

echo "\n📊 Today's Metrics:\n";
echo "   Sales Today: \$" . number_format($dashboardModel->getSalesToday(), 2) . "\n";

// Check recent sales manually
$db = new Database();
$db->query("SELECT COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE()");
$db->execute();
$result = $db->single();
$todayCount = $result ? $result->count : 0;

echo "   Transactions Today: {$todayCount}\n";

// Check for walk-in customers (bot pattern)
$db->query("SELECT name FROM customers WHERE name LIKE '%Walk-in%' OR name LIKE '%Bot%' ORDER BY customer_id DESC LIMIT 5");
$db->execute();
$walkIns = $db->resultSet();

echo "\n🤖 Recent Bot-Generated Customers:\n";
if ($walkIns) {
    foreach ($walkIns as $customer) {
        echo "   - {$customer->name}\n";
    }
} else {
    echo "   - No bot customers found\n";
}

// Test if sales bot can run now
echo "\n🧪 Testing Sales Bot Availability:\n";
$productModel = new Product();
$products = $productModel->getProductsWithProfitMargins(3);
echo "   Available products for bot: " . count($products) . "\n";

if (count($products) > 0) {
    echo "   ✅ Sales bot SHOULD work (products available)\n";
    foreach ($products as $p) {
        echo "      - {$p->product_name}: \${$p->selling_price}\n";
    }
} else {
    echo "   ❌ Sales bot CANNOT work (no products)\n";
}

echo "\n🎯 RECOMMENDATION:\n";
echo "If you're not seeing sales increase on the dashboard, the Sales Bot\n";
echo "may not be executing properly through the admin interface.\n";
echo "Check the bot dashboard buttons and ensure they're calling the correct endpoint.\n";

echo "\n🏁 Analysis Complete!\n";
?>