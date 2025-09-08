<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== FIXING PO STATUS ===\n\n";

    echo "Setting PO-250906-024518 status to 'off-loading'...\n";

    $db->query('UPDATE purchases SET status = :status WHERE po_number = :po');
    $db->bind(':status', 'off-loading');
    $db->bind(':po', 'PO-250906-024518');

    if ($db->execute()) {
        echo "✅ Update successful!\n";

        // Verify the update
        $db->query('SELECT po_number, status FROM purchases WHERE po_number = :po');
        $db->bind(':po', 'PO-250906-024518');
        $db->execute();
        $result = $db->single();

        echo "✅ PO-250906-024518 status is now: '{$result->status}'\n";

        // Check all off-loading records
        echo "\nAll off-loading records:\n";
        $db->query('SELECT po_number, status, dock_arrival_time FROM purchases WHERE status = :status');
        $db->bind(':status', 'off-loading');
        $db->execute();
        $offLoadingRecords = $db->resultSet();

        foreach ($offLoadingRecords as $record) {
            echo "- {$record->po_number} (status: {$record->status}) - arrived: {$record->dock_arrival_time}\n";
        }

    } else {
        echo "❌ Update failed!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>