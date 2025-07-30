<?php
class PurchasesController extends Controller
{
    public $productModel;
    public $purchaseModel;
    public $purchaseOrderModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->purchaseModel = $this->model('Purchase');
        $this->productModel = $this->model('Product');
        $this->purchaseOrderModel = $this->model('PurchaseOrder');
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
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
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
                $this->view('purchases/add', $data);
            }
        } else {
            $products = $this->productModel->getProducts();
            if (!$products) {
                $products = [];
                flash('purchase_message', 'No products found');
            }
            $data = [
                'supplier_id' => '',
                'total_amount' => '',
                'products' => $products
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
}
