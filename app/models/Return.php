<?php
class ReturnModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getSaleReturns(){
        $this->db->query("SELECT * FROM sale_returns");
        return $this->db->resultSet();
    }

    public function getPurchaseReturns(){
        $this->db->query("SELECT * FROM purchase_returns");
        return $this->db->resultSet();
    }

    public function addSaleReturn($data){
        $this->db->query("INSERT INTO sale_returns (sale_id, return_date, reason, refund_amount) VALUES (:sale_id, :return_date, :reason, :refund_amount)");
        // Bind values
        $this->db->bind(':sale_id', $data['sale_id']);
        $this->db->bind(':return_date', $data['return_date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':refund_amount', $data['refund_amount']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function addPurchaseReturn($data){
        $this->db->query("INSERT INTO purchase_returns (purchase_id, return_date, reason) VALUES (:purchase_id, :return_date, :reason)");
        // Bind values
        $this->db->bind(':purchase_id', $data['purchase_id']);
        $this->db->bind(':return_date', $data['return_date']);
        $this->db->bind(':reason', $data['reason']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
