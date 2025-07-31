<?php
class SalesController extends Controller
{
    public $productModel;
    public $saleModel;
    public $customerModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->saleModel = $this->model('Sale');
        $this->productModel = $this->model('Product');
        $this->customerModel = $this->model('Customer');
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

    public function today()
    {
        // Get today's sales
        $sales = $this->saleModel->getTodaysSales();
        if (!$sales) {
            $sales = [];
            flash('sale_message', 'No sales found for today');
        }
        $data = [
            'sales' => $sales,
            'title' => "Today's Sales"
        ];
        $this->view('sales/today', $data);
    }

    public function details($id)
    {
        $sale = $this->saleModel->getSaleById($id);
        if (!$sale) {
            flash('sale_message', 'Sale not found', 'alert alert-danger');
            redirect('sales/list');
        }

        $saleItems = $this->saleModel->getSaleItemsBySaleId($id);

        $data = [
            'sale' => $sale,
            'saleItems' => $saleItems
        ];
        $this->view('sales/details', $data);
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
                // Load products and customers for the form
                $products = $this->productModel->getProducts();
                if (!$products) {
                    $products = [];
                }
                $customers = $this->customerModel->getCustomers();
                if (!$customers) {
                    $customers = [];
                }
                $data['products'] = $products;
                $data['customers'] = $customers;
                $this->view('sales/add', $data);
            }
        } else {
            $products = $this->productModel->getProducts();
            if (!$products) {
                $products = [];
                flash('sale_message', 'No products found');
            }
            $customers = $this->customerModel->getCustomers();
            if (!$customers) {
                $customers = [];
                flash('sale_message', 'No customers found');
            }
            $data = [
                'customer_id' => '',
                'total_amount' => '',
                'payment_mode' => '',
                'products' => $products,
                'customers' => $customers
            ];
            $this->view('sales/add', $data);
        }
    }
}
