<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== DETAILED DATABASE STATUS INVESTIGATION ===\n\n";

    // Get all unique status values with their exact content
    echo "All unique status values (with quotes to show exact content):\n";
    $db->query('SELECT DISTINCT status, LENGTH(status) as length FROM purchases ORDER BY status');
    $db->execute();
    $statuses = $db->resultSet();

    foreach ($statuses as $status) {
        echo "- '{$status->status}' (length: {$status->length})\n";
    }

    echo "\nLooking for variations of 'pending_arrival':\n";

    // Check for exact match
    $db->query('SELECT po_number, status, LENGTH(status) as len FROM purchases WHERE status = :status');
    $db->bind(':status', 'pending_arrival');
    $db->execute();
    $exactMatch = $db->resultSet();
    echo "Exact 'pending_arrival': " . count($exactMatch) . " records\n";

    // Check for case variations
    $db->query('SELECT po_number, status FROM purchases WHERE LOWER(status) = :status');
    $db->bind(':status', 'pending_arrival');
    $db->execute();
    $caseMatch = $db->resultSet();
    echo "Case-insensitive 'pending_arrival': " . count($caseMatch) . " records\n";

    // Check for trimmed variations
    $db->query('SELECT po_number, status FROM purchases WHERE TRIM(status) = :status');
    $db->bind(':status', 'pending_arrival');
    $db->execute();
    $trimMatch = $db->resultSet();
    echo "Trimmed 'pending_arrival': " . count($trimMatch) . " records\n";

    // Show all records that might be close to pending_arrival
    echo "\nAll records containing 'pending' or 'arrival':\n";
    $db->query('SELECT po_number, status, LENGTH(status) as len FROM purchases WHERE status LIKE "%pending%" OR status LIKE "%arrival%"');
    $db->execute();
    $likeMatch = $db->resultSet();

    foreach ($likeMatch as $record) {
        echo "- {$record->po_number}: '{$record->status}' (length: {$record->len})\n";
    }

    if (count($likeMatch) > 0) {
        echo "\nFound records to migrate! Proceeding with update...\n";

        foreach ($likeMatch as $record) {
            if (
                trim(strtolower($record->status)) === 'pending_arrival' ||
                $record->status === 'pending_arrival' ||
                trim($record->status) === 'pending_arrival'
            ) {

                echo "Updating {$record->po_number} from '{$record->status}' to 'off-loading'\n";

                $db->query('UPDATE purchases SET status = :new_status WHERE po_number = :po_number');
                $db->bind(':new_status', 'off-loading');
                $db->bind(':po_number', $record->po_number);
                $db->execute();
            }
        }

        echo "\nVerification after update:\n";
        $db->query('SELECT status, COUNT(*) as count FROM purchases GROUP BY status ORDER BY status');
        $db->execute();
        $finalStatus = $db->resultSet();

        foreach ($finalStatus as $status) {
            echo "- {$status->status}: {$status->count} records\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>