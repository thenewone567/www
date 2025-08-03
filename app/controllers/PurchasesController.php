<?php
class PurchasesController extends Controller
{
    public $productModel;
    public $purchaseModel;
    public $purchaseOrderModel;
    public $supplierModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->purchaseModel = $this->model('Purchase');
        $this->productModel = $this->model('Product');
        $this->purchaseOrderModel = $this->model('PurchaseOrder');
        $this->supplierModel = $this->model('Supplier');
    }

    public function index()
    {
        $purchases = $this->purchaseModel->getPurchases();
        if (!$purchases) {
            $purchases = [];
            flash('purchase_message', 'No purchases found');
        }
        $data = [
            'purchases' => $purchases
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
                'total_amount_err' => ''
            ];

            // Validate supplier id
            if (empty($data['supplier_id'])) {
                $data['supplier_id_err'] = 'Please enter supplier id';
            }
            // Validate total amount
            if (empty($data['total_amount'])) {
                $data['total_amount_err'] = 'Please enter total amount';
            } elseif (!is_numeric($data['total_amount'])) {
                $data['total_amount_err'] = 'Total amount must be a number';
            }

            if (empty($data['supplier_id_err']) && empty($data['total_amount_err'])) {
                $purchase_id = $this->purchaseModel->addPurchase($data);
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
                    flash('purchase_message', 'Purchase Added');
                    redirect('purchases');
                } else {
                    die('Something went wrong');
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
                'total_amount' => '',
                'products' => $products,
                'suppliers' => $suppliers
            ];
            $this->view('purchases/add', $data);
        }
    }

    public function receive()
    {
        // Get pending purchase orders that can be received
        $pendingOrders = $this->purchaseOrderModel->getPurchaseOrders('sent');

        $data = [
            'title' => 'Receive Shipments',
            'purchase_orders' => $pendingOrders
        ];

        $this->view('purchases/receive', $data);
    }

    public function receiveShipment($poId = null)
    {
        if (!$poId) {
            flash('purchase_message', 'Purchase order ID is required', 'alert alert-danger');
            redirect('purchases/receive');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $result = $this->purchaseOrderModel->receivePurchaseOrder($poId, $_POST);

            if ($result) {
                flash('purchase_message', 'Shipment received successfully', 'alert alert-success');
                redirect('purchases/receive');
            } else {
                flash('purchase_message', 'Failed to receive shipment', 'alert alert-danger');
            }
        }

        // Get purchase order details for receiving
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderById($poId);
        $orderItems = $this->purchaseOrderModel->getPurchaseOrderItems($poId);

        if (!$purchaseOrder) {
            flash('purchase_message', 'Purchase order not found', 'alert alert-danger');
            redirect('purchases/receive');
        }

        $data = [
            'title' => 'Receive Shipment - PO #' . $purchaseOrder->po_number,
            'purchase_order' => $purchaseOrder,
            'order_items' => $orderItems
        ];

        $this->view('purchases/receive_shipment', $data);
    }

    public function received()
    {
        // Get received purchase orders
        $receivedOrders = $this->purchaseOrderModel->getPurchaseOrders('received');

        $data = [
            'title' => 'Received Shipments',
            'purchase_orders' => $receivedOrders
        ];

        $this->view('purchases/received', $data);
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
}
