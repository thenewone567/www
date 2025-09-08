<?php
require 'bootstrap.php';
$db=new Database();

echo "DESCRIBE purchases:\n";
$db->query('DESCRIBE purchases'); $db->execute(); $cols=$db->resultSet(); foreach($cols as $c) echo $c->Field.' ('.$c->Type.')\n';

echo "\nSAMPLE purchases (ids 173,182,187):\n";
$db->query('SELECT purchase_id, po_number, status, receiving_area_id, receiving_location_code, created_at FROM purchases WHERE purchase_id IN (173,182,187)'); $db->execute(); $rows = $db->resultSet(); if($rows) { foreach($rows as $r) echo "- {$r->purchase_id} | {$r->po_number} | status: {$r->status} | receiving_area_id: " . ($r->receiving_area_id ?? 'NULL') . " | receiving_location_code: " . ($r->receiving_location_code ?? 'NULL') . " | created: {$r->created_at}\n"; } else { echo "No sample POs found for those IDs\n"; }

echo "\nRECEIVING locations (location_type='receiving'):\n";
$db->query("SELECT location_id, location_code, location_name FROM locations WHERE location_type='receiving'"); $db->execute(); $receiving = $db->resultSet(); foreach($receiving as $r) echo "- {$r->location_id} | {$r->location_code} | {$r->location_name}\n";
?>
