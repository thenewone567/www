<?php require APPROOT . '/' . ''views/layout/header.php'; ?>
    <h1>Purchases Report</h1>
    <form action="<?php echo URLROOT; ?>/reports/purchases" method="post">
        <div class="form-row">
            <div class="col">
                <input type="date" name="from_date" class="form-control" placeholder="From Date" value="<?php echo isset($data['from_date']) ? $data['from_date'] : ''; ?>">
            </div>
            <div class="col">
                <input type="date" name="to_date" class="form-control" placeholder="To Date" value="<?php echo isset($data['to_date']) ? $data['to_date'] : ''; ?>">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </div>
    </form>

    <?php if(isset($data['purchases'])) : ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Date</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($data['purchases'] as $purchase) : ?>
            <tr>
                <td><?php echo $purchase->purchase_id; ?></td>
                <td><?php echo $purchase->supplier_id; ?></td>
                <td><?php echo $purchase->purchase_date; ?></td>
                <td><?php echo $purchase->total_amount; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
<?php require APPROOT . '/' . ''views/layout/footer.php'; ?>
