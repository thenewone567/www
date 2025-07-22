<?php
require_once 'config/config.php';

// Fetch all sales
$stmt = $pdo->query("SELECT s.*, c.customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.customer_id ORDER BY s.sale_id DESC");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Sales History</h2>

<a href="index.php?page=sales&action=new">New Sale</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th>Payment Mode</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?= htmlspecialchars($sale['sale_id']) ?></td>
                <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                <td><?= htmlspecialchars($sale['sale_date']) ?></td>
                <td><?= htmlspecialchars($sale['total_amount']) ?></td>
                <td><?= htmlspecialchars($sale['payment_mode']) ?></td>
                <td>
                    <a href="index.php?page=sales&action=view&id=<?= $sale['sale_id'] ?>">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
