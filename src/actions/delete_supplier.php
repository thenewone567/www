<?php
require_once '../../config/config.php';

if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];

    // Delete the supplier
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);

    header("Location: ../../index.php?page=suppliers");
    exit();
}
?>
