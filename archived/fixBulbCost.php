<?php
require_once '../app/config.php';
require_once '../app/helpers.php';

// Set headers for JSON response
header('Content-Type: application/json');

try {
    $db = new Database();

    echo json_encode(['message' => 'Updating bulb cost to ₹83.20']) . "\n";

    // First, check current bulb data
    $db->query("SELECT product_id, product_name, purchase_price, selling_price FROM products WHERE product_id = 139");
    $beforeUpdate = $db->single();

    echo json_encode([
        'before_update' => $beforeUpdate
    ]) . "\n";

    // Update the bulb cost to 83.20
    $db->query("UPDATE products SET purchase_price = :new_cost WHERE product_id = :product_id");
    $db->bind(':new_cost', 83.20);
    $db->bind(':product_id', 139);
    $result = $db->execute();

    if ($result) {
        // Check updated data
        $db->query("SELECT product_id, product_name, purchase_price, selling_price FROM products WHERE product_id = 139");
        $afterUpdate = $db->single();

        echo json_encode([
            'success' => true,
            'message' => 'Bulb cost updated successfully!',
            'after_update' => $afterUpdate,
            'cost_changed_from' => $beforeUpdate->purchase_price ?? 0,
            'cost_changed_to' => 83.20
        ]) . "\n";
    } else {
        throw new Exception('Failed to update bulb cost');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]) . "\n";
}
?>