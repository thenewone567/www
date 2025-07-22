<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $contact_info = $_POST['contact_info'];
    $gst_info = $_POST['gst_info'];

    if (empty($supplier_id)) {
        // Insert new supplier
        $sql = "INSERT INTO suppliers (supplier_name, contact_info, gst_info) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$supplier_name, $contact_info, $gst_info]);
    } else {
        // Update existing supplier
        $sql = "UPDATE suppliers SET supplier_name = ?, contact_info = ?, gst_info = ? WHERE supplier_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$supplier_name, $contact_info, $gst_info, $supplier_id]);
    }

    header("Location: ../../index.php?page=suppliers");
    exit();
}
?>
