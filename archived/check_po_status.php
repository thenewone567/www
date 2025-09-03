<?php
require_once 'bootstrap.php';

echo "📊 Purchase Order Status Check\n";
echo "==============================\n\n";

try {
    $database = new Database();

    // Check purchase orders
    $database->query("SELECT COUNT(*) as count FROM purchase_orders");
    $database->execute();
    $poCount = $database->single();
    echo "📦 Total Purchase Orders: " . $poCount->count . "\n";

    // Check pending purchase order items
    $database->query("SELECT COUNT(*) as count FROM purchase_order_items WHERE status = 'pending'");
    $database->execute();
    $pendingItems = $database->single();
    echo "⏳ Pending PO Items: " . $pendingItems->count . "\n";

    // Check recent purchase orders
    $database->query("SELECT id, supplier_id, total_amount, status, created_at FROM purchase_orders ORDER BY created_at DESC LIMIT 5");
    $database->execute();
    $recentPOs = $database->resultSet();

    echo "\n📋 Recent Purchase Orders:\n";
    foreach ($recentPOs as $po) {
        echo "   PO #{$po->id} - Status: {$po->status} - Amount: $" . number_format($po->total_amount, 2) . " - Date: {$po->created_at}\n";
    }

    echo "\n🎯 ANALYSIS:\n";
    if ($poCount->count > 0) {
        echo "✅ Purchase orders exist in database\n";
        if ($pendingItems->count > 0) {
            echo "⚠️  {$pendingItems->count} items are waiting to be received!\n";
            echo "❌ But receiving bot is ignoring them and using random products instead\n";
        } else {
            echo "✅ No pending items to receive\n";
        }
    } else {
        echo "❌ No purchase orders found in database\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>