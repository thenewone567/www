<?php

class Sale
{
    private $conn;

    public function __construct()
    {
        // For now, we will not connect to the database
        // require_once ROOT_PATH . 'config/database.php';
        // $this->conn = connect();
    }

    public function createSale($data)
    {
        // This is a dummy implementation
        // In a real application, you would insert the sale into the database
        // and update the stock
        return true;
    }

    public function getSalesHistory()
    {
        // This is a dummy implementation
        return [
            [
                'SaleID' => 1,
                'ProductName' => 'Product A',
                'Quantity' => 2,
                'Discount' => 0,
                'TotalAmount' => 20.00,
                'SaleDate' => '2024-07-25',
                'InvoiceLink' => '#'
            ],
            [
                'SaleID' => 2,
                'ProductName' => 'Product B',
                'Quantity' => 1,
                'Discount' => 5.00,
                'TotalAmount' => 15.00,
                'SaleDate' => '2024-07-24',
                'InvoiceLink' => '#'
            ]
        ];
    }
}
