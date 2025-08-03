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
            SELECT SUM(COALESCE(s.quantity, 0) * COALESCE(p.purchase_price, 0)) as total_value
            FROM products p
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE p.is_active = 1 AND COALESCE(s.quantity, 0) > 0
        ");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total_value !== null ? $result->total_value : 0;
    }

    public function getTotalProducts()
    {
        $this->db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getLowStockProducts($limit = 10)
    {
        $this->db->query("
            SELECT p.product_id, p.product_name, p.sku, 
                   COALESCE(SUM(s.quantity), 0) as current_stock,
                   p.min_stock_level, p.reorder_level, c.category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id, p.product_name, p.sku, p.min_stock_level, p.reorder_level, c.category_name
            HAVING COALESCE(SUM(s.quantity), 0) <= p.reorder_level
            ORDER BY (COALESCE(SUM(s.quantity), 0) / NULLIF(p.reorder_level, 1)) ASC
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getLowStockCount()
    {
        $this->db->query("
            SELECT COUNT(DISTINCT p.product_id) as total 
            FROM products p
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            AND COALESCE((SELECT SUM(quantity) FROM stock WHERE product_id = p.product_id), 0) <= p.reorder_level
        ");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getOutOfStockCount()
    {
        $this->db->query("
            SELECT COUNT(DISTINCT p.product_id) as total 
            FROM products p
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            AND COALESCE((SELECT SUM(quantity) FROM stock WHERE product_id = p.product_id), 0) <= 0
        ");
        $this->db->execute();
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getOutOfStockPercentage()
    {
        $total = $this->getTotalProducts();
        if ($total == 0)
            return 0;
        $outOfStock = $this->getOutOfStockCount();
        return round(($outOfStock / $total) * 100, 1);
    }

    public function getLowStockByCategory()
    {
        $this->db->query("
            SELECT c.category_name, COUNT(p.product_id) as low_stock_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.current_stock <= p.reorder_level AND p.is_active = 1
            GROUP BY c.category_id, c.category_name
            ORDER BY low_stock_count DESC
        ");
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    // Customer Analytics Methods
    public function getNewCustomers($days = 30)
    {
        $this->db->query("SELECT COUNT(*) as total FROM customers WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)");
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
        $this->db->query("
            SELECT 
                SUM(si.quantity * si.unit_price) as total_revenue,
                SUM(si.quantity * COALESCE(p.purchase_price, 0)) as total_cost
            FROM sale_items si
            JOIN products p ON si.product_id = p.product_id
            JOIN sales s ON si.sale_id = s.sale_id
            WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        ");
        $this->db->bind(':days', $days);
        $this->db->execute();
        $result = $this->db->single();

        if (!$result || $result->total_revenue == 0)
            return 0;

        $grossProfit = $result->total_revenue - $result->total_cost;
        return round(($grossProfit / $result->total_revenue) * 100, 1);
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
