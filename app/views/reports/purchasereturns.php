<?php require APPROOT . DS . 'app' . DS . '' . ''views/layout/header.php'; ?>
    <h1>Purchase Returns Report</h1>
    <form action="<?php echo URLROOT; ?>/reports/purchasereturns" method="post">
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

    <?php if(isset($data['purchasereturns'])) : ?>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Purchase ID</th>
                <th>Date</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($data['purchasereturns'] as $return) : ?>
            <tr>
                <td><?php echo $return->purchase_return_id; ?></td>
                <td><?php echo $return->purchase_id; ?></td>
                <td><?php echo $return->return_date; ?></td>
                <td><?php echo $return->reason; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
<?php require APPROOT . DS . 'app' . DS . '' . ''views/layout/footer.php'; ?>
