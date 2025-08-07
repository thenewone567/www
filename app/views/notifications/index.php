<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body theme-card-light mt-3">
            <h2><i class="fas fa-bell"></i> Notifications</h2>
            <?php flash('notification_message'); ?>

            <div class="list-group mt-3">
                <?php if (!empty($data['notifications'])): ?>
                    <?php foreach ($data['notifications'] as $notification): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?php echo $notification->message; ?></h6>
                                <?php if (isset($notification->created_at)): ?>
                                    <small
                                        class="text-muted"><?php echo date('F j, Y g:i A', strtotime($notification->created_at)); ?></small>
                                <?php endif; ?>
                            </div>
                            <?php if (isset($notification->notification_id)): ?>
                                <a href="<?php echo URLROOT; ?>/notifications/markAsRead/<?php echo $notification->notification_id; ?>"
                                    class="btn btn-sm btn-primary">Mark as read</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="list-group-item text-center text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p>No notifications found</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-3">
                <a href="<?php echo URLROOT; ?>/pages/index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

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