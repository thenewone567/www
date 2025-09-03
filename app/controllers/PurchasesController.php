<?php
class PurchasesController extends Controller
{
    public $productModel;
    public $purchaseModel;
    public $supplierModel;
    public $barcodeModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            // If this is an AJAX request, return JSON 401 instead of redirecting to login HTML
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Unauthenticated']);
                exit;
            }

            redirect('users/login');
        }
        $this->purchaseModel = $this->model('Purchase');
        $this->productModel = $this->model('Product');
        $this->supplierModel = $this->model('Supplier');
        $this->barcodeModel = $this->model('Barcode');

        // Initialize cart session manager for cart operations
        require_once APPROOT . DS . 'app' . DS . 'libraries' . DS . 'CartSessionManager.php';
    }

    public function index()
    {
        $purchases = $this->purchaseModel->getPurchases();
        if (!$purchases) {
            $purchases = [];
            flash('purchase_message', 'No purchases found');
        }

        // Get purchase summary statistics for backward compatibility
        $summaryStats = $this->purchaseModel->getPurchaseSummaryStats();

        // Get comprehensive purchase summary for KPI cards
        $purchaseSummary = $this->purchaseModel->getPurchaseSummary();

        $data = [
            'purchases' => $purchases,
            'orders' => $purchases, // Add for backward compatibility with view
            'summary' => $purchaseSummary, // Add comprehensive summary for KPI cards
            // Legacy stats for backward compatibility
            'monthly_purchases' => $summaryStats['monthly_purchases'],
            'pending_orders' => $summaryStats['pending_orders'],
            'active_suppliers' => $summaryStats['active_suppliers'],
            'items_received' => $summaryStats['items_received']
        ];
        $this->view('purchases/index', $data);
    }

    public function add()
    {
        $cartManager = CartSessionManager::getInstance();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $postedProducts = isset($_POST['products']) && is_array($_POST['products']) ? $_POST['products'] : [];
            $data = [
                'supplier_id' => isset($_POST['supplier_id']) ? trim($_POST['supplier_id']) : '', // optional if multi-supplier
                'products' => $postedProducts,
                'expected_date' => isset($_POST['expected_date']) ? trim($_POST['expected_date']) : date('Y-m-d', strtotime('+7 days')),
                'notes' => isset($_POST['notes']) ? trim($_POST['notes']) : '',
                'average_price_method' => isset($_POST['average_price_method']) ? 1 : 0,
                'total_amount' => '0.00', // will be recomputed
                'invoice_attachment' => '',
                'supplier_id_err' => '',
                'total_amount_err' => '',
                'products_err' => ''
            ];

            // Group items by supplier
            $supplierGroups = [];
            foreach ($postedProducts as $line) {
                $pid = $line['product_id'] ?? ($line['id'] ?? null);
                $qty = (int) ($line['quantity'] ?? $line['qty'] ?? 0);
                $price = (float) ($line['price'] ?? 0);
                $sid = $line['supplier_id'] ?? '';
                if (!$pid || $qty <= 0) {
                    continue;
                }
                if ($sid === '') {
                    $data['products_err'] = 'Each product must have a supplier.';
                    break;
                }
                if (!isset($supplierGroups[$sid])) {
                    $supplierGroups[$sid] = ['items' => [], 'total' => 0];
                }
                $supplierGroups[$sid]['items'][] = [
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'unit_price' => $price
                ];
                $supplierGroups[$sid]['total'] += $qty * $price;
            }

            $distinctSuppliers = count($supplierGroups);
            // Recompute overall total
            $overallTotal = 0;
            foreach ($supplierGroups as $g) {
                $overallTotal += $g['total'];
            }
            $data['total_amount'] = number_format($overallTotal, 2, '.', '');
            if ($overallTotal <= 0) {
                $data['total_amount_err'] = 'Cart total must be greater than zero';
            }
            if (empty($postedProducts)) {
                $data['products_err'] = 'Please add at least one product to the order';
            }

            // Single-supplier convenience: auto-fill supplier_id
            if ($distinctSuppliers === 1 && empty($data['supplier_id'])) {
                $data['supplier_id'] = array_key_first($supplierGroups);
            }
            // If user selected supplier but cart actually has multiple suppliers, ignore selection (split automatically)
            if ($distinctSuppliers > 1 && !empty($data['supplier_id'])) {
                // Not an error; clarify by clearing selection so downstream not enforcing single supplier
                $data['supplier_id'] = '';
            }

            if (empty($data['products_err']) && empty($data['total_amount_err']) && empty($data['supplier_id_err'])) {
                $created = 0;
                $errors = [];
                foreach ($supplierGroups as $sid => $group) {
                    $purchaseData = [
                        'supplier_id' => $sid,
                        'total_amount' => number_format($group['total'], 2, '.', ''),
                        'expected_date' => $data['expected_date'],
                        'notes' => $data['notes'],
                        'average_price_method' => $data['average_price_method'],
                        'created_by' => $_SESSION['user_id'] ?? 0
                    ];
                    $purchase_id = $this->purchaseModel->addPurchase($purchaseData);
                    if ($purchase_id) {
                        foreach ($group['items'] as $it) {
                            $this->purchaseModel->addPurchaseItem([
                                'purchase_id' => $purchase_id,
                                'product_id' => $it['product_id'],
                                'quantity' => $it['quantity'],
                                'unit_price' => $it['unit_price']
                            ]);
                        }
                        $created++;
                    } else {
                        $errors[] = "Failed to create order for supplier $sid";
                    }
                }
                if ($created > 0) {
                    $msg = $created === 1 ? 'Purchase Order Created Successfully' : "$created Purchase Orders Created Successfully";
                    if ($errors) {
                        $msg .= ' (with errors: ' . implode('; ', $errors) . ')';
                    }
                    flash('purchase_message', $msg, $errors ? 'alert alert-warning' : 'alert alert-success');
                    $cartManager->clearAllCartData();
                    redirect('purchases');
                } else {
                    $data['products_err'] = 'Failed to create any purchase orders.';
                }
            }

            // If errors, reload form
            if (!empty($data['products_err']) || !empty($data['total_amount_err']) || !empty($data['supplier_id_err'])) {
                // Use full product-supplier combinations with rankings instead of relying on primary supplier
                $products = $this->productModel->getProductsWithAllSuppliers();
                error_log('[PURCHASES_ADD] (POST reload) getProductsWithAllSuppliers() returned: ' . (is_array($products) ? count($products) : 0));
                if (!$products) {
                    // Fallback: return paginated products without requiring supplier links
                    $fallback = $this->productModel->getProductsPaginated(0, 100);
                    $products = $fallback ? array_map(function ($r) {
                        return (object) $r;
                    }, $fallback) : [];
                }
                $suppliers = $this->supplierModel->getSuppliers();
                if (!$suppliers) {
                    $suppliers = [];
                }
                $data['products_list'] = $products; // keep original products lines separate
                $data['products'] = $products; // expected by view for product grid
                $data['suppliers'] = $suppliers;
                $this->view('purchases/add', $data);
            }
        } else {
            // Load all product-supplier combinations (each supplier option is distinct)
            $products = $this->productModel->getProductsWithAllSuppliers();
            error_log('[PURCHASES_ADD] (GET) getProductsWithAllSuppliers() returned: ' . (is_array($products) ? count($products) : 0));
            if (!$products) {
                // Fallback to a permissive product list so the UI is usable
                $fallback = $this->productModel->getProductsPaginated(0, 100);
                $products = $fallback ? array_map(function ($r) {
                    return (object) $r;
                }, $fallback) : [];
                if (empty($products)) {
                    flash('purchase_message', 'No products found');
                } else {
                    // Informational flash when falling back
                    flash('purchase_message', 'Showing products without active supplier links (fallback).');
                }
            }
            $suppliers = $this->supplierModel->getSuppliers();
            if (!$suppliers) {
                $suppliers = [];
                flash('purchase_message', 'No suppliers found');
            }
            $data = [
                'supplier_id' => '',
                'supplier_id_err' => '',
                'total_amount' => '',
                'total_amount_err' => '',
                'products_err' => '', // Missing key added
                'expected_date' => date('Y-m-d', strtotime('+7 days')), // Missing key added
                'notes' => '', // Missing key added
                'average_price_method' => 0, // Default to unchecked
                'products' => $products,
                'suppliers' => $suppliers,
                'session_cart' => array_values($cartManager->getCart())
            ];
            $this->view('purchases/add', $data);
        }
    }

    /**
     * AJAX endpoint: return paginated product-supplier rows for purchases/add
     * GET params: page, perPage, search, supplier_id, category, priceMin, priceMax
     */
    public function productsForAdd()
    {
        // Only allow AJAX or local requests
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int) $_GET['perPage'] : 25;
        $filters = [];
        foreach (['search', 'supplier_id', 'category', 'priceMin', 'priceMax'] as $f) {
            if (isset($_GET[$f]) && $_GET[$f] !== '') {
                $filters[$f] = $_GET[$f];
            }
        }

        $res = $this->productModel->getProductsForAdd($page, $perPage, $filters);
        $rows = is_array($res) && isset($res['rows']) ? $res['rows'] : [];
        $total = is_array($res) && isset($res['total']) ? (int) $res['total'] : count($rows);

        $usedFallback = false;
        // If no rows found, fallback to a more permissive product list (include products without active suppliers)
        if ($total === 0) {
            // Use paginated product query (no supplier join) as a safe fallback so the UI can show products
            $offset = ($page - 1) * $perPage;
            $search = isset($filters['search']) ? $filters['search'] : '';
            $fallbackRows = $this->productModel->getProductsPaginated($offset, $perPage, $search);
            $fallbackTotal = $this->productModel->getTotalProductsCount($search);
            $rows = $fallbackRows ? array_map(function ($r) {
                return (object) $r;
            }, $fallbackRows) : [];
            $total = (int) $fallbackTotal;
            $usedFallback = true;
        }

        header('Content-Type: application/json');
        $out = ['page' => $page, 'perPage' => $perPage, 'count' => $total, 'rows' => $rows];
        if ($usedFallback)
            $out['fallback'] = true;
        echo json_encode($out);
        exit;
    }

    /**
     * AJAX endpoint: return suppliers for a single product
     * GET param: product_id
     */
    public function productSuppliers()
    {
        $productId = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
        if ($productId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid product_id']);
            exit;
        }

        $suppliers = $this->productModel->getSuppliersForProduct($productId);
        header('Content-Type: application/json');
        echo json_encode(['product_id' => $productId, 'suppliers' => $suppliers]);
        exit;
    }

    // API: add item to session cart (AJAX)
    public function apiAddCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die;
        }
        $cartManager = CartSessionManager::getInstance();
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $pid = $payload['product_id'] ?? null;
        $qty = max(1, (int) ($payload['quantity'] ?? 1));
        $price = (float) ($payload['price'] ?? 0);
        $sid = $payload['supplier_id'] ?? null;
        if (!$pid) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product_id']);
            return;
        }
        $cartManager->addToCart($pid, $qty, $price, $sid);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => $cartManager->getCartItemCount(), 'total' => $cartManager->getCartTotal()]);
    }

    // API: remove item
    public function apiRemoveCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die;
        }
        $cartManager = CartSessionManager::getInstance();
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $pid = $payload['product_id'] ?? null;
        $sid = $payload['supplier_id'] ?? null;
        if (!$pid) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product_id']);
            return;
        }
        $cartManager->removeFromCart($pid, $sid);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => $cartManager->getCartItemCount(), 'total' => $cartManager->getCartTotal()]);
    }

    // API: clear cart
    public function apiClearCart()
    {
        $cartManager = CartSessionManager::getInstance();
        $cartManager->clearAllCartData();
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    // API: calculate weighted average price
    public function apiCalculateAveragePrice()
    {
        header('Content-Type: application/json');

        // Add debugging
        error_log('API Calculate Average Price called');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        error_log('Input received: ' . json_encode($input));

        $productId = $input['product_id'] ?? null;
        $newQuantity = $input['quantity'] ?? 0;
        $newPrice = $input['price'] ?? 0;

        // Allow manual override for testing
        $manualCurrentStock = $input['manual_current_stock'] ?? null;
        $manualCurrentPrice = $input['manual_current_price'] ?? null;

        if (!$productId || !$newQuantity || !$newPrice) {
            error_log('Missing parameters - productId: ' . $productId . ', quantity: ' . $newQuantity . ', price: ' . $newPrice);
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            return;
        }

        try {
            // If manual parameters provided, use direct calculation
            if ($manualCurrentStock !== null && $manualCurrentPrice !== null) {
                $currentStock = (int) $manualCurrentStock;
                $currentPrice = (float) $manualCurrentPrice;

                if ($currentStock <= 0) {
                    $result = [
                        'new_average_price' => $newPrice,
                        'calculation' => [
                            'current_stock' => 0,
                            'current_avg_price' => 0,
                            'current_total_value' => 0,
                            'new_quantity' => $newQuantity,
                            'new_price' => $newPrice,
                            'new_total_value' => $newQuantity * $newPrice,
                            'total_stock_after' => $newQuantity,
                            'total_value_after' => $newQuantity * $newPrice,
                            'note' => 'Manual override used'
                        ]
                    ];
                } else {
                    $currentTotalValue = $currentStock * $currentPrice;
                    $newTotalValue = $newQuantity * $newPrice;
                    $totalStockAfter = $currentStock + $newQuantity;
                    $totalValueAfter = $currentTotalValue + $newTotalValue;
                    $newAveragePrice = $totalValueAfter / $totalStockAfter;

                    $result = [
                        'new_average_price' => round($newAveragePrice, 2),
                        'calculation' => [
                            'current_stock' => $currentStock,
                            'current_avg_price' => round($currentPrice, 2),
                            'current_total_value' => round($currentTotalValue, 2),
                            'new_quantity' => $newQuantity,
                            'new_price' => $newPrice,
                            'new_total_value' => round($newTotalValue, 2),
                            'total_stock_after' => $totalStockAfter,
                            'total_value_after' => round($totalValueAfter, 2),
                            'note' => 'Manual override used'
                        ]
                    ];
                }
            } else {
                // Use database calculation
                $result = $this->productModel->calculateWeightedAveragePrice($productId, $newQuantity, $newPrice);
            }

            $inventorySummary = $this->productModel->getCurrentInventorySummary($productId);

            error_log('Calculation result: ' . json_encode($result));

            echo json_encode([
                'success' => true,
                'average_price' => $result['new_average_price'],
                'calculation' => $result['calculation'],
                'inventory_summary' => $inventorySummary
            ]);

        } catch (Exception $e) {
            error_log('Error in calculation: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error calculating average price: ' . $e->getMessage()]);
        }
    }    /**
         * Clear cart session data
         */
    public function clearCart()
    {
        $cartManager = CartSessionManager::getInstance();
        $cartManager->clearAllCartData();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Cart cleared successfully']);
            exit;
        } else {
            flash('purchase_message', 'Cart cleared successfully');
            redirect('purchases/add');
        }
    }

    public function details($id = null)
    {
        if (!$id || !is_numeric($id)) {
            flash('purchase_message', 'Invalid purchase ID', 'alert alert-danger');
            redirect('purchases');
        }

        // Get purchase details
        $purchase = $this->purchaseModel->getPurchaseById($id);
        if (!$purchase) {
            flash('purchase_message', 'Purchase not found', 'alert alert-danger');
            redirect('purchases');
        }

        // Get purchase items
        $purchaseItems = $this->purchaseModel->getPurchaseItems($id);

        // Get receiving status if receiving table exists
        $receivingStatus = null;
        try {
            $db = new Database();
            $db->query("SELECT status, received_date, notes FROM receiving WHERE purchase_id = :purchase_id ORDER BY created_at DESC LIMIT 1");
            $db->bind(':purchase_id', $id);
            $db->execute();
            $receivingStatus = $db->single();
        } catch (Exception $e) {
            // Receiving table might not exist or no receiving record
            error_log("Could not fetch receiving status: " . $e->getMessage());
        }

        $data = [
            'title' => 'Purchase Details - #' . $id,
            'purchase' => $purchase,
            'purchase_items' => $purchaseItems,
            'receiving_status' => $receivingStatus
        ];

        $this->view('purchases/details', $data);
    }

    public function process_receive($id = null)
    {
        if (!$id || !is_numeric($id)) {
            flash('purchase_message', 'Invalid purchase ID', 'alert alert-danger');
            redirect('inventory/receiving');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $receiveQties = $_POST['receive_qty'] ?? [];
            $bulkLocationId = $_POST['bulk_location_id'] ?? null;
            $markComplete = isset($_POST['mark_complete']);

            // Validate bulk location selection
            if (empty($bulkLocationId)) {
                flash('receive_message', 'Please select a bulk location for receiving items', 'alert alert-danger');
                redirect('inventory/receiving');
                return;
            }

            $totalReceived = 0;
            $receivedItems = [];

            // Process each item with bulk location assignment
            foreach ($receiveQties as $itemId => $receiveQty) {
                $receiveQty = intval($receiveQty);
                if ($receiveQty > 0) {
                    // Update received quantity for this purchase item
                    $result = $this->purchaseModel->updateReceivedQuantity($itemId, $receiveQty);
                    if ($result) {
                        // Update product Inventory with bulk location tracking
                        $InventoryResult = $this->purchaseModel->updateProductInventoryWithLocation($itemId, $receiveQty, $bulkLocationId);
                        if ($InventoryResult) {
                            $totalReceived += $receiveQty;
                            $receivedItems[] = $itemId;
                        }
                    }
                }
            }

            if ($totalReceived > 0) {
                // Update purchase status if marked complete
                if ($markComplete) {
                    $this->purchaseModel->updatePurchaseStatus($id, 'received');
                } else {
                    $this->purchaseModel->updatePurchaseStatus($id, 'partially_received');
                }

                // Get bulk location name for success message
                $bulkLocation = $this->purchaseModel->getBulkLocationById($bulkLocationId);
                $locationName = $bulkLocation ? $bulkLocation->location_name : "Location #$bulkLocationId";

                flash('receive_message', "Successfully received $totalReceived items and assigned to bulk location $locationName", 'alert alert-success');
            } else {
                flash('receive_message', 'No items were received', 'alert alert-warning');
            }

            redirect('inventory/receiving');
        } else {
            redirect('inventory/receiving');
        }
    }

    /**
     * Mark purchase as received and staged at dock
     */
    public function markReceived($id = null)
    {
        error_log("markReceived method called with ID: $id");

        if (!$id) {
            error_log("No ID provided to markReceived");
            flash('purchase_message', 'Purchase ID is required', 'alert alert-danger');
            redirect('purchases');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            error_log("POST request detected, marking purchase $id as received");

            // Mark as received and staged
            $result = $this->purchaseModel->markAsReceivedAndStaged($id);

            error_log("markAsReceivedAndStaged returned: " . ($result ? 'true' : 'false'));

            if ($result) {
                // Set success message
                flash('purchase_message', 'Purchase order marked as received and staged at dock. Email confirmation sent and receipt generated.', 'alert alert-success');

                // Set flag to show receipt popup
                $_SESSION['show_receipt_popup'] = true;
                $_SESSION['redirected_from_received'] = true;

                // Redirect to receiving page as requested
                error_log("Redirecting to inventory/receiving page");
                redirect('inventory/receiving');
            } else {
                error_log("Failed to mark purchase as received");
                flash('purchase_message', 'Failed to update purchase status', 'alert alert-danger');
                redirect('purchases/view/' . $id);
            }
        } else {
            // Show confirmation page
            $purchase = $this->purchaseModel->getPurchaseById($id);

            if (!$purchase) {
                flash('purchase_message', 'Purchase not found', 'alert alert-danger');
                redirect('purchases');
            }

            $data = [
                'title' => 'Confirm Receipt - PO #' . $purchase->po_number,
                'purchase' => $purchase
            ];

            $this->view('purchases/confirm_received', $data);
        }
    }

    /**
     * Display receipt in popup/new window
     */
    public function viewReceipt($identifier = null)
    {
        if (!$identifier && isset($_SESSION['show_receipt'])) {
            // Load from session
            $receiptData = $_SESSION['show_receipt'];
            require_once APPROOT . '/app/helpers/ReceiptHelper.php';
            ReceiptHelper::displayReceipt($receiptData['html']);
            return;
        }

        if ($identifier) {
            $purchase = null;
            $items = null;

            // Try to get purchase by ID first (if numeric), then by PO number
            if (is_numeric($identifier)) {
                // It's a purchase ID
                $purchase = $this->purchaseModel->getPurchaseById($identifier);
            } else {
                // It's a PO number
                $purchase = $this->purchaseModel->getPurchaseByPONumber($identifier);
            }

            if ($purchase) {
                $items = $this->purchaseModel->getPurchaseItems($purchase->purchase_id);

                require_once APPROOT . '/app/helpers/ReceiptHelper.php';
                // Ensure the receipt shows the current user's profile name when regenerating the receipt
                $purchaseArr = (array) $purchase;
                if (empty($purchaseArr['received_by'])) {
                    // Prefer explicit session full name
                    $displayName = $_SESSION['user_full_name'] ?? null;

                    // If not present, try to look it up from users table using session user_id
                    if (empty($displayName) && !empty($_SESSION['user_id'])) {
                        try {
                            $db = new Database();
                            // Users table stores 'full_name' — select that or fall back to username
                            $db->query('SELECT COALESCE(full_name, username) as full_name FROM users WHERE user_id = ? LIMIT 1');
                            $db->bind(1, $_SESSION['user_id']);
                            $db->execute();
                            $row = $db->single();
                            if ($row && !empty($row->full_name)) {
                                $displayName = $row->full_name;
                            }
                        } catch (Exception $e) {
                            error_log('Failed to fetch user full name: ' . $e->getMessage());
                        }
                    }

                    // Fallback to username if still not present
                    if (empty($displayName)) {
                        $displayName = $_SESSION['username'] ?? null;
                    }

                    $purchaseArr['received_by'] = $displayName;
                }
                $receiptHtml = ReceiptHelper::generateReceivingReceipt($purchaseArr, $items);
                ReceiptHelper::displayReceipt($receiptHtml);
                return;
            }
        }

        // If we get here, show error
        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 50px;'>";
        echo "<h2 style='color: #dc3545;'>Receipt Not Found</h2>";
        echo "<p>Could not find purchase order with identifier: " . htmlspecialchars($identifier ?? 'N/A') . "</p>";
        echo "<p>Please check the purchase order number or ID and try again.</p>";
        echo "<button onclick='window.close()' style='background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;'>Close Window</button>";
        echo "</div>";
        echo "<script>setTimeout(function(){ window.close(); }, 5000);</script>";
    }

    /**
     * Generate or download a PDF version of the receiving receipt for a PO
     */
    public function downloadReceiptPdf($identifier = null)
    {
        if (!$identifier) {
            flash('purchase_message', 'Missing purchase identifier', 'alert alert-danger');
            redirect('purchases');
        }

        // Resolve purchase by id or po number
        if (is_numeric($identifier)) {
            $purchase = $this->purchaseModel->getPurchaseById($identifier);
        } else {
            $purchase = $this->purchaseModel->getPurchaseByPONumber($identifier);
        }

        if (!$purchase) {
            flash('purchase_message', 'Purchase not found', 'alert alert-danger');
            redirect('purchases');
        }

        $items = $this->purchaseModel->getPurchaseItems($purchase->purchase_id);
        require_once APPROOT . '/app/helpers/ReceiptHelper.php';

        // Ensure display name is available for the receipt
        $purchaseArr = (array) $purchase;
        if (empty($purchaseArr['received_by'])) {
            $purchaseArr['received_by'] = $_SESSION['display_name'] ?? $_SESSION['user_full_name'] ?? $_SESSION['username'] ?? null;
        }

        $receiptHtml = ReceiptHelper::generateReceivingReceipt($purchaseArr, $items);

        // Attempt to save as PDF
        $pdfPath = ReceiptHelper::saveReceiptPdf($receiptHtml, $purchase->po_number ?? 'po_' . $purchase->purchase_id);

        if ($pdfPath && file_exists($pdfPath)) {
            // Send headers for download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($pdfPath) . '"');
            header('Content-Length: ' . filesize($pdfPath));
            readfile($pdfPath);
            exit();
        }

        // If PDF generation not available, show HTML receipt and instruct user to save/print to PDF manually
        ReceiptHelper::displayReceipt($receiptHtml);
    }

    /**
     * Trigger sending of the receiving receipt via email (supplier/internal)
     * Expects POST with optional 'to' param to override recipient
     */
    public function emailReceipt($identifier = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!$identifier) {
            echo json_encode(['success' => false, 'message' => 'Missing purchase identifier']);
            exit;
        }

        // Resolve purchase
        if (is_numeric($identifier)) {
            $purchase = $this->purchaseModel->getPurchaseById($identifier);
        } else {
            $purchase = $this->purchaseModel->getPurchaseByPONumber($identifier);
        }

        if (!$purchase) {
            echo json_encode(['success' => false, 'message' => 'Purchase not found']);
            exit;
        }

        $items = $this->purchaseModel->getPurchaseItems($purchase->purchase_id);
        require_once APPROOT . '/app/helpers/ReceiptHelper.php';
        require_once APPROOT . '/app/helpers/EmailHelper.php';

        $purchaseArr = (array) $purchase;
        if (empty($purchaseArr['received_by'])) {
            $purchaseArr['received_by'] = $_SESSION['display_name'] ?? $_SESSION['user_full_name'] ?? $_SESSION['username'] ?? null;
        }

        $receiptHtml = ReceiptHelper::generateReceivingReceipt($purchaseArr, $items);
        $pdfPath = ReceiptHelper::saveReceiptPdf($receiptHtml, $purchase->po_number ?? 'po_' . $purchase->purchase_id);

        $attachments = [];
        if ($pdfPath && file_exists($pdfPath)) {
            $attachments[] = $pdfPath;
        }

        // Determine recipients - prefer supplier email from purchase data
        $supplierEmail = $purchase->supplier_email ?? null;
        $internalEmail = $_POST['internal_email'] ?? null; // optional override
        // If POST supplied 'to', send only to that address
        $overrideTo = $_POST['to'] ?? null;

        $sent = false;
        if ($overrideTo && filter_var($overrideTo, FILTER_VALIDATE_EMAIL)) {
            $sent = EmailHelper::sendRawEmail($overrideTo, 'Receiving Receipt - PO ' . ($purchase->po_number ?? ''), ReceiptHelper::generateReceivingReceipt($purchaseArr, $items), $attachments);
        } else {
            $sent = EmailHelper::sendPurchaseReceivedConfirmation($purchaseArr, $supplierEmail, $internalEmail, $attachments);
        }

        if ($sent) {
            echo json_encode(['success' => true, 'message' => 'Email sent']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email']);
        }
        exit;
    }

    /**
     * Purchase order approval workflow
     */
    public function approvals()
    {
        $pendingApprovals = $this->purchaseModel->getPurchaseOrdersRequiringApproval(1000);
        $overdueOrders = $this->purchaseModel->getOverduePurchaseOrders(7);

        $data = [
            'title' => 'Purchase Order Approvals',
            'pending_approvals' => $pendingApprovals,
            'overdue_orders' => $overdueOrders
        ];

        $this->view('purchases/approvals', $data);
    }

    /**
     * AJAX handler for adding a purchase order
     * Expects POST: supplier_id, cart (array of {id, qty, price})
     * Returns JSON: {success:bool, message:string}
     */
    public function ajax_add()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }
        $supplier_id = $_POST['supplier_id'] ?? null;
        $cart = isset($_POST['cart']) ? json_decode($_POST['cart'], true) : null;
        if (!$supplier_id || !$cart || !is_array($cart) || count($cart) === 0) {
            echo json_encode(['success' => false, 'message' => 'Missing supplier or cart data.']);
            exit;
        }
        // Validate supplier exists
        $supplier = $this->supplierModel->getSupplierById($supplier_id);
        if (!$supplier) {
            echo json_encode(['success' => false, 'message' => 'Invalid supplier.']);
            exit;
        }
        // Validate products and calculate total
        $total = 0;
        $items = [];
        foreach ($cart as $item) {
            $product = $this->productModel->getProductById($item['id']);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Invalid product in cart.']);
                exit;
            }
            $qty = (int) $item['qty'];
            $price = (float) $item['price'];
            if ($qty < 1 || $price < 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid quantity or price.']);
                exit;
            }
            $total += $qty * $price;
            $items[] = [
                'product_id' => $product->product_id,
                'qty' => $qty,
                'price' => $price
            ];
        }
        // Insert purchase order
        $purchase_id = $this->purchaseModel->addPurchase([
            'supplier_id' => $supplier_id,
            'total_amount' => $total,
            'created_by' => $_SESSION['user_id'] ?? 0
        ]);
        if (!$purchase_id) {
            echo json_encode(['success' => false, 'message' => 'Failed to create purchase order.']);
            exit;
        }
        // Insert purchase items
        foreach ($items as $item) {
            $this->purchaseModel->addPurchaseItem([
                'purchase_id' => $purchase_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['qty'],
                'unit_price' => $item['price']
            ]);
        }
        echo json_encode(['success' => true, 'message' => 'Purchase order created successfully.']);
        exit;
    }
    /**
     * Approve purchase order
     */
    public function approve($poId = null)
    {
        if (!$poId) {
            flash('purchase_message', 'Purchase order ID is required', 'alert alert-danger');
            redirect('purchases/approvals');
        }

        if ($this->purchaseModel->updateStatus($poId, 'sent')) {
            flash('purchase_message', 'Purchase order approved successfully', 'alert alert-success');
        } else {
            flash('purchase_message', 'Failed to approve purchase order', 'alert alert-danger');
        }

        redirect('purchases/approvals');
    }

    /**
     * Bulk approve purchase orders
     */
    public function bulkApprove()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $orderIds = $_POST['order_ids'] ?? [];

            if (empty($orderIds)) {
                flash('purchase_message', 'No orders selected', 'alert alert-warning');
                redirect('purchases/approvals');
            }

            if ($this->purchaseModel->bulkUpdateStatus($orderIds, 'sent')) {
                flash('purchase_message', count($orderIds) . ' purchase orders approved', 'alert alert-success');
            } else {
                flash('purchase_message', 'Failed to approve purchase orders', 'alert alert-danger');
            }
        }

        redirect('purchases/approvals');
    }

    /**
     * Auto-approve low value orders
     */
    public function autoApprove()
    {
        $threshold = 1000; // Configure this as needed
        $approvedCount = $this->purchaseModel->autoApprovePurchaseOrders($threshold);

        if ($approvedCount > 0) {
            flash('purchase_message', "$approvedCount purchase orders auto-approved", 'alert alert-success');
        } else {
            flash('purchase_message', 'No orders qualified for auto-approval', 'alert alert-info');
        }

        redirect('purchases/approvals');
    }

    /**
     * Print barcodes for received items
     */
    public function print_barcodes($purchase_id = null)
    {
        if (!$purchase_id || !is_numeric($purchase_id)) {
            flash('purchase_message', 'Invalid purchase ID', 'alert alert-danger');
            redirect('inventory/receiving');
            return;
        }

        // Get purchase details and items
        $purchase = $this->purchaseModel->getPurchaseById($purchase_id);
        if (!$purchase) {
            flash('purchase_message', 'Purchase not found', 'alert alert-danger');
            redirect('inventory/receiving');
            return;
        }

        $purchaseItems = $this->purchaseModel->getPurchaseItems($purchase_id);
        $barcodeData = [];

        foreach ($purchaseItems as $item) {
            // Generate or get existing barcode for the product
            $existingBarcode = $this->barcodeModel->getBarcodesForProduct($item->product_id);

            if (!$existingBarcode) {
                // Generate new barcode for product
                $barcodeValue = $this->barcodeModel->generateBarcodeForProduct($item->product_id);
                if ($barcodeValue) {
                    $existingBarcode = [
                        'barcode_value' => $barcodeValue,
                        'type' => 'CODE128'
                    ];
                }
            } else {
                $existingBarcode = $existingBarcode[0]; // Take first barcode
            }

            if ($existingBarcode) {
                $barcodeData[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'sku' => $item->sku ?? '',
                    'barcode_value' => $existingBarcode['barcode_value'],
                    'barcode_type' => $existingBarcode['type'] ?? 'CODE128',
                    'quantity_received' => $item->quantity_received ?? $item->quantity,
                    'location' => $item->location_name ?? 'Main Warehouse'
                ];
            }
        }

        $data = [
            'title' => 'Print Barcodes - Purchase #' . $purchase_id,
            'purchase' => $purchase,
            'barcodes' => $barcodeData
        ];

        $this->view('purchases/print_barcodes', $data);
    }

    /**
     * Generate barcode image for printing
     */
    public function generate_barcode_image()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barcode = $_POST['barcode'] ?? '';
            $type = $_POST['type'] ?? 'CODE128';

            if (empty($barcode)) {
                header('HTTP/1.1 400 Bad Request');
                echo 'Barcode value required';
                exit;
            }

            try {
                $barcodeImage = $this->barcodeModel->generateBarcodeImage($barcode, $type);

                header('Content-Type: image/png');
                header('Content-Disposition: inline; filename="barcode_' . $barcode . '.png"');
                echo $barcodeImage;
                exit;
            } catch (Exception $e) {
                header('HTTP/1.1 500 Internal Server Error');
                echo 'Error generating barcode: ' . $e->getMessage();
                exit;
            }
        }
    }

    /**
     * Bulk generate barcodes for products without barcodes
     */
    public function generate_missing_barcodes($purchase_id = null)
    {
        if (!$purchase_id || !is_numeric($purchase_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid purchase ID']);
            exit;
        }

        $purchaseItems = $this->purchaseModel->getPurchaseItems($purchase_id);
        $generatedCount = 0;
        $errors = [];

        foreach ($purchaseItems as $item) {
            $existingBarcode = $this->barcodeModel->getBarcodesForProduct($item->product_id);

            if (!$existingBarcode) {
                $barcodeValue = $this->barcodeModel->generateBarcodeForProduct($item->product_id);
                if ($barcodeValue) {
                    $generatedCount++;
                } else {
                    $errors[] = "Failed to generate barcode for " . $item->product_name;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'generated' => $generatedCount,
            'errors' => $errors,
            'message' => "$generatedCount barcodes generated successfully"
        ]);
        exit;
    }

    /**
     * Search purchase orders for receiving (AJAX endpoint)
     */
    public function searchForReceiving()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $searchTerm = trim($_POST['search'] ?? '');

            if (strlen($searchTerm) < 2) {
                echo json_encode([]);
                exit;
            }

            try {
                // Search for purchase orders that can be received
                $results = $this->purchaseModel->searchForReceiving($searchTerm);
                echo json_encode($results ?? []);
            } catch (Exception $e) {
                error_log("Error in searchForReceiving: " . $e->getMessage());
                echo json_encode([]);
            }
        } else {
            echo json_encode([]);
        }
        exit;
    }

    public function history()
    {
        $purchases = $this->purchaseModel->getHistory();
        if (!$purchases) {
            $purchases = [];
            flash('purchase_message', 'No purchase history found', 'alert alert-info');
        }

        $data = [
            'purchases' => $purchases,
            'title' => 'Purchase Order History'
        ];
        $this->view('purchases/history', $data);
    }

    /**
     * Edit purchase order
     */
    public function edit($id = null)
    {
        if (!$id || !is_numeric($id)) {
            flash('purchase_message', 'Invalid purchase ID', 'alert alert-danger');
            redirect('purchases');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'status' => trim($_POST['status'] ?? ''),
                'tracking_number' => trim($_POST['tracking_number'] ?? ''),
                'expected_date' => trim($_POST['expected_date'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'cancellation_reason' => trim($_POST['cancellation_reason'] ?? ''),
                'cancelled_action' => trim($_POST['cancelled_action'] ?? ''),
                'custom_reason' => trim($_POST['custom_reason'] ?? '')
            ];

            // Handle cancellation fields
            if ($data['status'] === 'cancelled') {
                if (!empty($data['cancellation_reason'])) {
                    $data['cancelled_by'] = $_SESSION['user_id'] ?? null;
                    $data['cancelled_at'] = date('Y-m-d H:i:s');

                    if ($data['cancellation_reason'] === 'other' && !empty($data['custom_reason'])) {
                        $data['cancellation_reason'] = $data['custom_reason'];
                    }
                }
            }

            // Update purchase order
            $result = $this->purchaseModel->updatePurchase($id, $data);

            if ($result) {
                // Handle tracking number update with automatic status change
                if (!empty($data['tracking_number'])) {
                    $this->purchaseModel->updateTracking($id, $data['tracking_number']);
                }

                flash('purchase_message', 'Purchase order updated successfully', 'alert alert-success');
                redirect('purchases');
            } else {
                flash('purchase_message', 'Failed to update purchase order', 'alert alert-danger');
            }
        }

        // GET request - show edit form
        $purchase = $this->purchaseModel->getPurchaseById($id);
        if (!$purchase) {
            flash('purchase_message', 'Purchase not found', 'alert alert-danger');
            redirect('purchases');
        }

        $data = [
            'title' => 'Edit Purchase Order',
            'order' => $purchase
        ];

        $this->view('purchases/edit', $data);
    }

    /**
     * AJAX method to update tracking number only
     */
    public function updateTrackingAjax()
    {
        // Set content type header
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = $_POST['purchase_id'] ?? null;
        $trackingNumber = trim($_POST['tracking_number'] ?? '');

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid purchase ID']);
            exit;
        }

        if (empty($trackingNumber)) {
            echo json_encode(['success' => false, 'message' => 'Tracking number is required']);
            exit;
        }

        $result = $this->purchaseModel->updateTracking($id, $trackingNumber);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Tracking number updated successfully',
                'status' => 'in_transit'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update tracking number']);
        }
        exit;
    }

    /**
     * AJAX method to soft delete a purchase order
     */
    public function softDeleteAjax()
    {
        // Set content type header
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = $_POST['purchase_id'] ?? null;
        $reason = trim($_POST['reason'] ?? '');

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid purchase ID']);
            exit;
        }

        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Deletion reason is required']);
            exit;
        }

        $result = $this->purchaseModel->softDeletePurchase($id, $reason);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Purchase order deleted successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete purchase order. Only pending orders can be deleted.']);
        }
        exit;
    }

    /**
     * AJAX method to cancel a purchase order with email notification
     */
    public function cancelPurchaseAjax()
    {
        // Set content type header first
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        try {
            $id = $_POST['purchase_id'] ?? null;
            $reason = trim($_POST['reason'] ?? '');

            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'Invalid purchase ID']);
                exit;
            }

            if (empty($reason)) {
                echo json_encode(['success' => false, 'message' => 'Cancellation reason is required']);
                exit;
            }

            $result = $this->purchaseModel->cancelPurchaseOrder($id, $reason);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Purchase order cancelled successfully. Cancellation notifications have been sent.'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to cancel purchase order. Only pending orders can be cancelled.']);
            }

        } catch (Exception $e) {
            error_log("Error in cancelPurchaseAjax: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Server error occurred while cancelling purchase order.'
            ]);
        }
        exit;
    }
}
