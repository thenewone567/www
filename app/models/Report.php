<?php
class Report
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSalesReports($from_date, $to_date)
    {
        $this->db->query("SELECT * FROM sales WHERE sale_date BETWEEN :from_date AND :to_date");
        $this->db->bind(':from_date', $from_date);
        $this->db->bind(':to_date', $to_date);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getPurchaseReports($from_date, $to_date)
    {
        $this->db->query("SELECT * FROM purchases WHERE purchase_date BETWEEN :from_date AND :to_date");
        $this->db->bind(':from_date', $from_date);
        $this->db->bind(':to_date', $to_date);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getSaleReturnReports($from_date, $to_date)
    {
        $this->db->query("SELECT * FROM sale_returns WHERE return_date BETWEEN :from_date AND :to_date");
        $this->db->bind(':from_date', $from_date);
        $this->db->bind(':to_date', $to_date);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getPurchaseReturnReports($from_date, $to_date)
    {
        $this->db->query("SELECT * FROM purchase_returns WHERE return_date BETWEEN :from_date AND :to_date");
        $this->db->bind(':from_date', $from_date);
        $this->db->bind(':to_date', $to_date);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    /**
     * Get sales analytics for dashboard
     * @param string $period (daily, weekly, monthly)
     * @return array
     */
    public function getSalesAnalytics($period = 'monthly')
    {
        $dateFormat = $this->getDateFormat($period);
        $query = "SELECT 
                    DATE_FORMAT(sale_date, '$dateFormat') as period,
                    COUNT(*) as transaction_count,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_sale,
                    MIN(total_amount) as min_sale,
                    MAX(total_amount) as max_sale
                  FROM sales 
                  WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                  GROUP BY DATE_FORMAT(sale_date, '$dateFormat')
                  ORDER BY period DESC";

        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Get top selling products
     * @param int $limit
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getTopSellingProducts($limit = 10, $dateFrom = null, $dateTo = null)
    {
        $whereClause = '';
        if ($dateFrom && $dateTo) {
            $whereClause = "WHERE s.sale_date BETWEEN :date_from AND :date_to";
        }

        $query = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.product_code,
                    SUM(si.quantity) as total_sold,
                    SUM(si.quantity * si.unit_price) as total_revenue,
                    AVG(si.unit_price) as avg_price,
                    COUNT(DISTINCT s.sale_id) as transaction_count
                  FROM products p
                  JOIN sale_items si ON p.product_id = si.product_id
                  JOIN sales s ON si.sale_id = s.sale_id
                  $whereClause
                  GROUP BY p.product_id, p.product_name, p.product_code
                  ORDER BY total_sold DESC
                  LIMIT :limit";

        $this->db->query($query);
        $this->db->bind(':limit', $limit);
        if ($dateFrom && $dateTo) {
            $this->db->bind(':date_from', $dateFrom);
            $this->db->bind(':date_to', $dateTo);
        }
        return $this->db->resultSet();
    }

    /**
     * Get profit margin analysis
     * @return array
     */
    public function getProfitMarginAnalysis()
    {
        $query = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.product_code,
                    AVG(pi.unit_price) as avg_cost_price,
                    AVG(si.unit_price) as avg_selling_price,
                    (AVG(si.unit_price) - AVG(pi.unit_price)) as avg_profit_per_unit,
                    ((AVG(si.unit_price) - AVG(pi.unit_price)) / AVG(pi.unit_price) * 100) as profit_margin_percent,
                    SUM(si.quantity) as total_sold,
                    SUM((si.unit_price - pi.unit_price) * si.quantity) as total_profit
                  FROM products p
                  LEFT JOIN purchase_items pi ON p.product_id = pi.product_id
                  LEFT JOIN sale_items si ON p.product_id = si.product_id
                  WHERE si.quantity > 0 AND pi.unit_price > 0
                  GROUP BY p.product_id, p.product_name, p.product_code
                  HAVING total_sold > 0
                  ORDER BY profit_margin_percent DESC";

        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Get customer purchase analysis
     * @param int $limit
     * @return array
     */
    public function getCustomerAnalysis($limit = 20)
    {
        $query = "SELECT 
                    c.customer_id,
                    c.customer_name,
                    c.contact_info,
                    COUNT(s.sale_id) as total_purchases,
                    SUM(s.total_amount) as total_spent,
                    AVG(s.total_amount) as avg_purchase_amount,
                    MAX(s.sale_date) as last_purchase_date,
                    DATEDIFF(NOW(), MAX(s.sale_date)) as days_since_last_purchase
                  FROM customers c
                  LEFT JOIN sales s ON c.customer_id = s.customer_id
                  WHERE s.sale_id IS NOT NULL
                  GROUP BY c.customer_id, c.customer_name, c.contact_info
                  ORDER BY total_spent DESC
                  LIMIT :limit";

        $this->db->query($query);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get supplier performance analysis
     * @return array
     */
    public function getSupplierPerformance()
    {
        $query = "SELECT 
                    s.supplier_id,
                    s.supplier_name,
                    s.contact_info,
                    COUNT(p.purchase_id) as total_orders,
                    SUM(p.total_amount) as total_purchased,
                    AVG(p.total_amount) as avg_order_amount,
                    MAX(p.purchase_date) as last_order_date,
                    s.due_amount
                  FROM suppliers s
                  LEFT JOIN purchases p ON s.supplier_id = p.supplier_id
                  GROUP BY s.supplier_id, s.supplier_name, s.contact_info, s.due_amount
                  ORDER BY total_purchased DESC";

        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Get inventory turnover analysis
     * @return array
     */
    public function getInventoryTurnover()
    {
        $query = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.product_code,
                    COALESCE(SUM(si.quantity), 0) as units_sold,
                    COALESCE(AVG(stock.quantity), 0) as avg_inventory,
                    CASE 
                        WHEN AVG(stock.quantity) > 0 
                        THEN SUM(si.quantity) / AVG(stock.quantity)
                        ELSE 0 
                    END as turnover_ratio,
                    CASE 
                        WHEN SUM(si.quantity) > 0 
                        THEN 365 / (SUM(si.quantity) / AVG(stock.quantity))
                        ELSE 0 
                    END as days_to_sell
                  FROM products p
                  LEFT JOIN sale_items si ON p.product_id = si.product_id
                  LEFT JOIN stock ON p.product_id = stock.product_id
                  GROUP BY p.product_id, p.product_name, p.product_code
                  ORDER BY turnover_ratio DESC";

        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Get date format for analytics grouping
     * @param string $period
     * @return string
     */
    private function getDateFormat($period)
    {
        switch ($period) {
            case 'daily':
                return '%Y-%m-%d';
            case 'weekly':
                return '%Y-%u';
            case 'monthly':
                return '%Y-%m';
            case 'yearly':
                return '%Y';
            default:
                return '%Y-%m';
        }
    }
}
