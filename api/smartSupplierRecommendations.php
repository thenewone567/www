<?php
/**
 * Smart Supplier Recommendations API
 * 
 * Returns intelligent supplier recommendations for purchase forms
 * Replaces the old primary supplier logic
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../bootstrap.php';
require_once '../app/services/PurchaseFormHelper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $productId = $_GET['product_id'] ?? null;
    $quantity = (int) ($_GET['quantity'] ?? 10);
    $urgency = $_GET['urgency'] ?? 'normal';

    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    // Validate urgency
    if (!in_array($urgency, ['normal', 'urgent', 'bulk'])) {
        $urgency = 'normal';
    }

    // Initialize helper
    $db = new Database();
    $helper = new PurchaseFormHelper($db);

    // Get suppliers with recommendations
    $suppliers = $helper->getSuppliersWithRecommendations($productId, $quantity, $urgency);

    if (empty($suppliers)) {
        echo json_encode([
            'success' => true,
            'suppliers' => [],
            'message' => 'No active suppliers found for this product'
        ]);
        exit;
    }

    // Format response
    $formattedSuppliers = [];
    foreach ($suppliers as $supplier) {
        $formattedSuppliers[] = [
            'supplier_id' => $supplier->supplier_id,
            'supplier_name' => $supplier->supplier_name,
            'purchase_price' => (float) $supplier->purchase_price,
            'lead_time_days' => (int) $supplier->lead_time_days,
            'min_order_quantity' => (int) $supplier->min_order_quantity,
            'quality_rating' => $supplier->quality_rating ? (float) $supplier->quality_rating : null,
            'supplier_tier' => $supplier->supplier_tier,
            'is_recommended' => (bool) $supplier->is_recommended,
            'is_primary' => (bool) $supplier->is_primary, // For backward compatibility
            'recommendation_badge' => $supplier->recommendation_badge,
            'recommendation_class' => $supplier->recommendation_class,
            'recommendation_reasoning' => $supplier->recommendation_reasoning
        ];
    }

    echo json_encode([
        'success' => true,
        'suppliers' => $formattedSuppliers,
        'context' => [
            'product_id' => (int) $productId,
            'quantity' => $quantity,
            'urgency' => $urgency
        ],
        'meta' => [
            'total_suppliers' => count($formattedSuppliers),
            'recommended_count' => count(array_filter($formattedSuppliers, function ($s) {
                return $s['is_recommended'];
            })),
            'api_version' => '2.0',
            'selection_method' => 'smart_recommendation'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'api_version' => '2.0'
    ]);
}
?>