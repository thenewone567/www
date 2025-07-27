<?php require APPROOT . DS . 'views/layout/header.php'; ?>
  <h1><?php echo $data['title']; ?></h1>
  <p><?php echo $data['description']; ?></p>
  <p>Version: <strong><?php echo APPVERSION; ?></strong></p>
<?php require APPROOT . DS . 'views/layout/footer.php'; ?>
