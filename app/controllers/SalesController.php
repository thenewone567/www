<?php
class SalesController extends Controller
{
    public $productModel;
    public $saleModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->saleModel = $this->model('Sale');
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        // This now serves as the Sales Hub page
        $data = [
            'title' => 'Sales Management Hub'
        ];

        $this->view('sales/index', $data);
    }

    public function list()
    {
        // Original index functionality moved here
        $sales = $this->saleModel->getSales();
        if (!$sales) {
            $sales = [];
            flash('sale_message', 'No sales found');
        }
        $data = [
            'sales' => $sales
        ];
        $this->view('sales/list', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost();
            $data = [
                'customer_id' => isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '',
                'total_amount' => isset($_POST['total_amount']) ? trim($_POST['total_amount']) : '',
                'payment_mode' => isset($_POST['payment_mode']) ? trim($_POST['payment_mode']) : '',
                'products' => isset($_POST['products']) && is_array($_POST['products']) ? $_POST['products'] : [],
                'customer_id_err' => '',
                'total_amount_err' => ''
            ];

            // Validate customer id
            if (empty($data['customer_id'])) {
                $data['customer_id_err'] = 'Please enter customer id';
            }
            // Validate total amount
            if (empty($data['total_amount'])) {
                $data['total_amount_err'] = 'Please enter total amount';
            } elseif (!is_numeric($data['total_amount'])) {
                $data['total_amount_err'] = 'Total amount must be a number';
            }

            if (empty($data['customer_id_err']) && empty($data['total_amount_err'])) {
                $sale_id = $this->saleModel->addSale($data);
                if ($sale_id) {
                    foreach ($data['products'] as $product) {
                        if (isset($product['id'], $product['quantity'], $product['price'], $product['discount'])) {
                            $sale_item_data = [
                                'sale_id' => $sale_id,
                                'product_id' => $product['id'],
                                'quantity' => $product['quantity'],
                                'unit_price' => $product['price'],
                                'discount' => $product['discount']
                            ];
                            $this->saleModel->addSaleItem($sale_item_data);
                        }
                    }
                    flash('sale_message', 'Sale Added');
                    redirect('sales');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('sales/add', $data);
            }
        } else {
            $products = $this->productModel->getProducts();
            if (!$products) {
                $products = [];
                flash('sale_message', 'No products found');
            }
            $data = [
                'customer_id' => '',
                'total_amount' => '',
                'payment_mode' => '',
                'products' => $products
            ];
            $this->view('sales/add', $data);
        }
    }
}
