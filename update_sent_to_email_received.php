<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== UPDATING 'SENT' TO 'EMAIL_RECEIVED' ===\n\n";

    echo "Step 1: Current ENUM values\n";
    $db->query("SHOW COLUMNS FROM purchases WHERE Field = 'status'");
    $db->execute();
    $statusColumn = $db->single();
    echo "Current ENUM: {$statusColumn->Type}\n\n";

    echo "Step 2: Checking if any records use 'sent' status...\n";
    $db->query('SELECT COUNT(*) as count FROM purchases WHERE status = :status');
    $db->bind(':status', 'sent');
    $db->execute();
    $sentCount = $db->single();

    echo "Found {$sentCount->count} records with 'sent' status\n";

    if ($sentCount->count > 0) {
        echo "Sample 'sent' records:\n";
        $db->query('SELECT po_number, status, created_at FROM purchases WHERE status = :status LIMIT 5');
        $db->bind(':status', 'sent');
        $db->execute();
        $sentRecords = $db->resultSet();

        foreach ($sentRecords as $record) {
            echo "- {$record->po_number}: {$record->status} - {$record->created_at}\n";
        }

        echo "\nStep 3: Updating existing 'sent' records to 'email_received'...\n";
        $db->query('UPDATE purchases SET status = :new_status WHERE status = :old_status');
        $db->bind(':new_status', 'email_received');
        $db->bind(':old_status', 'sent');

        if ($db->execute()) {
            echo "✅ Successfully updated {$sentCount->count} records from 'sent' to 'email_received'\n";
        } else {
            echo "❌ Failed to update records\n";
            exit("Cannot proceed without updating existing records.\n");
        }
    } else {
        echo "✅ No records use 'sent' status\n";
    }

    echo "\nStep 4: Updating ENUM to replace 'sent' with 'email_received'...\n";

    // Replace 'sent' with 'email_received' in the ENUM
    $alterQuery = "ALTER TABLE purchases MODIFY COLUMN status ENUM(
        'pending',
        'email_received',
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
        echo "✅ Successfully updated ENUM: 'sent' → 'email_received'\n";

        // Verify the new ENUM structure
        echo "\nStep 5: Verifying updated ENUM structure...\n";
        $db->query("SHOW COLUMNS FROM purchases WHERE Field = 'status'");
        $db->execute();
        $newStatusColumn = $db->single();
        echo "New ENUM: {$newStatusColumn->Type}\n";

        echo "\nStep 6: Verifying current status distribution...\n";
        $db->query('SELECT status, COUNT(*) as count FROM purchases GROUP BY status ORDER BY status');
        $db->execute();
        $statuses = $db->resultSet();

        foreach ($statuses as $status) {
            echo "- '{$status->status}': {$status->count} records\n";
        }

        echo "\n✅ STATUS UPDATE COMPLETE!\n";
        echo "✅ 'sent' → 'email_received' (more descriptive)\n";
        echo "✅ Ready for email tracking features\n";
        echo "✅ Database terminology is now clearer\n";

    } else {
        echo "❌ Failed to alter table ENUM\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>