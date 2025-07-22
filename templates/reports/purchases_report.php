<?php
require_once 'config/config.php';

// Default date range to the last 30 days
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

$stmt = $pdo->prepare("SELECT p.*, s.supplier_name FROM purchases p LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id WHERE p.purchase_date BETWEEN ? AND ? ORDER BY p.purchase_date DESC");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_purchases = array_sum(array_column($purchases, 'total_amount'));
?>

<h3>Purchases Report (<?= $start_date ?> to <?= $end_date ?>)</h3>

<form method="get">
    <input type="hidden" name="page" value="reports">
    <input type="hidden" name="report" value="purchases">
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" value="<?= $start_date ?>">
    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" value="<?= $end_date ?>">
    <button type="submit">Generate Report</button>
    <a href="src/actions/export.php?report=purchases&format=csv" class="export-btn">Export as CSV</a>
</form>

<h4>Total Purchases: ₹<?= number_format($total_purchases, 2) ?></h4>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($purchases as $purchase): ?>
            <tr>
                <td><?= htmlspecialchars($purchase['purchase_id']) ?></td>
                <td><?= htmlspecialchars($purchase['supplier_name']) ?></td>
                <td><?= htmlspecialchars($purchase['purchase_date']) ?></td>
                <td><?= htmlspecialchars($purchase['total_amount']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
