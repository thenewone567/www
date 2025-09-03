<?php
class Dashboard
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Sales Performance Methods
    public function getTotalSales($days = 30)
    {
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getSalesGrowth($days = 30)
    {
        // Current period
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $current = $this->db->single();
        $currentTotal = $current && $current->total !== null ? $current->total : 0;

        // Previous period
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL :days2 DAY) AND sale_date < DATE_SUB(CURDATE(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days);
        $this->db->bind(':days2', $days * 2);
        $this->db->execute();
        $previous = $this->db->single();
        $previousTotal = $previous && $previous->total !== null ? $previous->total : 0;

        if ($previousTotal == 0)
            return 0;
        return round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1);
    }

    public function getAverageTransactionValue($days = 30)
    {
        $this->db->query("SELECT AVG(total_amount) as avg_value FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->avg_value !== null ? $result->avg_value : 0;
    }

    public function getTotalTransactions($days = 30)
    {
        $this->db->query("SELECT COUNT(*) as total FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getSalesByCategory($days = 30)
    {
        $this->db->query("
            SELECT c.category_name, SUM(si.quantity * si.unit_price) as total_sales
            FROM sale_items si
            JOIN products p ON si.product_id = p.product_id
            JOIN categories c ON p.category_id = c.category_id
            JOIN sales s ON si.sale_id = s.sale_id
            WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY c.category_id, c.category_name
            ORDER BY total_sales DESC
            LIMIT 10
        ");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getTopSellingProducts($limit = 5, $days = 30)
    {
        $this->db->query("
            SELECT p.product_name, c.category_name, SUM(si.quantity) as total_quantity, 
                   SUM(si.quantity * si.unit_price) as total_revenue
            FROM sale_items si 
            JOIN products p ON si.product_id = p.product_id 
            LEFT JOIN categories c ON p.category_id = c.category_id
            JOIN sales s ON si.sale_id = s.sale_id
            WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY si.product_id, p.product_name, c.category_name
            ORDER BY total_quantity DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    // Inventory Management Methods
    public function getTotalInventoryValue()
    {
        $this->db->query("
            SELECT SUM(COALESCE(i.quantity, 0) * COALESCE(p.current_average_cost, p.purchase_price, 0)) as total_value
            FROM products p
            LEFT JOIN inventory i ON p.product_id = i.product_id
            WHERE p.is_active = 1 AND COALESCE(i.quantity, 0) > 0
        ");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total_value !== null ? $result->total_value : 0;
    }

    public function getTotalProducts()
    {
        $this->db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1 AND deleted_at IS NULL");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getLowInventoryProducts($limit = 10)
    {
        $this->db->query("
            SELECT p.product_id, p.product_name, p.sku, 
                   COALESCE(SUM(i.quantity), 0) as current_inventory,
                   p.min_Inventory_level, p.reorder_level, c.category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN inventory i ON p.product_id = i.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id, p.product_name, p.sku, p.min_Inventory_level, p.reorder_level, c.category_name
            HAVING COALESCE(SUM(i.quantity), 0) <= COALESCE(p.reorder_level, 10)
            ORDER BY (COALESCE(SUM(i.quantity), 0) / NULLIF(COALESCE(p.reorder_level, 10), 1)) ASC
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getLowInventoryCount()
    {
        $this->db->query("
            SELECT COUNT(p.product_id) as total 
            FROM products p
            LEFT JOIN (
                SELECT product_id, SUM(quantity) as total_qty 
                FROM inventory 
                GROUP BY product_id
            ) inv ON p.product_id = inv.product_id
            WHERE p.is_active = 1
            AND p.deleted_at IS NULL
            AND COALESCE(inv.total_qty, 0) > 0
            AND COALESCE(inv.total_qty, 0) <= COALESCE(p.reorder_level, 10)
        ");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getOutOfInventoryCount()
    {
        $this->db->query("
            SELECT COUNT(p.product_id) as total 
            FROM products p
            LEFT JOIN (
                SELECT product_id, SUM(quantity) as total_qty 
                FROM inventory 
                GROUP BY product_id
            ) inv ON p.product_id = inv.product_id
            WHERE p.is_active = 1
            AND p.deleted_at IS NULL
            AND COALESCE(inv.total_qty, 0) <= 0
        ");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getOutOfInventoryPercentage()
    {
        $total = $this->getTotalProducts();
        if ($total == 0)
            return 0;
        $outOfInventory = $this->getOutOfInventoryCount();
        return round(($outOfInventory / $total) * 100, 1);
    }

    public function getLowInventoryByCategory()
    {
        $this->db->query("
            SELECT c.category_name, COUNT(p.product_id) as low_Inventory_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN (
                SELECT product_id, COALESCE(SUM(quantity), 0) as current_inventory 
                FROM inventory 
                GROUP BY product_id
            ) inv ON p.product_id = inv.product_id
            WHERE COALESCE(inv.current_inventory, 0) <= p.reorder_level AND p.is_active = 1
            GROUP BY c.category_id, c.category_name
            ORDER BY low_Inventory_count DESC
        ");
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    /**
     * Get product counts grouped by price ranges
     * Returns array with buckets: 0-500, 500-2000, 2000-5000, 5000-10000, 10000+
     */
    public function getPriceRangeDistribution()
    {
        $this->db->query("
            SELECT
                SUM(CASE WHEN COALESCE(p.selling_price,0) <= 500 THEN 1 ELSE 0 END) AS r1,
                SUM(CASE WHEN COALESCE(p.selling_price,0) > 500 AND COALESCE(p.selling_price,0) <= 2000 THEN 1 ELSE 0 END) AS r2,
                SUM(CASE WHEN COALESCE(p.selling_price,0) > 2000 AND COALESCE(p.selling_price,0) <= 5000 THEN 1 ELSE 0 END) AS r3,
                SUM(CASE WHEN COALESCE(p.selling_price,0) > 5000 AND COALESCE(p.selling_price,0) <= 10000 THEN 1 ELSE 0 END) AS r4,
                SUM(CASE WHEN COALESCE(p.selling_price,0) > 10000 THEN 1 ELSE 0 END) AS r5
            FROM products p
            WHERE p.is_active = 1 AND (p.deleted_at IS NULL)
        ");
        $this->db->execute();
        $row = $this->db->single();
        if (!$row)
            return [0, 0, 0, 0, 0];
        return [(int) $row->r1, (int) $row->r2, (int) $row->r3, (int) $row->r4, (int) $row->r5];
    }

    /**
     * Inventory Status Distribution - counts for In Stock, Low Stock, Out of Stock, Reorder Level
     */
    public function getInventoryStatusDistribution()
    {
        // We'll compute per product current inventory
        $this->db->query("
            SELECT
                SUM(CASE WHEN COALESCE(inv.total_qty,0) > COALESCE(p.reorder_level,0) THEN 1 ELSE 0 END) AS in_stock,
                SUM(CASE WHEN COALESCE(inv.total_qty,0) > 0 AND COALESCE(inv.total_qty,0) <= COALESCE(p.reorder_level,0) THEN 1 ELSE 0 END) AS low_stock,
                SUM(CASE WHEN COALESCE(inv.total_qty,0) <= 0 THEN 1 ELSE 0 END) AS out_of_stock,
                SUM(CASE WHEN COALESCE(inv.total_qty,0) = COALESCE(p.reorder_level,0) THEN 1 ELSE 0 END) AS reorder_level
            FROM products p
            LEFT JOIN (SELECT product_id, SUM(quantity) as total_qty FROM inventory GROUP BY product_id) inv ON p.product_id = inv.product_id
            WHERE p.is_active = 1 AND (p.deleted_at IS NULL)
        ");
        $this->db->execute();
        $row = $this->db->single();
        if (!$row)
            return [0, 0, 0, 0];
        return [(int) $row->in_stock, (int) $row->low_stock, (int) $row->out_of_stock, (int) $row->reorder_level];
    }

    // Customer Analytics Methods
    public function getNewCustomers($days = 30)
    {
        // Count customers whose first-ever sale falls within the last :days days.
        // Use a grouped derived table to avoid a correlated subquery per row which can be very slow on large sales tables.
        // Ensure there is an index on (customer_id, sale_date) on the sales table for best performance.
        $this->db->query("\n            SELECT COUNT(*) as total FROM (\n                SELECT customer_id, MIN(sale_date) as first_sale\n                FROM sales\n                GROUP BY customer_id\n                HAVING MIN(sale_date) >= DATE_SUB(CURDATE(), INTERVAL :days DAY)\n            ) first_sales\n            JOIN customers c ON c.customer_id = first_sales.customer_id\n            WHERE c.status = 'active'\n        ");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getCustomerTransactionTrend($days = 30)
    {
        $this->db->query("
            SELECT DATE(s.sale_date) as sale_date, COUNT(DISTINCT s.customer_id) as unique_customers,
                   COUNT(*) as total_transactions
            FROM sales s
            WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(s.sale_date)
            ORDER BY sale_date ASC
        ");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    // Financial Metrics Methods
    public function getGrossMargin($days = 30)
    {
        // Get gross margin calculation with proper cost basis
        // Use the unit_price from sale_items (actual sale price) vs the cost from products
        $this->db->query("
            SELECT 
                SUM(si.quantity * si.unit_price) as total_revenue,
                SUM(si.quantity * COALESCE(p.current_average_cost, p.purchase_price, 0)) as total_cost
            FROM sale_items si
            JOIN products p ON si.product_id = p.product_id
            JOIN sales s ON si.sale_id = s.sale_id
            WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            AND si.unit_price > 0  -- Only include sales with actual prices
        ");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->single();

        if (!$result || $result->total_revenue == 0) {
            return 0;
        }

        $grossProfit = $result->total_revenue - $result->total_cost;
        $marginPercent = round(($grossProfit / $result->total_revenue) * 100, 1);

        return $marginPercent;
    }

    public function getDailySalesTrend($days = 7)
    {
        $this->db->query("
            SELECT DATE(sale_date) as sale_date, SUM(total_amount) as daily_sales
            FROM sales
            WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(sale_date)
            ORDER BY sale_date ASC
        ");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    // Legacy methods for backward compatibility
    public function getSalesToday()
    {
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE DATE(sale_date) = CURDATE()");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getSalesWeek()
    {
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getSalesMonth()
    {
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }
}
