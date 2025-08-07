<?php
class PurchasesController extends Controller
{
    public $productModel;
    public $purchaseModel;
    public $purchaseOrderModel;
    public $supplierModel;
    public $barcodeModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->purchaseModel = $this->model('Purchase');
        $this->productModel = $this->model('Product');
        $this->purchaseOrderModel = $this->model('PurchaseOrder');
        $this->supplierModel = $this->model('Supplier');
        $this->barcodeModel = $this->model('Barcode');
    }

    public function index()
    {
        $purchases = $this->purchaseModel->getPurchases();
        if (!$purchases) {
            $purchases = [];
            flash('purchase_message', 'No purchases found');
        }

        // Get purchase summary statistics
        $summaryStats = $this->purchaseModel->getPurchaseSummaryStats();

        $data = [
            'purchases' => $purchases,
            'monthly_purchases' => $summaryStats['monthly_purchases'],
            'pending_orders' => $summaryStats['pending_orders'],
            'active_suppliers' => $summaryStats['active_suppliers'],
            'items_received' => $summaryStats['items_received']
        ];
        $this->view('purchases/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'supplier_id' => isset($_POST['supplier_id']) ? trim($_POST['supplier_id']) : '',
                'total_amount' => isset($_POST['total_amount']) ? trim($_POST['total_amount']) : '',
                'invoice_attachment' => '',
                'products' => isset($_POST['products']) && is_array($_POST['products']) ? $_POST['products'] : [],
                'supplier_id_err' => '',
                'total_amount_err' => '',
                'products_err' => ''
            ];

            // Validate supplier id
            if (empty($data['supplier_id'])) {
                $data['supplier_id_err'] = 'Please select a supplier';
            }
            // Validate total amount
            if (empty($data['total_amount'])) {
                $data['total_amount_err'] = 'Please enter total amount';
            } elseif (!is_numeric($data['total_amount'])) {
                $data['total_amount_err'] = 'Total amount must be a number';
            }
            // Validate products
            if (empty($data['products']) || !is_array($data['products']) || count($data['products']) == 0) {
                $data['products_err'] = 'Please add at least one product to the order';
            }

            if (empty($data['supplier_id_err']) && empty($data['total_amount_err']) && empty($data['products_err'])) {
                // Add data for the purchase
                $purchaseData = [
                    'supplier_id' => $data['supplier_id'],
                    'total_amount' => $data['total_amount'],
                    'invoice_attachment' => ''
                ];

                $purchase_id = $this->purchaseModel->addPurchase($purchaseData);
                if ($purchase_id) {
                    foreach ($data['products'] as $product) {
                        if (isset($product['id'], $product['quantity'], $product['price'])) {
                            $purchase_item_data = [
                                'purchase_id' => $purchase_id,
                                'product_id' => $product['id'],
                                'quantity' => $product['quantity'],
                                'unit_price' => $product['price']
                            ];
                            $this->purchaseModel->addPurchaseItem($purchase_item_data);
                        }
                    }
                    flash('purchase_message', 'Purchase Order Created Successfully');
                    redirect('purchases');
                } else {
                    die('Something went wrong while creating the purchase order');
                }
            } else {
                // Load products and suppliers for the form
                $products = $this->productModel->getProducts();
                if (!$products) {
                    $products = [];
                }
                $suppliers = $this->supplierModel->getSuppliers();
                if (!$suppliers) {
                    $suppliers = [];
                }
                $data['products'] = $products;
                $data['suppliers'] = $suppliers;
                $this->view('purchases/add', $data);
            }
        } else {
            $products = $this->productModel->getProducts();
            if (!$products) {
                $products = [];
                flash('purchase_message', 'No products found');
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
                'products' => $products,
                'suppliers' => $suppliers
            ];
            $this->view('purchases/add', $data);
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

        $data = [
            'title' => 'Purchase Details - #' . $id,
            'purchase' => $purchase,
            'purchase_items' => $purchaseItems
        ];

        $this->view('purchases/details', $data);
    }

    public function process_receive($id = null)
    {
        if (!$id || !is_numeric($id)) {
            flash('purchase_message', 'Invalid purchase ID', 'alert alert-danger');
            redirect('receiving/pending');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $receiveQties = $_POST['receive_qty'] ?? [];
            $bulkLocationId = $_POST['bulk_location_id'] ?? null;
            $markComplete = isset($_POST['mark_complete']);

            // Validate bulk location selection
            if (empty($bulkLocationId)) {
                flash('receive_message', 'Please select a bulk location for receiving items', 'alert alert-danger');
                redirect('receiving/process/' . $id);
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

            redirect('receiving/pending');
        } else {
            redirect('receiving/process/' . $id);
        }
    }

    public function receiveShipment($poId = null)
    {
        if (!$poId) {
            flash('receive_message', 'Purchase order ID is required', 'alert alert-danger');
            redirect('receiving/pending');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $result = $this->purchaseOrderModel->receivePurchaseOrder($poId, $_POST);

            if ($result) {
                flash('receive_message', 'Shipment received successfully', 'alert alert-success');
                redirect('receiving/pending');
            } else {
                flash('receive_message', 'Failed to receive shipment', 'alert alert-danger');
            }
        }

        // Get purchase order details for receiving
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderById($poId);
        $orderItems = $this->purchaseOrderModel->getPurchaseOrderItems($poId);

        if (!$purchaseOrder) {
            flash('receive_message', 'Purchase order not found', 'alert alert-danger');
            redirect('receiving/pending');
        }

        $data = [
            'title' => 'Receive Shipment - PO #' . $purchaseOrder->po_number,
            'purchase_order' => $purchaseOrder,
            'order_items' => $orderItems
        ];

        $this->view('purchases/receive_shipment', $data);
    }

    /**
     * Purchase order approval workflow
     */
    public function approvals()
    {
        $pendingApprovals = $this->purchaseOrderModel->getPurchaseOrdersRequiringApproval(1000);
        $overdueOrders = $this->purchaseOrderModel->getOverduePurchaseOrders(7);

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

        if ($this->purchaseOrderModel->updateStatus($poId, 'sent')) {
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

            if ($this->purchaseOrderModel->bulkUpdateStatus($orderIds, 'sent')) {
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
        $approvedCount = $this->purchaseOrderModel->autoApprovePurchaseOrders($threshold);

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
            redirect('receiving/pending');
            return;
        }

        // Get purchase details and items
        $purchase = $this->purchaseModel->getPurchaseById($purchase_id);
        if (!$purchase) {
            flash('purchase_message', 'Purchase not found', 'alert alert-danger');
            redirect('receiving/pending');
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
}
