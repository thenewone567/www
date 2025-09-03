<?php
/**
 * API Endpoint: Get Purchase Items
 * Returns detailed items for a specific purchase order
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include required files
require_once '../bootstrap.php';

try {
    // Get purchase ID from query parameter
    $purchaseId = $_GET['purchase_id'] ?? null;

    if (!$purchaseId || !is_numeric($purchaseId)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid or missing purchase ID'
        ]);
        exit;
    }

    // Create database connection
    $db = new Database();

    // Get purchase information
    $db->query("
        SELECT p.purchase_id, p.po_number, p.purchase_date, p.status, p.total_amount,
               s.supplier_name
        FROM purchases p
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        WHERE p.purchase_id = :purchase_id
    ");
    $db->bind(':purchase_id', $purchaseId);
    $purchaseInfo = $db->single();

    if (!$purchaseInfo) {
        echo json_encode([
            'success' => false,
            'error' => 'Purchase order not found'
        ]);
        exit;
    }

    // Get purchase items with product details
    $db->query("
        SELECT pi.purchase_item_id, pi.quantity, pi.unit_price,
               pi.quantity * pi.unit_price as line_total,
               p.product_id, p.product_name, p.sku, p.image_path,
               c.category_name, b.brand_name, u.unit_name
        FROM purchase_items pi
        LEFT JOIN products p ON pi.product_id = p.product_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN units u ON p.unit_id = u.unit_id
        WHERE pi.purchase_id = :purchase_id
        ORDER BY pi.purchase_item_id
    ");
    $db->bind(':purchase_id', $purchaseId);
    $items = $db->resultSet();

    // Calculate totals
    $totalQuantity = 0;
    $totalValue = 0;

    foreach ($items as $item) {
        $totalQuantity += $item->quantity ?? 0;
        $totalValue += $item->line_total ?? 0;
    }

    // Return successful response
    echo json_encode([
        'success' => true,
        'purchase_info' => $purchaseInfo,
        'items' => $items,
        'summary' => [
            'total_items' => count($items),
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in getPurchaseItems.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error occurred'
    ]);
}
?>