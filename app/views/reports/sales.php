<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<h1>Sales Report</h1>
<form action="<?php echo URLROOT; ?>/reports/sales" method="post">
    <div class="form-row">
        <div class="col">
            <input type="date" name="from_date" class="form-control" placeholder="From Date"
                value="<?php echo isset($data['from_date']) ? $data['from_date'] : ''; ?>">
        </div>
        <div class="col">
            <input type="date" name="to_date" class="form-control" placeholder="To Date"
                value="<?php echo isset($data['to_date']) ? $data['to_date'] : ''; ?>">
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary">Generate</button>
        </div>
    </div>
</form>

<?php if (isset($data['sales']) && is_array($data['sales']) && count($data['sales']) > 0): ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Payment Mode</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['sales'] as $sale): ?>
                <tr>
                    <td><?php echo $sale->sale_id; ?></td>
                    <td><?php echo $sale->customer_id; ?></td>
                    <td><?php echo $sale->sale_date ?? '-'; ?></td>
                    <td><?php echo $sale->total_amount; ?></td>
                    <td><?php echo $sale->payment_mode; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif (isset($data['sales'])): ?>
    <div class="mt-3 text-muted">No sales found for selected dates.</div>
<?php endif; ?>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>