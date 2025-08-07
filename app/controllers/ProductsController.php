<?php
require_once 'app/helpers.php';


class ProductsController extends Controller
{
    public $productModel;
    public $categoryModel;
    public $brandModel;
    public $unitModel;
    public $barcodeModel;
    public $supplierModel;


    public function downloadMappingsCSV()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products_mappings.csv"');

        $output = fopen('php://output', 'w');

        // Categories
        fputcsv($output, ['Category ID', 'Category Name']);
        $categories = $this->categoryModel->getCategories();
        foreach ($categories as $cat) {
            fputcsv($output, [$cat->category_id, $cat->category_name]);
        }
        fputcsv($output, []); // Blank line

        // Brands
        fputcsv($output, ['Brand ID', 'Brand Name']);
        $brands = $this->brandModel->getBrands();
        foreach ($brands as $brand) {
            fputcsv($output, [$brand->brand_id, $brand->brand_name]);
        }
        fputcsv($output, []); // Blank line

        // Units
        fputcsv($output, ['Unit ID', 'Unit Name']);
        $units = $this->unitModel->getUnits();
        foreach ($units as $unit) {
            fputcsv($output, [$unit->unit_id, $unit->unit_name]);
        }

        fclose($output);
    }

    public function __construct()
    {
        // Only redirect for non-AJAX requests
        if (!isLoggedIn()) {
            $isAjax = (
                (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            );
            if ($isAjax || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile']))) {
                // For AJAX/POST/CSV import, return JSON error
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'You are not logged in. Please login to import.']);
                exit();
            } else {
                redirect('users/login');
            }
        }
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->brandModel = $this->model('Brand');
        $this->unitModel = $this->model('Unit');
        $this->barcodeModel = $this->model('Barcode');
        $this->supplierModel = $this->model('Supplier');
    }

    public function index()
    {
        $products = $this->productModel->getProducts();
        $categories = $this->categoryModel->getCategories();
        $brands = $this->brandModel->getBrands();

        // Get recent product activities (last 50)
        $activities = $this->getRecentActivities(50);

        $data = [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'activities' => $activities
        ];
        $this->view('products/index', $data);
    }
    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        $InventoryByLocation = $this->productModel->getInventoryByLocation($id);

        if (!$product) {
            redirect('products');
        }

        $data = [
            'product' => $product,
            'Inventory_locations' => $InventoryByLocation
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
                'barcode' => trim($_POST['barcode'] ?? ''),
                'model_number' => trim($_POST['model_number'] ?? ''),
                'supplier_code' => trim($_POST['supplier_code'] ?? ''),
                'category_id' => intval($_POST['category_id'] ?? 0),
                'supplier_id' => intval($_POST['supplier_id'] ?? 0),
                'brand_id' => !empty($_POST['brand_id']) ? intval($_POST['brand_id']) : null,
                'unit_id' => !empty($_POST['unit_id']) ? intval($_POST['unit_id']) : null,
                'min_Inventory_level' => intval($_POST['min_Inventory_level'] ?? 5),
                'max_Inventory_level' => intval($_POST['max_Inventory_level'] ?? 100),
                'reorder_level' => intval($_POST['reorder_level'] ?? 10),
                'purchase_price' => !empty($_POST['purchase_price']) ? floatval($_POST['purchase_price']) : null,
                'selling_price' => !empty($_POST['selling_price']) ? floatval($_POST['selling_price']) : null,
                'profit_margin' => floatval($_POST['profit_margin'] ?? 0),
                'gst_rate' => floatval($_POST['gst_rate'] ?? 18),
                'weight' => !empty($_POST['weight']) ? floatval($_POST['weight']) : null,
                'dimensions' => trim($_POST['dimensions'] ?? ''),
                'warranty_period' => intval($_POST['warranty_period'] ?? 0),
                'storage_location' => trim($_POST['storage_location'] ?? ''),
                'product_status' => trim($_POST['product_status'] ?? 'active'),
                'initial_quantity' => intval($_POST['initial_quantity'] ?? 0),
                'image_path' => '',
                'product_name_err' => '',
                'sku_err' => '',
                'category_id_err' => '',
                'supplier_id_err' => '',
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

            if ($data['supplier_id'] <= 0) {
                $data['supplier_id_err'] = 'Please select a valid supplier';
            }

            // Remove mandatory validation for brand and unit since they're optional
            // if ($data['brand_id'] <= 0) {
            //     $data['brand_id_err'] = 'Please select a valid brand';
            // }

            // if ($data['unit_id'] <= 0) {
            //     $data['unit_id_err'] = 'Please select a valid unit';
            // }

            if ($data['initial_quantity'] < 0) {
                $data['initial_quantity_err'] = 'Initial quantity cannot be negative';
            }

            // Remove mandatory validation for purchase and selling price since they're optional
            // if (empty($data['purchase_price']) || $data['purchase_price'] <= 0) {
            //     $data['purchase_price_err'] = 'Please enter a valid purchase price';
            // }

            // if (empty($data['selling_price']) || $data['selling_price'] <= 0) {
            //     $data['selling_price_err'] = 'Please enter a valid selling price';
            // }

            // Optional validation: if both prices are provided, selling price should be higher
            if (
                $data['purchase_price'] !== null && $data['selling_price'] !== null &&
                $data['selling_price'] <= $data['purchase_price']
            ) {
                $data['selling_price_err'] = 'Selling price must be higher than purchase price';
            }

            // If no errors, add product (only check mandatory fields)
            if (
                empty($data['product_name_err']) && empty($data['sku_err']) &&
                empty($data['category_id_err']) && empty($data['supplier_id_err']) &&
                empty($data['initial_quantity_err']) && empty($data['selling_price_err'])
            ) {
                // Debug logging
                error_log('ProductsController::add - Attempting to add product with data: ' . print_r($data, true));

                $result = $this->productModel->addProduct($data);
                if ($result) {
                    // Log the activity
                    $this->logActivity('product_add', 'product', $result, 'Product "' . $data['product_name'] . '" added successfully');

                    flash('product_message', 'Product Added Successfully with Complete Details');
                    redirect('products');
                } else {
                    error_log('ProductsController::add - addProduct returned false');
                    $data['error'] = 'Something went wrong. Please try again.';
                }
            } else {
                // Debug validation errors
                error_log('ProductsController::add - Validation errors: ' . print_r([
                    'product_name_err' => $data['product_name_err'] ?? '',
                    'sku_err' => $data['sku_err'] ?? '',
                    'category_id_err' => $data['category_id_err'] ?? '',
                    'supplier_id_err' => $data['supplier_id_err'] ?? '',
                    'initial_quantity_err' => $data['initial_quantity_err'] ?? '',
                    'selling_price_err' => $data['selling_price_err'] ?? ''
                ], true));
            }

            // Load form data for redisplay
            $data['categories'] = $this->categoryModel->getCategories();
            $data['suppliers'] = $this->supplierModel->getSuppliers();
            $data['brands'] = $this->brandModel->getBrands();
            $data['units'] = $this->unitModel->getUnits();
            $this->view('products/add', $data);

        } else {
            // GET request - show empty form
            $data = [
                'product_name' => '',
                'sku' => '',
                'barcode' => '',
                'model_number' => '',
                'supplier_code' => '',
                'category_id' => '',
                'supplier_id' => '',
                'brand_id' => '',
                'unit_id' => '',
                'min_Inventory_level' => 5,
                'max_Inventory_level' => 100,
                'reorder_level' => 10,
                'purchase_price' => '',
                'selling_price' => '',
                'profit_margin' => '',
                'gst_rate' => '',
                'weight' => '',
                'dimensions' => '',
                'warranty_period' => 0,
                'storage_location' => '',
                'product_status' => 'active',
                'initial_quantity' => 0,
                'categories' => $this->categoryModel->getCategories(),
                'suppliers' => $this->supplierModel->getSuppliers(),
                'brands' => $this->brandModel->getBrands(),
                'units' => $this->unitModel->getUnits(),
                'product_name_err' => '',
                'sku_err' => '',
                'category_id_err' => '',
                'supplier_id_err' => '',
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
                'min_Inventory_level' => intval($_POST['min_Inventory_level'] ?? 0),
                'max_Inventory_level' => intval($_POST['max_Inventory_level'] ?? 0),
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
                'min_Inventory_level' => $product->min_Inventory_level,
                'max_Inventory_level' => $product->max_Inventory_level,
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

    public function adjustInventory($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost();

            $newQuantity = intval($_POST['new_quantity'] ?? 0);
            $reason = trim($_POST['reason'] ?? 'Manual Adjustment');
            $notes = trim($_POST['notes'] ?? '');

            $adjustmentReason = $reason . ($notes ? ': ' . $notes : '');

            if ($this->productModel->adjustInventory($id, $newQuantity, $adjustmentReason)) {
                echo json_encode(['success' => true, 'message' => 'Inventory adjusted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to adjust Inventory']);
            }
        }
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost();

            $searchTerm = trim($_POST['search'] ?? '');
            $categoryId = intval($_POST['category_id'] ?? 0);
            $inInventoryOnly = isset($_POST['in_Inventory_only']);

            $products = $this->productModel->searchProducts($searchTerm, $categoryId ?: null, $inInventoryOnly);

            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products)
            ]);
        }
    }

    public function importCSV()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
            return;
        }

        $file = $_FILES['csvFile'];
        $updateExisting = isset($_POST['update_existing']) && $_POST['update_existing'] === 'on';
        $validateOnly = isset($_POST['validate_only']) && $_POST['validate_only'] === 'on';

        // Validate file type
        if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
            echo json_encode(['success' => false, 'message' => 'Please upload a CSV file']);
            return;
        }

        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB allowed']);
            return;
        }

        try {
            $results = $this->processCSVFile($file['tmp_name'], $updateExisting, $validateOnly);
            echo json_encode($results);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()]);
        }
    }

    private function processCSVFile($filePath, $updateExisting = false, $validateOnly = false)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception('Could not open CSV file');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new Exception('CSV file appears to be empty');
        }

        // Validate required headers
        $requiredHeaders = ['product_name', 'sku'];
        // Accept either ID or name for category, brand, unit
        $optionalHeaders = [
            'category_id',
            'category_name',
            'brand_id',
            'brand_name',
            'unit_id',
            'unit_name',
            'min_Inventory_level',
            'max_Inventory_level',
            'reorder_level'
        ];
        $missingHeaders = [];
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headers)) {
                $missingHeaders[] = $required;
            }
        }

        if (!empty($missingHeaders)) {
            fclose($handle);
            throw new Exception('Missing required columns: ' . implode(', ', $missingHeaders));
        }

        $results = [
            'success' => true,
            'total_rows' => 0,
            'processed' => 0,
            'skipped' => 0,
            'errors' => [],
            'warnings' => []
        ];

        $rowNumber = 1; // Start from 1 (header row)

        while (($data = fgetcsv($handle)) !== FALSE) {
            $rowNumber++;
            $results['total_rows']++;

            if (count($data) !== count($headers)) {
                $results['errors'][] = "Row $rowNumber: Column count mismatch";
                $results['skipped']++;
                continue;
            }

            $row = array_combine($headers, $data);

            try {
                // Validate required fields
                if (empty(trim($row['product_name']))) {
                    throw new Exception("product_name is required");
                }
                if (empty(trim($row['sku']))) {
                    throw new Exception("sku is required");
                }

                // CATEGORY: Accept either category_id or category_name
                $categoryId = null;
                if (!empty($row['category_id'])) {
                    if (!is_numeric($row['category_id'])) {
                        throw new Exception("category_id must be numeric");
                    }
                    $cat = $this->categoryModel->getCategoryById($row['category_id']);
                    if (!$cat) {
                        throw new Exception("Category ID {$row['category_id']} does not exist");
                    }
                    $categoryId = (int) $row['category_id'];
                } elseif (!empty($row['category_name'])) {
                    $cat = $this->categoryModel->getCategoryByName($row['category_name']);
                    if (!$cat) {
                        throw new Exception("Category name '{$row['category_name']}' does not exist");
                    }
                    $categoryId = (int) $cat->category_id;
                }

                // BRAND: Accept either brand_id or brand_name
                $brandId = null;
                if (!empty($row['brand_id'])) {
                    if (!is_numeric($row['brand_id'])) {
                        throw new Exception("brand_id must be numeric");
                    }
                    $brand = $this->brandModel->getBrandById($row['brand_id']);
                    if (!$brand) {
                        throw new Exception("Brand ID {$row['brand_id']} does not exist");
                    }
                    $brandId = (int) $row['brand_id'];
                } elseif (!empty($row['brand_name'])) {
                    $brand = $this->brandModel->getBrandByName($row['brand_name']);
                    if (!$brand) {
                        throw new Exception("Brand name '{$row['brand_name']}' does not exist");
                    }
                    $brandId = (int) $brand->brand_id;
                }

                // UNIT: Accept either unit_id or unit_name
                $unitId = null;
                if (!empty($row['unit_id'])) {
                    if (!is_numeric($row['unit_id'])) {
                        throw new Exception("unit_id must be numeric");
                    }
                    $unit = $this->unitModel->getUnitById($row['unit_id']);
                    if (!$unit) {
                        throw new Exception("Unit ID {$row['unit_id']} does not exist");
                    }
                    $unitId = (int) $row['unit_id'];
                } elseif (!empty($row['unit_name'])) {
                    $unit = $this->unitModel->getUnitByName($row['unit_name']);
                    if (!$unit) {
                        throw new Exception("Unit name '{$row['unit_name']}' does not exist");
                    }
                    $unitId = (int) $unit->unit_id;
                }

                // Check for existing SKU
                $existingProduct = $this->productModel->getProductBySku(trim($row['sku']));

                if ($existingProduct && !$updateExisting) {
                    $results['warnings'][] = "Row $rowNumber: SKU '{$row['sku']}' already exists (skipped)";
                    $results['skipped']++;
                    continue;
                }

                if (!$validateOnly) {
                    // Prepare data for insertion/update (only fields that exist in the products table)
                    $productData = [
                        'product_name' => trim($row['product_name']),
                        'sku' => trim($row['sku']),
                        'category_id' => $categoryId,
                        'brand_id' => $brandId,
                        'unit_id' => $unitId,
                        'min_Inventory_level' => isset($row['min_Inventory_level']) && is_numeric($row['min_Inventory_level']) ? (int) $row['min_Inventory_level'] : 0,
                        'max_Inventory_level' => isset($row['max_Inventory_level']) && is_numeric($row['max_Inventory_level']) ? (int) $row['max_Inventory_level'] : 0,
                        'reorder_level' => isset($row['reorder_level']) && is_numeric($row['reorder_level']) ? (int) $row['reorder_level'] : 0,
                        'image_path' => null,
                        'is_active' => 1
                    ];

                    if ($existingProduct && $updateExisting) {
                        // Update existing product
                        $success = $this->productModel->updateSimpleProduct($existingProduct->product_id, $productData);
                        if ($success) {
                            $results['processed']++;
                        } else {
                            throw new Exception("Failed to update product");
                        }
                    } else {
                        // Add new product
                        $success = $this->productModel->addSimpleProduct($productData);
                        if ($success) {
                            $results['processed']++;
                        } else {
                            throw new Exception("Failed to add product");
                        }
                    }
                } else {
                    // Validation only
                    $results['processed']++;
                }

            } catch (Exception $e) {
                $results['errors'][] = "Row $rowNumber: " . $e->getMessage();
                $results['skipped']++;
            }
        }

        fclose($handle);
        return $results;
    }

    public function downloadSampleCSV()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products_sample.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers (show both ID and name columns for category, brand, unit)
        fputcsv($output, [
            'product_name',
            'sku',
            'category_id',
            'category_name',
            'brand_id',
            'brand_name',
            'unit_id',
            'unit_name',
            'min_Inventory_level',
            'max_Inventory_level',
            'reorder_level'
        ]);

        // Sample data rows (show both options)
        fputcsv($output, [
            'Sample Hammer',
            'HAM001',
            '1',
            'Hand Tools',
            '1',
            'Acme',
            '1',
            'Piece',
            '5',
            '100',
            '10'
        ]);

        fputcsv($output, [
            'Sample Screwdriver Set',
            'SCREW001',
            '',
            'Screwdrivers',
            '',
            'ToolPro',
            '',
            'Set',
            '3',
            '50',
            '5'
        ]);

        fclose($output);
    }

    /**
     * Generate barcode for a product
     */
    public function generate_barcode($product_id = null)
    {
        if (!$product_id || !is_numeric($product_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }

        // Check if product exists
        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        // Check if barcode already exists
        $existingBarcode = $this->barcodeModel->getBarcodesForProduct($product_id);
        if ($existingBarcode) {
            echo json_encode([
                'success' => false,
                'message' => 'Product already has a barcode',
                'barcode' => $existingBarcode[0]['barcode_value']
            ]);
            exit;
        }

        // Generate new barcode
        $barcodeValue = $this->barcodeModel->generateBarcodeForProduct($product_id);
        if ($barcodeValue) {
            echo json_encode([
                'success' => true,
                'message' => 'Barcode generated successfully',
                'barcode' => $barcodeValue
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate barcode']);
        }
        exit;
    }

    /**
     * View product barcodes
     */
    public function view_barcodes($product_id = null)
    {
        if (!$product_id || !is_numeric($product_id)) {
            flash('product_message', 'Invalid product ID', 'alert alert-danger');
            redirect('products');
            return;
        }

        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            flash('product_message', 'Product not found', 'alert alert-danger');
            redirect('products');
            return;
        }

        $barcodes = $this->barcodeModel->getBarcodesForProduct($product_id);

        $data = [
            'title' => 'Barcodes - ' . $product->product_name,
            'product' => $product,
            'barcodes' => $barcodes
        ];

        $this->view('products/barcodes', $data);
    }

    /**
     * Print product barcode
     */
    public function print_product_barcode($product_id = null)
    {
        if (!$product_id || !is_numeric($product_id)) {
            flash('product_message', 'Invalid product ID', 'alert alert-danger');
            redirect('products');
            return;
        }

        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            flash('product_message', 'Product not found', 'alert alert-danger');
            redirect('products');
            return;
        }

        $barcodes = $this->barcodeModel->getBarcodesForProduct($product_id);
        if (!$barcodes) {
            // Generate barcode if doesn't exist
            $barcodeValue = $this->barcodeModel->generateBarcodeForProduct($product_id);
            if ($barcodeValue) {
                $barcodes = [['barcode_value' => $barcodeValue, 'type' => 'CODE128']];
            }
        }

        $data = [
            'title' => 'Print Barcode - ' . $product->product_name,
            'product' => $product,
            'barcodes' => $barcodes
        ];

        $this->view('products/print_barcode', $data);
    }

    /**
     * Bulk generate barcodes for products without barcodes
     */
    public function bulk_generate_barcodes()
    {
        // Get all products without barcodes
        $products = $this->productModel->getProducts();
        $generatedCount = 0;
        $errors = [];

        foreach ($products as $product) {
            $existingBarcode = $this->barcodeModel->getBarcodesForProduct($product->product_id);

            if (!$existingBarcode) {
                $barcodeValue = $this->barcodeModel->generateBarcodeForProduct($product->product_id);
                if ($barcodeValue) {
                    $generatedCount++;
                } else {
                    $errors[] = "Failed to generate barcode for " . $product->product_name;
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
     * Get product by barcode (AJAX)
     */
    public function get_by_barcode()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barcode = $_POST['barcode'] ?? '';

            if (empty($barcode)) {
                echo json_encode(['success' => false, 'message' => 'Barcode required']);
                exit;
            }

            $product = $this->barcodeModel->getProductByBarcode($barcode);

            if ($product) {
                echo json_encode([
                    'success' => true,
                    'product' => [
                        'id' => $product['product_id'],
                        'name' => $product['product_name'],
                        'sku' => $product['sku'] ?? '',
                        'price' => $product['sale_price'] ?? 0,
                        'Inventory' => $product['Inventory_quantity'] ?? 0
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
            exit;
        }
    }

    /**
     * Get recent product activities
     */
    private function getRecentActivities($limit = 50)
    {
        try {
            $db = new Database();

            // Query to get recent product activities from activity_logs table
            $db->query("
                SELECT 
                    al.log_id as id,
                    al.action,
                    COALESCE(p.product_name, 'Unknown Product') as product_name,
                    COALESCE(u.username, 'System') as user_name,
                    CONCAT(al.action, ' - ', COALESCE(p.product_name, CONCAT('Product ID: ', CAST(al.target_id AS CHAR)))) as details,
                    al.log_timestamp as created_at,
                    CASE 
                        WHEN al.action LIKE '%add%' OR al.action LIKE '%create%' THEN 'success'
                        WHEN al.action LIKE '%delete%' OR al.action LIKE '%remove%' THEN 'warning'
                        WHEN al.action LIKE '%error%' OR al.action LIKE '%fail%' THEN 'error'
                        ELSE 'success'
                    END as status
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.user_id
                LEFT JOIN products p ON al.target_type = 'product' AND al.target_id = p.product_id
                WHERE al.target_type = 'product' OR al.action LIKE '%product%'
                ORDER BY al.log_timestamp DESC
                LIMIT :limit
            ");

            $db->bind(':limit', (int) $limit);
            $db->execute();
            $result = $db->resultSet();

            if ($result) {
                // Format the action names for better display
                foreach ($result as &$activity) {
                    $originalAction = $activity->action;
                    if (strpos($originalAction, 'add') !== false || strpos($originalAction, 'create') !== false) {
                        $activity->action = 'ADD';
                    } elseif (strpos($originalAction, 'edit') !== false || strpos($originalAction, 'update') !== false) {
                        $activity->action = 'EDIT';
                    } elseif (strpos($originalAction, 'delete') !== false || strpos($originalAction, 'remove') !== false) {
                        $activity->action = 'DELETE';
                    } else {
                        $activity->action = strtoupper($originalAction);
                    }
                }
                return $result;
            }

            return [];

        } catch (Exception $e) {
            // Return empty array if there's an error
            error_log('Error fetching recent activities: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear activity history (AJAX)
     */
    public function clearActivity()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $db = new Database();

                // Clear product-related activities
                $db->query("
                    DELETE FROM user_activity_log 
                    WHERE action IN ('product_created', 'product_updated', 'product_deleted', 'Inventory_adjusted')
                       OR details LIKE '%product%'
                       OR details LIKE '%Inventory%'
                ");

                $db->execute();

                echo json_encode(['success' => true, 'message' => 'Activity history cleared successfully']);

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Failed to clear activity history']);
            }
            exit;
        }
    }

    /**
     * Log activity to the activity_logs table
     */
    private function logActivity($action, $targetType, $targetId, $description = null)
    {
        try {
            $db = new Database();

            // Get current user ID from session
            $userId = $_SESSION['user_id'] ?? null;

            $db->query("
                INSERT INTO activity_logs (user_id, action, target_type, target_id) 
                VALUES (:user_id, :action, :target_type, :target_id)
            ");

            $db->bind(':user_id', $userId);
            $db->bind(':action', $action);
            $db->bind(':target_type', $targetType);
            $db->bind(':target_id', $targetId);

            $db->execute();

        } catch (Exception $e) {
            error_log('Error logging activity: ' . $e->getMessage());
            // Don't throw exception, just log error - activity logging shouldn't break the main functionality
        }
    }
}
?>