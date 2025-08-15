<?php
class ImportController extends Controller
{
    private $productModel;
    private $supplierModel;
    private $categoryModel;
    private $brandModel;
    private $productSupplierModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        $this->productModel = $this->model('Product');
        $this->supplierModel = $this->model('Supplier');
        $this->categoryModel = $this->model('Category');
        $this->brandModel = $this->model('Brand');
        $this->productSupplierModel = $this->model('ProductSupplier');
    }

    /**
     * Import page - show import forms and options
     */
    public function index()
    {
        $data = [
            'title' => 'Bulk Import',
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'categories' => $this->categoryModel->getCategories(),
            'brands' => $this->brandModel->getBrands()
        ];

        $this->view('import/index', $data);
    }

    /**
     * Import products from CSV
     */
    public function products()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $this->processProductsImport();
        }

        $data = [
            'title' => 'Import Products',
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
            'categories' => $this->categoryModel->getCategories(),
            'brands' => $this->brandModel->getBrands()
        ];

        $this->view('import/products', $data);
    }

    /**
     * Import suppliers from CSV
     */
    public function suppliers()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $this->processSuppliersImport();
        }

        $data = [
            'title' => 'Import Suppliers'
        ];

        $this->view('import/suppliers', $data);
    }

    /**
     * Import product-supplier relationships from CSV
     */
    public function productSuppliers()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $this->processProductSuppliersImport();
        }

        $data = [
            'title' => 'Import Product-Supplier Relationships',
            'products' => $this->productModel->getActiveProducts(),
            'suppliers' => $this->supplierModel->getActiveSuppliers()
        ];

        $this->view('import/product_suppliers', $data);
    }

    /**
     * Process products CSV import
     */
    private function processProductsImport()
    {
        try {
            // Validate file upload
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Please select a valid CSV file');
            }

            $file = $_FILES['csv_file'];

            // Validate file type
            $fileInfo = pathinfo($file['name']);
            if (strtolower($fileInfo['extension']) !== 'csv') {
                throw new Exception('File must be a CSV file');
            }

            // Read and process CSV
            $csvData = $this->readCSV($file['tmp_name']);
            $results = $this->processProductsData($csvData);

            $data = [
                'title' => 'Import Results',
                'success' => true,
                'results' => $results,
                'message' => 'Products import completed successfully'
            ];

        } catch (Exception $e) {
            $data = [
                'title' => 'Import Error',
                'success' => false,
                'error' => $e->getMessage(),
                'suppliers' => $this->supplierModel->getActiveSuppliers(),
                'categories' => $this->categoryModel->getCategories(),
                'brands' => $this->brandModel->getBrands()
            ];
        }

        $this->view('import/products', $data);
    }

    /**
     * Process suppliers CSV import
     */
    private function processSuppliersImport()
    {
        try {
            // Validate file upload
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Please select a valid CSV file');
            }

            $file = $_FILES['csv_file'];

            // Validate file type
            $fileInfo = pathinfo($file['name']);
            if (strtolower($fileInfo['extension']) !== 'csv') {
                throw new Exception('File must be a CSV file');
            }

            // Read and process CSV
            $csvData = $this->readCSV($file['tmp_name']);
            $results = $this->processSuppliersData($csvData);

            $data = [
                'title' => 'Import Results',
                'success' => true,
                'results' => $results,
                'message' => 'Suppliers import completed successfully'
            ];

        } catch (Exception $e) {
            $data = [
                'title' => 'Import Error',
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        $this->view('import/suppliers', $data);
    }

    /**
     * Process product-suppliers relationships CSV import
     */
    private function processProductSuppliersImport()
    {
        try {
            // Validate file upload
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Please select a valid CSV file');
            }

            $file = $_FILES['csv_file'];

            // Validate file type
            $fileInfo = pathinfo($file['name']);
            if (strtolower($fileInfo['extension']) !== 'csv') {
                throw new Exception('File must be a CSV file');
            }

            // Read and process CSV
            $csvData = $this->readCSV($file['tmp_name']);
            $results = $this->processProductSuppliersData($csvData);

            $data = [
                'title' => 'Import Results',
                'success' => true,
                'results' => $results,
                'message' => 'Product-Supplier relationships import completed successfully'
            ];

        } catch (Exception $e) {
            $data = [
                'title' => 'Import Error',
                'success' => false,
                'error' => $e->getMessage(),
                'products' => $this->productModel->getActiveProducts(),
                'suppliers' => $this->supplierModel->getActiveSuppliers()
            ];
        }

        $this->view('import/product_suppliers', $data);
    }

    /**
     * Read CSV file and return data array
     */
    private function readCSV($filePath)
    {
        $csvData = [];
        $headers = [];

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            $rowIndex = 0;

            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if ($rowIndex === 0) {
                    // First row contains headers
                    $headers = array_map('trim', $row);
                } else {
                    // Convert row to associative array using headers
                    $rowData = [];
                    foreach ($headers as $index => $header) {
                        $rowData[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                    }
                    $csvData[] = $rowData;
                }
                $rowIndex++;
            }

            fclose($handle);
        }

        return $csvData;
    }

    /**
     * Process products data from CSV
     */
    private function processProductsData($csvData)
    {
        $results = [
            'total_rows' => count($csvData),
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
            'created_products' => []
        ];

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header row

            try {
                // Validate required fields
                if (empty($row['product_name']) || empty($row['sku'])) {
                    throw new Exception("Product name and SKU are required");
                }

                // Check if product already exists
                if ($this->productModel->productExists($row['sku'])) {
                    throw new Exception("Product with SKU '{$row['sku']}' already exists");
                }

                // Prepare product data
                $productData = [
                    'product_name' => $row['product_name'],
                    'sku' => $row['sku'],
                    'category_id' => $this->getCategoryId($row['category_name'] ?? ''),
                    'brand_id' => $this->getBrandId($row['brand_name'] ?? ''),
                    'unit_id' => $this->getUnitId($row['unit_name'] ?? ''),
                    'selling_price' => floatval($row['selling_price'] ?? 0),
                    'cost_price' => floatval($row['cost_price'] ?? 0),
                    'profit_margin' => floatval($row['profit_margin'] ?? 0),
                    'min_inventory_level' => intval($row['min_inventory_level'] ?? 0),
                    'reorder_level' => intval($row['reorder_level'] ?? 0),
                    'max_inventory_level' => intval($row['max_inventory_level'] ?? 0),
                    'initial_quantity' => intval($row['initial_quantity'] ?? 0),
                    'storage_location' => $row['storage_location'] ?? '',
                    'barcode' => $row['barcode'] ?? '',
                    'model_number' => $row['model_number'] ?? '',
                    'dimensions' => $row['dimensions'] ?? '',
                    'warranty_period' => intval($row['warranty_period'] ?? 0),
                    'gst_rate' => floatval($row['gst_rate'] ?? 0),
                    'product_status' => $row['product_status'] ?? 'active',
                    'added_by' => $_SESSION['user_id'] ?? 1
                ];

                // Create product
                $productId = $this->productModel->addProduct($productData);

                if (!$productId) {
                    throw new Exception("Failed to create product");
                }

                $results['successful']++;
                $results['created_products'][] = [
                    'row' => $rowNumber,
                    'sku' => $row['sku'],
                    'name' => $row['product_name'],
                    'id' => $productId
                ];

            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'sku' => $row['sku'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process suppliers data from CSV
     */
    private function processSuppliersData($csvData)
    {
        $results = [
            'total_rows' => count($csvData),
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
            'created_suppliers' => []
        ];

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header row

            try {
                // Validate required fields
                if (empty($row['supplier_name'])) {
                    throw new Exception("Supplier name is required");
                }

                // Check if supplier already exists
                if ($this->supplierModel->isSupplierNameExists($row['supplier_name'])) {
                    throw new Exception("Supplier '{$row['supplier_name']}' already exists");
                }

                // Check email uniqueness if provided
                if (!empty($row['email']) && $this->supplierModel->isEmailExists($row['email'])) {
                    throw new Exception("Email '{$row['email']}' already exists");
                }

                // Check GST number uniqueness if provided
                if (!empty($row['gst_number']) && $this->supplierModel->isGstNumberExists($row['gst_number'])) {
                    throw new Exception("GST number '{$row['gst_number']}' already exists");
                }

                // Prepare supplier data
                $supplierData = [
                    'supplier_name' => $row['supplier_name'],
                    'contact_person' => $row['contact_person'] ?? '',
                    'phone' => $row['phone'] ?? '',
                    'email' => $row['email'] ?? '',
                    'address' => $row['address'] ?? '',
                    'gst_number' => $row['gst_number'] ?? '',
                    'added_by' => $_SESSION['user_id'] ?? 1
                ];

                // Create supplier
                if ($this->supplierModel->addSupplier($supplierData)) {
                    $results['successful']++;
                    $results['created_suppliers'][] = [
                        'row' => $rowNumber,
                        'name' => $row['supplier_name'],
                        'email' => $row['email'] ?? 'N/A'
                    ];
                } else {
                    throw new Exception("Failed to create supplier");
                }

            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'name' => $row['supplier_name'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process product-suppliers relationships data from CSV
     */
    private function processProductSuppliersData($csvData)
    {
        $results = [
            'total_rows' => count($csvData),
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
            'created_relationships' => []
        ];

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header row

            try {
                // Validate required fields
                if (empty($row['product_sku']) || empty($row['supplier_name'])) {
                    throw new Exception("Product SKU and Supplier name are required");
                }

                // Get product ID by SKU
                $product = $this->productModel->getProductBySku($row['product_sku']);
                if (!$product) {
                    throw new Exception("Product with SKU '{$row['product_sku']}' not found");
                }

                // Get supplier ID by name
                $supplier = $this->supplierModel->getSupplierByName($row['supplier_name']);
                if (!$supplier) {
                    throw new Exception("Supplier '{$row['supplier_name']}' not found");
                }

                // Check if relationship already exists
                if ($this->productSupplierModel->linkExists($product->product_id, $supplier->supplier_id)) {
                    throw new Exception("Product-Supplier relationship already exists");
                }

                // Prepare relationship data
                $relationshipData = [
                    'product_id' => $product->product_id,
                    'supplier_id' => $supplier->supplier_id,
                    'supplier_sku' => $row['supplier_sku'] ?? '',
                    'purchase_price' => floatval($row['purchase_price'] ?? 0),
                    'min_order_quantity' => intval($row['min_order_quantity'] ?? 1),
                    'lead_time_days' => intval($row['lead_time_days'] ?? 7),
                    'payment_terms' => $row['payment_terms'] ?? '',
                    'shipping_cost' => floatval($row['shipping_cost'] ?? 0),
                    'discount_percentage' => floatval($row['discount_percentage'] ?? 0),
                    'is_primary' => (strtolower($row['is_primary'] ?? '') === 'yes') ? 1 : 0,
                    'is_active' => 1,
                    'quality_rating' => floatval($row['quality_rating'] ?? 0),
                    'delivery_rating' => floatval($row['delivery_rating'] ?? 0),
                    'notes' => $row['notes'] ?? ''
                ];

                // Create relationship
                if ($this->productSupplierModel->addProductSupplier($relationshipData)) {
                    $results['successful']++;
                    $results['created_relationships'][] = [
                        'row' => $rowNumber,
                        'product_sku' => $row['product_sku'],
                        'supplier_name' => $row['supplier_name'],
                        'price' => $row['purchase_price'] ?? 'N/A'
                    ];
                } else {
                    throw new Exception("Failed to create product-supplier relationship");
                }

            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'product_sku' => $row['product_sku'] ?? 'N/A',
                    'supplier_name' => $row['supplier_name'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get category ID by name, create if not exists
     */
    private function getCategoryId($categoryName)
    {
        if (empty($categoryName)) {
            return null;
        }

        $category = $this->categoryModel->getCategoryByName($categoryName);
        if ($category) {
            return $category->category_id;
        }

        // Create new category if it doesn't exist
        $categoryData = [
            'category_name' => $categoryName,
            'description' => 'Imported category'
        ];

        return $this->categoryModel->addCategory($categoryData);
    }

    /**
     * Get brand ID by name, create if not exists
     */
    private function getBrandId($brandName)
    {
        if (empty($brandName)) {
            return null;
        }

        $brand = $this->brandModel->getBrandByName($brandName);
        if ($brand) {
            return $brand->brand_id;
        }

        // Create new brand if it doesn't exist
        $brandData = [
            'brand_name' => $brandName,
            'description' => 'Imported brand'
        ];

        return $this->brandModel->addBrand($brandData);
    }

    /**
     * Get unit ID by name
     */
    private function getUnitId($unitName)
    {
        $unitMap = [
            'pieces' => 1,
            'grams' => 2,
            'kilograms' => 3,
            'liters' => 4,
            'milliliters' => 5,
            'meters' => 6,
            'centimeters' => 7,
            'boxes' => 8,
            'packets' => 9,
            'sets' => 10
        ];

        $unitName = strtolower(trim($unitName));
        return $unitMap[$unitName] ?? 1; // Default to pieces
    }

    /**
     * Download sample CSV templates
     */
    public function downloadTemplate($type = 'products')
    {
        switch ($type) {
            case 'products':
                $this->downloadProductsTemplate();
                break;
            case 'suppliers':
                $this->downloadSuppliersTemplate();
                break;
            case 'product_suppliers':
                $this->downloadProductSuppliersTemplate();
                break;
            default:
                redirect('import');
        }
    }

    private function downloadProductsTemplate()
    {
        $headers = [
            'product_name',
            'sku',
            'category_name',
            'brand_name',
            'unit_name',
            'selling_price',
            'cost_price',
            'profit_margin',
            'min_inventory_level',
            'reorder_level',
            'max_inventory_level',
            'initial_quantity',
            'storage_location',
            'barcode',
            'model_number',
            'dimensions',
            'warranty_period',
            'gst_rate',
            'product_status'
        ];

        $sampleData = [
            [
                'Heavy Duty Drill Machine',
                'DRILL-HD-001',
                'Power Tools',
                'DeWalt',
                'pieces',
                '15000.00',
                '12000.00',
                '25.00',
                '5',
                '10',
                '50',
                '10',
                'A1-B2-C3',
                '123456789012',
                'DCD771C2',
                '25x10x8 cm',
                '24',
                '18',
                'active'
            ],
            [
                'Steel Hammer 500g',
                'HAM-ST-500',
                'Hand Tools',
                'Stanley',
                'pieces',
                '450.00',
                '350.00',
                '28.57',
                '10',
                '20',
                '100',
                '25',
                'A2-B1-C4',
                '234567890123',
                'HAM-500-ST',
                '30x12x5 cm',
                '12',
                '18',
                'active'
            ]
        ];

        $this->generateCSV('products_template.csv', $headers, $sampleData);
    }

    private function downloadSuppliersTemplate()
    {
        $headers = [
            'supplier_name',
            'contact_person',
            'phone',
            'email',
            'address',
            'gst_number'
        ];

        $sampleData = [
            [
                'ABC Hardware Supply',
                'John Smith',
                '9876543210',
                'john@abchardware.com',
                '123 Industrial Area, Mumbai, Maharashtra 400001',
                '27ABCDE1234F1Z5'
            ],
            [
                'ProTools Distribution',
                'Sarah Johnson',
                '9876543211',
                'sarah@protools.com',
                '456 Trade Center, Delhi, Delhi 110001',
                '07FGHIJ5678K2Y9'
            ]
        ];

        $this->generateCSV('suppliers_template.csv', $headers, $sampleData);
    }

    private function downloadProductSuppliersTemplate()
    {
        $headers = [
            'product_sku',
            'supplier_name',
            'supplier_sku',
            'purchase_price',
            'min_order_quantity',
            'lead_time_days',
            'payment_terms',
            'shipping_cost',
            'discount_percentage',
            'is_primary',
            'quality_rating',
            'delivery_rating',
            'notes'
        ];

        $sampleData = [
            [
                'DRILL-HD-001',
                'ABC Hardware Supply',
                'ABC-DRILL-2024',
                '12000.00',
                '1',
                '7',
                'Net 30',
                '500.00',
                '5.00',
                'yes',
                '4.5',
                '4.2',
                'Primary supplier for drills'
            ],
            [
                'DRILL-HD-001',
                'ProTools Distribution',
                'PT-DRILL-HD',
                '12500.00',
                '2',
                '5',
                'Net 15',
                '300.00',
                '3.00',
                'no',
                '4.0',
                '4.8',
                'Fast delivery alternative'
            ]
        ];

        $this->generateCSV('product_suppliers_template.csv', $headers, $sampleData);
    }

    private function generateCSV($filename, $headers, $sampleData)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Write headers
        fputcsv($output, $headers);

        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
?>