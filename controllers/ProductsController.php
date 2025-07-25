<?php

class ProductsController
{
    public function index()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();

        $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : null;
        $searchType = isset($_GET['searchType']) ? $_GET['searchType'] : null;

        $products = $productModel->getProducts($searchTerm, $searchType);

        require_once ROOT_PATH . 'views/products.php';
    }

    public function add()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();
        $productModel->addProduct($_POST, $_FILES);

        header('Location: /products');
    }

    public function edit()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();
        $productModel->updateProduct($_POST, $_FILES);

        header('Location: /products');
    }

    public function delete()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();
        $productModel->deleteProduct($_GET['id']);

        header('Location: /products');
    }
}
