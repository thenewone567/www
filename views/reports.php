<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Reports</h1>
        <hr>
        <form action="/reports/generate" method="GET" class="row mb-3">
            <div class="col">
                <select name="type" class="form-control">
                    <option value="sales">Sales Report</option>
                    <option value="top-products">Top Products Report</option>
                    <option value="purchases">Purchase Report</option>
                    <option value="inventory-value">Inventory Value Report</option>
                    <option value="returns">Returns Report</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </form>

        <?php if (isset($data) && !empty($data)) : ?>
            <hr>
            <div class="row mb-3">
                <div class="col">
                    <a href="/reports/export?type=<?php echo $_GET['type']; ?>&export=csv" class="btn btn-success">Export to CSV</a>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <?php foreach (array_keys($data[0]) as $key) : ?>
                            <th><?php echo $key; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row) : ?>
                        <tr>
                            <?php foreach ($row as $value) : ?>
                                <td><?php echo $value; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
