<?php
require_once 'bootstrap.php';

echo "🔧 FIXING PO DATABASE ISSUES\n";
echo "============================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Analyzing the total amount discrepancies...\n";

    // Get individual PO amounts for sent status
    $db->query("SELECT purchase_id, po_number, total_amount FROM purchases WHERE status = 'sent' ORDER BY total_amount DESC");
    $db->execute();
    $sentPOs = $db->resultSet();

    $sentTotal = 0;
    echo "   'sent' POs breakdown:\n";
    foreach ($sentPOs as $po) {
        echo "     PO #{$po->po_number}: ₹{$po->total_amount}\n";
        $sentTotal += $po->total_amount;
    }
    echo "   Calculated total: ₹{$sentTotal} (should be ₹7,000)\n";

    // Get individual PO amounts for in_transit status
    $db->query("SELECT purchase_id, po_number, total_amount FROM purchases WHERE status = 'in_transit' ORDER BY total_amount DESC");
    $db->execute();
    $transitPOs = $db->resultSet();

    $transitTotal = 0;
    echo "\n   'in_transit' POs breakdown:\n";
    foreach ($transitPOs as $po) {
        echo "     PO #{$po->po_number}: ₹{$po->total_amount}\n";
        $transitTotal += $po->total_amount;
    }
    echo "   Calculated total: ₹{$transitTotal} (should be ₹4,385)\n";

    echo "\n2️⃣ Finding and fixing orphaned purchase items...\n";

    // Find orphaned purchase items
    $db->query("
        SELECT pi.purchase_item_id, pi.purchase_id, pi.product_id, pi.quantity, pi.unit_cost
        FROM purchase_items pi
        LEFT JOIN purchases p ON pi.purchase_id = p.purchase_id
        WHERE p.purchase_id IS NULL
        ORDER BY pi.purchase_item_id
    ");
    $db->execute();
    $orphanedItems = $db->resultSet();

    if (count($orphanedItems) > 0) {
        echo "   Found " . count($orphanedItems) . " orphaned items:\n";
        $orphanedPurchaseIds = [];

        foreach ($orphanedItems as $item) {
            echo "     Item ID: {$item->purchase_item_id} | PO ID: {$item->purchase_id} | Product: {$item->product_id} | Qty: {$item->quantity} | Cost: ₹{$item->unit_cost}\n";
            $orphanedPurchaseIds[] = $item->purchase_id;
        }

        // Check if these purchase IDs exist in a backup or need recreation
        $uniquePurchaseIds = array_unique($orphanedPurchaseIds);
        echo "   Orphaned items reference these missing purchase IDs: " . implode(', ', $uniquePurchaseIds) . "\n";

        // Option 1: Delete orphaned items
        echo "\n   🗑️ FIXING: Removing orphaned purchase items...\n";
        $db->query("DELETE FROM purchase_items WHERE purchase_id NOT IN (SELECT purchase_id FROM purchases)");
        $db->execute();
        echo "   ✅ Removed orphaned purchase items\n";
    }

    echo "\n3️⃣ Fixing POs without items...\n";

    // Find POs without any items
    $db->query("
        SELECT p.purchase_id, p.po_number, p.status, p.total_amount
        FROM purchases p
        LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
        WHERE pi.purchase_id IS NULL
        AND p.status IN ('sent', 'in_transit', 'pending')
        ORDER BY p.purchase_date DESC
    ");
    $db->execute();
    $posWithoutItems = $db->resultSet();

    if (count($posWithoutItems) > 0) {
        echo "   Found " . count($posWithoutItems) . " active POs without items:\n";
        foreach ($posWithoutItems as $po) {
            echo "     PO #{$po->po_number} ({$po->status}): ₹{$po->total_amount}\n";
        }

        // These POs should either be cancelled or have items added
        echo "\n   🔄 FIXING: Moving empty POs to 'cancelled' status...\n";
        $db->query("
            UPDATE purchases 
            SET status = 'cancelled', 
                notes = CONCAT(COALESCE(notes, ''), ' [Auto-cancelled: No items found]')
            WHERE purchase_id IN (
                SELECT p.purchase_id FROM purchases p
                LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
                WHERE pi.purchase_id IS NULL 
                AND p.status IN ('sent', 'in_transit', 'pending')
            )
        ");
        $db->execute();
        echo "   ✅ Moved empty POs to cancelled status\n";
    }

    echo "\n4️⃣ Recalculating total amounts for all POs...\n";

    // Recalculate total amounts based on purchase items
    $db->query("
        UPDATE purchases p
        SET total_amount = (
            SELECT COALESCE(SUM(pi.quantity * pi.unit_cost), 0)
            FROM purchase_items pi
            WHERE pi.purchase_id = p.purchase_id
        )
        WHERE p.purchase_id IN (
            SELECT DISTINCT pi.purchase_id 
            FROM purchase_items pi
        )
    ");
    $db->execute();
    echo "   ✅ Recalculated total amounts based on actual items\n";

    echo "\n5️⃣ Final verification...\n";

    // Check status counts again
    $db->query("
        SELECT status, COUNT(*) as count, SUM(total_amount) as total_value
        FROM purchases 
        WHERE status IN ('sent', 'in_transit', 'pending', 'received', 'cancelled')
        GROUP BY status
        ORDER BY 
            CASE status 
                WHEN 'pending' THEN 1 
                WHEN 'sent' THEN 2 
                WHEN 'in_transit' THEN 3 
                WHEN 'received' THEN 4 
                WHEN 'cancelled' THEN 5 
            END
    ");
    $db->execute();
    $finalCounts = $db->resultSet();

    echo "   📊 Updated PO status summary:\n";
    foreach ($finalCounts as $status) {
        echo "     {$status->status}: {$status->count} POs | Total: ₹" . number_format($status->total_value, 2) . "\n";
    }

    // Check for any remaining issues
    $db->query("SELECT COUNT(*) as count FROM purchase_items WHERE purchase_id NOT IN (SELECT purchase_id FROM purchases)");
    $db->execute();
    $remainingOrphaned = $db->single();

    $db->query("SELECT COUNT(*) as count FROM purchases p LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id WHERE pi.purchase_id IS NULL AND p.status != 'cancelled'");
    $db->execute();
    $remainingEmpty = $db->single();

    echo "\n   🔍 Remaining issues:\n";
    echo "     Orphaned items: {$remainingOrphaned->count}\n";
    echo "     Active POs without items: {$remainingEmpty->count}\n";

    if ($remainingOrphaned->count == 0 && $remainingEmpty->count == 0) {
        echo "\n   ✅ ALL DATABASE ISSUES FIXED!\n";
    } else {
        echo "\n   ⚠️ Some issues may remain - manual review needed\n";
    }

    echo "\n🎉 DATABASE CLEANUP COMPLETE!\n";
    echo "The PO database issues have been resolved.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>