<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== DATABASE ENUM MIGRATION ===\n\n";

    echo "Step 1: Current ENUM values for status column\n";
    $db->query("SHOW COLUMNS FROM purchases WHERE Field = 'status'");
    $db->execute();
    $statusColumn = $db->single();
    echo "Current ENUM: {$statusColumn->Type}\n\n";

    echo "Step 2: Adding 'off-loading' to the ENUM values...\n";

    // Add 'off-loading' to the ENUM by replacing 'pending_arrival' with both values
    $alterQuery = "ALTER TABLE purchases MODIFY COLUMN status ENUM(
        'pending',
        'sent',
        'pending_arrival',
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
        echo "✅ Successfully added 'off-loading' to ENUM values\n";

        // Verify the new ENUM structure
        echo "\nStep 3: Verifying new ENUM structure...\n";
        $db->query("SHOW COLUMNS FROM purchases WHERE Field = 'status'");
        $db->execute();
        $newStatusColumn = $db->single();
        echo "New ENUM: {$newStatusColumn->Type}\n";

        echo "\nStep 4: Updating PO-250906-024518 to 'off-loading' status...\n";
        $db->query('UPDATE purchases SET status = :status WHERE po_number = :po');
        $db->bind(':status', 'off-loading');
        $db->bind(':po', 'PO-250906-024518');

        if ($db->execute()) {
            echo "✅ Successfully updated PO-250906-024518 status\n";

            // Verify the update
            $db->query('SELECT po_number, status FROM purchases WHERE po_number = :po');
            $db->bind(':po', 'PO-250906-024518');
            $db->execute();
            $result = $db->single();

            echo "✅ PO-250906-024518 status is now: '{$result->status}'\n";

            echo "\nStep 5: Final verification - all off-loading records:\n";
            $db->query('SELECT po_number, status, dock_arrival_time FROM purchases WHERE status = :status');
            $db->bind(':status', 'off-loading');
            $db->execute();
            $offLoadingRecords = $db->resultSet();

            echo "Found " . count($offLoadingRecords) . " off-loading records:\n";
            foreach ($offLoadingRecords as $record) {
                echo "- {$record->po_number} (status: {$record->status}) - arrived: {$record->dock_arrival_time}\n";
            }

            echo "\n🎉 DATABASE ENUM MIGRATION COMPLETE!\n";
            echo "✅ 'off-loading' status is now available in the database\n";
            echo "✅ PO-250906-024518 successfully converted to 'off-loading'\n";

        } else {
            echo "❌ Failed to update PO status\n";
        }

    } else {
        echo "❌ Failed to alter table ENUM\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>