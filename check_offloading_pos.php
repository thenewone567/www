<?php
require_once 'bootstrap.php';

$db = new Database();

// Check all PO statuses
$db->query('SELECT po_number, status, dock_arrival_time FROM purchase_orders ORDER BY created_at DESC LIMIT 10');
$allPos = $db->resultset();

echo "Recent POs and their statuses:\n";
foreach ($allPos as $po) {
    echo "PO: " . $po->po_number . " | Status: " . $po->status . " | Dock Time: " . ($po->dock_arrival_time ?: 'NULL') . "\n";
}

echo "\n--- Checking specifically for off-loading status ---\n";
$db->query('SELECT po_number, status, dock_arrival_time FROM purchase_orders WHERE status = :status ORDER BY dock_arrival_time DESC');
$db->bind(':status', 'pending_arrival');
$pos = $db->resultset();

echo "Off-loading POs in database:\n";
foreach ($pos as $po) {
    echo "PO: " . $po->po_number . " | Status: " . $po->status . " | Started: " . $po->dock_arrival_time . "\n";
}

if (empty($pos)) {
    echo "No POs currently in off-loading status (pending_arrival)\n";
}
?>