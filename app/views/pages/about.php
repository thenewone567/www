<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<h1><?php echo isset($data['title']) ? $data['title'] : ''; ?></h1>
<p><?php echo isset($data['description']) ? $data['description'] : ''; ?></p>
<p>Version: <strong><?php echo defined('APPVERSION') ? APPVERSION : ''; ?></strong></p>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>