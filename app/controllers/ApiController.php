<?php
class ApiController extends Controller
{
    public function __construct()
    {
        // Temporarily allow AJAX requests without login for testing
        // TODO: Re-enable authentication once session issues are resolved
        /*
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }
        */
    }

    public function getCategories()
    {
        header('Content-Type: application/json');

        try {
            $categoryModel = $this->model('Category');
            $categories = $categoryModel->getCategories();

            echo json_encode($categories);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch categories']);
        }
    }

    public function getSuppliers()
    {
        header('Content-Type: application/json');

        try {
            $supplierModel = $this->model('Supplier');
            $suppliers = $supplierModel->getSuppliers();

            echo json_encode($suppliers);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch suppliers']);
        }
    }

    public function getBrands()
    {
        header('Content-Type: application/json');

        try {
            $brandModel = $this->model('Brand');
            $brands = $brandModel->getBrands();

            echo json_encode($brands);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch brands']);
        }
    }
}
