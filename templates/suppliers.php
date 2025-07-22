<?php
require_once 'config/config.php';

$stmt = $pdo->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Supplier Management</h2>

<a href="index.php?page=suppliers&action=new">Add New Supplier</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>GST Info</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($suppliers as $supplier): ?>
            <tr>
                <td><?= htmlspecialchars($supplier['supplier_id']) ?></td>
                <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                <td><?= htmlspecialchars($supplier['contact_info']) ?></td>
                <td><?= htmlspecialchars($supplier['gst_info']) ?></td>
                <td>
                    <a href="index.php?page=suppliers&action=edit&id=<?= $supplier['supplier_id'] ?>">Edit</a>
                    <a href="src/actions/delete_supplier.php?id=<?= $supplier['supplier_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
