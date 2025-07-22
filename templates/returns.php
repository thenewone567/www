<?php
require_once 'config/config.php';

$stmt = $pdo->query("SELECT r.*, s.sale_id, p.purchase_id FROM returns r LEFT JOIN sales s ON r.sale_id = s.sale_id LEFT JOIN purchases p ON r.purchase_id = p.purchase_id ORDER BY r.return_id DESC");
$returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Returns Management</h2>

<a href="index.php?page=returns&action=new">New Return</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Associated ID</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($returns as $return): ?>
            <tr>
                <td><?= htmlspecialchars($return['return_id']) ?></td>
                <td><?= htmlspecialchars(ucfirst($return['return_type'])) ?></td>
                <td>
                    <?php if ($return['sale_id']): ?>
                        Sale #<?= htmlspecialchars($return['sale_id']) ?>
                    <?php elseif ($return['purchase_id']): ?>
                        Purchase #<?= htmlspecialchars($return['purchase_id']) ?>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($return['return_date']) ?></td>
                <td><?= htmlspecialchars($return['status']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
