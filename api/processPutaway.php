<?php
/**
 * Process Putaway API Endpoint
 * Handles putaway operations from receivin        // Find product by barcode, SKU, or product ID
        $db->query("SELECT product_id, product_name, sku, barcode FROM products WHERE barcode = ? OR sku = ? OR CONCAT('P-', product_id) = ? LIMIT 1");
        $db->bind(1, $itemBarcode);
        $db->bind(2, $itemBarcode);
        $db->bind(3, $itemBarcode);
        $db->execute();
        $product = $db->single();

        if (!$product) {
            throw new Exception('Product not found with barcode/SKU: ' . $itemBarcode);
        }

        // Find storage location
        $db->query("SELECT location_id, location_name, location_code FROM locations WHERE location_code = ? AND location_type = 'storage'");
        $db->bind(1, $locationCode);
        $db->execute();
        $location = $db->single();

        if (!$location) {
            throw new Exception('Storage location not found: ' . $locationCode);
        }ocations
 */

// Prevent PHP from emitting HTML error pages in API responses
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Buffer output so we can strip stray HTML or notices and always return valid JSON
ob_start();

// Register shutdown handler to catch fatal errors and return JSON instead of empty body/HTML
function putaway_shutdown_handler()
{
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Capture and log any buffered output
        while (ob_get_length() !== false && ob_get_level() > 0) {
            $buf = ob_get_clean();
            if (!empty($buf)) {
                error_log("Putaway API stray output (shutdown): " . $buf);
            }
        }

        // Log fatal error details for debugging
        $msg = sprintf("Fatal error in %s on line %d: %s", $error['file'], $error['line'], $error['message']);
        error_log("Putaway API fatal error: " . $msg);

        // Ensure JSON response to client
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode(['success' => false, 'message' => 'Internal server error.']);
        // flush output
        @flush();
    }
}
register_shutdown_function('putaway_shutdown_handler');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get request data
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    $itemBarcode = trim($input['item_barcode'] ?? '');
    $locationCode = trim($input['location_code'] ?? '');
    $quantity = (int) ($input['quantity'] ?? 0);
    $userId = (int) ($input['user_id'] ?? 0);
    $purchaseItemId = (int) ($input['purchase_item_id'] ?? 0); // Add this for better tracking

    // Validate input
    if (empty($itemBarcode) || empty($locationCode) || $quantity <= 0) {
        throw new Exception('Missing required fields: item_barcode, location_code, quantity');
    }

    // Log the putaway request for debugging
    error_log("Putaway request: Barcode=$itemBarcode, Location=$locationCode, Qty=$quantity, PurchaseItemId=$purchaseItemId");

    // Load database
    require_once __DIR__ . '/../bootstrap.php';
    $db = new Database();

    $db->beginTransaction();

    try {
        // Find product by SKU/barcode
        $db->query("SELECT product_id, product_name, sku FROM products WHERE sku = ? OR barcode = ?");
        $db->bind(1, $itemBarcode);
        $db->bind(2, $itemBarcode);
        $db->execute();
        $product = $db->single();

        if (!$product) {
            throw new Exception('Product not found with SKU/barcode: ' . $itemBarcode);
        }

        // Find storage location
        $db->query("SELECT location_id, location_name FROM locations WHERE location_code = ? AND location_type IN ('storage', 'bin')");
        $db->bind(1, $locationCode);
        $db->execute();
        $location = $db->single();

        if (!$location) {
            throw new Exception('Storage location not found: ' . $locationCode);
        }

        // Check if item exists in putaway queue (pending from receiving)
        // If we have a specific purchase_item_id, use it for exact matching
        if ($purchaseItemId > 0) {
            $db->query("
                SELECT pi.purchase_item_id, pi.quantity, pi.received_quantity, 
                       COALESCE(pi.putaway_quantity, 0) as putaway_quantity,
                       po.po_number, pi.product_id
                FROM purchase_items pi
                JOIN purchases po ON pi.purchase_id = po.purchase_id
                WHERE pi.purchase_item_id = ? 
                AND pi.product_id = ?
                AND pi.received_quantity > COALESCE(pi.putaway_quantity, 0)
                AND po.status IN ('received', 'receiving_in_progress', 'completed')
                LIMIT 1
            ");
            $db->bind(1, $purchaseItemId);
            $db->bind(2, $product->product_id);
        } else {
            // Fallback to finding by product ID (FIFO)
            $db->query("
                SELECT pi.purchase_item_id, pi.quantity, pi.received_quantity, 
                       COALESCE(pi.putaway_quantity, 0) as putaway_quantity,
                       po.po_number, pi.product_id
                FROM purchase_items pi
                JOIN purchases po ON pi.purchase_id = po.purchase_id
                WHERE pi.product_id = ? 
                AND pi.received_quantity > COALESCE(pi.putaway_quantity, 0)
                AND po.status IN ('received', 'receiving_in_progress', 'completed')
                ORDER BY pi.received_at ASC
                LIMIT 1
            ");
            $db->bind(1, $product->product_id);
        }
        $db->execute();
        $pendingItem = $db->single();

        if ($pendingItem) {
            $availableQty = $pendingItem->received_quantity - $pendingItem->putaway_quantity;

            if ($quantity > $availableQty) {
                throw new Exception("Requested quantity ($quantity) exceeds available quantity ($availableQty)");
            }

            // Update putaway quantity in purchase_items
            $newPutawayQty = $pendingItem->putaway_quantity + $quantity;
            $db->query("UPDATE purchase_items SET putaway_quantity = ? WHERE purchase_item_id = ?");
            $db->bind(1, $newPutawayQty);
            $db->bind(2, $pendingItem->purchase_item_id);
            $db->execute();

            // Remove quantity from receiving area inventory
            $db->query("UPDATE inventory SET quantity = quantity - ? 
                       WHERE product_id = ? 
                       AND location_id IN (SELECT location_id FROM locations WHERE location_type = 'receiving')
                       AND quantity >= ?
                       LIMIT 1");
            $db->bind(1, $quantity);
            $db->bind(2, $product->product_id);
            $db->bind(3, $quantity);
            $db->execute();

            $reference = "PO: " . $pendingItem->po_number;
        } else {
            $reference = "Manual putaway";
        }

        // Update inventory at storage location
        $db->query("
            SELECT inventory_id, quantity 
            FROM inventory 
            WHERE product_id = ? AND location_id = ?
        ");
        $db->bind(1, $product->product_id);
        $db->bind(2, $location->location_id);
        $db->execute();
        $existingInventory = $db->single();

        if ($existingInventory) {
            // Update existing inventory
            $db->query("UPDATE inventory SET quantity = quantity + ? WHERE inventory_id = ?");
            $db->bind(1, $quantity);
            $db->bind(2, $existingInventory->inventory_id);
            $db->execute();
        } else {
            // Create new inventory record
            $db->query("INSERT INTO inventory (product_id, location_id, quantity) VALUES (?, ?, ?)");
            $db->bind(1, $product->product_id);
            $db->bind(2, $location->location_id);
            $db->bind(3, $quantity);
            $db->execute();
        }

        // Record inventory movement
        $db->query("
            INSERT INTO inventory_movements 
            (product_id, location_id, movement_type, quantity_change, reference_id, notes, created_by, putaway_status) 
            VALUES (?, ?, 'putaway', ?, ?, ?, ?, 'completed')
        ");
        $db->bind(1, $product->product_id);
        $db->bind(2, $location->location_id);
        $db->bind(3, $quantity);
        $db->bind(4, $pendingItem->purchase_item_id ?? null);
        $db->bind(5, "Putaway: {$product->product_name} to {$locationCode}. {$reference}");
        $db->bind(6, $userId);
        $db->execute();

        // Update any pending inventory movements for this product
        $db->query("
            UPDATE inventory_movements 
            SET putaway_status = 'completed', location_id = ? 
            WHERE product_id = ? 
            AND movement_type = 'received' 
            AND putaway_status = 'pending'
            LIMIT ?
        ");
        $db->bind(1, $location->location_id);
        $db->bind(2, $product->product_id);
        $db->bind(3, $quantity);
        $db->execute();

        $db->commit();

        // Clear and log any stray output, then return clean JSON
        $buffer = ob_get_clean();
        if (!empty($buffer)) {
            error_log("Putaway API stray output (success): " . $buffer);
        }

        // Calculate remaining quantities
        $remainingInQueue = 0;
        if ($pendingItem) {
            $remainingInQueue = $pendingItem->received_quantity - ($pendingItem->putaway_quantity + $quantity);
        }

        echo json_encode([
            'success' => true,
            'message' => "Successfully put away {$quantity} units of {$product->product_name} to {$location->location_name}",
            'data' => [
                'product_name' => $product->product_name,
                'quantity' => $quantity,
                'location' => $location->location_name,
                'location_code' => $locationCode,
                'remaining_quantity' => $remainingInQueue,
                'has_remaining' => $remainingInQueue > 0,
                'total_received' => $pendingItem ? $pendingItem->received_quantity : 0,
                'total_putaway' => $pendingItem ? ($pendingItem->putaway_quantity + $quantity) : $quantity
            ]
        ]);

    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Ensure any buffered output is captured and logged, then return JSON error
    $buffer = '';
    if (ob_get_length() !== false) {
        $buffer = ob_get_clean();
    }
    if (!empty($buffer)) {
        error_log("Putaway API stray output (error): " . $buffer);
    }

    error_log("Putaway API error: " . $e->getMessage());

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>