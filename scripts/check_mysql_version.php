<?php
require_once __DIR__ . '/../bootstrap.php';
require_once APPROOT . DS . 'app' . DS . 'Database.php';
$db = new Database();
$db->query('SELECT VERSION() as v');
$db->execute();
$r = $db->single();
echo 'MySQL version: ' . $r->v . PHP_EOL;
