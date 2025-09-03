<?php
require_once '../app/config.php';
require_once '../app/helpers.php';

// Set headers for JSON response
header('Content-Type: application/json');

try {
    $db = new Database();

    echo json_encode(['message' => 'Setting up 3-column cost system']) . "\n";

    // Check if current_average_cost column exists
    $db->query("SHOW COLUMNS FROM products LIKE 'current_average_cost'");
    $columnExists = $db->single();

    if (!$columnExists) {
        echo json_encode(['action' => 'Adding current_average_cost column']) . "\n";

        // Add the new column
        $db->query("ALTER TABLE products ADD COLUMN current_average_cost DECIMAL(10,2) DEFAULT NULL AFTER purchase_price");
        $db->execute();

        echo json_encode(['success' => 'Column added successfully']) . "\n";
    } else {
        echo json_encode(['info' => 'Column already exists']) . "\n";
    }

    // Update bulb with proper 3-cost system
    echo json_encode(['action' => 'Setting up bulb 3-cost system']) . "\n";

    // Set bulb: purchase_price=64.00, current_average_cost=83.20, selling_price=140.80
    $db->query("
        UPDATE products 
        SET purchase_price = :purchase_price, 
            current_average_cost = :avg_cost,
            selling_price = :selling_price
        WHERE product_id = 139
    ");
    $db->bind(':purchase_price', 64.00);  // Base purchase price
    $db->bind(':avg_cost', 83.20);        // Current average cost
    $db->bind(':selling_price', 140.80);  // Sale price
    $result = $db->execute();

    if ($result) {
        // Verify the update
        $db->query("
            SELECT product_id, product_name, purchase_price, current_average_cost, selling_price 
            FROM products 
            WHERE product_id = 139
        ");
        $bulbData = $db->single();

        echo json_encode([
            'success' => true,
            'message' => '3-column cost system implemented!',
            'bulb_data' => $bulbData,
            'explanation' => [
                'purchase_price' => '₹64.00 (Base/Supplier cost)',
                'current_average_cost' => '₹83.20 (Real average cost)',
                'selling_price' => '₹140.80 (Sale price from price management)'
            ]
        ]) . "\n";
    } else {
        throw new Exception('Failed to update bulb costs');
    }

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]) . "\n";
}
?>