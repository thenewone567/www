<?php
class ProductsController extends Controller
{
    public $productModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        $products = $this->productModel->getProducts();
        if (!$products) {
            $products = [];
            flash('product_message', 'No products found');
        }
        $data = [
            'products' => $products
        ];
        $this->view('products/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'product_name' => isset($_POST['product_name']) ? trim($_POST['product_name']) : '',
                'sku' => isset($_POST['sku']) ? trim($_POST['sku']) : '',
                'category_id' => isset($_POST['category_id']) ? trim($_POST['category_id']) : '',
                'brand_id' => isset($_POST['brand_id']) ? trim($_POST['brand_id']) : '',
                'unit_id' => isset($_POST['unit_id']) ? trim($_POST['unit_id']) : '',
                'min_stock_level' => isset($_POST['min_stock_level']) ? trim($_POST['min_stock_level']) : '',
                'max_stock_level' => isset($_POST['max_stock_level']) ? trim($_POST['max_stock_level']) : '',
                'reorder_level' => isset($_POST['reorder_level']) ? trim($_POST['reorder_level']) : '',
                'image_path' => '',
                'product_name_err' => '',
                'sku_err' => ''
            ];

            // Validate product name
            if (empty($data['product_name'])) {
                $data['product_name_err'] = 'Please enter product name';
            }
            // Validate sku
            if (empty($data['sku'])) {
                $data['sku_err'] = 'Please enter sku';
            }

            if (empty($data['product_name_err']) && empty($data['sku_err'])) {
                if ($this->productModel->addProduct($data)) {
                    flash('product_message', 'Product Added');
                    redirect('products');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('products/add', $data);
            }
        } else {
            $data = [
                'product_name' => '',
                'sku' => '',
                'category_id' => '',
                'brand_id' => '',
                'unit_id' => '',
                'min_stock_level' => '',
                'max_stock_level' => '',
                'reorder_level' => '',
                'image_path' => '',
                'product_name_err' => '',
                'sku_err' => ''
            ];
            $this->view('products/add', $data);
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'product_name' => isset($_POST['product_name']) ? trim($_POST['product_name']) : '',
                'sku' => isset($_POST['sku']) ? trim($_POST['sku']) : '',
                'category_id' => isset($_POST['category_id']) ? trim($_POST['category_id']) : '',
                'brand_id' => isset($_POST['brand_id']) ? trim($_POST['brand_id']) : '',
                'unit_id' => isset($_POST['unit_id']) ? trim($_POST['unit_id']) : '',
                'min_stock_level' => isset($_POST['min_stock_level']) ? trim($_POST['min_stock_level']) : '',
                'max_stock_level' => isset($_POST['max_stock_level']) ? trim($_POST['max_stock_level']) : '',
                'reorder_level' => isset($_POST['reorder_level']) ? trim($_POST['reorder_level']) : '',
                'image_path' => '',
                'product_name_err' => '',
                'sku_err' => ''
            ];

            // Validate product name
            if (empty($data['product_name'])) {
                $data['product_name_err'] = 'Please enter product name';
            }
            // Validate sku
            if (empty($data['sku'])) {
                $data['sku_err'] = 'Please enter sku';
            }

            if (empty($data['product_name_err']) && empty($data['sku_err'])) {
                if ($this->productModel->updateProduct($data)) {
                    flash('product_message', 'Product Updated');
                    redirect('products');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('products/edit', $data);
            }
        } else {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                $data = [
                    'id' => $id,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                    'category_id' => $product->category_id,
                    'brand_id' => $product->brand_id,
                    'unit_id' => $product->unit_id,
                    'min_stock_level' => $product->min_stock_level,
                    'max_stock_level' => $product->max_stock_level,
                    'reorder_level' => $product->reorder_level,
                    'image_path' => $product->image_path,
                    'product_name_err' => '',
                    'sku_err' => ''
                ];
            } else {
                $data = [
                    'id' => $id,
                    'product_name' => '',
                    'sku' => '',
                    'category_id' => '',
                    'brand_id' => '',
                    'unit_id' => '',
                    'min_stock_level' => '',
                    'max_stock_level' => '',
                    'reorder_level' => '',
                    'image_path' => '',
                    'product_name_err' => '',
                    'sku_err' => ''
                ];
                flash('product_message', 'Product not found');
            }
            $this->view('products/edit', $data);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->productModel->deleteProduct($id)) {
                flash('product_message', 'Product Removed');
                redirect('products');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('products');
        }
    }
}
