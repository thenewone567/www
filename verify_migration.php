<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== MIGRATION VERIFICATION ===\n\n";

    // Check the specific PO that was migrated
    echo "Checking PO-250906-024518 status:\n";
    $db->query('SELECT po_number, status FROM purchases WHERE po_number = :po');
    $db->bind(':po', 'PO-250906-024518');
    $db->execute();
    $po = $db->single();

    if ($po) {
        echo "✅ PO-250906-024518 status: '{$po->status}'\n";
    } else {
        echo "❌ PO-250906-024518 not found\n";
    }

    // Check all current statuses
    echo "\nAll current status values:\n";
    $db->query('SELECT status, COUNT(*) as count FROM purchases WHERE status IS NOT NULL AND status != "" GROUP BY status ORDER BY status');
    $db->execute();
    $statuses = $db->resultSet();

    foreach ($statuses as $status) {
        echo "- '{$status->status}': {$status->count} records\n";
    }

    // Check specifically for off-loading records
    echo "\nChecking for 'off-loading' records:\n";
    $db->query('SELECT po_number, status, dock_arrival_time FROM purchases WHERE status = :status');
    $db->bind(':status', 'off-loading');
    $db->execute();
    $offLoadingRecords = $db->resultSet();

    echo "Found " . count($offLoadingRecords) . " off-loading records:\n";
    foreach ($offLoadingRecords as $record) {
        echo "- {$record->po_number} (status: {$record->status}) - arrived: {$record->dock_arrival_time}\n";
    }

    // Check for any remaining pending_arrival records
    echo "\nChecking for remaining 'pending_arrival' records:\n";
    $db->query('SELECT COUNT(*) as count FROM purchases WHERE status = :status');
    $db->bind(':status', 'pending_arrival');
    $db->execute();
    $remainingCount = $db->single();

    if ($remainingCount->count == 0) {
        echo "✅ SUCCESS: No 'pending_arrival' records remain\n";
    } else {
        echo "⚠️  WARNING: {$remainingCount->count} 'pending_arrival' records still exist\n";
    }

    echo "\n🎉 DATABASE MIGRATION VERIFICATION COMPLETE!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>