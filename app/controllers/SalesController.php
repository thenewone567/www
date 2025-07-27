<?php
class SalesController extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->saleModel = $this->model('Sale');
        $this->productModel = $this->model('Product');
    }

    public function index(){
        $sales = $this->saleModel->getSales();
        $data = [
            'sales' => $sales
        ];
        $this->view('sales/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'customer_id' => trim($_POST['customer_id']),
                'total_amount' => trim($_POST['total_amount']),
                'payment_mode' => trim($_POST['payment_mode']),
                'products' => $_POST['products'],
                'customer_id_err' => '',
                'total_amount_err' => ''
            ];

            // Validate customer id
            if(empty($data['customer_id'])){
                $data['customer_id_err'] = 'Please enter customer id';
            }
            // Validate total amount
            if(empty($data['total_amount'])){
                $data['total_amount_err'] = 'Please enter total amount';
            }

            if(empty($data['customer_id_err']) && empty($data['total_amount_err'])){
                $sale_id = $this->saleModel->addSale($data);
                if($sale_id){
                    foreach($data['products'] as $product){
                        $sale_item_data = [
                            'sale_id' => $sale_id,
                            'product_id' => $product['id'],
                            'quantity' => $product['quantity'],
                            'unit_price' => $product['price'],
                            'discount' => $product['discount']
                        ];
                        $this->saleModel->addSaleItem($sale_item_data);
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
