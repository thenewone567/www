<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<h1>Sale Returns Report</h1>
<form action="<?php echo URLROOT; ?>/reports/salereturns" method="post">
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

<?php if (isset($data['salereturns']) && is_array($data['salereturns']) && count($data['salereturns']) > 0): ?>
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
            <?php foreach ($data['salereturns'] as $return): ?>
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
<?php elseif (isset($data['salereturns'])): ?>
    <div class="mt-3 text-muted">No sale returns found for selected dates.</div>
<?php endif; ?>

            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>