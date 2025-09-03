<?php
require_once 'bootstrap.php';

echo "🔍 Recent Purchase Orders Check\n";
echo "===============================\n\n";

$db = new Database();

// Check recent purchases and their created_by field
$sql = "SELECT purchase_id, created_by, created_at FROM purchases ORDER BY created_at DESC LIMIT 10";

try {
    $db->query($sql);
    $results = $db->resultSet();

    if (empty($results)) {
        echo "❌ No purchases found.\n";
    } else {
        echo "📋 Recent Purchases:\n";
        echo "===================\n";

        foreach ($results as $purchase) {
            $createdBy = $purchase->created_by ?? 'NULL';
            echo "• ID: {$purchase->purchase_id} | Created by: '{$createdBy}' | Date: {$purchase->created_at}\n";
        }

        // Check specifically for bot-related entries
        echo "\n🤖 Checking for bot-related entries...\n";
        $botSql = "SELECT COUNT(*) as count FROM purchases WHERE created_by LIKE '%bot%'";
        $db->query($botSql);
        $botCount = $db->single();
        echo "Bot-related purchases: {$botCount->count}\n";

        // Check for recent purchases in general
        $recentSql = "SELECT COUNT(*) as count FROM purchases WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        $db->query($recentSql);
        $recentCount = $db->single();
        echo "Purchases in last hour: {$recentCount->count}\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>