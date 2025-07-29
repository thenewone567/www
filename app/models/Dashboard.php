<?php
class Dashboard
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSalesToday()
    {
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE DATE(sale_date) = CURDATE()");
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getSalesWeek()
    {
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)");
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getSalesMonth()
    {
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())");
        $result = $this->db->single();
        return $result && $result->total !== null ? $result->total : 0;
    }

    public function getTopSellingProducts()
    {
        $this->db->query("SELECT p.product_name, SUM(si.quantity) as total_quantity FROM sale_items si JOIN products p ON si.product_id = p.product_id GROUP BY si.product_id ORDER BY total_quantity DESC LIMIT 5");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getLowStockProducts()
    {
        $this->db->query("SELECT p.product_id, p.product_name, p.min_stock_level, COALESCE(SUM(s.quantity),0) as current_stock
            FROM products p
            LEFT JOIN stock s ON p.product_id = s.product_id
            GROUP BY p.product_id, p.product_name, p.min_stock_level
            HAVING p.min_stock_level >= current_stock");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }
}
