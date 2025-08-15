<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database.php';

$db = new Database();
$db->query('SELECT * FROM purchase_orders');
$db->execute();
$orders = $db->resultSet();
print_r($orders);
