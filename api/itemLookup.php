<?php
/**
 * Item Lookup API for Putaway Scanner
 * Validates scanned items and suggests storage locations
 */

// Turn off error display to prevent HTML in JSON response
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $barcode = $input['barcode'] ?? $_POST['barcode'] ?? $_GET['barcode'] ?? '';

    if (empty($barcode)) {
        throw new Exception('Barcode is required');
    }

    require_once __DIR__ . '/../bootstrap.php';
    $db = new Database();

    // Add debug logging
    error_log("ItemLookup: Processing barcode: " . $barcode);

    // Look up product by SKU or barcode
    $query = "SELECT 
        product_id, 
        product_name, 
        sku, 
        barcode, 
        category_id,
        storage_location,
        product_type
        FROM products 
        WHERE sku = ? OR barcode = ?";

    error_log("ItemLookup: Executing query: " . $query);

    $db->query($query);
    $db->bind(1, $barcode);
    $db->bind(2, $barcode);

    if (!$db->execute()) {
        error_log("ItemLookup: Database execute failed");
        throw new Exception('Database query failed: ' . $db->getLastError());
    }

    error_log("ItemLookup: Query executed successfully");

    $product = $db->single();
    error_log("ItemLookup: Product result: " . json_encode($product));

    if (!$product) {
        // Add debug info when product not found
        $db->query("SELECT COUNT(*) as count FROM products");
        $db->execute();
        $count = $db->single();
        error_log("ItemLookup: No product found, total products: " . ($count->count ?? 0));
        throw new Exception('Product not found with barcode/SKU: ' . $barcode . ' (Total products in DB: ' . ($count->count ?? 0) . ')');
    }

    // Check if item is in receiving areas
    $db->query("SELECT 
        i.quantity,
        l.location_name,
        l.location_code
        FROM inventory i
        JOIN locations l ON i.location_id = l.location_id
        WHERE i.product_id = ? 
        AND l.location_type = 'receiving'
        AND i.quantity > 0");
    $db->bind(1, $product->product_id);
    $db->execute();
    $receivingLocation = $db->single();

    if (!$receivingLocation) {
        throw new Exception('Item not found in receiving areas. Check if it has been received.');
    }

    // Suggest optimal storage location with improved priority logic
    // Priority 1: Locations that already contain this product (for product consolidation)
    $db->query("SELECT 
        l.location_id,
        l.location_code,
        l.location_name,
        l.capacity_cubic_feet,
        l.max_weight_kg,
        SUM(i.quantity) as current_inventory,
        COUNT(DISTINCT i.product_id) as product_variety,
        SUM(CASE WHEN i.product_id = ? THEN i.quantity ELSE 0 END) as this_product_qty
        FROM locations l
        JOIN inventory i ON l.location_id = i.location_id
        WHERE l.location_type = 'storage'
        AND l.is_active = 1
        AND i.product_id = ?
        AND i.quantity > 0
        GROUP BY l.location_id
        HAVING (l.capacity_cubic_feet IS NULL OR current_inventory < l.capacity_cubic_feet * 0.9)
        ORDER BY this_product_qty DESC, current_inventory ASC
        LIMIT 1");
    $db->bind(1, $product->product_id);
    $db->bind(2, $product->product_id);
    $db->execute();
    $suggestedLocation = $db->single();

    // Priority 2: If no existing location with this product, find empty storage locations
    if (!$suggestedLocation) {
        $db->query("SELECT 
            l.location_id,
            l.location_code,
            l.location_name,
            l.capacity_cubic_feet,
            l.max_weight_kg,
            0 as current_inventory,
            0 as product_variety,
            0 as this_product_qty
            FROM locations l
            WHERE l.location_type = 'storage'
            AND l.is_active = 1
            AND l.location_id NOT IN (
                SELECT DISTINCT location_id 
                FROM inventory 
                WHERE quantity > 0
            )
            ORDER BY l.location_code ASC
            LIMIT 1");
        $db->execute();
        $suggestedLocation = $db->single();
    }

    // Priority 3: If no empty locations, find locations with available capacity
    if (!$suggestedLocation) {
        $db->query("SELECT 
            l.location_id,
            l.location_code,
            l.location_name,
            l.capacity_cubic_feet,
            l.max_weight_kg,
            COALESCE(SUM(i.quantity), 0) as current_inventory,
            COUNT(DISTINCT i.product_id) as product_variety,
            0 as this_product_qty
            FROM locations l
            LEFT JOIN inventory i ON l.location_id = i.location_id
            WHERE l.location_type = 'storage'
            AND l.is_active = 1
            GROUP BY l.location_id
            HAVING (l.capacity_cubic_feet IS NULL OR current_inventory < l.capacity_cubic_feet * 0.8)
            ORDER BY current_inventory ASC, product_variety ASC, l.location_code ASC
            LIMIT 1");
        $db->execute();
        $suggestedLocation = $db->single();
    }

    $response = [
        'success' => true,
        'product' => [
            'id' => $product->product_id,
            'name' => $product->product_name,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'category_id' => $product->category_id
        ],
        'receiving_location' => [
            'name' => $receivingLocation->location_name,
            'code' => $receivingLocation->location_code,
            'quantity' => $receivingLocation->quantity
        ],
        'suggested_location' => $suggestedLocation ? [
            'code' => $suggestedLocation->location_code,
            'name' => $suggestedLocation->location_name,
            'available_space' => $suggestedLocation->capacity_cubic_feet ?
                ($suggestedLocation->capacity_cubic_feet - $suggestedLocation->current_inventory) : 'Unlimited',
            'current_inventory' => $suggestedLocation->current_inventory ?? 0,
            'product_variety' => $suggestedLocation->product_variety ?? 0,
            'this_product_qty' => $suggestedLocation->this_product_qty ?? 0,
            'suggestion_reason' => $suggestedLocation->this_product_qty > 0 ?
                'Product consolidation - location already contains this item' :
                ($suggestedLocation->current_inventory == 0 ?
                    'Empty location - ideal for new product placement' :
                    'Available capacity - efficient space utilization')
        ] : [
            'code' => 'No suitable location found',
            'name' => 'Manual assignment required',
            'available_space' => 'N/A',
            'suggestion_reason' => 'All storage locations are at capacity'
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>