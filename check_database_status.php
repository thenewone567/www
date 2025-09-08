<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== CURRENT DATABASE STATUS ANALYSIS ===\n\n";

    echo "Current status values in purchases table:\n";
    $db->query('SELECT status, COUNT(*) as count FROM purchases GROUP BY status');
    $db->execute();
    $results = $db->resultSet();
    foreach ($results as $row) {
        echo "- {$row->status}: {$row->count} records\n";
    }

    echo "\nSample pending_arrival records:\n";
    $db->query('SELECT po_number, status, created_at FROM purchases WHERE status = :status LIMIT 5');
    $db->bind(':status', 'pending_arrival');
    $db->execute();
    $results = $db->resultSet();
    foreach ($results as $row) {
        echo "- {$row->po_number} ({$row->status}) - {$row->created_at}\n";
    }

    echo "\n=== PROPOSED DATABASE MIGRATION ===\n";
    echo "Will update: pending_arrival → off-loading\n";
    echo "This will affect all records with 'pending_arrival' status\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>