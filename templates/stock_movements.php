<?php
require_once 'config/config.php';

$stmt = $pdo->query("SELECT sm.*, p.product_name FROM stock_movements sm JOIN products p ON sm.product_id = p.product_id ORDER BY sm.movement_id DESC");
$movements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Stock Movements</h2>

<a href="index.php?page=stock_movements&action=new">New Stock Movement</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>From Location</th>
            <th>To Location</th>
            <th>Quantity</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($movements as $movement): ?>
            <tr>
                <td><?= htmlspecialchars($movement['movement_id']) ?></td>
                <td><?= htmlspecialchars($movement['product_name']) ?></td>
                <td><?= htmlspecialchars($movement['from_location']) ?></td>
                <td><?= htmlspecialchars($movement['to_location']) ?></td>
                <td><?= htmlspecialchars($movement['quantity']) ?></td>
                <td><?= htmlspecialchars($movement['movement_date']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
