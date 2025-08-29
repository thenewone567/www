<?php
require_once __DIR__ . '/../bootstrap.php';

$model = new User();
$id = 3;

echo "getUserByIdAndTable($id, 'users'):\n";
$r1 = $model->getUserByIdAndTable($id, 'users');
var_dump($r1);

echo "\ngetUserById($id):\n";
$r2 = $model->getUserById($id);
var_dump($r2);

// Also try direct SQL via Database class to probe users table
$db = new Database();
$db->query('SELECT * FROM users WHERE user_id = :user_id');
$db->bind(':user_id', $id);
$db->execute();
$d = $db->single();
echo "\nDirect DB query (users) result: \n";
var_dump($d);

?>