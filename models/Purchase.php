<?php

class Purchase
{
    private $conn;

    public function __construct()
    {
        // For now, we will not connect to the database
        // require_once ROOT_PATH . 'config/database.php';
        // $this->conn = connect();
    }

    public function createPurchase($data)
    {
        // This is a dummy implementation
        // In a real application, you would insert the purchase into the database
        // and update the stock
        return true;
    }

    public function getPurchasesHistory()
    {
        // This is a dummy implementation
        return [
            [
                'PurchaseID' => 1,
                'SupplierName' => 'Supplier A',
                'ProductName' => 'Product A',
                'Quantity' => 10,
                'Cost' => 100.00,
                'PurchaseDate' => '2024-07-25'
            ],
            [
                'PurchaseID' => 2,
                'SupplierName' => 'Supplier B',
                'ProductName' => 'Product B',
                'Quantity' => 5,
                'Cost' => 50.00,
                'PurchaseDate' => '2024-07-24'
            ]
        ];
    }
}
