<?php
$pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
$st = $pdo->prepare('SELECT product_id, is_active, deleted_at FROM products WHERE product_id = ?');
$st->execute([1]);
$r = $st->fetch(PDO::FETCH_ASSOC);
var_export($r);
