<?php
require_once 'app/helpers.php';

class ProductsController extends Controller
{
    public $productModel;
    public $categoryModel;
    public $brandModel;
    public $unitModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->brandModel = $this->model('Brand');
        $this->unitModel = $this->model('Unit');
    }

    public function index()
    {
        $products = $this->productModel->getProducts();
        $categories = $this->categoryModel->getCategories();

        $data = [
            'products' => $products,
            'categories' => $categories
        ];
        $this->view('products/index', $data);
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        $stockByLocation = $this->productModel->getStockByLocation($id);

        if (!$product) {
            redirect('products');
        }

        $data = [
            'product' => $product,
            'stock_locations' => $stockByLocation
        ];
        $this->view('products/show', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost();

            $data = [
                'product_name' => trim($_POST['product_name'] ?? ''),
                'sku' => trim($_POST['sku'] ?? ''),
                'supplier_code' => trim($_POST['supplier_code'] ?? ''),
                'category_id' => intval($_POST['category_id'] ?? 0),
                'brand_id' => intval($_POST['brand_id'] ?? 0),
                'unit_id' => intval($_POST['unit_id'] ?? 0),
                'min_stock_level' => intval($_POST['min_stock_level'] ?? 0),
                'max_stock_level' => intval($_POST['max_stock_level'] ?? 0),
                'reorder_level' => intval($_POST['reorder_level'] ?? 0),
                'purchase_price' => floatval($_POST['purchase_price'] ?? 0),
                'selling_price' => floatval($_POST['selling_price'] ?? 0),
                'profit_margin' => floatval($_POST['profit_margin'] ?? 0),
                'weight' => floatval($_POST['weight'] ?? 0),
                'dimensions' => trim($_POST['dimensions'] ?? ''),
                'warranty_period' => intval($_POST['warranty_period'] ?? 0),
                'initial_quantity' => intval($_POST['initial_quantity'] ?? 0),
                'image_path' => '',
                'product_name_err' => '',
                'sku_err' => '',
                'category_id_err' => '',
                'brand_id_err' => '',
                'unit_id_err' => '',
                'purchase_price_err' => '',
                'selling_price_err' => '',
                'initial_quantity_err' => ''
            ];

            // Handle file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $targetDir = APPROOT . DS . 'public' . DS . 'uploads' . DS;
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $data['image_path'] = $fileName;
                }
            }

            // Validation
            if (empty($data['product_name'])) {
                $data['product_name_err'] = 'Please enter product name';
            }

            if (empty($data['sku'])) {
                $data['sku_err'] = 'Please enter SKU';
            }

            if ($data['category_id'] <= 0) {
                $data['category_id_err'] = 'Please select a valid category';
            }

            if ($data['brand_id'] <= 0) {
                $data['brand_id_err'] = 'Please select a valid brand';
            }

            if ($data['unit_id'] <= 0) {
                $data['unit_id_err'] = 'Please select a valid unit';
            }

            if ($data['initial_quantity'] < 0) {
                $data['initial_quantity_err'] = 'Initial quantity cannot be negative';
            }

            if (empty($data['purchase_price']) || $data['purchase_price'] <= 0) {
                $data['purchase_price_err'] = 'Please enter a valid purchase price';
            }

            if (empty($data['selling_price']) || $data['selling_price'] <= 0) {
                $data['selling_price_err'] = 'Please enter a valid selling price';
            }

            if (
                !empty($data['purchase_price']) && !empty($data['selling_price']) &&
                $data['selling_price'] <= $data['purchase_price']
            ) {
                $data['selling_price_err'] = 'Selling price must be higher than purchase price';
            }

            // If no errors, add product
            if (
                empty($data['product_name_err']) && empty($data['sku_err']) && empty($data['category_id_err']) &&
                empty($data['brand_id_err']) && empty($data['unit_id_err']) && empty($data['initial_quantity_err']) &&
                empty($data['purchase_price_err']) && empty($data['selling_price_err'])
            ) {

                $result = $this->productModel->addProduct($data);
                if ($result) {
                    flash('product_message', 'Product Added Successfully with Complete Details');
                    redirect('products');
                } else {
                    $data['error'] = 'Something went wrong. Please try again.';
                }
            }

            // Load form data for redisplay
            $data['categories'] = $this->categoryModel->getCategories();
            $data['brands'] = $this->brandModel->getBrands();
            $data['units'] = $this->unitModel->getUnits();
            $this->view('products/add', $data);

        } else {
            // GET request - show empty form
            $data = [
                'product_name' => '',
                'sku' => '',
                'supplier_code' => '',
                'category_id' => '',
                'brand_id' => '',
                'unit_id' => '',
                'min_stock_level' => 5,
                'max_stock_level' => 100,
                'reorder_level' => 10,
                'purchase_price' => '',
                'selling_price' => '',
                'profit_margin' => '',
                'weight' => '',
                'dimensions' => '',
                'warranty_period' => 0,
                'initial_quantity' => 0,
                'categories' => $this->categoryModel->getCategories(),
                'brands' => $this->brandModel->getBrands(),
                'units' => $this->unitModel->getUnits(),
                'product_name_err' => '',
                'sku_err' => '',
                'category_id_err' => '',
                'brand_id_err' => '',
                'unit_id_err' => '',
                'purchase_price_err' => '',
                'selling_price_err' => '',
                'initial_quantity_err' => ''
            ];
            $this->view('products/add', $data);
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost();

            $data = [
                'product_id' => $id,
                'product_name' => trim($_POST['product_name'] ?? ''),
                'sku' => trim($_POST['sku'] ?? ''),
                'supplier_code' => trim($_POST['supplier_code'] ?? ''),
                'category_id' => intval($_POST['category_id'] ?? 0),
                'brand_id' => intval($_POST['brand_id'] ?? 0),
                'unit_id' => intval($_POST['unit_id'] ?? 0),
                'min_stock_level' => intval($_POST['min_stock_level'] ?? 0),
                'max_stock_level' => intval($_POST['max_stock_level'] ?? 0),
                'reorder_level' => intval($_POST['reorder_level'] ?? 0),
                'purchase_price' => floatval($_POST['purchase_price'] ?? 0),
                'selling_price' => floatval($_POST['selling_price'] ?? 0),
                'profit_margin' => floatval($_POST['profit_margin'] ?? 0),
                'weight' => floatval($_POST['weight'] ?? 0),
                'dimensions' => trim($_POST['dimensions'] ?? ''),
                'warranty_period' => intval($_POST['warranty_period'] ?? 0),
                'image_path' => trim($_POST['current_image'] ?? ''),
                'product_name_err' => '',
                'sku_err' => '',
                'category_id_err' => '',
                'brand_id_err' => '',
                'unit_id_err' => '',
                'purchase_price_err' => '',
                'selling_price_err' => ''
            ];

            // Handle file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $targetDir = APPROOT . DS . 'public' . DS . 'uploads' . DS;
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $data['image_path'] = $fileName;
                }
            }

            // Validation (same as add method)
            if (empty($data['product_name'])) {
                $data['product_name_err'] = 'Please enter product name';
            }

            if (empty($data['sku'])) {
                $data['sku_err'] = 'Please enter SKU';
            }

            if ($data['category_id'] <= 0) {
                $data['category_id_err'] = 'Please select a valid category';
            }

            if ($data['brand_id'] <= 0) {
                $data['brand_id_err'] = 'Please select a valid brand';
            }

            if ($data['unit_id'] <= 0) {
                $data['unit_id_err'] = 'Please select a valid unit';
            }

            if (empty($data['purchase_price']) || $data['purchase_price'] <= 0) {
                $data['purchase_price_err'] = 'Please enter a valid purchase price';
            }

            if (empty($data['selling_price']) || $data['selling_price'] <= 0) {
                $data['selling_price_err'] = 'Please enter a valid selling price';
            }

            if (
                !empty($data['purchase_price']) && !empty($data['selling_price']) &&
                $data['selling_price'] <= $data['purchase_price']
            ) {
                $data['selling_price_err'] = 'Selling price must be higher than purchase price';
            }

            // If no errors, update product
            if (
                empty($data['product_name_err']) && empty($data['sku_err']) && empty($data['category_id_err']) &&
                empty($data['brand_id_err']) && empty($data['unit_id_err']) &&
                empty($data['purchase_price_err']) && empty($data['selling_price_err'])
            ) {

                if ($this->productModel->updateProduct($id, $data)) {
                    flash('product_message', 'Product Updated Successfully');
                    redirect('products');
                } else {
                    $data['error'] = 'Something went wrong. Please try again.';
                }
            }

            // Load form data for redisplay
            $data['categories'] = $this->categoryModel->getCategories();
            $data['brands'] = $this->brandModel->getBrands();
            $data['units'] = $this->unitModel->getUnits();
            $this->view('products/edit', $data);

        } else {
            // GET request - load existing product data
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                redirect('products');
            }

            $data = [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'supplier_code' => $product->supplier_code ?? '',
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'unit_id' => $product->unit_id,
                'min_stock_level' => $product->min_stock_level,
                'max_stock_level' => $product->max_stock_level,
                'reorder_level' => $product->reorder_level,
                'purchase_price' => $product->purchase_price ?? 0,
                'selling_price' => $product->selling_price ?? 0,
                'profit_margin' => $product->profit_margin ?? 0,
                'weight' => $product->weight ?? 0,
                'dimensions' => $product->dimensions ?? '',
                'warranty_period' => $product->warranty_period ?? 0,
                'image_path' => $product->image_path,
                'categories' => $this->categoryModel->getCategories(),
                'brands' => $this->brandModel->getBrands(),
                'units' => $this->unitModel->getUnits(),
                'product_name_err' => '',
                'sku_err' => '',
                'category_id_err' => '',
                'brand_id_err' => '',
                'unit_id_err' => '',
                'purchase_price_err' => '',
                'selling_price_err' => ''
            ];
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

    public function adjustStock($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost();

            $newQuantity = intval($_POST['new_quantity'] ?? 0);
            $reason = trim($_POST['reason'] ?? 'Manual Adjustment');
            $notes = trim($_POST['notes'] ?? '');

            $adjustmentReason = $reason . ($notes ? ': ' . $notes : '');

            if ($this->productModel->adjustStock($id, $newQuantity, $adjustmentReason)) {
                echo json_encode(['success' => true, 'message' => 'Stock adjusted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to adjust stock']);
            }
        }
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost();

            $searchTerm = trim($_POST['search'] ?? '');
            $categoryId = intval($_POST['category_id'] ?? 0);
            $inStockOnly = isset($_POST['in_stock_only']);

            $products = $this->productModel->searchProducts($searchTerm, $categoryId ?: null, $inStockOnly);

            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products)
            ]);
        }
    }
}
?>