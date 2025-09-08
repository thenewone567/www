<?php
class ReturnOrder
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSaleReturns()
    {
        $this->db->query("SELECT * FROM sale_returns");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getPurchaseReturns()
    {
        $this->db->query("
            SELECT pr.*, p.po_number, s.supplier_name 
            FROM purchase_returns pr
            LEFT JOIN purchases p ON pr.purchase_id = p.purchase_id
            LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
            ORDER BY pr.return_date DESC
        ");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addSaleReturn($data)
    {
        $this->db->query("INSERT INTO sale_returns (sale_id, return_date, reason, refund_amount) VALUES (:sale_id, :return_date, :reason, :refund_amount)");
        // Bind values
        $this->db->bind(':sale_id', $data['sale_id']);
        $this->db->bind(':return_date', $data['return_date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':refund_amount', $data['refund_amount']);

        // Execute
        return $this->db->execute();
    }

    public function addPurchaseReturn($data)
    {
        $this->db->query("INSERT INTO purchase_returns (purchase_id, return_date, reason) VALUES (:purchase_id, :return_date, :reason)");
        // Bind values
        $this->db->bind(':purchase_id', $data['purchase_id']);
        $this->db->bind(':return_date', $data['return_date']);
        $this->db->bind(':reason', $data['reason']);

        // Execute
        return $this->db->execute();
    }
}
