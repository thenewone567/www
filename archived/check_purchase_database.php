<?php
require_once 'bootstrap.php';

echo "🔍 CHECKING PURCHASE ORDER DATABASE\n";
echo "===================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Checking if purchase order ID 1321 exists...\n";

    $db->query("SELECT * FROM purchases WHERE purchase_id = 1321");
    $db->execute();
    $purchase = $db->single();

    if ($purchase) {
        echo "   ✅ Purchase order 1321 found!\n";
        echo "   📋 Details:\n";
        echo "      • PO Number: {$purchase->po_number}\n";
        echo "      • Supplier ID: {$purchase->supplier_id}\n";
        echo "      • Total Amount: $" . number_format($purchase->total_amount, 2) . "\n";
        echo "      • Status: {$purchase->status}\n";
        echo "      • Created At: {$purchase->created_at}\n";
    } else {
        echo "   ❌ Purchase order 1321 not found in database\n";
    }

    echo "\n2️⃣ Checking purchase_items table...\n";

    $db->query("SELECT * FROM purchase_items WHERE purchase_id = 1321");
    $db->execute();
    $items = $db->resultSet();

    if (count($items) > 0) {
        echo "   ✅ Purchase items found!\n";
        foreach ($items as $item) {
            echo "   📦 Product ID: {$item->product_id}, Quantity: {$item->quantity}, Unit Price: $" . number_format($item->unit_price, 2) . "\n";
        }
    } else {
        echo "   ❌ No purchase items found for order 1321\n";
    }

    echo "\n3️⃣ Checking all recent purchases...\n";

    $db->query("
        SELECT p.purchase_id, p.po_number, p.total_amount, p.status, p.created_at, p.created_by
        FROM purchases p
        ORDER BY p.created_at DESC
        LIMIT 10
    ");
    $db->execute();
    $recentPurchases = $db->resultSet();

    echo "   📊 Last 10 purchase orders:\n";
    if (count($recentPurchases) > 0) {
        foreach ($recentPurchases as $purchase) {
            echo "      • ID: {$purchase->purchase_id}, PO: {$purchase->po_number}, Amount: $" . number_format($purchase->total_amount, 2) . ", Status: {$purchase->status}, Created: {$purchase->created_at}\n";
        }
    } else {
        echo "      • No purchase orders found at all\n";
    }

    echo "\n4️⃣ Checking table structure...\n";

    $db->query("DESCRIBE purchases");
    $db->execute();
    $columns = $db->resultSet();

    echo "   📋 Purchases table columns:\n";
    foreach ($columns as $column) {
        echo "      • {$column->Field}: {$column->Type} ({$column->Null})\n";
    }

    $db->query("DESCRIBE purchase_items");
    $db->execute();
    $columns = $db->resultSet();

    echo "\n   📋 Purchase_items table columns:\n";
    foreach ($columns as $column) {
        echo "      • {$column->Field}: {$column->Type} ({$column->Null})\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>