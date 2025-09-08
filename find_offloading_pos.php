<?php
require_once 'bootstrap.php';

$db = new Database();

$db->query('SELECT po_number, status, dock_arrival_time FROM purchases WHERE status = "off-loading"');
$db->execute();
$offloading = $db->resultSet();

echo "Off-loading records:\n";
foreach ($offloading as $po) {
    echo "- {$po->po_number} ({$po->status}) - arrived: {$po->dock_arrival_time}\n";
}
?>