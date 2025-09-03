<?php
require_once '../app/config.php';
require_once '../app/Database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getDbh();

    echo json_encode(["message" => "Setting up walk-in customer for sales bot"]) . "\n";

    // Check if walk-in customer exists
    $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = 1");
    $stmt->execute();
    $walkInCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$walkInCustomer) {
        // Create walk-in customer
        echo json_encode(["action" => "Creating walk-in customer"]) . "\n";

        $stmt = $pdo->prepare("
            INSERT INTO customers (customer_id, customer_name, name, contact_info, phone, email, address, status) 
            VALUES (1, 'Walk-in Customer', 'Walk-in Customer', 'N/A', 'N/A', 'walk-in@store.local', 'Store Counter', 'active')
            ON DUPLICATE KEY UPDATE 
            customer_name = 'Walk-in Customer',
            name = 'Walk-in Customer',
            status = 'active'
        ");
        $stmt->execute();

        echo json_encode(["success" => "Walk-in customer created successfully"]) . "\n";
    } else {
        echo json_encode(["info" => "Walk-in customer already exists"]) . "\n";
    }

    // Verify walk-in customer
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = 1");
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "message" => "Walk-in customer setup completed",
        "customer" => [
            "id" => $customer['customer_id'],
            "name" => $customer['name'],
            "status" => $customer['status']
        ]
    ]) . "\n";

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]) . "\n";
}
?>