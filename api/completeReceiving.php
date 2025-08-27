<?php
// Disable error reporting and XDebug output for clean JSON
error_reporting(0);
ini_set('display_errors', 0);
ini_set('xdebug.show_exception_trace', 0);

// Ensure clean output for JSON API
ob_start();

// Set headers first before any output
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Clean any previous output
ob_clean();

// Include necessary files
require_once dirname(__DIR__) . '/bootstrap.php';

try {
    // Log the incoming request for debugging
    error_log("Receiving API called: " . json_encode($_POST) . " | JSON: " . file_get_contents('php://input'));

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        // Also try $_POST as fallback
        $input = $_POST;
    }

    if (!$input) {
        throw new Exception('Invalid JSON data and no POST data received');
    }

    // Log parsed input
    error_log("Parsed input: " . json_encode($input));

    // Validate required fields
    $poNumber = $input['po_number'] ?? '';
    $poId = $input['po_id'] ?? '';
    $receivingArea = $input['receiving_area'] ?? '';
    $products = $input['products'] ?? [];
    $notes = $input['notes'] ?? '';

    if (empty($poNumber) || empty($poId) || empty($products)) {
        throw new Exception('Missing required fields');
    }

    // Initialize database
    $db = new Database();

    // Start transaction
    $db->beginTransaction();

    try {
        // Create receiving record
        $db->query('
            INSERT INTO receiving (purchase_id, status, received_date, created_by, notes)
            VALUES (?, "received", NOW(), ?, ?)
        ');
        $db->bind(1, $poId);
        $db->bind(2, $_SESSION['user_id'] ?? 1);
        $db->bind(3, $notes);
        $db->execute();

        $receivingId = $db->lastInsertId();

        // Insert received items
        foreach ($products as $product) {
            $receivedQty = isset($product['received_qty']) ? (int) $product['received_qty'] :
                (isset($product['quantity_received']) ? (int) $product['quantity_received'] : 0);

            if ($receivedQty > 0) {
                // Insert receiving item
                $db->query('
                    INSERT INTO receiving_items (receiving_id, product_id, quantity_expected, quantity_received, condition_status, notes)
                    VALUES (?, ?, ?, ?, "good", "")
                ');
                $db->bind(1, $receivingId);
                $db->bind(2, $product['product_id']);
                $db->bind(3, isset($product['expected_qty']) ? $product['expected_qty'] : $receivedQty);
                $db->bind(4, $receivedQty);
                $db->execute();

                // Update inventory with location assignment
                $locationId = $product['location_id'] ?? null;

                if ($locationId) {
                    // Check if inventory record exists for this product and location
                    $db->query('
                        SELECT inventory_id, quantity 
                        FROM inventory 
                        WHERE product_id = ? AND location_id = ?
                    ');
                    $db->bind(1, $product['product_id']);
                    $db->bind(2, $locationId);
                    $db->execute();
                    $existingInventory = $db->single();

                    if ($existingInventory) {
                        // Update existing inventory at this location
                        $db->query('
                            UPDATE inventory 
                            SET quantity = quantity + ?
                            WHERE product_id = ? AND location_id = ?
                        ');
                        $db->bind(1, $receivedQty);
                        $db->bind(2, $product['product_id']);
                        $db->bind(3, $locationId);
                        $db->execute();
                    } else {
                        // Create new inventory record at this location
                        $db->query('
                            INSERT INTO inventory (product_id, quantity, location_id)
                            VALUES (?, ?, ?)
                        ');
                        $db->bind(1, $product['product_id']);
                        $db->bind(2, $receivedQty);
                        $db->bind(3, $locationId);
                        $db->execute();
                    }
                } else {
                    // No location specified, use default inventory update
                    $db->query('
                        UPDATE inventory 
                        SET quantity = quantity + ?
                        WHERE product_id = ? AND location_id IS NULL
                    ');
                    $db->bind(1, $product['received_qty']);
                    $db->bind(2, $product['product_id']);
                    $db->execute();

                    // If no inventory record exists, create one without location
                    if ($db->rowCount() === 0) {
                        $db->query('
                            INSERT INTO inventory (product_id, quantity)
                            VALUES (?, ?)
                        ');
                        $db->bind(1, $product['product_id']);
                        $db->bind(2, $product['received_qty']);
                        $db->execute();
                    }
                }
            }
        }

        // Check if all items are received
        $db->query('
            SELECT 
                SUM(pi.quantity) as total_expected,
                COALESCE(SUM(ri.quantity_received), 0) as total_received
            FROM purchase_items pi
            LEFT JOIN (
                SELECT product_id, SUM(quantity_received) as quantity_received
                FROM receiving_items ri2
                JOIN receiving r2 ON ri2.receiving_id = r2.receiving_id
                WHERE r2.po_number = ?
                GROUP BY product_id
            ) ri ON pi.product_id = ri.product_id
            WHERE pi.purchase_id = ?
        ');
        $db->bind(1, $poNumber);
        $db->bind(2, $poId);
        $db->execute();
        $totals = $db->single();

        // Update PO status
        $newStatus = 'received';
        if ($totals && $totals->total_received < $totals->total_expected) {
            $newStatus = 'partially_received';
        }

        $db->query('
            UPDATE purchases 
            SET status = ?
            WHERE purchase_id = ?
        ');
        $db->bind(1, $newStatus);
        $db->bind(2, $poId);
        $db->execute();

        // Commit transaction
        $db->commit();

        // Log the activity
        error_log("Receiving completed for PO: $poNumber by user: " . ($_SESSION['user_id'] ?? 'Unknown'));

        echo json_encode([
            'success' => true,
            'message' => 'Receiving completed successfully',
            'data'    => [
                'receiving_id'   => $receivingId,
                'po_status'      => $newStatus,
                'total_expected' => $totals ? $totals->total_expected : 0,
                'total_received' => $totals ? $totals->total_received : 0
            ]
        ]);
        exit();

    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit();
}
?>