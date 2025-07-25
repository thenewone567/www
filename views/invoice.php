<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="invoice-title">
                <h2>Invoice</h2>
                <h3 class="pull-right">Order # <?php echo $invoice['InvoiceID']; ?></h3>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <address>
                        <strong>Billed To:</strong><br>
                        <?php echo $invoice['CustomerInfo']['FirstName'] . ' ' . $invoice['CustomerInfo']['LastName']; ?><br>
                        <?php echo $invoice['CustomerInfo']['Email']; ?>
                    </address>
                </div>
                <div class="col-6 text-right">
                    <address>
                        <strong>Shipped To:</strong><br>
                        <?php echo $invoice['StoreInfo']['Name']; ?><br>
                        <?php echo $invoice['StoreInfo']['Address']; ?><br>
                        <?php echo $invoice['StoreInfo']['Phone']; ?><br>
                        <?php echo $invoice['StoreInfo']['Email']; ?>
                    </address>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <address>
                        <strong>Payment Method:</strong><br>
                        Visa ending **** 4242<br>
                        jsmith@email.com
                    </address>
                </div>
                <div class="col-6 text-right">
                    <address>
                        <strong>Order Date:</strong><br>
                        <?php echo $invoice['InvoiceDate']; ?><br><br>
                    </address>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Order summary</strong></h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <td><strong>Item</strong></td>
                                    <td class="text-center"><strong>Price</strong></td>
                                    <td class="text-center"><strong>Quantity</strong></td>
                                    <td class="text-right"><strong>Totals</strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoice['Items'] as $item) : ?>
                                    <tr>
                                        <td><?php echo $item['ProductName']; ?></td>
                                        <td class="text-center">$<?php echo $item['Price']; ?></td>
                                        <td class="text-center"><?php echo $item['Quantity']; ?></td>
                                        <td class="text-right">$<?php echo $item['Total']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="thick-line"></td>
                                    <td class="thick-line"></td>
                                    <td class="thick-line text-center"><strong>Subtotal</strong></td>
                                    <td class="thick-line text-right">$<?php echo $invoice['Subtotal']; ?></td>
                                </tr>
                                <tr>
                                    <td class="no-line"></td>
                                    <td class="no-line"></td>
                                    <td class="no-line text-center"><strong>Tax</strong></td>
                                    <td class="no-line text-right">$<?php echo $invoice['Tax']; ?></td>
                                </tr>
                                <tr>
                                    <td class="no-line"></td>
                                    <td class="no-line"></td>
                                    <td class="no-line text-center"><strong>Total</strong></td>
                                    <td class="no-line text-right">$<?php echo $invoice['Total']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="/invoices/pdf/<?php echo $invoice['InvoiceID']; ?>" class="btn btn-primary">Export to PDF</a>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
