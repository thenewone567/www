<?php

class ReportsController
{
    public function index()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);
        require_once ROOT_PATH . 'views/reports.php';
    }

    public function generate()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        $reportType = $_GET['type'];

        require_once ROOT_PATH . 'models/Report.php';
        $reportModel = new Report();

        $data = [];
        switch ($reportType) {
            case 'sales':
                $data = $reportModel->getSalesReport();
                break;
            case 'top-products':
                $data = $reportModel->getTopProductsReport();
                break;
            case 'purchases':
                $data = $reportModel->getPurchaseReport();
                break;
            case 'inventory-value':
                $data = $reportModel->getInventoryValueReport();
                break;
            case 'returns':
                $data = $reportModel->getReturnsReport();
                break;
        }

        require_once ROOT_PATH . 'views/reports.php';
    }

    public function export()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        $reportType = $_GET['type'];
        $exportType = $_GET['export'];

        require_once ROOT_PATH . 'models/Report.php';
        $reportModel = new Report();

        $data = [];
        switch ($reportType) {
            case 'sales':
                $data = $reportModel->getSalesReport();
                break;
            case 'top-products':
                $data = $reportModel->getTopProductsReport();
                break;
            case 'purchases':
                $data = $reportModel->getPurchaseReport();
                break;
            case 'inventory-value':
                $data = $reportModel->getInventoryValueReport();
                break;
            case 'returns':
                $data = $reportModel->getReturnsReport();
                break;
        }

        if ($exportType === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $reportType . '.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }
    }
}
