<?php
require_once 'config/config.php';

$stmt = $pdo->query("SELECT p.product_name, p.stock_quantity, pi.unit_price FROM products p LEFT JOIN (SELECT product_id, MAX(unit_price) as unit_price FROM purchase_items GROUP BY product_id) pi ON p.product_id = pi.product_id");
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_valuation = 0;
?>

<h3>Inventory Valuation Report</h3>

<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Stock Quantity</th>
            <th>Last Purchase Price</th>
            <th>Valuation</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inventory as $item):
            $valuation = ($item['stock_quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            $total_valuation += $valuation;
        ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= htmlspecialchars($item['stock_quantity']) ?></td>
                <td><?= htmlspecialchars($item['unit_price'] ? '₹' . number_format($item['unit_price'], 2) : 'N/A') ?></td>
                <td><?= '₹' . number_format($valuation, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total Inventory Valuation</th>
            <th>₹<?= number_format($total_valuation, 2) ?></th>
        </tr>
    </tfoot>
</table>
