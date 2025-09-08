<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== REMOVING PENDING_ARRIVAL FROM ENUM ===\n\n";

    echo "Step 1: Current ENUM values\n";
    $db->query("SHOW COLUMNS FROM purchases WHERE Field = 'status'");
    $db->execute();
    $statusColumn = $db->single();
    echo "Current ENUM: {$statusColumn->Type}\n\n";

    echo "Step 2: Checking if any records still use 'pending_arrival'...\n";
    $db->query('SELECT COUNT(*) as count FROM purchases WHERE status = :status');
    $db->bind(':status', 'pending_arrival');
    $db->execute();
    $pendingCount = $db->single();

    if ($pendingCount->count > 0) {
        echo "❌ WARNING: {$pendingCount->count} records still use 'pending_arrival'\n";
        echo "These need to be migrated first!\n";

        $db->query('SELECT po_number, status FROM purchases WHERE status = :status');
        $db->bind(':status', 'pending_arrival');
        $db->execute();
        $pendingRecords = $db->resultSet();

        foreach ($pendingRecords as $record) {
            echo "- {$record->po_number}: {$record->status}\n";
        }

        exit("Please migrate these records first before removing the ENUM value.\n");
    } else {
        echo "✅ No records use 'pending_arrival' - safe to remove\n";
    }

    echo "\nStep 3: Removing 'pending_arrival' from ENUM...\n";

    // Remove 'pending_arrival' from the ENUM
    $alterQuery = "ALTER TABLE purchases MODIFY COLUMN status ENUM(
        'pending',
        'sent',
        'off-loading',
        'in_transit',
        'ready_to_receive',
        'receiving_in_progress',
        'partially_received',
        'received',
        'cancelled'
    )";

    $db->query($alterQuery);
    if ($db->execute()) {
        echo "✅ Successfully removed 'pending_arrival' from ENUM\n";

        // Verify the new ENUM structure
        echo "\nStep 4: Verifying updated ENUM structure...\n";
        $db->query("SHOW COLUMNS FROM purchases WHERE Field = 'status'");
        $db->execute();
        $newStatusColumn = $db->single();
        echo "New ENUM: {$newStatusColumn->Type}\n";

        echo "\nStep 5: Verifying current status distribution...\n";
        $db->query('SELECT status, COUNT(*) as count FROM purchases GROUP BY status ORDER BY status');
        $db->execute();
        $statuses = $db->resultSet();

        foreach ($statuses as $status) {
            echo "- '{$status->status}': {$status->count} records\n";
        }

        echo "\n✅ ENUM CLEANUP COMPLETE!\n";
        echo "✅ 'pending_arrival' removed - no more confusion\n";
        echo "✅ Only 'off-loading' status remains for active off-loading\n";
        echo "✅ Database is now clean and consistent\n";

    } else {
        echo "❌ Failed to alter table ENUM\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>