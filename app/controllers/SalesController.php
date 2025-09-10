<?php
class SalesController extends Controller
{
    public $productModel;
    public $saleModel;
    public $customerModel;
    public $barcodeModel;
    public $discountCommissionModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->saleModel = $this->model('Sale');
        $this->productModel = $this->model('Product');
        $this->customerModel = $this->model('Customer');
        $this->barcodeModel = $this->model('Barcode');
        $this->discountCommissionModel = $this->model('DiscountCommission');
    }

    public function index()
    {
        // This now serves as the Sales Hub page
        $data = [
            'title' => 'Sales Management Hub'
        ];

        $this->view('sales/index', $data);
    }

    /**
     * Point of Sale interface with barcode scanning
     */
    public function pos()
    {
        $customers = $this->customerModel->getCustomers();
        $products = $this->productModel->getProducts();

        $data = [
            'title' => 'Point of Sale System',
            'customers' => $customers,
            'products' => $products
        ];

        $this->view('sales/pos', $data);
    }

    /**
     * Barcode scanning API for POS
     */
    public function scan_barcode()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barcode = trim($_POST['barcode'] ?? '');

            if (empty($barcode)) {
                echo json_encode(['success' => false, 'message' => 'Barcode is required']);
                return;
            }

            $product = $this->barcodeModel->getProductByBarcode($barcode);

            if ($product) {
                echo json_encode([
                    'success' => true,
                    'product' => [
                        'id' => $product->product_id,
                        'name' => $product->product_name,
                        'sku' => $product->sku,
                        'price' => number_format($product->selling_price ?? 0, 2),
                        'inventory' => $product->inventory_quantity ?? 0,
                        'barcode' => $product->barcode_value
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    /**
     * Unique ID scanning API for discount/commission system
     */
    public function scan_unique_id()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $uniqueId = trim($_POST['unique_id'] ?? '');

            if (empty($uniqueId)) {
                echo json_encode(['success' => false, 'message' => 'Unique ID is required']);
                return;
            }

            if (strlen($uniqueId) !== 12) {
                echo json_encode(['success' => false, 'message' => 'Unique ID must be 12 characters']);
                return;
            }

            // Use the DiscountCommission model to scan the unique ID
            $result = $this->discountCommissionModel->scanUniqueId($uniqueId);

            echo json_encode($result);

        } catch (Exception $e) {
            error_log("Unique ID scan error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error scanning unique ID'
            ]);
        }
    }

    /**
     * Enhanced product search for sales (URL: /sales/search-products)
     */
    public function search_products()
    {
        return $this->searchProducts();
    }

    /**
     * Search customer by barcode/ID (URL: /sales/search-customer)
     */
    public function search_customer()
    {
        return $this->searchCustomer();
    }

    /**
     * Enhanced product search for sales
     */
    private function searchProducts()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $query = $input['query'] ?? '';
            $type = $input['type'] ?? 'all';

            if (empty($query)) {
                echo json_encode(['success' => true, 'products' => []]);
                return;
            }

            $products = [];

            switch ($type) {
                case 'barcode':
                    $products = $this->searchProductsByBarcode($query);
                    break;
                case 'sku':
                    $products = $this->searchProductsBySKU($query);
                    break;
                case 'name':
                    $products = $this->searchProductsByName($query);
                    break;
                case 'category':
                    $products = $this->searchProductsByCategory($query);
                    break;
                default:
                    $products = $this->searchProductsAll($query);
                    break;
            }

            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products)
            ]);

        } catch (Exception $e) {
            error_log("Sales product search error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search customer by barcode/ID
     */
    private function searchCustomer()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $barcode = $input['barcode'] ?? '';

            if (empty($barcode)) {
                echo json_encode(['success' => false, 'message' => 'Barcode is required']);
                return;
            }

            $customer = $this->customerModel->getCustomerByBarcode($barcode);

            if ($customer) {
                echo json_encode([
                    'success' => true,
                    'customer' => [
                        'id' => $customer->customer_id,
                        'name' => $customer->customer_name,
                        'email' => $customer->email ?? '',
                        'phone' => $customer->phone ?? '',
                        'barcode' => $barcode
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Customer not found'
                ]);
            }

        } catch (Exception $e) {
            error_log("Customer search error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Customer search failed'
            ]);
        }
    }

    // ==================== ENHANCED SEARCH HELPER METHODS ====================

    /**
     * Search products by barcode
     */
    private function searchProductsByBarcode($barcode)
    {
        try {
            // First try exact barcode match
            $product = $this->barcodeModel->getProductByBarcode($barcode);
            if ($product) {
                return [$this->formatProductForSearch($product)];
            }

            // Then try partial barcode match
            $products = $this->productModel->searchByBarcodePartial($barcode);
            return array_map([$this, 'formatProductForSearch'], $products ?: []);

        } catch (Exception $e) {
            error_log("Barcode search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search products by SKU
     */
    private function searchProductsBySKU($sku)
    {
        try {
            $products = $this->productModel->searchBySKU($sku);
            return array_map([$this, 'formatProductForSearch'], $products ?: []);
        } catch (Exception $e) {
            error_log("SKU search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search products by name
     */
    private function searchProductsByName($name)
    {
        try {
            $products = $this->productModel->searchByName($name);
            return array_map([$this, 'formatProductForSearch'], $products ?: []);
        } catch (Exception $e) {
            error_log("Name search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search products by category
     */
    private function searchProductsByCategory($category)
    {
        try {
            $products = $this->productModel->searchByCategory($category);
            return array_map([$this, 'formatProductForSearch'], $products ?: []);
        } catch (Exception $e) {
            error_log("Category search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search products across all fields
     */
    private function searchProductsAll($query)
    {
        try {
            $products = $this->productModel->searchProducts($query);
            return array_map([$this, 'formatProductForSearch'], $products ?: []);
        } catch (Exception $e) {
            error_log("All fields search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Format product data for search results
     */
    private function formatProductForSearch($product)
    {
        return [
            'id' => $product->product_id,
            'name' => $product->product_name,
            'sku' => $product->sku ?? '',
            'category' => $product->category_name ?? 'Uncategorized',
            'price' => floatval($product->selling_price ?? $product->unit_price ?? 0),
            'inventory' => intval($product->current_Inventory ?? $product->inventory_quantity ?? 0),
            'image' => $product->image_path ?? null,
            'barcode' => $product->barcode_value ?? '',
            'brand' => $product->brand_name ?? '',
            'unit' => $product->unit_name ?? ''
        ];
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

    /**
     * Process POS sale transaction with discount and commission support
     */
    public function process_sale()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Invalid input data']);
                return;
            }

            $customer_id = $input['customer_id'] ?? null;
            $payment_method = $input['payment_method'] ?? 'cash';
            $amount_received = $input['amount_received'] ?? 0;
            $cart_items = $input['cart_items'] ?? [];
            $total_amount = $input['total_amount'] ?? 0;

            // Discount and commission data
            $scanned_user = $input['scanned_user'] ?? null;
            $discount_credits_used = floatval($input['discount_credits_used'] ?? 0);
            $applied_discount_amount = floatval($input['applied_discount_amount'] ?? 0);
            $commission_contractor = $input['commission_contractor'] ?? null;

            // Validate input
            if (empty($cart_items)) {
                echo json_encode(['success' => false, 'message' => 'Cart is empty']);
                return;
            }

            if ($total_amount <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid total amount']);
                return;
            }

            try {
                // Calculate original amount before discount
                $original_amount = $total_amount + $applied_discount_amount;

                // Prepare sale data
                $sale_data = [
                    'customer_id' => !empty($customer_id) ? $customer_id : null,
                    'total_amount' => $original_amount, // Store original amount
                    'discount_amount' => $applied_discount_amount,
                    'final_amount' => $total_amount, // Final amount after discount
                    'payment_mode' => $payment_method,
                    'sale_date' => date('Y-m-d H:i:s'),
                    'notes' => $this->buildSaleNotes($scanned_user, $discount_credits_used, $commission_contractor)
                ];

                error_log('Initiating sale processing...');

                // Add the sale
                $sale_id = $this->saleModel->addSale($sale_data);

                if ($sale_id) {
                    // Add sale items
                    $items_added = 0;
                    foreach ($cart_items as $item) {
                        $sale_item_data = [
                            'sale_id' => $sale_id,
                            'product_id' => $item['id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['price'],
                            'discount' => 0 // Item-level discount handled at sale level
                        ];
                        $result = $this->saleModel->addSaleItem($sale_item_data);
                        if ($result) {
                            $items_added++;
                        } else {
                            error_log("Failed to add sale item: " . json_encode($sale_item_data));
                        }
                    }

                    if ($items_added > 0) {
                        // Process discount credits and commissions
                        $rewards_result = $this->processDiscountAndCommission(
                            $sale_id,
                            $original_amount,
                            $scanned_user,
                            $discount_credits_used,
                            $commission_contractor
                        );

                        if ($rewards_result['success']) {
                            error_log('Sale submission successful! Rewards: ' . json_encode($rewards_result));

                            echo json_encode([
                                'success' => true,
                                'message' => 'Sale processed successfully',
                                'sale_id' => $sale_id,
                                'items_added' => $items_added,
                                'change' => max(0, $amount_received - $total_amount),
                                'rewards' => $rewards_result['results']
                            ]);
                        } else {
                            // Sale was created but rewards processing failed - warn but don't fail
                            echo json_encode([
                                'success' => true,
                                'message' => 'Sale processed but rewards processing failed: ' . $rewards_result['message'],
                                'sale_id' => $sale_id,
                                'items_added' => $items_added,
                                'change' => max(0, $amount_received - $total_amount),
                                'rewards' => ['credits_used' => 0, 'discount_credits_earned' => 0, 'commission_earned' => 0]
                            ]);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Sale created but no items were added']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to create sale']);
                }
            } catch (Exception $e) {
                error_log("POS Sale processing error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error processing sale: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    /**
     * Process discount credits and commissions for a sale
     */
    private function processDiscountAndCommission($sale_id, $sale_amount, $scanned_user, $discount_credits_used, $commission_contractor)
    {
        try {
            $customer_id = null;
            $contractor_id = null;

            // Determine customer and contractor IDs
            if ($scanned_user && $scanned_user['type'] === 'customer') {
                $customer_id = $scanned_user['data']['customer_id'];
            }

            if ($commission_contractor) {
                $contractor_id = $commission_contractor['contractor_id'];
                // If contractor referral, also set customer for commission link
                if (!$customer_id && $scanned_user && $scanned_user['type'] === 'customer') {
                    $customer_id = $scanned_user['data']['customer_id'];
                }
            }

            // Process discount credits usage first
            if ($customer_id && $discount_credits_used > 0) {
                $credits_used = $this->discountCommissionModel->useDiscountCredits($customer_id, $discount_credits_used, $sale_id);
                if ($credits_used != $discount_credits_used) {
                    error_log("Warning: Expected to use $discount_credits_used credits but actually used $credits_used");
                }
            }

            // Process rewards (credits earned and commissions)
            $rewards_result = $this->discountCommissionModel->processSaleRewards(
                $sale_id,
                $sale_amount,
                $customer_id,
                $contractor_id,
                $discount_credits_used
            );

            return $rewards_result;

        } catch (Exception $e) {
            error_log("Discount and commission processing error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error processing rewards: ' . $e->getMessage(),
                'results' => ['credits_used' => 0, 'discount_credits_earned' => 0, 'commission_earned' => 0]
            ];
        }
    }

    /**
     * Build sale notes with discount and commission info
     */
    private function buildSaleNotes($scanned_user, $discount_credits_used, $commission_contractor)
    {
        $notes = [];

        if ($scanned_user) {
            $notes[] = "Scanned ID: " . $scanned_user['data']['unique_id'] . " (" . $scanned_user['data']['name'] . ")";
        }

        if ($discount_credits_used > 0) {
            $notes[] = "Discount credits used: $" . number_format($discount_credits_used, 2);
        }

        if ($commission_contractor) {
            $notes[] = "Commission contractor: " . $commission_contractor['unique_id'] . " (" . $commission_contractor['name'] . ")";
        }

        return implode('; ', $notes);
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
