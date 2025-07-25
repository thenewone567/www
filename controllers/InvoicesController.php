<?php

class InvoicesController
{
    public function show($id)
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Invoice.php';
        $invoiceModel = new Invoice();
        $invoice = $invoiceModel->getInvoiceDetails($id);

        require_once ROOT_PATH . 'views/invoice.php';
    }

    public function generatePDF($id)
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Invoice.php';
        $invoiceModel = new Invoice();
        $invoice = $invoiceModel->getInvoiceDetails($id);

        require_once ROOT_PATH . 'vendor/tcpdf/tcpdf.php';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Home Hardware Store');
        $pdf->SetTitle('Invoice ' . $invoice['InvoiceID']);
        $pdf->SetSubject('Invoice');

        $pdf->AddPage();

        $html = '<h1>Invoice #' . $invoice['InvoiceID'] . '</h1>';
        // Add more HTML content here to build the invoice

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('invoice-' . $invoice['InvoiceID'] . '.pdf', 'I');
    }
}
