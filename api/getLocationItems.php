<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include necessary files
require_once dirname(__DIR__) . '/bootstrap.php';

try {
    // Check if request method is GET or POST
    if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
        throw new Exception('Only GET and POST methods allowed');
    }

    // Get location ID from request
    $locationId = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $locationId = $input['location_id'] ?? '';
    } else {
        $locationId = $_GET['location_id'] ?? '';
    }

    if (empty($locationId)) {
        throw new Exception('Location ID is required');
    }

    // Initialize database
    $db = new Database();

    // Get location details
    $db->query('
        SELECT location_id, location_name, location_code, standardized_address, location_type, zone
        FROM locations 
        WHERE location_id = ? AND is_active = 1
    ');
    $db->bind(1, $locationId);
    $db->execute();
    $location = $db->single();

    if (!$location) {
        throw new Exception('Location not found');
    }

    // Get inventory items for this location
    $db->query('
        SELECT i.inventory_id, i.product_id, i.quantity,
               p.product_name, p.sku, p.barcode, p.selling_price, p.purchase_price,
               p.category_id, c.category_name,
               (i.quantity * COALESCE(p.purchase_price, 0)) as total_cost_value,
               (i.quantity * COALESCE(p.selling_price, 0)) as total_selling_value
        FROM inventory i
        LEFT JOIN products p ON i.product_id = p.product_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE i.location_id = ? AND i.quantity > 0
        ORDER BY p.product_name ASC
    ');
    $db->bind(1, $locationId);
    $db->execute();
    $items = $db->resultSet() ?? [];

    // Debug logging
    error_log("getLocationItems API - Location ID: $locationId, Items found: " . count($items));
    if (count($items) > 0) {
        error_log("First item: " . json_encode($items[0]));
    }

    // Calculate summary statistics
    $totalItems = count($items);
    $totalQuantity = array_sum(array_column($items, 'quantity'));
    $totalCostValue = array_sum(array_column($items, 'total_cost_value'));
    $totalSellingValue = array_sum(array_column($items, 'total_selling_value'));

    // Format items for display
    $formattedItems = array_map(function ($item) {
        return [
            'inventory_id'        => $item->inventory_id,
            'product_id'          => $item->product_id,
            'product_name'        => $item->product_name ?? 'Unknown Product',
            'sku'                 => $item->sku ?? 'N/A',
            'barcode'             => $item->barcode ?? '',
            'category_name'       => $item->category_name ?? 'Uncategorized',
            'quantity'            => (int) $item->quantity,
            'cost_price'          => (float) ($item->purchase_price ?? 0),
            'selling_price'       => (float) ($item->selling_price ?? 0),
            'total_cost_value'    => (float) $item->total_cost_value,
            'total_selling_value' => (float) $item->total_selling_value
        ];
    }, $items);

    // Format response
    echo json_encode([
        'success' => true,
        'data'    => [
            'location' => [
                'location_id'          => $location->location_id,
                'location_name'        => $location->location_name,
                'location_code'        => $location->location_code,
                'standardized_address' => $location->standardized_address,
                'location_type'        => $location->location_type,
                'zone'                 => $location->zone
            ],
            'items'    => $formattedItems,
            'summary'  => [
                'total_items'         => $totalItems,
                'total_quantity'      => $totalQuantity,
                'total_cost_value'    => $totalCostValue,
                'total_selling_value' => $totalSellingValue,
                'estimated_profit'    => $totalSellingValue - $totalCostValue
            ]
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>