<?php
require_once 'bootstrap.php';

$db = new Database();

echo "Checking PO-250906-024518 status:\n";
$db->query('SELECT po_number, status FROM purchases WHERE po_number = :po');
$db->bind(':po', 'PO-250906-024518');
$db->execute();
$po = $db->single();

if ($po) {
    echo "PO: {$po->po_number} - Status: {$po->status}\n";
} else {
    echo "PO not found!\n";
}

echo "\nAll current statuses:\n";
$db->query('SELECT status, COUNT(*) as count FROM purchases GROUP BY status ORDER BY status');
$db->execute();
$statuses = $db->resultSet();

foreach ($statuses as $s) {
    echo "- {$s->status}: {$s->count} records\n";
}
?>