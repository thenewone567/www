<?php require APPROOT . DS . 'views/layout/header.php'; ?>
    <h1>Notifications</h1>
    <ul class="list-group">
    <?php foreach($data['notifications'] as $notification) : ?>
        <li class="list-group-item">
            <?php echo $notification->message; ?>
            <a href="<?php echo URLROOT; ?>/notifications/markAsRead/<?php echo $notification->notification_id; ?>" class="btn btn-sm btn-primary float-right">Mark as read</a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php require APPROOT . DS . 'views/layout/footer.php'; ?>
