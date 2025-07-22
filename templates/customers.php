<?php
require_once 'config/config.php';

$stmt = $pdo->query("SELECT * FROM customers ORDER BY customer_id DESC");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Customer Management</h2>

<a href="index.php?page=customers&action=new">Add New Customer</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Outstanding Balance</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?= htmlspecialchars($customer['customer_id']) ?></td>
                <td><?= htmlspecialchars($customer['customer_name']) ?></td>
                <td><?= htmlspecialchars($customer['contact_info']) ?></td>
                <td><?= htmlspecialchars($customer['outstanding_balance']) ?></td>
                <td>
                    <a href="index.php?page=customers&action=edit&id=<?= $customer['customer_id'] ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
