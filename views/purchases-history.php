<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Purchases History</h1>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th>Purchase ID</th>
                    <th>Supplier Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Cost</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $purchase) : ?>
                    <tr>
                        <td><?php echo $purchase['PurchaseID']; ?></td>
                        <td><?php echo $purchase['SupplierName']; ?></td>
                        <td><?php echo $purchase['ProductName']; ?></td>
                        <td><?php echo $purchase['Quantity']; ?></td>
                        <td><?php echo $purchase['Cost']; ?></td>
                        <td><?php echo $purchase['PurchaseDate']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
