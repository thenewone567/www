<?php
require_once '../app/config.php';
require_once '../app/helpers.php';

// Set headers for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $productId = $_POST['product_id'] ?? 0;
        $newCost = $_POST['new_cost'] ?? 0;

        if ($productId <= 0 || $newCost < 0) {
            throw new Exception('Invalid product ID or cost');
        }

        $db = new Database();

        // Update the purchase_price in products table
        $db->query("UPDATE products SET purchase_price = :cost WHERE product_id = :product_id");
        $db->bind(':cost', $newCost);
        $db->bind(':product_id', $productId);
        $result = $db->execute();

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Product cost updated successfully',
                'product_id' => $productId,
                'new_cost' => $newCost
            ]);
        } else {
            throw new Exception('Failed to update cost in database');
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    // Show current bulb data and form
    echo json_encode([
        'message' => 'Use POST to update cost',
        'example' => 'POST product_id=139&new_cost=83.20'
    ]);
}
?>