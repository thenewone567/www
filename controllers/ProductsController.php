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

        require_once ROOT_PATH . 'models/AuditLog.php';
        $auditLogModel = new AuditLog();
        $auditLogModel->logAction($_SESSION['user']['username'], 'Added new product: ' . $_POST['productName']);

        header('Location: /products');
    }

    public function edit()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();
        $productModel->updateProduct($_POST, $_FILES);

        require_once ROOT_PATH . 'models/AuditLog.php';
        $auditLogModel = new AuditLog();
        $auditLogModel->logAction($_SESSION['user']['username'], 'Updated product: ' . $_POST['productName']);

        header('Location: /products');
    }

    public function delete()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();
        $productModel->deleteProduct($_GET['id']);

        require_once ROOT_PATH . 'models/AuditLog.php';
        $auditLogModel = new AuditLog();
        $auditLogModel->logAction($_SESSION['user']['username'], 'Deleted product with ID: ' . $_GET['id']);

        header('Location: /products');
    }

    public function showCsvImportForm()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);
        require_once ROOT_PATH . 'views/csv-import.php';
    }

    public function importCsv()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        // Handle CSV import logic here

        header('Location: /products');
    }

    public function exportCsv()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Product.php';
        $productModel = new Product();
        $products = $productModel->getProducts();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, array_keys($products[0]));
        foreach ($products as $product) {
            fputcsv($output, $product);
        }
        fclose($output);
    }

    public function generateQrCode()
    {
        require_once ROOT_PATH . 'vendor/phpqrcode/qrlib.php';
        $productID = $_GET['id'];
        QRcode::png($productID);
    }
}
