<?php
/**
 * Universal Transaction Verification System
 * Verifies that submissions are properly saved to database
 * Works with all types of transactions (sales, inventory, products, customers, etc.)
 */

class TransactionVerifier
{
    private $db;
    private $logFile;

    public function __construct()
    {
        $this->db = new Database();
        $this->logFile = APPROOT . '/storage/logs/transaction_verification.log';

        // Ensure log directory exists
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Verify a transaction was successfully saved
     * @param string $transactionType - Type of transaction (sale, product, customer, inventory, etc.)
     * @param array $data - The data that was submitted
     * @param mixed $insertId - The ID returned from the insert operation
     * @return array - Verification result with status and details
     */
    public function verifyTransaction($transactionType, $data, $insertId = null)
    {
        $verification = [
            'success' => false,
            'transaction_type' => $transactionType,
            'timestamp' => date('Y-m-d H:i:s'),
            'insert_id' => $insertId,
            'message' => '',
            'details' => [],
            'errors' => []
        ];

        try {
            switch (strtolower($transactionType)) {
                case 'sale':
                    $verification = $this->verifySaleTransaction($data, $insertId, $verification);
                    break;

                case 'product':
                    $verification = $this->verifyProductTransaction($data, $insertId, $verification);
                    break;

                case 'customer':
                    $verification = $this->verifyCustomerTransaction($data, $insertId, $verification);
                    break;

                case 'inventory':
                case 'inventory_adjustment':
                    $verification = $this->verifyInventoryTransaction($data, $insertId, $verification);
                    break;

                case 'category':
                    $verification = $this->verifyCategoryTransaction($data, $insertId, $verification);
                    break;

                case 'supplier':
                    $verification = $this->verifySupplierTransaction($data, $insertId, $verification);
                    break;

                case 'user':
                    $verification = $this->verifyUserTransaction($data, $insertId, $verification);
                    break;

                default:
                    $verification['errors'][] = "Unknown transaction type: {$transactionType}";
                    $verification['message'] = "Cannot verify unknown transaction type";
                    break;
            }

            // Log the verification result
            $this->logVerification($verification);

        } catch (Exception $e) {
            $verification['success'] = false;
            $verification['errors'][] = "Verification error: " . $e->getMessage();
            $verification['message'] = "Transaction verification failed due to system error";
            $this->logVerification($verification);
        }

        return $verification;
    }

    /**
     * Verify sale transaction
     */
    private function verifySaleTransaction($data, $insertId, $verification)
    {
        if (!$insertId) {
            $verification['errors'][] = "No sale ID provided for verification";
            return $verification;
        }

        // Check if sale exists
        $this->db->query("SELECT * FROM sales WHERE sale_id = :sale_id");
        $this->db->bind(':sale_id', $insertId);
        $this->db->execute();
        $sale = $this->db->single();

        if (!$sale) {
            $verification['errors'][] = "Sale record not found in database";
            return $verification;
        }

        $verification['details']['sale_found'] = true;
        $verification['details']['sale_amount'] = $sale->total_amount ?? 0;

        // Check sale items if products were provided
        if (isset($data['products']) && is_array($data['products'])) {
            $this->db->query("SELECT COUNT(*) as item_count FROM sale_items WHERE sale_id = :sale_id");
            $this->db->bind(':sale_id', $insertId);
            $this->db->execute();
            $itemCount = $this->db->single();

            $expectedItems = count($data['products']);
            $actualItems = $itemCount->item_count ?? 0;

            $verification['details']['expected_items'] = $expectedItems;
            $verification['details']['actual_items'] = $actualItems;

            if ($expectedItems == $actualItems) {
                $verification['success'] = true;
                $verification['message'] = "Sale transaction verified successfully";
            } else {
                $verification['errors'][] = "Item count mismatch: expected {$expectedItems}, found {$actualItems}";
            }
        } else {
            $verification['success'] = true;
            $verification['message'] = "Sale record verified in database";
        }

        return $verification;
    }

    /**
     * Verify product transaction
     */
    private function verifyProductTransaction($data, $insertId, $verification)
    {
        if (!$insertId) {
            $verification['errors'][] = "No product ID provided for verification";
            return $verification;
        }

        $this->db->query("SELECT * FROM products WHERE product_id = :product_id");
        $this->db->bind(':product_id', $insertId);
        $this->db->execute();
        $product = $this->db->single();

        if (!$product) {
            $verification['errors'][] = "Product record not found in database";
            return $verification;
        }

        $verification['details']['product_found'] = true;
        $verification['details']['product_name'] = $product->product_name ?? '';

        // Verify key fields match
        $fieldsToCheck = ['product_name', 'sku', 'unit_price'];
        $mismatches = [];

        foreach ($fieldsToCheck as $field) {
            if (isset($data[$field]) && isset($product->$field)) {
                if ($data[$field] != $product->$field) {
                    $mismatches[] = "{$field}: expected '{$data[$field]}', found '{$product->$field}'";
                }
            }
        }

        if (empty($mismatches)) {
            $verification['success'] = true;
            $verification['message'] = "Product transaction verified successfully";
        } else {
            $verification['errors'] = array_merge($verification['errors'], $mismatches);
        }

        return $verification;
    }

    /**
     * Verify customer transaction
     */
    private function verifyCustomerTransaction($data, $insertId, $verification)
    {
        if (!$insertId) {
            $verification['errors'][] = "No customer ID provided for verification";
            return $verification;
        }

        $this->db->query("SELECT * FROM customers WHERE customer_id = :customer_id");
        $this->db->bind(':customer_id', $insertId);
        $this->db->execute();
        $customer = $this->db->single();

        if (!$customer) {
            $verification['errors'][] = "Customer record not found in database";
            return $verification;
        }

        $verification['details']['customer_found'] = true;
        $verification['details']['customer_name'] = $customer->customer_name ?? '';

        // Verify customer name matches
        if (isset($data['customer_name']) && $data['customer_name'] == $customer->customer_name) {
            $verification['success'] = true;
            $verification['message'] = "Customer transaction verified successfully";
        } else {
            $verification['errors'][] = "Customer name mismatch";
        }

        return $verification;
    }

    /**
     * Verify inventory transaction
     */
    private function verifyInventoryTransaction($data, $insertId, $verification)
    {
        // For inventory adjustments, check the inventory_movements table
        if (isset($data['product_id'])) {
            $this->db->query("
                SELECT * FROM inventory_movements 
                WHERE product_id = :product_id 
                ORDER BY movement_date DESC 
                LIMIT 1
            ");
            $this->db->bind(':product_id', $data['product_id']);
            $this->db->execute();
            $movement = $this->db->single();

            if ($movement) {
                $verification['details']['movement_found'] = true;
                $verification['details']['movement_id'] = $movement->movement_id ?? null;
                $verification['success'] = true;
                $verification['message'] = "Inventory movement verified successfully";
            } else {
                $verification['errors'][] = "No inventory movement found for product";
            }
        }

        return $verification;
    }

    /**
     * Verify category transaction
     */
    private function verifyCategoryTransaction($data, $insertId, $verification)
    {
        if (!$insertId) {
            $verification['errors'][] = "No category ID provided for verification";
            return $verification;
        }

        $this->db->query("SELECT * FROM categories WHERE category_id = :category_id");
        $this->db->bind(':category_id', $insertId);
        $this->db->execute();
        $category = $this->db->single();

        if ($category) {
            $verification['details']['category_found'] = true;
            $verification['details']['category_name'] = $category->category_name ?? '';
            $verification['success'] = true;
            $verification['message'] = "Category transaction verified successfully";
        } else {
            $verification['errors'][] = "Category record not found in database";
        }

        return $verification;
    }

    /**
     * Verify supplier transaction
     */
    private function verifySupplierTransaction($data, $insertId, $verification)
    {
        if (!$insertId) {
            $verification['errors'][] = "No supplier ID provided for verification";
            return $verification;
        }

        $this->db->query("SELECT * FROM suppliers WHERE supplier_id = :supplier_id");
        $this->db->bind(':supplier_id', $insertId);
        $this->db->execute();
        $supplier = $this->db->single();

        if ($supplier) {
            $verification['details']['supplier_found'] = true;
            $verification['details']['supplier_name'] = $supplier->supplier_name ?? '';
            $verification['success'] = true;
            $verification['message'] = "Supplier transaction verified successfully";
        } else {
            $verification['errors'][] = "Supplier record not found in database";
        }

        return $verification;
    }

    /**
     * Verify user transaction
     */
    private function verifyUserTransaction($data, $insertId, $verification)
    {
        if (!$insertId) {
            $verification['errors'][] = "No user ID provided for verification";
            return $verification;
        }

        $this->db->query("SELECT * FROM users WHERE user_id = :user_id");
        $this->db->bind(':user_id', $insertId);
        $this->db->execute();
        $user = $this->db->single();

        if ($user) {
            $verification['details']['user_found'] = true;
            $verification['details']['username'] = $user->username ?? '';
            $verification['success'] = true;
            $verification['message'] = "User transaction verified successfully";
        } else {
            $verification['errors'][] = "User record not found in database";
        }

        return $verification;
    }

    /**
     * Log verification results
     */
    private function logVerification($verification)
    {
        $logEntry = [
            'timestamp' => $verification['timestamp'],
            'type' => $verification['transaction_type'],
            'success' => $verification['success'],
            'insert_id' => $verification['insert_id'],
            'message' => $verification['message'],
            'errors' => $verification['errors']
        ];

        $logLine = date('Y-m-d H:i:s') . " - " . json_encode($logEntry) . PHP_EOL;
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get verification history for a specific transaction type
     */
    public function getVerificationHistory($transactionType = null, $limit = 50)
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $history = [];

        foreach (array_reverse($lines) as $line) {
            if (count($history) >= $limit)
                break;

            $parts = explode(' - ', $line, 2);
            if (count($parts) == 2) {
                $data = json_decode($parts[1], true);
                if ($data && (!$transactionType || $data['type'] == $transactionType)) {
                    $history[] = $data;
                }
            }
        }

        return $history;
    }

    /**
     * Quick verification for AJAX calls
     */
    public function quickVerify($transactionType, $insertId)
    {
        $verification = $this->verifyTransaction($transactionType, [], $insertId);
        return [
            'success' => $verification['success'],
            'message' => $verification['message']
        ];
    }
}
?>