<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Audit Log</h1>
        <hr>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Username</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $auditLogModel = new AuditLog();
                $auditLogs = $auditLogModel->getAuditLogs();
                foreach ($auditLogs as $log) :
                ?>
                    <tr>
                        <td><?php echo $log['Timestamp']; ?></td>
                        <td><?php echo $log['Username']; ?></td>
                        <td><?php echo $log['Action']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
