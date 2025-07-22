<?php
require_once 'config/config.php';

// Fetch all purchases
$stmt = $pdo->query("SELECT p.*, s.supplier_name FROM purchases p LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id ORDER BY p.purchase_id DESC");
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Purchase History</h2>

<a href="index.php?page=purchases&action=new">New Purchase</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($purchases as $purchase): ?>
            <tr>
                <td><?= htmlspecialchars($purchase['purchase_id']) ?></td>
                <td><?= htmlspecialchars($purchase['supplier_name']) ?></td>
                <td><?= htmlspecialchars($purchase['purchase_date']) ?></td>
                <td><?= htmlspecialchars($purchase['total_amount']) ?></td>
                <td>
                    <a href="index.php?page=purchases&action=view&id=<?= $purchase['purchase_id'] ?>">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
