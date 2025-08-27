<?php
$pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
echo "Officials in users table:\n";
$stmt = $pdo->query('SELECT user_id, name FROM users WHERE is_active = 1 ORDER BY user_id DESC LIMIT 3');
while ($row = $stmt->fetch()) {
    echo "ID: " . $row['user_id'] . ' - ' . $row['name'] . "\n";
}
echo "\nCustomers in customers table:\n";
$stmt = $pdo->query('SELECT customer_id, customer_name FROM customers ORDER BY customer_id DESC LIMIT 3');
while ($row = $stmt->fetch()) {
    echo "ID: " . $row['customer_id'] . ' - ' . $row['customer_name'] . "\n";
}
echo "\nContractors in contractors table:\n";
$stmt = $pdo->query('SELECT contractor_id, contractor_name FROM contractors ORDER BY contractor_id DESC LIMIT 3');
while ($row = $stmt->fetch()) {
    echo "ID: " . $row['contractor_id'] . ' - ' . $row['contractor_name'] . "\n";
}
?>
