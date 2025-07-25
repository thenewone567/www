<?php

class Invoice
{
    private $conn;

    public function __construct()
    {
        // For now, we will not connect to the database
        // require_once ROOT_PATH . 'config/database.php';
        // $this->conn = connect();
    }

    public function createInvoice($saleID)
    {
        // This is a dummy implementation
        // In a real application, you would insert the invoice into the database
        // and return the new invoice ID
        return 1;
    }

    public function getInvoiceDetails($invoiceID)
    {
        // This is a dummy implementation
        return [
            'InvoiceID' => $invoiceID,
            'InvoiceDate' => '2024-07-25',
            'DueDate' => '2024-08-25',
            'StoreInfo' => [
                'Name' => 'Home Hardware Store',
                'Address' => '123 Main St, Anytown, USA',
                'Phone' => '555-123-4567',
                'Email' => 'contact@homehardware.com'
            ],
            'CustomerInfo' => [
                'FirstName' => 'John',
                'LastName' => 'Doe',
                'Email' => 'john.doe@example.com'
            ],
            'Items' => [
                [
                    'ProductName' => 'Product A',
                    'Quantity' => 2,
                    'Price' => 10.00,
                    'Total' => 20.00
                ],
                [
                    'ProductName' => 'Product B',
                    'Quantity' => 1,
                    'Price' => 20.00,
                    'Total' => 20.00
                ]
            ],
            'Subtotal' => 40.00,
            'Tax' => 4.00,
            'Total' => 44.00
        ];
    }
}
