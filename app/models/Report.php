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
}
