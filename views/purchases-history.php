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
                        <td>
                            <form action="/purchases/rate-supplier" method="POST" class="form-inline">
                                <input type="hidden" name="supplierID" value="<?php echo $purchase['SupplierID']; ?>">
                                <select name="rating" class="form-control mr-2">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Rate</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
