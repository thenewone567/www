<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database.php';

$db = new Database();
$db->query('SELECT DISTINCT status FROM purchase_orders');
$db->execute();
$statuses = $db->resultSet();
print_r($statuses);
