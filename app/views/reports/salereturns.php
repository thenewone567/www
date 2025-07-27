<?php require APPROOT . '/views/layout/header.php'; ?>
    <h1>Sale Returns Report</h1>
    <form action="<?php echo URLROOT; ?>/reports/salereturns" method="post">
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

    <?php if(isset($data['salereturns'])) : ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sale ID</th>
                <th>Date</th>
                <th>Reason</th>
                <th>Refund Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($data['salereturns'] as $return) : ?>
            <tr>
                <td><?php echo $return->sale_return_id; ?></td>
                <td><?php echo $return->sale_id; ?></td>
                <td><?php echo $return->return_date; ?></td>
                <td><?php echo $return->reason; ?></td>
                <td><?php echo $return->refund_amount; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
<?php require APPROOT . '/views/layout/footer.php'; ?>
