<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="jumbotron jumbotron-flud text-center">
  <div class="container">
    <h1 class="display-3"><?php echo isset($data['title']) ? $data['title'] : ''; ?></h1>
    <p class="lead"><?php echo isset($data['description']) ? $data['description'] : ''; ?></p>
  </div>
</div>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>