<?php
/**
 * ProductSupplier Model
 * Handles the relationship between products and suppliers with pricing and terms
 */
class ProductSupplier
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Add a product-supplier relationship
     * @param array $data
     * @return bool
     */
    public function addProductSupplier($data)
    {
        try {
            // Validate required fields
            if (empty($data['product_id']) || empty($data['supplier_id'])) {
                error_log("ProductSupplier::addProductSupplier - Missing required fields");
                return false;
            }

            $this->db->query("
                INSERT INTO product_suppliers (
                    product_id, supplier_id, supplier_sku, supplier_name_for_product,
                    purchase_price, min_order_quantity, lead_time_days, payment_terms,
                    shipping_cost, discount_percentage, currency, is_primary, is_active,
                    quality_rating, delivery_rating, notes
                ) VALUES (
                    :product_id, :supplier_id, :supplier_sku, :supplier_name_for_product,
                    :purchase_price, :min_order_quantity, :lead_time_days, :payment_terms,
                    :shipping_cost, :discount_percentage, :currency, :is_primary, :is_active,
                    :quality_rating, :delivery_rating, :notes
                )
            ");

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':supplier_id', $data['supplier_id']);
            $this->db->bind(':supplier_sku', $data['supplier_sku'] ?? null);
            $this->db->bind(':supplier_name_for_product', $data['supplier_name_for_product'] ?? null);
            $this->db->bind(':purchase_price', $data['purchase_price'] ?? 0.00);
            $this->db->bind(':min_order_quantity', $data['min_order_quantity'] ?? 1);
            $this->db->bind(':lead_time_days', $data['lead_time_days'] ?? 7);
            $this->db->bind(':payment_terms', $data['payment_terms'] ?? null);
            $this->db->bind(':shipping_cost', $data['shipping_cost'] ?? 0.00);
            $this->db->bind(':discount_percentage', $data['discount_percentage'] ?? 0.00);
            $this->db->bind(':currency', $data['currency'] ?? 'INR');
            $this->db->bind(':is_primary', $data['is_primary'] ?? 0);
            $this->db->bind(':is_active', $data['is_active'] ?? 1);
            $this->db->bind(':quality_rating', $data['quality_rating'] ?? null);
            $this->db->bind(':delivery_rating', $data['delivery_rating'] ?? null);
            $this->db->bind(':notes', $data['notes'] ?? null);

            $result = $this->db->execute();

            if (!$result) {
                error_log("ProductSupplier::addProductSupplier - Database execute failed");
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error adding product supplier: " . $e->getMessage());
            error_log("Data: " . print_r($data, true));
            return false;
        }
    }

    /**
     * Get all suppliers for a product
     * @param int $productId
     * @return array
     */
    public function getProductSuppliers($productId)
    {
        $this->db->query("
            SELECT 
                ps.*,
                s.supplier_name,
                s.contact_person,
                s.phone,
                s.email,
                s.address
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id AND ps.is_active = 1
            ORDER BY ps.is_primary DESC, ps.purchase_price ASC
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Get primary supplier for a product
     * @param int $productId
     * @return object|null
     */
    public function getPrimarySupplier($productId)
    {
        $this->db->query("
            SELECT 
                ps.*,
                s.supplier_name,
                s.contact_person,
                s.phone,
                s.email
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id AND ps.is_primary = 1 AND ps.is_active = 1
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Get best price supplier for a product
     * @param int $productId
     * @return object|null
     */
    public function getBestPriceSupplier($productId)
    {
        $this->db->query("
            SELECT 
                ps.*,
                s.supplier_name
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id AND ps.is_active = 1
            ORDER BY ps.purchase_price ASC
            LIMIT 1
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Update product-supplier relationship
     * @param array $data
     * @return bool
     */
    public function updateProductSupplier($data)
    {
        try {
            $this->db->query("
                UPDATE product_suppliers SET
                    supplier_sku = :supplier_sku,
                    supplier_name_for_product = :supplier_name_for_product,
                    purchase_price = :purchase_price,
                    min_order_quantity = :min_order_quantity,
                    lead_time_days = :lead_time_days,
                    payment_terms = :payment_terms,
                    shipping_cost = :shipping_cost,
                    discount_percentage = :discount_percentage,
                    is_primary = :is_primary,
                    is_active = :is_active,
                    quality_rating = :quality_rating,
                    delivery_rating = :delivery_rating,
                    notes = :notes
                WHERE ps_id = :ps_id
            ");

            $this->db->bind(':ps_id', $data['ps_id']);
            $this->db->bind(':supplier_sku', $data['supplier_sku'] ?? null);
            $this->db->bind(':supplier_name_for_product', $data['supplier_name_for_product'] ?? null);
            $this->db->bind(':purchase_price', $data['purchase_price']);
            $this->db->bind(':min_order_quantity', $data['min_order_quantity'] ?? 1);
            $this->db->bind(':lead_time_days', $data['lead_time_days'] ?? 7);
            $this->db->bind(':payment_terms', $data['payment_terms'] ?? null);
            $this->db->bind(':shipping_cost', $data['shipping_cost'] ?? 0.00);
            $this->db->bind(':discount_percentage', $data['discount_percentage'] ?? 0.00);
            $this->db->bind(':is_primary', $data['is_primary'] ?? 0);
            $this->db->bind(':is_active', $data['is_active'] ?? 1);
            $this->db->bind(':quality_rating', $data['quality_rating'] ?? null);
            $this->db->bind(':delivery_rating', $data['delivery_rating'] ?? null);
            $this->db->bind(':notes', $data['notes'] ?? null);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error updating product supplier: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a product-supplier relationship
     * @param int $psId
     * @return bool
     */
    public function removeProductSupplier($psId)
    {
        $this->db->query("DELETE FROM product_suppliers WHERE ps_id = :ps_id");
        $this->db->bind(':ps_id', $psId);
        return $this->db->execute();
    }

    /**
     * Set primary supplier for a product
     * @param int $productId
     * @param int $supplierId
     * @return bool
     */
    public function setPrimarySupplier($productId, $supplierId)
    {
        try {
            $this->db->beginTransaction();

            // First, unset all primary flags for this product
            $this->db->query("
                UPDATE product_suppliers 
                SET is_primary = 0 
                WHERE product_id = :product_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();

            // Set the new primary supplier
            $this->db->query("
                UPDATE product_suppliers 
                SET is_primary = 1 
                WHERE product_id = :product_id AND supplier_id = :supplier_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':supplier_id', $supplierId);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error setting primary supplier: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get supplier comparison for a product
     * @param int $productId
     * @return array
     */
    public function getSupplierComparison($productId)
    {
        $this->db->query("
            SELECT 
                ps.*,
                s.supplier_name,
                s.contact_person,
                s.phone,
                s.email,
                (ps.purchase_price + ps.shipping_cost) as total_cost,
                CASE 
                    WHEN ps.is_primary = 1 THEN 'Primary'
                    WHEN ps.purchase_price = (
                        SELECT MIN(purchase_price) 
                        FROM product_suppliers 
                        WHERE product_id = :product_id AND is_active = 1
                    ) THEN 'Best Price'
                    WHEN ps.lead_time_days = (
                        SELECT MIN(lead_time_days) 
                        FROM product_suppliers 
                        WHERE product_id = :product_id AND is_active = 1
                    ) THEN 'Fastest Delivery'
                    ELSE 'Alternative'
                END as supplier_type
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id AND ps.is_active = 1
            ORDER BY ps.is_primary DESC, ps.purchase_price ASC
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Record a purchase from a supplier
     * @param int $productId
     * @param int $supplierId
     * @param int $quantity
     * @return bool
     */
    public function recordPurchase($productId, $supplierId, $quantity)
    {
        $this->db->query("
            UPDATE product_suppliers 
            SET 
                last_order_date = CURDATE(),
                total_orders = total_orders + 1
            WHERE product_id = :product_id AND supplier_id = :supplier_id
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->bind(':supplier_id', $supplierId);
        return $this->db->execute();
    }

    /**
     * Update supplier ratings
     * @param int $psId
     * @param float $qualityRating
     * @param float $deliveryRating
     * @return bool
     */
    public function updateSupplierRatings($psId, $qualityRating, $deliveryRating)
    {
        $this->db->query("
            UPDATE product_suppliers 
            SET 
                quality_rating = :quality_rating,
                delivery_rating = :delivery_rating
            WHERE ps_id = :ps_id
        ");

        $this->db->bind(':ps_id', $psId);
        $this->db->bind(':quality_rating', $qualityRating);
        $this->db->bind(':delivery_rating', $deliveryRating);
        return $this->db->execute();
    }

    /**
     * Get product-supplier links with details for Link Suppliers page
     * @param int $page
     * @param int $perPage
     * @param string $search
     * @return array
     */
    public function getProductSuppliersWithDetails($page = 1, $perPage = 25, $search = '')
    {
        $offset = ($page - 1) * $perPage;

        $searchCondition = '';
        if (!empty($search)) {
            $searchCondition = " AND (p.product_name LIKE :search OR p.sku LIKE :search OR s.supplier_name LIKE :search OR s.email LIKE :search OR c.category_name LIKE :search)";
        }

        $this->db->query("
            SELECT 
                ps.ps_id as id,
                ps.product_id,
                ps.supplier_id,
                ps.purchase_price as supplier_price,
                ps.lead_time_days,
                ps.min_order_quantity,
                ps.is_primary,
                ps.is_active,
                ps.notes,
                ps.created_at,
                p.product_name,
                COALESCE(p.sku, 'N/A') as sku,
                p.category_id,
                COALESCE(c.category_name, 'Uncategorized') as category_name,
                s.supplier_name,
                s.email as supplier_email
            FROM product_suppliers ps
            INNER JOIN products p ON ps.product_id = p.product_id
            INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE 1=1 {$searchCondition}
            ORDER BY ps.created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        $this->db->bind(':limit', (int) $perPage, PDO::PARAM_INT);
        $this->db->bind(':offset', (int) $offset, PDO::PARAM_INT);

        // Add debug logging
        error_log("ProductSupplier::getProductSuppliersWithDetails - Params: page=$page, perPage=$perPage, search='$search', offset=$offset");

        if (!$this->db->execute()) {
            error_log("ProductSupplier::getProductSuppliersWithDetails - Query execution failed");
            return [];
        }

        $results = $this->db->resultSet();
        error_log("ProductSupplier::getProductSuppliersWithDetails - Results count: " . count($results));

        return $results;
    }

    /**
     * Get total count of product-supplier links
     * @param string $search
     * @return int
     */
    public function getProductSuppliersCount($search = '')
    {
        $searchCondition = '';
        if (!empty($search)) {
            $searchCondition = " AND (p.product_name LIKE :search OR p.sku LIKE :search OR s.supplier_name LIKE :search OR s.email LIKE :search OR c.category_name LIKE :search)";
        }

        $this->db->query("
            SELECT COUNT(*) as total
            FROM product_suppliers ps
            INNER JOIN products p ON ps.product_id = p.product_id
            INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE 1=1 {$searchCondition}
        ");

        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        $this->db->execute();

        $result = $this->db->single();
        return $result->total ?? 0;
    }

    /**
     * Get supplier link statistics
     * @return array
     */
    public function getSupplierLinkStats()
    {
        // Total links
        $this->db->query("SELECT COUNT(*) as total FROM product_suppliers");
        $this->db->execute();
        $result = $this->db->single();
        $totalLinks = $result->total ?? 0;

        // Active links
        $this->db->query("SELECT COUNT(*) as total FROM product_suppliers WHERE is_active = 1");
        $this->db->execute();
        $result = $this->db->single();
        $activeLinks = $result->total ?? 0;

        // Linked products count
        $this->db->query("SELECT COUNT(DISTINCT product_id) as total FROM product_suppliers");
        $this->db->execute();
        $result = $this->db->single();
        $linkedProducts = $result->total ?? 0;

        // Available suppliers count
        $this->db->query("SELECT COUNT(*) as total FROM suppliers WHERE status = 'active'");
        $this->db->execute();
        $result = $this->db->single();
        $availableSuppliers = $result->total ?? 0;

        // Unlinked products count (products that don't have any supplier links)
        $this->db->query("
            SELECT COUNT(*) as total 
            FROM products p 
            WHERE p.is_active = 1 
            AND (p.status != 'deleted' OR p.status IS NULL)
            AND p.product_id NOT IN (
                SELECT DISTINCT product_id 
                FROM product_suppliers 
                WHERE is_active = 1
            )
        ");
        $this->db->execute();
        $result = $this->db->single();
        $unlinkedProducts = $result->total ?? 0;

        return [
            'total_links' => $totalLinks,
            'active_links' => $activeLinks,
            'linked_products' => $linkedProducts,
            'available_suppliers' => $availableSuppliers,
            'unlinked_products' => $unlinkedProducts
        ];
    }

    /**
     * Check if a product-supplier link exists
     * @param int $productId
     * @param int $supplierId
     * @return bool
     */
    public function linkExists($productId, $supplierId)
    {
        try {
            $this->db->query("
                SELECT COUNT(*) as count 
                FROM product_suppliers 
                WHERE product_id = :product_id AND supplier_id = :supplier_id
            ");

            $this->db->bind(':product_id', $productId);
            $this->db->bind(':supplier_id', $supplierId);
            $this->db->execute();

            $result = $this->db->single();
            return ($result && $result->count > 0);
        } catch (Exception $e) {
            error_log("Error checking if link exists: " . $e->getMessage());
            return false; // If there's an error, assume link doesn't exist to allow creation attempt
        }
    }

    /**
     * Update link status
     * @param int $linkId
     * @param int $isActive
     * @return bool
     */
    public function updateLinkStatus($linkId, $isActive)
    {
        $this->db->query("
            UPDATE product_suppliers 
            SET is_active = :is_active
            WHERE ps_id = :link_id
        ");

        $this->db->bind(':link_id', $linkId);
        $this->db->bind(':is_active', $isActive);

        return $this->db->execute();
    }

    /**
     * Set primary supplier by link ID
     * @param int $linkId
     * @return bool
     */
    public function setPrimarySupplierByLinkId($linkId)
    {
        // First, get the product_id for this link
        $this->db->query("SELECT product_id FROM product_suppliers WHERE ps_id = :link_id");
        $this->db->bind(':link_id', $linkId);
        $this->db->execute();
        $result = $this->db->single();

        if (!$result) {
            return false;
        }

        $productId = $result->product_id;

        // Remove primary status from all suppliers for this product
        $this->db->query("
            UPDATE product_suppliers 
            SET is_primary = 0 
            WHERE product_id = :product_id
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->execute();

        // Set this link as primary
        $this->db->query("
            UPDATE product_suppliers 
            SET is_primary = 1 
            WHERE ps_id = :link_id
        ");
        $this->db->bind(':link_id', $linkId);

        return $this->db->execute();
    }

    /**
     * Get all supplier links for a specific product
     * @param int $productId
     * @return array
     */
    public function getProductSupplierLinks($productId)
    {
        $this->db->query("
            SELECT 
                ps.ps_id as link_id,
                ps.supplier_id,
                ps.product_id,
                ps.purchase_price as supplier_cost_price,
                ps.lead_time_days,
                ps.min_order_quantity,
                ps.is_primary,
                ps.is_active,
                ps.notes,
                ps.created_at,
                s.supplier_name,
                s.email as supplier_email,
                CASE 
                    WHEN ps.is_primary = 1 THEN 'primary'
                    ELSE 'secondary'
                END as link_type,
                CASE 
                    WHEN ps.is_active = 1 THEN 'active'
                    ELSE 'inactive'
                END as link_status
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id
            ORDER BY ps.is_primary DESC, s.supplier_name ASC
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Get unlinked products (products without any supplier links)
     * @param int $page
     * @param int $perPage
     * @param string $search
     * @return array
     */
    public function getUnlinkedProducts($page = 1, $perPage = 25, $search = '')
    {
        $offset = ($page - 1) * $perPage;

        $searchCondition = '';
        if (!empty($search)) {
            $searchCondition = " AND (p.product_name LIKE :search OR p.sku LIKE :search OR c.category_name LIKE :search)";
        }

        $this->db->query("
            SELECT
                p.product_id,
                p.product_name,
                COALESCE(p.sku, 'N/A') as sku,
                p.category_id,
                COALESCE(c.category_name, 'Uncategorized') as category_name,
                p.is_active,
                p.created_at
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.is_active = 1
            AND p.deleted_at IS NULL
            AND p.product_id NOT IN (
                SELECT DISTINCT product_id FROM product_suppliers WHERE is_active = 1
            )
            {$searchCondition}
            ORDER BY p.product_name ASC
            LIMIT :limit OFFSET :offset
        ");

        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        $this->db->bind(':limit', (int) $perPage, PDO::PARAM_INT);
        $this->db->bind(':offset', (int) $offset, PDO::PARAM_INT);

        if (!$this->db->execute()) {
            error_log("ProductSupplier::getUnlinkedProducts - Query execution failed");
            return [];
        }

        $results = $this->db->resultSet();
        error_log("ProductSupplier::getUnlinkedProducts - Results count: " . count($results));

        return $results;
    }

    /**
     * Get total count of unlinked products
     * @param string $search
     * @return int
     */
    public function getUnlinkedProductsCount($search = '')
    {
        $searchCondition = '';
        if (!empty($search)) {
            $searchCondition = " AND (p.product_name LIKE :search OR p.sku LIKE :search OR c.category_name LIKE :search)";
        }

        $this->db->query("
            SELECT COUNT(*) as total
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.is_active = 1
            AND p.deleted_at IS NULL
            AND p.product_id NOT IN (
                SELECT DISTINCT product_id FROM product_suppliers WHERE is_active = 1
            )
            {$searchCondition}
        ");

        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }

        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }
}
?>