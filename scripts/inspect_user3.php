<?php
require_once __DIR__ . '/../bootstrap.php';

$model = new User();
$id = 3;

echo "getUserById({$id}):\n";
$r = $model->getUserById($id);
var_export($r);

echo "\n\ngetUserByIdAndTable({$id}, 'users'):\n";
var_export($model->getUserByIdAndTable($id, 'users'));

echo "\n\ngetUserByIdAndTable({$id}, 'customers'):\n";
var_export($model->getUserByIdAndTable($id, 'customers'));

echo "\n\ngetUserByIdAndTable({$id}, 'contractors'):\n";
var_export($model->getUserByIdAndTable($id, 'contractors'));

echo "\n";
?>