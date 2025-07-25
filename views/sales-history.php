<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Sales History</h1>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Total Amount</th>
                    <th>Sale Date</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale) : ?>
                    <tr>
                        <td><?php echo $sale['SaleID']; ?></td>
                        <td><?php echo $sale['ProductName']; ?></td>
                        <td><?php echo $sale['Quantity']; ?></td>
                        <td><?php echo $sale['Discount']; ?></td>
                        <td><?php echo $sale['TotalAmount']; ?></td>
                        <td><?php echo $sale['SaleDate']; ?></td>
                        <td><a href="<?php echo $sale['InvoiceLink']; ?>">View Invoice</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
