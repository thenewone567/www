<?php
require_once 'config/config.php';

// Default date range to the last 30 days
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

$stmt = $pdo->prepare("SELECT s.*, c.customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.customer_id WHERE s.sale_date BETWEEN ? AND ? ORDER BY s.sale_date DESC");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_sales = array_sum(array_column($sales, 'total_amount'));
?>

<h3>Sales Report (<?= $start_date ?> to <?= $end_date ?>)</h3>

<form method="get">
    <input type="hidden" name="page" value="reports">
    <input type="hidden" name="report" value="sales">
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" value="<?= $start_date ?>">
    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" value="<?= $end_date ?>">
    <button type="submit">Generate Report</button>
    <a href="src/actions/export.php?report=sales&format=csv" class="export-btn">Export as CSV</a>
</form>

<h4>Total Sales: ₹<?= number_format($total_sales, 2) ?></h4>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?= htmlspecialchars($sale['sale_id']) ?></td>
                <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                <td><?= htmlspecialchars($sale['sale_date']) ?></td>
                <td><?= htmlspecialchars($sale['total_amount']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
