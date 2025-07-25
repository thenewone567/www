<?php

class Product
{
    private $conn;

    public function __construct()
    {
        // For now, we will not connect to the database
        // require_once ROOT_PATH . 'config/database.php';
        // $this->conn = connect();
    }

    public function getProducts($searchTerm = null, $searchType = null)
    {
        // This is a dummy implementation
        $products = [
            [
                'ProductID' => 1,
                'ProductName' => 'Product A',
                'SKU' => 'PA001',
                'Description' => 'Description for Product A',
                'Price' => 10.00,
                'Quantity' => 10,
                'Photo' => 'https://via.placeholder.com/150'
            ],
            [
                'ProductID' => 2,
                'ProductName' => 'Product B',
                'SKU' => 'PB002',
                'Description' => 'Description for Product B',
                'Price' => 20.00,
                'Quantity' => 5,
                'Photo' => 'https://via.placeholder.com/150'
            ],
            [
                'ProductID' => 3,
                'ProductName' => 'Product C',
                'SKU' => 'PC003',
                'Description' => 'Description for Product C',
                'Price' => 30.00,
                'Quantity' => 0,
                'Photo' => 'https://via.placeholder.com/150'
            ]
        ];

        if ($searchTerm && $searchType) {
            $results = [];
            foreach ($products as $product) {
                if (stripos($product[$searchType], $searchTerm) !== false) {
                    $results[] = $product;
                }
            }
            return $results;
        }

        return $products;
    }

    public function getAlternativeProducts($productID)
    {
        // This is a dummy implementation
        return [
            [
                'ProductID' => 4,
                'ProductName' => 'Alternative Product D',
                'Price' => 12.00,
                'Photo' => 'https://via.placeholder.com/150'
            ],
            [
                'ProductID' => 5,
                'ProductName' => 'Alternative Product E',
                'Price' => 22.00,
                'Photo' => 'https://via.placeholder.com/150'
            ]
        ];
    }

    public function getLowStockProducts($threshold = 5)
    {
        // This is a dummy implementation
        $products = $this->getProducts();
        $lowStockProducts = [];
        foreach ($products as $product) {
            if ($product['Quantity'] <= $threshold) {
                $lowStockProducts[] = $product;
            }
        }
        return $lowStockProducts;
    }

    public function addProduct($data, $file)
    {
        $photoPath = $this->uploadPhoto($file);
        // In a real application, you would insert the product into the database
        // with the photo path
        return true;
    }

    public function updateProduct($data, $file)
    {
        $photoPath = $this->uploadPhoto($file);
        // In a real application, you would update the product in the database
        // with the new photo path if there is one
        return true;
    }

    private function uploadPhoto($file)
    {
        if (isset($file['photo']) && $file['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_PATH . 'public/uploads/';
            $fileName = uniqid() . '-' . basename($file['photo']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['photo']['tmp_name'], $targetPath)) {
                return '/uploads/' . $fileName;
            }
        }
        return null;
    }

    public function deleteProduct($id)
    {
        // This is a dummy implementation
        // In a real application, you would delete the product from the database
        return true;
    }

    public function trackStockMovement($data)
    {
        // This is a dummy implementation
        // In a real application, you would insert a record into the StockManagement table
        return true;
    }
}
