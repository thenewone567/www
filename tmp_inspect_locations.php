<?php
require 'bootstrap.php';
$db=new Database();

echo "DESCRIBE locations:\n";
$db->query('DESCRIBE locations'); $db->execute(); $cols=$db->resultSet(); foreach($cols as $c) echo $c->Field.' ('.$c->Type.')\n';

echo "\nSAMPLE rows:\n";
$db->query('SELECT location_id, location_code, location_name, location_type FROM locations LIMIT 10'); $db->execute(); $rows = $db->resultSet(); foreach($rows as $r) echo "- {$r->location_id} | {$r->location_code} | {$r->location_name} | {$r->location_type}\n";
?>
