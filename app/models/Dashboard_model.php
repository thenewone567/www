<?php
class Dashboard_model {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getSalesToday(){
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE DATE(sale_date) = CURDATE()");
        return $this->db->single()->total;
    }

    public function getSalesWeek(){
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)");
        return $this->db->single()->total;
    }

    public function getSalesMonth(){
        $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())");
        return $this->db->single()->total;
    }

    public function getTopSellingProducts(){
        $this->db->query("SELECT p.product_name, SUM(si.quantity) as total_quantity FROM sale_items si JOIN products p ON si.product_id = p.product_id GROUP BY si.product_id ORDER BY total_quantity DESC LIMIT 5");
        return $this->db->resultSet();
    }

    public function getLowStockProducts(){
        $this->db->query("SELECT * FROM products WHERE min_stock_level >= (SELECT SUM(quantity) FROM stock WHERE product_id = products.product_id)");
        return $this->db->resultSet();
    }
}
