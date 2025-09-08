<?php
require_once APPROOT . DS . 'app' . DS . 'traits' . DS . 'SoftDelete.php';

class Product
{
    use SoftDelete;

    private $db;

    /**
     * Allow optional Database injection for easier testing
     * @param mixed $db Optional Database-like object
     */
    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            $this->db = new Database;
        }
    }

    public function getProducts()
    {
        $this->db->query("
            WITH supplier_stats AS (
                SELECT 
                    ps.product_id,
                    ps.supplier_id,
                    s.supplier_name,
                    ps.purchase_price,
                    ps.lead_time_days,
                    ps.min_order_quantity,
                    ps.is_primary,
                    COALESCE(s.quality_rating, 0) AS quality_rating,
                    COALESCE(s.communication_rating, 0) AS communication_rating,
                    -- Rankings (1 = best)
                    RANK() OVER (PARTITION BY ps.product_id ORDER BY ps.purchase_price ASC) AS price_rank,
                    RANK() OVER (PARTITION BY ps.product_id ORDER BY ps.lead_time_days ASC) AS delivery_rank,
                    RANK() OVER (PARTITION BY ps.product_id ORDER BY COALESCE(s.quality_rating,0) DESC) AS quality_rank,
                    (s.supplier_tier = 'Gold' OR s.supplier_tier = 'Silver') AS preferred_supplier
                FROM product_suppliers ps
                INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id AND s.deleted_at IS NULL
                WHERE ps.is_active = 1
            ),
            chosen_supplier AS (
                SELECT *,
                       -- Choose cheapest supplier (ignore is_primary preference)
                       ROW_NUMBER() OVER (PARTITION BY product_id ORDER BY price_rank ASC, supplier_id ASC) AS rn
                FROM supplier_stats
            ),
            supplier_agg AS (
                SELECT product_id,
                       COUNT(*) AS supplier_count,
                       MIN(purchase_price) AS min_supplier_price,
                       MAX(purchase_price) AS max_supplier_price,
                       AVG(purchase_price) AS avg_supplier_price
                FROM supplier_stats
                GROUP BY product_id
            ),
            last_price AS (
                SELECT 
                    pi.product_id,
                    pi.unit_price,
                    ROW_NUMBER() OVER (PARTITION BY pi.product_id ORDER BY pur.purchase_date DESC) AS rn
                FROM purchase_items pi
                INNER JOIN purchases pur ON pi.purchase_id = pur.purchase_id
            ),
            last_order AS (
                SELECT pi.product_id, MAX(pur.purchase_date) AS last_ordered_date
                FROM purchase_items pi
                INNER JOIN purchases pur ON pi.purchase_id = pur.purchase_id
                GROUP BY pi.product_id
            )
            SELECT 
                p.product_id, p.product_name, p.sku, p.model_number, p.category_id, p.brand_id, p.unit_id,
                p.min_inventory_level, p.max_inventory_level, p.reorder_level, p.image_path, p.is_active,
                c.category_name, b.brand_name, u.unit_name,
                cs.supplier_id AS primary_supplier_id,
                cs.supplier_name AS primary_supplier_name,
                cs.purchase_price AS primary_purchase_price,
                cs.lead_time_days AS primary_lead_time,
                cs.min_order_quantity,
                cs.is_primary,
                cs.price_rank, cs.delivery_rank, cs.quality_rank,
                cs.preferred_supplier,
                cs.quality_rating,
                cs.communication_rating AS delivery_rating,
                ((cs.quality_rating + cs.communication_rating) / 2) AS overall_rating,
                CASE 
                    WHEN cs.lead_time_days <= 2 THEN 'Fast'
                    WHEN cs.lead_time_days <= 5 THEN 'Normal'
                    ELSE 'Slow'
                END AS delivery_speed,
                -- Flags for UI
                (cs.price_rank = 1) AS is_cheapest,
                (cs.delivery_rank = 1) AS is_fastest,
                (cs.quality_rank = 1) AS is_best_quality,
                -- Pricing fallbacks
                COALESCE(cs.purchase_price, lp.unit_price, 50.00) AS unit_price,
                COALESCE(cs.purchase_price, lp.unit_price, 50.00) * 1.3 AS selling_price,
                lo.last_ordered_date,
                COALESCE(SUM(inv.quantity), 0) AS current_inventory,
                CASE 
                    WHEN COALESCE(SUM(inv.quantity), 0) <= p.min_inventory_level THEN 'Low Inventory'
                    WHEN COALESCE(SUM(inv.quantity), 0) <= p.reorder_level THEN 'Reorder'
                    ELSE 'Normal'
                END AS inventory_status,
                MIN(inv.expiry_date) AS expiry_date,
                sa.supplier_count, sa.min_supplier_price, sa.max_supplier_price, sa.avg_supplier_price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN chosen_supplier cs ON p.product_id = cs.product_id AND cs.rn = 1
            LEFT JOIN supplier_agg sa ON p.product_id = sa.product_id
            LEFT JOIN last_price lp ON p.product_id = lp.product_id AND lp.rn = 1
            LEFT JOIN last_order lo ON p.product_id = lo.product_id
            LEFT JOIN inventory inv ON p.product_id = inv.product_id
            WHERE (p.status != 'deleted' OR p.status IS NULL) AND p.is_active = 1
            GROUP BY p.product_id, p.product_name, p.sku, p.model_number, p.category_id, p.brand_id, p.unit_id,
                     p.min_inventory_level, p.max_inventory_level, p.reorder_level, p.image_path, p.is_active,
                     c.category_name, b.brand_name, u.unit_name,
                     cs.supplier_id, cs.supplier_name, cs.purchase_price, cs.lead_time_days, cs.min_order_quantity, cs.is_primary,
                     cs.price_rank, cs.delivery_rank, cs.quality_rank, cs.preferred_supplier, cs.quality_rating, cs.communication_rating,
                     lo.last_ordered_date, sa.supplier_count, sa.min_supplier_price, sa.max_supplier_price, sa.avg_supplier_price, lp.unit_price
            ORDER BY p.product_name ASC
        ");
        $this->db->execute();
        $result = $this->db->resultSet();

        // If the query produced no rows, check for DB errors and fallback
        $dbError = '';
        if (method_exists($this->db, 'getLastError')) {
            $dbError = $this->db->getLastError();
        }
        if (empty($result) && !empty($dbError)) {
            error_log('[PRODUCT::getProductsWithAllSuppliers] SQL error: ' . $dbError);
            // Attempt a simple fallback query to return basic product-supplier rows
            try {
                $this->db->query("SELECT p.product_id, p.product_name, ps.supplier_id, ps.purchase_price AS supplier_price, s.supplier_name FROM products p JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1 JOIN suppliers s ON ps.supplier_id = s.supplier_id AND s.deleted_at IS NULL WHERE p.is_active = 1 ORDER BY p.product_name ASC LIMIT 100");
                $this->db->execute();
                $fallback = $this->db->resultSet();
                if ($fallback) {
                    return array_map(function ($item) {
                        return (object) $item;
                    }, $fallback);
                }
            } catch (Exception $e) {
                error_log('[PRODUCT::getProductsWithAllSuppliers] Fallback query failed: ' . $e->getMessage());
            }
        }
        return $result ? $result : [];
    }

    public function getProductsPaginated($offset = 0, $limit = 25, $search = '')
    {
        $searchCondition = '';
        $searchParam = '';

        if (!empty($search)) {
            $searchCondition = " AND (p.product_name LIKE :search OR p.sku LIKE :search OR c.category_name LIKE :search OR b.brand_name LIKE :search)";
            $searchParam = '%' . $search . '%';
        }

        $this->db->query("
            WITH supplier_counts AS (
                SELECT 
                    ps.product_id,
                    COUNT(*) as supplier_count,
                    MIN(ps.purchase_price) as min_supplier_price,
                    MAX(ps.purchase_price) as max_supplier_price,
                    AVG(ps.purchase_price) as avg_supplier_price
                FROM product_suppliers ps
                INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id AND s.deleted_at IS NULL
                WHERE ps.is_active = 1 AND ps.purchase_price > 0
                GROUP BY ps.product_id
            )
            SELECT 
                p.*,
                c.category_name,
                b.brand_name,
                u.unit_name,
                COALESCE(sc.supplier_count, 0) as supplier_count,
                COALESCE(sc.min_supplier_price, 0) as min_supplier_price,
                COALESCE(sc.max_supplier_price, 0) as max_supplier_price,
                COALESCE(sc.avg_supplier_price, 0) as avg_supplier_price,
                COALESCE(SUM(s.quantity), 0) as current_inventory,
                -- unit_price is the latest recorded purchase unit price (if any)
                COALESCE(lp.unit_price, 50.00) as unit_price,
                -- primary_purchase_price: best available supplier price or last purchase price
                COALESCE(sc.min_supplier_price, lp.unit_price, 50.00) as primary_purchase_price,
                -- selling_price: use actual selling_price from products table
                COALESCE(p.selling_price, sc.min_supplier_price * 1.3, lp.unit_price * 1.3, 50.00) as selling_price,
                -- current_average_cost: use the new column if it has data
                COALESCE(p.current_average_cost, p.purchase_price, sc.min_supplier_price) as current_average_cost,
                CASE 
                    WHEN COALESCE(SUM(s.quantity), 0) <= p.min_inventory_level THEN 'Low Inventory'
                    WHEN COALESCE(SUM(s.quantity), 0) <= p.reorder_level THEN 'Reorder'
                    ELSE 'Normal'
                END as inventory_status,
                MIN(s.expiry_date) as expiry_date
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN supplier_counts sc ON p.product_id = sc.product_id
            LEFT JOIN inventory s ON p.product_id = s.product_id
            LEFT JOIN (
                SELECT 
                    pi.product_id,
                    pi.unit_price,
                    ROW_NUMBER() OVER (PARTITION BY pi.product_id ORDER BY pur.purchase_date DESC) as rn
                FROM purchase_items pi
                JOIN purchases pur ON pi.purchase_id = pur.purchase_id
            ) lp ON p.product_id = lp.product_id AND lp.rn = 1
            WHERE p.is_active = 1" . $searchCondition . "
            GROUP BY p.product_id, p.product_name, p.sku, p.category_id, p.brand_id, p.unit_id, 
                     p.min_Inventory_level, p.max_Inventory_level, p.reorder_level, 
                     p.image_path, p.is_active, c.category_name, b.brand_name, u.unit_name, 
                     sc.supplier_count, sc.min_supplier_price, sc.max_supplier_price, sc.avg_supplier_price, lp.unit_price
            ORDER BY p.product_name ASC
            LIMIT :offset, :limit
        ");

        if (!empty($search)) {
            $this->db->bind(':search', $searchParam);
        }
        $this->db->bind(':offset', $offset, \PDO::PARAM_INT);
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);

        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getTotalProductsCount($search = '')
    {
        $searchCondition = '';
        $searchParam = '';

        if (!empty($search)) {
            $searchCondition = " AND (p.product_name LIKE :search OR p.sku LIKE :search OR c.category_name LIKE :search OR b.brand_name LIKE :search)";
            $searchParam = '%' . $search . '%';
        }

        $this->db->query("
            SELECT COUNT(DISTINCT p.product_id) as total
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            WHERE p.is_active = 1" . $searchCondition
        );

        if (!empty($search)) {
            $this->db->bind(':search', $searchParam);
        }

        $this->db->execute();
        $result = $this->db->single();
        return $result ? (int) $result->total : 0;
    }

    public function getTotalInventoryCount()
    {
        $this->db->query("
            SELECT COALESCE(SUM(i.quantity), 0) as total_inventory
            FROM inventory i
            WHERE i.product_id IS NOT NULL
        ");

        $this->db->execute();
        $result = $this->db->single();
        return $result ? (int) $result->total_inventory : 0;
    }

    public function getProductsForSale()
    {
        $this->db->query("
            SELECT 
                p.product_id,
                p.product_name,
                p.sku,
                c.category_name,
                b.brand_name,
                u.unit_name,
                COALESCE(lp.unit_price, 50.00) as purchase_price,
                COALESCE(lp.unit_price * 1.3, 65.00) as selling_price,
                30 as profit_margin,
                COALESCE(SUM(s.quantity), 0) as current_inventory,
                p.min_inventory_level,
                p.image_path,
                bar.barcode_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN inventory s ON p.product_id = s.product_id
            LEFT JOIN barcode bar ON p.product_id = bar.product_id AND bar.is_active = 1
            LEFT JOIN (
                SELECT 
                    pi.product_id,
                    pi.unit_price,
                    ROW_NUMBER() OVER (PARTITION BY pi.product_id ORDER BY pur.purchase_date DESC) as rn
                FROM purchase_items pi
                JOIN purchases pur ON pi.purchase_id = pur.purchase_id
            ) lp ON p.product_id = lp.product_id AND lp.rn = 1
            WHERE p.is_active = 1
            GROUP BY p.product_id, p.product_name, p.sku, c.category_name, 
                     b.brand_name, u.unit_name, p.min_Inventory_level, p.image_path, 
                     bar.barcode_value, lp.unit_price
            ORDER BY p.product_name ASC
        ");
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get paginated product-supplier rows for purchases/add table view
     * @param int $page
     * @param int $perPage
     * @param array $filters (search, supplier_id, category, priceMin, priceMax)
     * @return array
     */
    public function getProductsForAdd($page = 1, $perPage = 25, $filters = [])
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $where = "WHERE p.is_active = 1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (p.product_name LIKE :search OR p.sku LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['supplier_id'])) {
            $where .= " AND ps.supplier_id = :supplier_id";
            $params[':supplier_id'] = (int) $filters['supplier_id'];
        }

        if (!empty($filters['category'])) {
            $where .= " AND c.category_name = :category";
            $params[':category'] = $filters['category'];
        }

        if (isset($filters['priceMin']) && $filters['priceMin'] !== '') {
            $where .= " AND COALESCE(ps.purchase_price, lp.unit_price, 0) >= :priceMin";
            $params[':priceMin'] = (float) $filters['priceMin'];
        }
        if (isset($filters['priceMax']) && $filters['priceMax'] !== '') {
            $where .= " AND COALESCE(ps.purchase_price, lp.unit_price, 0) <= :priceMax";
            $params[':priceMax'] = (float) $filters['priceMax'];
        }

        $sql = <<<SQL
            SELECT
                p.product_id,
                p.product_name,
                p.sku,
                COALESCE(lp.unit_price, 0) AS unit_price,
                ps.supplier_id,
                s.supplier_name,
                ps.purchase_price AS supplier_price,
                ps.lead_time_days,
                ps.min_order_quantity,
                COALESCE((SELECT SUM(inv.quantity) FROM inventory inv WHERE inv.product_id = p.product_id), 0) AS current_inventory,
                c.category_name
            FROM products p
            JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1
            JOIN suppliers s ON ps.supplier_id = s.supplier_id AND s.deleted_at IS NULL
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN (
                SELECT product_id, AVG(unit_price) as unit_price
                FROM purchase_items
                GROUP BY product_id
            ) lp ON p.product_id = lp.product_id
            {$where}
            ORDER BY p.product_name ASC, ps.purchase_price ASC
            LIMIT :offset, :limit
        SQL;

        // First, compute total matching rows for accurate pagination
        $countSql = <<<SQL
            SELECT COUNT(*) as total
            FROM products p
            JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1
            JOIN suppliers s ON ps.supplier_id = s.supplier_id AND s.deleted_at IS NULL
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN (
                SELECT product_id, AVG(unit_price) as unit_price
                FROM purchase_items
                GROUP BY product_id
            ) lp ON p.product_id = lp.product_id
            {$where}
        SQL;

        // Count
        $this->db->query($countSql);
        foreach ($params as $k => $v) {
            $type = is_int($v) ? \PDO::PARAM_INT : null;
            $this->db->bind($k, $v, $type);
        }
        $this->db->execute();
        $cntRow = $this->db->single();
        $total = $cntRow ? (int) $cntRow->total : 0;

        // Now fetch paginated rows
        $this->db->query($sql);
        foreach ($params as $k => $v) {
            $type = is_int($v) ? \PDO::PARAM_INT : null;
            $this->db->bind($k, $v, $type);
        }
        $this->db->bind(':offset', $offset, \PDO::PARAM_INT);
        $this->db->bind(':limit', $perPage, \PDO::PARAM_INT);

        $this->db->execute();
        $rows = $this->db->resultSet();
        $rowsObj = $rows ? array_map(function ($r) {
            return (object) $r;
        }, $rows) : [];
        return ['rows' => $rowsObj, 'total' => $total];
    }

    /**
     * Return supplier links for a product
     * @param int $productId
     * @return array
     */
    public function getSuppliersForProduct($productId)
    {
        $this->db->query(<<<'SQL'
            SELECT ps.supplier_id, s.supplier_name, ps.purchase_price, ps.lead_time_days, ps.min_order_quantity
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id AND ps.is_active = 1 AND s.deleted_at IS NULL
            ORDER BY ps.is_primary DESC, ps.purchase_price ASC
            LIMIT 50
        SQL
        );
        $this->db->bind(':product_id', (int) $productId, \PDO::PARAM_INT);
        $this->db->execute();
        $rows = $this->db->resultSet();
        return $rows ? $rows : [];
    }

    /**
     * Get products supplied by a specific supplier
     * @param int $supplierId
     * @return array
     */
    public function getProductsBySupplier($supplierId)
    {
        $this->db->query("
            SELECT
                p.product_id,
                p.product_name,
                p.sku,
                p.model_number,
                p.image_path,
                p.min_inventory_level,
                p.reorder_level,
                c.category_name,
                b.brand_name,
                u.unit_name,
                ps.supplier_id,
                ps.purchase_price AS supplier_price,
                ps.lead_time_days,
                ps.min_order_quantity,
                ps.is_primary,
                ps.supplier_sku AS supplier_product_code,
                ps.is_active as status,
                COALESCE((SELECT SUM(inv.quantity) FROM inventory inv WHERE inv.product_id = p.product_id), 0) AS current_inventory
            FROM products p
            JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            WHERE p.is_active = 1 AND ps.supplier_id = :supplier_id
            ORDER BY p.product_name ASC, ps.purchase_price ASC
            LIMIT 100
        ");
        $this->db->bind(':supplier_id', $supplierId);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    /**
     * Get product by SKU
     * @param string $sku
     * @return object|null
     */
    public function getProductBySku($sku)
    {
        $this->db->query("SELECT * FROM products WHERE sku = :sku");
        $this->db->bind(':sku', $sku);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Get active products for dropdowns
     * @return array
     */
    public function getActiveProducts()
    {
        $this->db->query("
            SELECT product_id, product_name, sku
            FROM products 
            WHERE is_active = 1
            ORDER BY product_name ASC
        ");
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    public function addProduct($data)
    {
        error_log('Product::addProduct - Starting method');

        try {
            // Start transaction
            error_log('Product::addProduct - Starting transaction');
            $this->db->beginTransaction();

            // Get primary supplier information for main product record
            $primarySupplierData = null;
            $primaryPurchasePrice = null;

            if (!empty($data['suppliers']) && is_array($data['suppliers'])) {
                error_log('Product::addProduct - Processing suppliers: ' . print_r($data['suppliers'], true));
                $primarySupplierIndex = $data['primary_supplier'] ?? 0;

                // Find primary supplier data
                foreach ($data['suppliers'] as $index => $supplier) {
                    if ($index == $primarySupplierIndex && !empty($supplier['supplier_id'])) {
                        $primarySupplierData = $supplier;
                        $primaryPurchasePrice = $supplier['purchase_price'] ?? null;
                        break;
                    }
                }

                // If no primary supplier found, use the first valid supplier
                if (!$primarySupplierData) {
                    foreach ($data['suppliers'] as $supplier) {
                        if (!empty($supplier['supplier_id'])) {
                            $primarySupplierData = $supplier;
                            $primaryPurchasePrice = $supplier['purchase_price'] ?? null;
                            break;
                        }
                    }
                }
            }

            // Process dimensions into JSON format for storage
            $dimensionsData = [];
            if (!empty($data['width']))
                $dimensionsData['width'] = $data['width'];
            if (!empty($data['width_unit']))
                $dimensionsData['width_unit'] = $data['width_unit'];
            if (!empty($data['height']))
                $dimensionsData['height'] = $data['height'];
            if (!empty($data['height_unit']))
                $dimensionsData['height_unit'] = $data['height_unit'];
            if (!empty($data['length']))
                $dimensionsData['length'] = $data['length'];
            if (!empty($data['length_unit']))
                $dimensionsData['length_unit'] = $data['length_unit'];
            if (!empty($data['weight']))
                $dimensionsData['weight'] = $data['weight'];
            if (!empty($data['weight_unit']))
                $dimensionsData['weight_unit'] = $data['weight_unit'];

            $dimensionsJson = !empty($dimensionsData) ? json_encode($dimensionsData) : null;

            // Calculate profit margin if possible
            $profitMargin = 0;
            if ($primaryPurchasePrice > 0 && isset($data['selling_price']) && $data['selling_price'] > 0) {
                $profitMargin = round((($data['selling_price'] - $primaryPurchasePrice) / $data['selling_price']) * 100, 2);
            }

            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSKU($data['product_name'], $data['category_id']);
            }

            // Insert product (updated for modern form structure)
            $this->db->query("
                INSERT INTO products (
                    product_name, sku, model_number, category_id, product_type, product_status,
                    weight, dimensions, warranty_period, has_expiry, expiry_months, has_warranty,
                    image_path, is_active, created_at, updated_at
                ) VALUES (
                    :product_name, :sku, :model_number, :category_id, :product_type, :product_status,
                    :weight, :dimensions, :warranty_period, :has_expiry, :expiry_months, :has_warranty,
                    :image_path, :is_active, NOW(), NOW()
                )
            ");

            // Bind values with proper null handling
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':model_number', !empty($data['model_number']) ? $data['model_number'] : null);
            $this->db->bind(':category_id', !empty($data['category_id']) ? (int) $data['category_id'] : null);
            $this->db->bind(':product_type', !empty($data['product_type']) ? $data['product_type'] : null);
            $this->db->bind(':product_status', $data['product_status'] ?? 'active');
            $this->db->bind(':weight', !empty($data['weight']) ? (float) $data['weight'] : null);
            $this->db->bind(':dimensions', $dimensionsJson);
            $this->db->bind(':warranty_period', !empty($data['warranty_period']) ? (int) $data['warranty_period'] : null);
            $this->db->bind(':has_expiry', $data['has_expiry'] ? 1 : 0);
            $this->db->bind(':expiry_months', !empty($data['expiry_months']) ? (int) $data['expiry_months'] : null);
            $this->db->bind(':has_warranty', $data['has_warranty'] ? 1 : 0);
            $this->db->bind(':image_path', !empty($data['image_path']) ? $data['image_path'] : null);
            $this->db->bind(':is_active', 1);

            // Execute product insert
            error_log('Product::addProduct - About to execute product insert');
            if (!$this->db->execute()) {
                error_log('Product::addProduct - Product insert failed');
                throw new Exception('Failed to insert product');
            }

            // Get the newly inserted product ID
            $productId = $this->db->lastInsertId();
            error_log('Product::addProduct - Product inserted with ID: ' . $productId);

            // Commit transaction
            error_log('Product::addProduct - Committing transaction');
            $this->db->commit();
            error_log('Product::addProduct - SUCCESS - Product added with ID: ' . $productId);
            return $productId;

        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            error_log('DB Insert Error in Product::addProduct(): ' . $e->getMessage());
            error_log('Data submitted: ' . print_r($data, true));
            return false;
        }
    }

    private function addProductSuppliers($productId, $suppliers, $primarySupplierIndex = 0)
    {
        $primarySupplierId = null;

        // First, insert all suppliers with is_primary = 0 to avoid trigger conflicts
        foreach ($suppliers as $index => $supplier) {
            if (empty($supplier['supplier_id'])) {
                continue; // Skip empty suppliers
            }

            // Remember which supplier should be primary
            if ($index == $primarySupplierIndex) {
                $primarySupplierId = (int) $supplier['supplier_id'];
            }

            $this->db->query("
                INSERT INTO product_suppliers (
                    product_id, supplier_id, supplier_sku, purchase_price, lead_time_days, 
                    min_order_quantity, payment_terms, notes, is_primary, created_at, updated_at
                ) VALUES (
                    :product_id, :supplier_id, :supplier_sku, :purchase_price, :lead_time_days, 
                    :min_order_quantity, :payment_terms, :notes, 0, NOW(), NOW()
                )
            ");

            $this->db->bind(':product_id', $productId);
            $this->db->bind(':supplier_id', (int) $supplier['supplier_id']);
            $this->db->bind(':supplier_sku', !empty($supplier['supplier_sku']) ? $supplier['supplier_sku'] : null);
            $this->db->bind(':purchase_price', !empty($supplier['purchase_price']) ? (float) $supplier['purchase_price'] : null);
            $this->db->bind(':lead_time_days', !empty($supplier['lead_time_days']) ? (int) $supplier['lead_time_days'] : 7);
            $this->db->bind(':min_order_quantity', !empty($supplier['min_order_quantity']) ? (int) $supplier['min_order_quantity'] : 1);
            $this->db->bind(':payment_terms', !empty($supplier['payment_terms']) ? $supplier['payment_terms'] : null);
            $this->db->bind(':notes', !empty($supplier['notes']) ? $supplier['notes'] : null);

            if (!$this->db->execute()) {
                throw new Exception('Failed to insert product supplier relationship');
            }
        }

        // Now set the primary supplier in a separate operation
        // This allows the trigger to work properly
        if ($primarySupplierId) {
            $this->db->query("
                UPDATE product_suppliers 
                SET is_primary = 1 
                WHERE product_id = :product_id AND supplier_id = :supplier_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':supplier_id', $primarySupplierId);

            if (!$this->db->execute()) {
                throw new Exception('Failed to set primary supplier');
            }
        }
    }

    private function addInitialInventory($productId, $data)
    {
        $this->db->query("
            INSERT INTO inventory (
                product_id, location_id, quantity, batch_number, expiry_date
            ) VALUES (
                :product_id, :location_id, :quantity, :batch_number, :expiry_date
            )
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->bind(':location_id', $data['location_id'] ?? 1); // Default to main warehouse
        $this->db->bind(':quantity', $data['initial_quantity']);
        $this->db->bind(':batch_number', 'INITIAL_' . str_pad($productId, 6, '0', STR_PAD_LEFT));
        $this->db->bind(':expiry_date', $data['expiry_date'] ?? null); // Optional expiry date

        if (!$this->db->execute()) {
            throw new Exception('Failed to insert initial inventory');
        }

        // Log Inventory movement
        $this->logInventoryMovement($productId, 'in', $data['initial_quantity'], null, $data['location_id'] ?? 1, 'Initial Inventory');
    }

    public function updateProduct($id, $data)
    {
        try {
            // Calculate profit margin if not provided
            $profitMargin = 0;
            if (
                isset($data['purchase_price']) && isset($data['selling_price']) &&
                $data['purchase_price'] > 0 && $data['selling_price'] > 0
            ) {
                $profitMargin = round((($data['selling_price'] - $data['purchase_price']) / $data['purchase_price']) * 100, 2);
            }

            $this->db->query("
                UPDATE products SET 
                    product_name = :product_name,
                    sku = :sku,
                    model_number = :model_number,
                    supplier_code = :supplier_code,
                    category_id = :category_id,
                    brand_id = :brand_id,
                    unit_id = :unit_id,
                    product_type = :product_type,
                    has_expiry = :has_expiry,
                    expiry_months = :expiry_months,
                    min_Inventory_level = :min_Inventory_level,
                    max_Inventory_level = :max_Inventory_level,
                    reorder_level = :reorder_level,
                    purchase_price = :purchase_price,
                    selling_price = :selling_price,
                    profit_margin = :profit_margin,
                    weight = :weight,
                    dimensions = :dimensions,
                    warranty_period = :warranty_period,
                    image_path = :image_path,
                    updated_at = CURRENT_TIMESTAMP
                WHERE product_id = :product_id
            ");

            // Bind values
            $this->db->bind(':product_id', $id);
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':model_number', $data['model_number'] ?? null);
            $this->db->bind(':supplier_code', $data['supplier_code'] ?? null);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id'] ?? null);
            $this->db->bind(':unit_id', $data['unit_id'] ?? null);
            $this->db->bind(':product_type', $data['product_type'] ?? 'STANDARD');
            $this->db->bind(':has_expiry', $data['has_expiry'] ?? 0);
            $this->db->bind(':expiry_months', $data['expiry_months'] ?? 0);
            $this->db->bind(':min_Inventory_level', $data['min_Inventory_level']);
            $this->db->bind(':max_Inventory_level', $data['max_Inventory_level']);
            $this->db->bind(':reorder_level', $data['reorder_level']);
            $this->db->bind(':purchase_price', $data['purchase_price'] ?? 0.00);
            $this->db->bind(':selling_price', $data['selling_price'] ?? 0.00);
            $this->db->bind(':profit_margin', $profitMargin);
            $this->db->bind(':weight', $data['weight'] ?? 0.000);
            $this->db->bind(':dimensions', $data['dimensions'] ?? null);
            $this->db->bind(':warranty_period', $data['warranty_period'] ?? 0);
            $this->db->bind(':image_path', $data['image_path']);

            return $this->db->execute();

        } catch (Exception $e) {
            error_log('DB Update Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductById($id)
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name, 
                   b.brand_name, 
                   u.unit_name,
                   COALESCE(SUM(s.quantity), 0) as current_inventory,
                   -- Calculate profit margin correctly using current_average_cost if available, otherwise purchase_price
                   CASE 
                       WHEN p.selling_price > 0 AND COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0) > 0
                       THEN ROUND(((p.selling_price - COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0)) / p.selling_price) * 100, 2)
                       ELSE 0
                   END as calculated_profit_margin
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            WHERE p.product_id = :id
            GROUP BY p.product_id
        ");
        $this->db->bind(':id', $id);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function getProductByBarcode($barcode)
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name, 
                   b.brand_name, 
                   u.unit_name,
                   COALESCE(SUM(s.quantity), 0) as current_inventory,
                   bar.barcode_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            LEFT JOIN barcode bar ON p.product_id = bar.product_id
            WHERE bar.barcode_value = :barcode AND bar.is_active = 1 AND p.is_active = 1
            GROUP BY p.product_id
        ");
        $this->db->bind(':barcode', $barcode);
        $this->db->execute();
        return $this->db->single();
    }

    public function getCurrentInventory($productId)
    {
        $this->db->query("SELECT COALESCE(SUM(quantity), 0) as current_inventory FROM Inventory WHERE product_id = :product_id");
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result->current_inventory : 0;
    }

    public function getInventoryByLocation($productId)
    {
        $this->db->query("
            SELECT s.*, wl.location_code, wl.standardized_address, wl.location_name, wl.location_type
            FROM Inventory s
            LEFT JOIN locations wl ON s.location_id = wl.location_id
            WHERE s.product_id = :product_id AND s.quantity > 0
            ORDER BY wl.standardized_address, wl.location_code
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    public function getLowInventoryProducts()
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name,
                   COALESCE(SUM(s.quantity), 0) as current_Inventory
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id
            HAVING current_Inventory <= p.min_Inventory_level
            ORDER BY current_Inventory ASC
        ");
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    public function getReorderProducts()
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name,
                   sup.supplier_name,
                   COALESCE(SUM(s.quantity), 0) as current_Inventory
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN suppliers sup ON p.supplier_code = sup.supplier_code
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id
            HAVING current_Inventory <= p.reorder_level
            ORDER BY current_Inventory ASC
        ");
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    /**
     * Adjust inventory - proper inventory terminology version
     * @param int $productId
     * @param int $newQuantity
     * @param string $reason
     * @param int $userId
     * @return bool
     */
    public function adjustInventory($productId, $newQuantity, $reason = 'Manual Inventory Adjustment', $userId = null)
    {
        try {
            $this->db->beginTransaction();

            $currentInventory = $this->getCurrentInventory($productId);
            $adjustment = $newQuantity - $currentInventory;

            if ($adjustment != 0) {
                // Insert inventory adjustment
                $this->db->query("
                    INSERT INTO Inventory (product_id, quantity, batch_number) 
                    VALUES (:product_id, :quantity, :batch_number)
                ");
                $this->db->bind(':product_id', $productId);
                $this->db->bind(':quantity', $adjustment);
                $this->db->bind(':batch_number', 'INV_ADJ_' . date('YmdHis'));
                $this->db->execute();

                // Log inventory movement
                $this->logInventoryMovement($productId, 'inventory_adjustment', abs($adjustment), null, null, $reason, $userId);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Inventory Adjustment Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log inventory movement - proper inventory terminology version
     */
    private function logInventoryMovement($productId, $type, $quantity, $fromLocation = null, $toLocation = null, $notes = null, $userId = null)
    {
        $this->db->query("
            INSERT INTO inventory_movements (
                product_id, quantity, from_location_id, to_location_id
            ) VALUES (
                :product_id, :quantity, :from_location_id, :to_location_id
            )
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':from_location_id', $fromLocation);
        $this->db->bind(':to_location_id', $toLocation);
        $this->db->execute();
    }

    private function generateSKU($productName, $categoryId)
    {
        // Simple SKU generation: first 3 letters of product name + category + random number
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $productName), 0, 3));
        $categoryCode = str_pad($categoryId, 2, '0', STR_PAD_LEFT);
        $random = rand(100, 999);
        return $prefix . $categoryCode . $random;
    }

    public function deleteProduct($id)
    {
        // Use standardized soft delete
        return $this->softDelete($id, 'products', 'product_id');
    }

    /**
     * Restore a soft deleted product
     */
    public function restoreProduct($id)
    {
        return $this->restoreDeleted($id, 'products', 'product_id');
    }

    /**
     * Get deleted products for admin recovery
     */
    public function getDeletedProducts()
    {
        return $this->getDeletedRecords('products');
    }

    public function searchProducts($searchTerm, $categoryId = null, $inInventoryOnly = false)
    {
        $whereConditions = ["p.is_active = 1"];
        $params = [];

        if (!empty($searchTerm)) {
            $whereConditions[] = "(p.product_name LIKE :search OR p.sku LIKE :search OR p.supplier_code LIKE :search)";
            $params['search'] = "%$searchTerm%";
        }

        if ($categoryId) {
            $whereConditions[] = "p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $havingClause = "";
        if ($inInventoryOnly) {
            $havingClause = "HAVING current_Inventory > 0";
        }

        $sql = "
            SELECT p.*, 
                   c.category_name, 
                   b.brand_name, 
                   u.unit_name,
                   COALESCE(SUM(s.quantity), 0) as current_Inventory
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            WHERE " . implode(" AND ", $whereConditions) . "
            GROUP BY p.product_id
            $havingClause
            ORDER BY p.product_name ASC
        ";

        $this->db->query($sql);

        foreach ($params as $key => $value) {
            $this->db->bind(":$key", $value);
        }

        return $this->db->resultSet() ?: [];
    }

    public function getCategories()
    {
        $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY category_name");
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    public function addSimpleProduct($data)
    {
        try {
            $this->db->query("
                INSERT INTO products (
                    product_name, sku, category_id, brand_id, unit_id, 
                    min_Inventory_level, max_Inventory_level, reorder_level, image_path, is_active
                ) VALUES (
                    :product_name, :sku, :category_id, :brand_id, :unit_id, 
                    :min_Inventory_level, :max_Inventory_level, :reorder_level, :image_path, :is_active
                )
            ");

            // Bind values
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':unit_id', $data['unit_id']);
            $this->db->bind(':min_Inventory_level', $data['min_Inventory_level']);
            $this->db->bind(':max_Inventory_level', $data['max_Inventory_level']);
            $this->db->bind(':reorder_level', $data['reorder_level']);
            $this->db->bind(':image_path', $data['image_path']);
            $this->db->bind(':is_active', $data['is_active']);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log('Simple Product Insert Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateSimpleProduct($id, $data)
    {
        try {
            $this->db->query("
                UPDATE products SET 
                    product_name = :product_name,
                    sku = :sku,
                    category_id = :category_id,
                    brand_id = :brand_id,
                    unit_id = :unit_id,
                    min_Inventory_level = :min_Inventory_level,
                    max_Inventory_level = :max_Inventory_level,
                    reorder_level = :reorder_level,
                    image_path = :image_path,
                    is_active = :is_active
                WHERE product_id = :product_id
            ");

            // Bind values
            $this->db->bind(':product_id', $id);
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':unit_id', $data['unit_id']);
            $this->db->bind(':min_Inventory_level', $data['min_Inventory_level']);
            $this->db->bind(':max_Inventory_level', $data['max_Inventory_level']);
            $this->db->bind(':reorder_level', $data['reorder_level']);
            $this->db->bind(':image_path', $data['image_path']);
            $this->db->bind(':is_active', $data['is_active']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Simple Product Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get suppliers for a specific product
     * @param int $productId
     * @return array
     */
    public function getProductSuppliers($productId)
    {
        try {
            $this->db->query("
                SELECT ps.*, s.supplier_name, s.contact_person, s.email, s.phone
                FROM product_suppliers ps
                LEFT JOIN suppliers s ON ps.supplier_id = s.supplier_id
                WHERE ps.product_id = :product_id
                AND ps.is_active = 1
                ORDER BY ps.is_primary DESC, s.supplier_name ASC
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error getting product suppliers: " . $e->getMessage());
            return [];
        }
    }

    // ==================== PHASE 3: BARCODE SEARCH METHODS ====================

    /**
     * Search products by barcode for Phase 3 receiving interface
     */
    public function searchByBarcode($barcode)
    {
        try {
            $this->db->query("
                SELECT p.*, 
                       c.category_name, 
                       b.brand_name, 
                       u.unit_name,
                       COALESCE(SUM(inv.quantity), 0) as current_inventory,
                       p.barcode as primary_barcode
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN units u ON p.unit_id = u.unit_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                WHERE (p.barcode = :barcode OR p.product_code = :barcode OR p.sku = :barcode)
                AND p.is_active = 1
                GROUP BY p.product_id
                ORDER BY 
                    CASE 
                        WHEN p.barcode = :barcode THEN 1
                        WHEN p.product_code = :barcode THEN 2
                        WHEN p.sku = :barcode THEN 3
                        ELSE 4
                    END
                LIMIT 10
            ");
            $this->db->bind(':barcode', $barcode);
            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error searching by barcode: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search products by name for Phase 3 interface
     */
    public function searchByName($productName)
    {
        try {
            $this->db->query("
                SELECT p.*, 
                       c.category_name, 
                       b.brand_name,
                       COALESCE(SUM(inv.quantity), 0) as current_inventory
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                WHERE (p.product_name LIKE :product_name 
                       OR p.sku LIKE :product_name 
                       OR p.product_code LIKE :product_name)
                AND p.is_active = 1
                GROUP BY p.product_id
                ORDER BY 
                    CASE 
                        WHEN p.product_name = :exact_name THEN 1
                        WHEN p.product_name LIKE :starts_with THEN 2
                        ELSE 3
                    END,
                    p.product_name ASC
                LIMIT 10
            ");

            $searchTerm = "%{$productName}%";
            $this->db->bind(':product_name', $searchTerm);
            $this->db->bind(':exact_name', $productName);
            $this->db->bind(':starts_with', $productName . '%');

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error searching by name: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find product by barcode (single result)
     */
    public function findByBarcode($barcode)
    {
        try {
            $this->db->query("
                SELECT p.*, 
                       c.category_name, 
                       b.brand_name, 
                       u.unit_name,
                       COALESCE(SUM(inv.quantity), 0) as current_inventory
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN units u ON p.unit_id = u.unit_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                WHERE (p.barcode = :barcode OR p.product_code = :barcode)
                AND p.is_active = 1
                GROUP BY p.product_id
                ORDER BY 
                    CASE 
                        WHEN p.barcode = :barcode THEN 1
                        WHEN p.product_code = :barcode THEN 2
                        ELSE 3
                    END
                LIMIT 1
            ");
            $this->db->bind(':barcode', $barcode);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error finding by barcode: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get product details for receiving interface
     */
    public function getProductForReceiving($productId)
    {
        try {
            $this->db->query("
                SELECT p.*, 
                       c.category_name, 
                       b.brand_name, 
                       u.unit_name,
                       COALESCE(SUM(inv.quantity), 0) as current_inventory,
                       -- Get primary supplier info
                       ps.supplier_id as primary_supplier_id,
                       s.supplier_name as primary_supplier_name,
                       ps.purchase_price as primary_purchase_price,
                       ps.supplier_sku as primary_supplier_sku
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN units u ON p.unit_id = u.unit_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                LEFT JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_primary = 1
                LEFT JOIN suppliers s ON ps.supplier_id = s.supplier_id
                WHERE p.product_id = :product_id
                AND p.is_active = 1
                GROUP BY p.product_id
            ");
            $this->db->bind(':product_id', $productId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error getting product for receiving: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Search products with enhanced relevance ranking for smart interface
     */
    public function smartSearchProducts($query, $limit = 10)
    {
        try {
            $this->db->query("
                SELECT p.*, 
                       c.category_name, 
                       b.brand_name,
                       COALESCE(SUM(inv.quantity), 0) as current_inventory,
                       -- Relevance scoring
                       CASE 
                           WHEN p.barcode = :exact_query THEN 100
                           WHEN p.product_code = :exact_query THEN 95
                           WHEN p.sku = :exact_query THEN 90
                           WHEN p.product_name = :exact_query THEN 85
                           WHEN p.product_name LIKE :starts_with THEN 80
                           WHEN p.barcode LIKE :starts_with THEN 75
                           WHEN p.product_code LIKE :starts_with THEN 70
                           WHEN p.product_name LIKE :contains THEN 60
                           ELSE 50
                       END as relevance_score
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                WHERE (
                    p.product_name LIKE :contains
                    OR p.sku LIKE :contains
                    OR p.product_code LIKE :contains
                    OR p.barcode LIKE :contains
                    OR c.category_name LIKE :contains
                    OR b.brand_name LIKE :contains
                )
                AND p.is_active = 1
                GROUP BY p.product_id
                ORDER BY relevance_score DESC, p.product_name ASC
                LIMIT :limit
            ");

            $searchTerm = "%{$query}%";
            $this->db->bind(':exact_query', $query);
            $this->db->bind(':starts_with', $query . '%');
            $this->db->bind(':contains', $searchTerm);
            $this->db->bind(':limit', $limit);

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error in smart search: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recently received products for suggestions
     */
    public function getRecentlyReceivedProducts($limit = 5)
    {
        try {
            $this->db->query("
                SELECT DISTINCT p.*, 
                       c.category_name,
                       COALESCE(SUM(inv.quantity), 0) as current_inventory,
                       MAX(inv.created_at) as last_received
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                WHERE p.is_active = 1
                AND inv.movement_type = 'received'
                AND inv.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY p.product_id
                ORDER BY last_received DESC
                LIMIT :limit
            ");
            $this->db->bind(':limit', $limit);
            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error getting recently received products: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Validate barcode format
     */
    public function isValidBarcode($barcode)
    {
        // Basic barcode validation
        if (empty($barcode) || strlen($barcode) < 6) {
            return false;
        }

        // Check if barcode contains only valid characters (numbers and letters)
        if (!preg_match('/^[A-Za-z0-9]+$/', $barcode)) {
            return false;
        }

        return true;
    }

    /**
     * Generate product suggestions based on partial input
     */
    public function getProductSuggestions($partial, $limit = 5)
    {
        try {
            $this->db->query("
                SELECT p.product_id, p.product_name, p.product_code, p.barcode, p.sku,
                       c.category_name,
                       COALESCE(SUM(inv.quantity), 0) as current_inventory
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                WHERE (
                    p.product_name LIKE :partial
                    OR p.product_code LIKE :partial
                    OR p.sku LIKE :partial
                    OR p.barcode LIKE :partial
                )
                AND p.is_active = 1
                GROUP BY p.product_id
                ORDER BY 
                    CASE 
                        WHEN p.product_name LIKE :starts_with THEN 1
                        WHEN p.product_code LIKE :starts_with THEN 2
                        ELSE 3
                    END,
                    p.product_name ASC
                LIMIT :limit
            ");

            $searchTerm = "%{$partial}%";
            $this->db->bind(':partial', $searchTerm);
            $this->db->bind(':starts_with', $partial . '%');
            $this->db->bind(':limit', $limit);

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error getting product suggestions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get product statistics
     * @param int $productId
     * @return object
     */
    public function getProductStats($productId)
    {
        // This is a placeholder for product statistics
        // You can implement actual statistics based on your purchase/sales data
        $stats = new stdClass();
        $stats->total_sold = 0;
        $stats->revenue = 0;
        $stats->total_purchased = 0;
        $stats->turnover_rate = 0;

        // Try to get actual stats if you have sales/purchase tables
        try {
            $this->db->query("
                SELECT 
                    COALESCE(SUM(pi.quantity), 0) as total_purchased,
                    COALESCE(SUM(pi.quantity * pi.unit_price), 0) as total_purchase_value
                FROM purchase_items pi
                WHERE pi.product_id = :product_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $purchaseStats = $this->db->single();

            if ($purchaseStats) {
                $stats->total_purchased = $purchaseStats->total_purchased;
            }
        } catch (Exception $e) {
            error_log('Error getting product stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get products with all their suppliers for multi-supplier purchasing
     * Returns each product-supplier combination as a separate entry
     */
    public function getProductsWithAllSuppliers()
    {
        $this->db->query(<<<'SQL'
            SELECT
                p.product_id,
                p.product_name,
                p.sku,
                p.category_id,
                c.category_name,
                ps.supplier_id,
                s.supplier_name,
                ps.purchase_price AS supplier_price,
                ps.lead_time_days,
                ps.min_order_quantity,
                ps.is_primary,
                s.reliability_score,
                s.on_time_delivery_rate,
                s.average_delivery_days,
                COALESCE(SUM(inv.quantity), 0) AS current_inventory
            FROM products p
            JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1
            JOIN suppliers s ON ps.supplier_id = s.supplier_id AND s.deleted_at IS NULL
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN inventory inv ON p.product_id = inv.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id, p.product_name, p.sku, p.category_id, c.category_name, 
                     ps.supplier_id, s.supplier_name, ps.purchase_price, ps.lead_time_days, 
                     ps.min_order_quantity, ps.is_primary, s.reliability_score, 
                     s.on_time_delivery_rate, s.average_delivery_days
            ORDER BY p.product_name ASC, ps.purchase_price ASC
            LIMIT 2000
        SQL
        );
        $this->db->execute();
        $rows = $this->db->resultSet();
        if (!$rows) {
            return [];
        }
        return array_map(function ($r) {
            return (object) $r;
        }, $rows);
    }

    /**
     * Update supplier price for a product
     */
    public function updateSupplierPrice($productId, $supplierId, $newPrice)
    {
        try {
            $this->db->query("
                UPDATE product_suppliers 
                SET purchase_price = :new_price, updated_at = NOW()
                WHERE product_id = :product_id AND supplier_id = :supplier_id
            ");

            $this->db->bind(':product_id', $productId);
            $this->db->bind(':supplier_id', $supplierId);
            $this->db->bind(':new_price', $newPrice);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error updating supplier price: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set primary supplier for a product
     */
    public function setPrimarySupplier($productId, $supplierId)
    {
        try {
            $this->db->beginTransaction();

            // First, remove primary status from all suppliers for this product
            $this->db->query("
                UPDATE product_suppliers 
                SET is_primary = 0, updated_at = NOW()
                WHERE product_id = :product_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();

            // Then set the new primary supplier
            $this->db->query("
                UPDATE product_suppliers 
                SET is_primary = 1, updated_at = NOW()
                WHERE product_id = :product_id AND supplier_id = :supplier_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':supplier_id', $supplierId);

            $success = $this->db->execute();
            if ($success) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Error setting primary supplier: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update product price
     */
    public function updateProductPrice($productId, $newPrice)
    {
        try {
            $this->db->query("
                UPDATE products 
                SET selling_price = :price, updated_at = NOW() 
                WHERE product_id = :product_id AND deleted_at IS NULL
            ");

            $this->db->bind(':price', $newPrice);
            $this->db->bind(':product_id', $productId);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error updating product price: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get product price by ID
     */
    public function getProductPrice($productId)
    {
        try {
            $this->db->query("
                SELECT price 
                FROM products 
                WHERE product_id = :product_id AND deleted_at IS NULL
            ");

            $this->db->bind(':product_id', $productId);
            $result = $this->db->single();

            return $result ? floatval($result->price) : 0;
        } catch (Exception $e) {
            error_log('Error getting product price: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get price history for a product (simplified version)
     */
    public function getPriceHistory($productId)
    {
        try {
            // For now, return the current price as history
            // In a full implementation, you'd have a price_history table
            $this->db->query("
                SELECT 
                    updated_at as date,
                    price,
                    'System' as user,
                    'Price update' as reason
                FROM products 
                WHERE product_id = :product_id AND deleted_at IS NULL
                ORDER BY updated_at DESC
                LIMIT 10
            ");

            $this->db->bind(':product_id', $productId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error getting price history: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get price management statistics
     */
    public function getPriceManagementStats()
    {
        try {
            // First, get basic product stats
            $this->db->query("
                SELECT 
                    COUNT(*) as total_products,
                    AVG(
                        CASE 
                            WHEN price > 0 AND cost > 0 
                            THEN ((price - cost) / price) * 100 
                            ELSE 0 
                        END
                    ) as average_margin,
                    COUNT(
                        CASE 
                            WHEN price > 0 AND cost > 0 AND ((price - cost) / price) * 100 < 15 
                            THEN 1 
                        END
                    ) as low_margin_products
                FROM products 
                WHERE deleted_at IS NULL
            ");

            $basicStats = $this->db->single();

            // Calculate total gross margin (weighted by sales volume)
            $this->db->query("
                SELECT 
                    SUM(COALESCE(sales_data.monthly_revenue, 0)) as total_revenue,
                    SUM(COALESCE(sales_data.monthly_cost, 0)) as total_cost
                FROM products p
                LEFT JOIN (
                    SELECT 
                        si.product_id,
                        SUM(si.quantity * si.unit_price) / 3 as monthly_revenue,
                        SUM(si.quantity * p2.cost) / 3 as monthly_cost
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.sale_id
                    JOIN products p2 ON si.product_id = p2.product_id
                    WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                    AND p2.cost > 0
                    GROUP BY si.product_id
                ) sales_data ON p.product_id = sales_data.product_id
                WHERE p.deleted_at IS NULL
            ");

            $grossMarginData = $this->db->single();

            $totalRevenue = floatval($grossMarginData->total_revenue ?? 0);
            $totalCost = floatval($grossMarginData->total_cost ?? 0);
            $totalGrossMargin = 0;

            if ($totalRevenue > 0) {
                $totalGrossMargin = (($totalRevenue - $totalCost) / $totalRevenue) * 100;
            }

            return [
                'total_products' => intval($basicStats->total_products ?? 0),
                'average_margin' => floatval($basicStats->average_margin ?? 0),
                'low_margin_products' => intval($basicStats->low_margin_products ?? 0),
                'total_gross_margin' => $totalGrossMargin
            ];
        } catch (Exception $e) {
            error_log('Error getting price management stats: ' . $e->getMessage());
            return [
                'total_products' => 0,
                'average_margin' => 0,
                'low_margin_products' => 0,
                'total_gross_margin' => 0
            ];
        }
    }

    /**
     * Get products for the price management page with optional filters
     * Expected filters: category (string), price_range (e.g. "0-10", "500+"),
     * stock_status (in-stock, low-stock, out-of-stock), margin_filter (low, medium, high)
     * Returns array of objects with fields used by the view (product_id, name, description, sku,
     * category, price, cost, stock_quantity, image_path, price_updated_at)
     */
    /**
     * $filters supports:
     * - category (string name) OR category_id (int)
     * - price_range ("0-10" or "500+")
     * - stock_status (in-stock, low-stock, out-of-stock)
     * - margin_filter (low, medium, high)
     * - offset (int)
     * - limit (int)
     */
    public function getProductsForPriceManagement($filters = [])
    {
        try {
            // Build filters
            $where = "WHERE p.is_active = 1 AND p.deleted_at IS NULL";
            $params = [];

            // Category filter
            if (!empty($filters['category'])) {
                $where .= " AND c.category_name LIKE :category";
                $params[':category'] = '%' . $filters['category'] . '%';
            }

            // Price range filter
            if (!empty($filters['price_range'])) {
                $pr = $filters['price_range'];
                if (strpos($pr, '+') !== false) {
                    $min = (float) rtrim($pr, '+');
                    $where .= " AND COALESCE(p.selling_price,0) >= :price_min";
                    $params[':price_min'] = $min;
                } elseif (strpos($pr, '-') !== false) {
                    list($min, $max) = explode('-', $pr);
                    $where .= " AND COALESCE(p.selling_price,0) BETWEEN :price_min AND :price_max";
                    $params[':price_min'] = (float) $min;
                    $params[':price_max'] = (float) $max;
                }
            }

            // Pagination
            $offset = isset($filters['offset']) ? max(0, (int) $filters['offset']) : 0;
            $limit = isset($filters['limit']) ? max(1, (int) $filters['limit']) : 200;

            // Dual-cost system: separate purchase price and current average cost
            $sql = "
                SELECT
                    p.product_id,
                    p.product_name AS name,
                    p.sku,
                    COALESCE(c.category_name, 'Uncategorized') AS category_name,
                    COALESCE(p.selling_price, 0) AS price,
                    COALESCE(p.current_average_cost, p.purchase_price, ps.purchase_price, 0) AS cost,
                    p.image_path,
                    p.updated_at AS price_updated_at,
                    COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) AS stock_quantity,
                    COALESCE(sales_data.total_sold, 0) AS total_sold,
                    COALESCE(sales_data.sales_rank, 9999) AS sales_rank,
                    COALESCE(sales_data.total_revenue, 0) AS total_revenue,
                    COALESCE(p.purchase_price, 0) as base_purchase_price,
                    COALESCE(p.current_average_cost, 0) as current_average_cost,
                    COALESCE(ps.purchase_price, 0) as supplier_cost
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN (
                    SELECT 
                        ps.product_id,
                        MIN(ps.purchase_price) as purchase_price
                    FROM product_suppliers ps
                    WHERE ps.is_active = 1
                    GROUP BY ps.product_id
                ) ps ON p.product_id = ps.product_id
                LEFT JOIN (
                    SELECT 
                        si.product_id,
                        SUM(si.quantity) as total_sold,
                        SUM(si.quantity * si.unit_price) as total_revenue,
                        DENSE_RANK() OVER (ORDER BY SUM(si.quantity) DESC) as sales_rank
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.sale_id
                    WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                    GROUP BY si.product_id
                ) sales_data ON p.product_id = sales_data.product_id
                {$where}
                ORDER BY p.product_name ASC
                LIMIT :offset, :limit
            ";

            $this->db->query($sql);

            // Bind parameters
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }
            $this->db->bind(':offset', $offset, \PDO::PARAM_INT);
            $this->db->bind(':limit', $limit, \PDO::PARAM_INT);

            $this->db->execute();
            $rows = $this->db->resultSet();

            return $rows ? $rows : [];
        } catch (Exception $e) {
            error_log('Error getting products for price management: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get products by IDs for export
     */
    public function getProductsByIds($productIds)
    {
        try {
            if (empty($productIds)) {
                return [];
            }

            $placeholders = str_repeat('?,', count($productIds) - 1) . '?';

            $this->db->query("
                SELECT 
                    product_id,
                    sku,
                    name,
                    category,
                    price,
                    cost,
                    stock_quantity
                FROM products 
                WHERE product_id IN ({$placeholders}) AND deleted_at IS NULL
                ORDER BY name ASC
            ");

            foreach ($productIds as $index => $id) {
                $this->db->bind($index + 1, intval($id));
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error getting products by IDs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all products for export
     */
    public function getAllProductsForExport()
    {
        try {
            $this->db->query("
                SELECT 
                    product_id,
                    sku,
                    name,
                    category,
                    price,
                    cost,
                    stock_quantity
                FROM products 
                WHERE deleted_at IS NULL
                ORDER BY name ASC
            ");

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error getting all products for export: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate weighted average price for a product
     * Takes into account current inventory and their purchase prices
     */
    public function calculateWeightedAveragePrice($productId, $newQuantity, $newPrice)
    {
        try {
            // Get current inventory quantity (sum all inventory records for this product)
            $this->db->query("
                SELECT 
                    COALESCE(SUM(quantity), 0) as current_stock
                FROM inventory 
                WHERE product_id = :product_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $inventoryData = $this->db->single();

            $currentStock = $inventoryData ? $inventoryData->current_stock : 0;

            // Get the current average cost from multiple sources (in order of preference):
            // 1. Current purchase_price from products table (master inventory cost)
            // 2. Recent weighted average from last purchase
            // 3. Average of recent purchase prices
            // 4. Fallback to new price

            $currentAvgPrice = 0;

            // Try products table purchase_price first (this is the "official" inventory cost)
            $this->db->query("
                SELECT purchase_price 
                FROM products 
                WHERE product_id = :product_id AND purchase_price > 0
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $productData = $this->db->single();

            if ($productData && $productData->purchase_price > 0) {
                $currentAvgPrice = $productData->purchase_price;
            } else {
                // Fallback to recent purchase price (most recent actual cost)
                $this->db->query("
                    SELECT unit_price 
                    FROM purchase_items 
                    WHERE product_id = :product_id 
                    ORDER BY purchase_item_id DESC 
                    LIMIT 1
                ");
                $this->db->bind(':product_id', $productId);
                $this->db->execute();
                $recentPurchase = $this->db->single();

                if ($recentPurchase && $recentPurchase->unit_price > 0) {
                    $currentAvgPrice = $recentPurchase->unit_price;
                } else {
                    // Final fallback: use the new price as estimate for current stock
                    $currentAvgPrice = $newPrice;
                }
            }

            if ($currentStock <= 0) {
                // No existing inventory, return new price
                return [
                    'new_average_price' => $newPrice,
                    'calculation' => [
                        'current_stock' => 0,
                        'current_avg_price' => 0,
                        'current_total_value' => 0,
                        'new_quantity' => $newQuantity,
                        'new_price' => $newPrice,
                        'new_total_value' => $newQuantity * $newPrice,
                        'total_stock_after' => $newQuantity,
                        'total_value_after' => $newQuantity * $newPrice
                    ]
                ];
            }

            // Calculate weighted average
            $currentTotalValue = $currentStock * $currentAvgPrice;
            $newTotalValue = $newQuantity * $newPrice;
            $totalStockAfter = $currentStock + $newQuantity;
            $totalValueAfter = $currentTotalValue + $newTotalValue;

            $newAveragePrice = $totalStockAfter > 0 ? $totalValueAfter / $totalStockAfter : 0;

            return [
                'new_average_price' => round($newAveragePrice, 2),
                'calculation' => [
                    'current_stock' => $currentStock,
                    'current_avg_price' => round($currentAvgPrice, 2),
                    'current_total_value' => round($currentTotalValue, 2),
                    'new_quantity' => $newQuantity,
                    'new_price' => $newPrice,
                    'new_total_value' => round($newTotalValue, 2),
                    'total_stock_after' => $totalStockAfter,
                    'total_value_after' => round($totalValueAfter, 2)
                ]
            ];

        } catch (Exception $e) {
            error_log('Error calculating weighted average price: ' . $e->getMessage());
            return [
                'new_average_price' => $newPrice,
                'calculation' => [
                    'error' => 'Could not calculate weighted average: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Get current inventory summary for a product
     */
    public function getCurrentInventorySummary($productId)
    {
        try {
            $this->db->query("
                SELECT 
                    p.product_name,
                    COALESCE(SUM(inv.quantity), 0) as current_stock,
                    COALESCE(AVG(pi.unit_price), 0) as avg_purchase_price,
                    COALESCE(MIN(pi.unit_price), 0) as min_purchase_price,
                    COALESCE(MAX(pi.unit_price), 0) as max_purchase_price
                FROM products p
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                LEFT JOIN purchase_items pi ON p.product_id = pi.product_id
                WHERE p.product_id = :product_id
                GROUP BY p.product_id, p.product_name
            ");

            $this->db->bind(':product_id', $productId);
            $this->db->execute();

            return $this->db->single();

        } catch (Exception $e) {
            error_log('Error getting inventory summary: ' . $e->getMessage());
            return null;
        }
    }

    // =============== BOT HELPER METHODS ===============

    /**
     * Get total products count for bot dashboard
     */
    public function getTotalProducts()
    {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1 AND deleted_at IS NULL");
            $result = $this->db->executeSingle();
            return $result->total ?? 0;
        } catch (Exception $e) {
            error_log('Error getting total products: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get random products for bot operations
     */
    public function getRandomProducts($limit = 5)
    {
        try {
            $this->db->query("
                SELECT product_id, product_name, sku, purchase_price as cost, selling_price as price
                FROM products 
                WHERE is_active = 1 AND deleted_at IS NULL 
                ORDER BY RAND() 
                LIMIT :limit
            ");
            $this->db->bind(':limit', $limit);
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error getting random products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get random products that are in stock for sales bot
     */
    public function getRandomProductsInStock($limit = 5)
    {
        try {
            $this->db->query("
                SELECT p.product_id, p.product_name, p.sku, p.purchase_price as cost, p.selling_price as price, i.quantity as stock_quantity
                FROM products p
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.is_active = 1 AND p.deleted_at IS NULL 
                AND COALESCE(i.quantity, 0) > 0
                ORDER BY RAND() 
                LIMIT :limit
            ");
            $this->db->bind(':limit', $limit);
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error getting random products in stock: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get products with profit margin calculation for smart sales bot
     */
    public function getProductsWithProfitMargins($limit = 10)
    {
        try {
            // Use current average cost / primary purchase price / unit price / purchase_price
            // and aggregate inventory quantities across locations to match UI calculations.
            $this->db->query("
                SELECT p.product_id as product_id, p.product_id as id,
                       p.product_name,
                       p.sku,
                       p.purchase_price,
                       p.current_average_cost,
                       p.selling_price,
                       COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as current_stock,
                       -- treat 0 as NULL for current_average_cost so fallback to purchase_price works
                       COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0) as cost,
                       CASE
                           WHEN p.selling_price > 0
                           THEN ROUND(((p.selling_price - COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0)) / p.selling_price) * 100, 2)
                           ELSE 0
                       END as profit_margin_percent,
                       (p.selling_price - COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0)) as profit_amount
                FROM products p
                WHERE p.is_active = 1
                AND p.deleted_at IS NULL
                AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) > 0
                AND p.selling_price > 0
                AND COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0) > 0
                ORDER BY profit_margin_percent DESC, RAND()
                LIMIT :limit
            ");
            $this->db->bind(':limit', $limit);
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error getting products with profit margins: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get KPI statistics for all products (not paginated)
     * Used for dashboard KPI cards that should show all products data
     */
    public function getAllProductsKpiStats()
    {
        try {
            $stats = [];

            // Total products count
            $this->db->query("
                SELECT COUNT(*) as total_products
                FROM products p
                WHERE p.is_active = 1 AND p.deleted_at IS NULL
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['total_products'] = $result ? (int) $result->total_products : 0;

            // Total inventory count (sum all inventory)
            $this->db->query("
                SELECT COALESCE(SUM(i.quantity), 0) as total_inventory
                FROM inventory i
                INNER JOIN products p ON i.product_id = p.product_id
                WHERE p.is_active = 1 AND p.deleted_at IS NULL
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['total_inventory'] = $result ? (int) $result->total_inventory : 0;

            // Average margin calculation (across all products with valid prices)
            $this->db->query("
                SELECT AVG(
                    CASE 
                        WHEN p.selling_price > 0 AND 
                             COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0) > 0
                        THEN ((p.selling_price - COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0)) / p.selling_price) * 100
                        ELSE NULL
                    END
                ) as avg_margin
                FROM products p
                WHERE p.is_active = 1 AND p.deleted_at IS NULL
                AND p.selling_price > 0
                AND COALESCE(NULLIF(p.current_average_cost, 0), p.purchase_price, 0) > 0
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['avg_margin'] = $result && $result->avg_margin !== null ? (float) $result->avg_margin : null;

            // Products needing reorder (across all products)
            $this->db->query("
                SELECT COUNT(*) as need_reorder
                FROM products p
                LEFT JOIN (
                    SELECT product_id, SUM(quantity) as total_quantity 
                    FROM inventory 
                    GROUP BY product_id
                ) i ON p.product_id = i.product_id
                WHERE p.is_active = 1 AND p.deleted_at IS NULL
                AND COALESCE(i.total_quantity, 0) <= COALESCE(p.reorder_level, 10)
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['need_reorder'] = $result ? (int) $result->need_reorder : 0;

            return $stats;
        } catch (Exception $e) {
            error_log('Error getting all products KPI stats: ' . $e->getMessage());
            return [
                'total_products' => 0,
                'total_inventory' => 0,
                'avg_margin' => null,
                'need_reorder' => 0
            ];
        }
    }

    /**
     * Check if a product-supplier relationship already exists (including inactive ones)
     */
    public function getProductSupplierLink($productId, $supplierId)
    {
        try {
            $this->db->query("
                SELECT * FROM product_suppliers 
                WHERE product_id = :product_id AND supplier_id = :supplier_id
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':supplier_id', $supplierId);
            $this->db->execute();
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error checking product supplier link: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Link a supplier to a product (insert or update if exists)
     */
    public function linkSupplier($productId, $supplierId, $purchasePrice, $leadTimeDays = 7, $minOrderQuantity = 1, $supplierSku = null, $supplierNotes = '', $supplierRating = 4, $isActive = 1)
    {
        try {
            // Log the parameters being passed
            error_log("LinkSupplier parameters: productId=$productId, supplierId=$supplierId, purchasePrice=$purchasePrice");

            // Check if a relationship already exists (including inactive ones)
            $existingLink = $this->getProductSupplierLink($productId, $supplierId);

            if ($existingLink) {
                // Update existing record
                error_log("LinkSupplier: Updating existing relationship");
                $this->db->query("
                    UPDATE product_suppliers SET 
                        supplier_sku = :supplier_sku,
                        purchase_price = :purchase_price,
                        min_order_quantity = :min_order_quantity,
                        lead_time_days = :lead_time_days,
                        notes = :notes,
                        quality_rating = :quality_rating,
                        is_active = :is_active,
                        updated_at = NOW()
                    WHERE product_id = :product_id AND supplier_id = :supplier_id
                ");

                $this->db->bind(':product_id', (int) $productId);
                $this->db->bind(':supplier_id', (int) $supplierId);
                $this->db->bind(':supplier_sku', $supplierSku);
                $this->db->bind(':purchase_price', (float) $purchasePrice);
                $this->db->bind(':min_order_quantity', (int) $minOrderQuantity);
                $this->db->bind(':lead_time_days', (int) $leadTimeDays);
                $this->db->bind(':notes', $supplierNotes);
                $this->db->bind(':quality_rating', (float) $supplierRating);
                $this->db->bind(':is_active', (int) $isActive);
            } else {
                // Insert new record
                error_log("LinkSupplier: Inserting new relationship");
                $this->db->query("
                    INSERT INTO product_suppliers (
                        product_id, supplier_id, supplier_sku, purchase_price, 
                        min_order_quantity, lead_time_days, notes, quality_rating, is_active
                    ) VALUES (
                        :product_id, :supplier_id, :supplier_sku, :purchase_price, 
                        :min_order_quantity, :lead_time_days, :notes, :quality_rating, :is_active
                    )
                ");

                $this->db->bind(':product_id', (int) $productId);
                $this->db->bind(':supplier_id', (int) $supplierId);
                $this->db->bind(':supplier_sku', $supplierSku);
                $this->db->bind(':purchase_price', (float) $purchasePrice);
                $this->db->bind(':min_order_quantity', (int) $minOrderQuantity);
                $this->db->bind(':lead_time_days', (int) $leadTimeDays);
                $this->db->bind(':notes', $supplierNotes);
                $this->db->bind(':quality_rating', (float) $supplierRating);
                $this->db->bind(':is_active', (int) $isActive);
            }

            $result = $this->db->execute();
            error_log("LinkSupplier execute result: " . ($result ? 'success' : 'failed'));

            return $result;
        } catch (Exception $e) {
            error_log("Error linking supplier to product: " . $e->getMessage());
            error_log("LinkSupplier exception stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Unlink a supplier from a product (soft delete by setting is_active = 0)
     * @param int $productId
     * @param int $supplierId
     * @return bool
     */
    public function unlinkSupplier($productId, $supplierId)
    {
        try {
            // Log the parameters being passed
            error_log("UnlinkSupplier parameters: productId=$productId, supplierId=$supplierId");

            // Check if the relationship exists
            $existingLink = $this->getProductSupplierLink($productId, $supplierId);

            if (!$existingLink) {
                error_log("UnlinkSupplier: No relationship found between product $productId and supplier $supplierId");
                return false;
            }

            // Soft delete by setting is_active = 0
            $this->db->query("
                UPDATE product_suppliers SET 
                    is_active = 0,
                    updated_at = NOW()
                WHERE product_id = :product_id AND supplier_id = :supplier_id
            ");

            $this->db->bind(':product_id', (int) $productId);
            $this->db->bind(':supplier_id', (int) $supplierId);

            $result = $this->db->execute();
            error_log("UnlinkSupplier execute result: " . ($result ? 'success' : 'failed'));

            return $result;
        } catch (Exception $e) {
            error_log("Error unlinking supplier from product: " . $e->getMessage());
            error_log("UnlinkSupplier exception stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
}
?>