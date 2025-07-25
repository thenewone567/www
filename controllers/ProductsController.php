<?php

class ProductsController
{
    public function index()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();
        $products = $productModel->getProducts();

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
