<?php
require_once 'config/config.php';

$customer = [
    'customer_id' => '',
    'customer_name' => '',
    'contact_info' => '',
    'outstanding_balance' => '',
    'credit_limit' => ''
];
$is_edit = false;

if (isset($_GET['id'])) {
    $is_edit = true;
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$_GET['id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2><?= $is_edit ? 'Edit Customer' : 'Add New Customer' ?></h2>

<form action="src/actions/save_customer.php" method="post">
    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer['customer_id']) ?>">

    <label for="customer_name">Customer Name:</label>
    <input type="text" name="customer_name" id="customer_name" value="<?= htmlspecialchars($customer['customer_name']) ?>" required>
    <br>

    <label for="contact_info">Contact Info:</label>
    <input type="text" name="contact_info" id="contact_info" value="<?= htmlspecialchars($customer['contact_info']) ?>">
    <br>

    <label for="outstanding_balance">Outstanding Balance:</label>
    <input type="text" name="outstanding_balance" id="outstanding_balance" value="<?= htmlspecialchars($customer['outstanding_balance']) ?>">
    <br>

    <label for="credit_limit">Credit Limit:</label>
    <input type="text" name="credit_limit" id="credit_limit" value="<?= htmlspecialchars($customer['credit_limit']) ?>">
    <br>

    <button type="submit"><?= $is_edit ? 'Update Customer' : 'Save Customer' ?></button>
</form>
