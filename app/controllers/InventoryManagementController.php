<?php
class InventoryManagementController extends Controller
{
    public function index()
    {
        // Load inventory data (reuse product model for now)
        $productModel = $this->model('Product');
        $products = $productModel->getAllProductsWithDetails();
        $categories = $productModel->getAllCategories();
        $data = [
            'products' => $products,
            'categories' => $categories
        ];
        $this->view('inventory_management/index', $data);
    }
}
