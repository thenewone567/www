<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $contact_info = $_POST['contact_info'];
    $outstanding_balance = $_POST['outstanding_balance'];
    $credit_limit = $_POST['credit_limit'];

    if (empty($customer_id)) {
        // Insert new customer
        $sql = "INSERT INTO customers (customer_name, contact_info, outstanding_balance, credit_limit) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_name, $contact_info, $outstanding_balance, $credit_limit]);
    } else {
        // Update existing customer
        $sql = "UPDATE customers SET customer_name = ?, contact_info = ?, outstanding_balance = ?, credit_limit = ? WHERE customer_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_name, $contact_info, $outstanding_balance, $credit_limit, $customer_id]);
    }

    header("Location: ../../index.php?page=customers");
    exit();
}
?>
