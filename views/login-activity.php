<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Login Activity</h1>
        <hr>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Login Time</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loginActivity as $activity) : ?>
                    <tr>
                        <td><?php echo $activity['Username']; ?></td>
                        <td><?php echo $activity['LoginTime']; ?></td>
                        <td><?php echo $activity['IPAddress']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
