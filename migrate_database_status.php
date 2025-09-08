<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== DATABASE STATUS MIGRATION ===\n\n";

    // Check current pending_arrival records
    echo "Step 1: Checking pending_arrival records before migration...\n";
    $db->query('SELECT po_number, status, created_at FROM purchases WHERE status = :status');
    $db->bind(':status', 'pending_arrival');
    $db->execute();
    $pendingRecords = $db->resultSet();

    echo "Found " . count($pendingRecords) . " records with 'pending_arrival' status:\n";
    foreach ($pendingRecords as $record) {
        echo "- {$record->po_number} ({$record->status}) - {$record->created_at}\n";
    }

    if (count($pendingRecords) > 0) {
        echo "\nStep 2: Updating 'pending_arrival' to 'off-loading'...\n";

        // Update the records
        $db->query('UPDATE purchases SET status = :new_status WHERE status = :old_status');
        $db->bind(':new_status', 'off-loading');
        $db->bind(':old_status', 'pending_arrival');

        if ($db->execute()) {
            echo "✅ Successfully updated " . count($pendingRecords) . " records!\n";

            // Verify the update
            echo "\nStep 3: Verifying migration...\n";
            $db->query('SELECT po_number, status FROM purchases WHERE status = :status');
            $db->bind(':status', 'off-loading');
            $db->execute();
            $updatedRecords = $db->resultSet();

            echo "Records now with 'off-loading' status:\n";
            foreach ($updatedRecords as $record) {
                echo "- {$record->po_number} ({$record->status})\n";
            }

            // Check if any pending_arrival records remain
            $db->query('SELECT COUNT(*) as count FROM purchases WHERE status = :status');
            $db->bind(':status', 'pending_arrival');
            $db->execute();
            $remainingCount = $db->single();

            if ($remainingCount->count == 0) {
                echo "\n✅ MIGRATION COMPLETE: No 'pending_arrival' records remain\n";
                echo "✅ All records successfully converted to 'off-loading' status\n";
            } else {
                echo "\n⚠️  WARNING: {$remainingCount->count} 'pending_arrival' records still exist\n";
            }

        } else {
            echo "❌ Error: Failed to update records\n";
        }
    } else {
        echo "\n✅ No migration needed - no 'pending_arrival' records found\n";
    }

    echo "\nStep 4: Current status summary after migration:\n";
    $db->query('SELECT status, COUNT(*) as count FROM purchases GROUP BY status ORDER BY status');
    $db->execute();
    $allStatuses = $db->resultSet();

    foreach ($allStatuses as $status) {
        echo "- {$status->status}: {$status->count} records\n";
    }

    echo "\n🎉 DATABASE MIGRATION COMPLETE!\n";
    echo "The database now uses 'off-loading' instead of 'pending_arrival'\n";

} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage() . "\n";
}
?>