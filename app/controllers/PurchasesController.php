<?php
class PurchasesController extends Controller
{
    public $productModel;
    public $purchaseModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->purchaseModel = $this->model('Purchase');
        $this->productModel = $this->model('Product');
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
}
