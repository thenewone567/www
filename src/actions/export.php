<?php
require_once '../../config/config.php';

if (isset($_GET['report']) && isset($_GET['format'])) {
    $report = $_GET['report'];
    $format = $_GET['format'];
    $filename = $report . '_report_' . date('Y-m-d') . '.' . $format;

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    $output = fopen('php://output', 'w');

    // Fetch data based on the report type
    $data = [];
    if ($report === 'sales') {
        fputcsv($output, ['ID', 'Customer', 'Date', 'Total Amount']);
        $stmt = $pdo->query("SELECT s.sale_id, c.customer_name, s.sale_date, s.total_amount FROM sales s LEFT JOIN customers c ON s.customer_id = c.customer_id ORDER BY s.sale_id DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($report === 'purchases') {
        fputcsv($output, ['ID', 'Supplier', 'Date', 'Total Amount']);
        $stmt = $pdo->query("SELECT p.purchase_id, s.supplier_name, p.purchase_date, p.total_amount FROM purchases p LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id ORDER BY p.purchase_id DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}
?>
