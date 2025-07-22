<?php
require_once 'config/config.php';

$supplier = [
    'supplier_id' => '',
    'supplier_name' => '',
    'contact_info' => '',
    'gst_info' => ''
];
$is_edit = false;

if (isset($_GET['id'])) {
    $is_edit = true;
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$_GET['id']]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2><?= $is_edit ? 'Edit Supplier' : 'Add New Supplier' ?></h2>

<form action="src/actions/save_supplier.php" method="post">
    <input type="hidden" name="supplier_id" value="<?= htmlspecialchars($supplier['supplier_id']) ?>">

    <label for="supplier_name">Supplier Name:</label>
    <input type="text" name="supplier_name" id="supplier_name" value="<?= htmlspecialchars($supplier['supplier_name']) ?>" required>
    <br>

    <label for="contact_info">Contact Info:</label>
    <input type="text" name="contact_info" id="contact_info" value="<?= htmlspecialchars($supplier['contact_info']) ?>">
    <br>

    <label for="gst_info">GST Info:</label>
    <input type="text" name="gst_info" id="gst_info" value="<?= htmlspecialchars($supplier['gst_info']) ?>">
    <br>

    <button type="submit"><?= $is_edit ? 'Update Supplier' : 'Save Supplier' ?></button>
</form>
