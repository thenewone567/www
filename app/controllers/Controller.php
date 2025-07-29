<?php
/*
 * Base Controller
 * Loads the models and views
 */
class Controller
{
  public $notificationModel;

  public function __construct()
  {
    if (isLoggedIn()) {
      $this->notificationModel = $this->model('Notification');
    }
  }

  // Load model
  public function model($model)
  {
    require_once APPROOT . DS . 'app' . DS . 'models' . DS . $model . '.php';
    return new $model();
  }

  // Load view
  public function view($view, $data = [])
  {
    if (!isset($this->notificationModel)) {
      $this->notificationModel = $this->model('Notification');
    }
    if (isset($_SESSION['user_id'])) {
      $data['notifications'] = $this->notificationModel->getNotifications($_SESSION['user_id']);
    }
    // Check for view file
    $viewPath = APPROOT . DS . 'app' . DS . 'views' . DS . $view . '.php';
    if (file_exists($viewPath)) {
      require_once $viewPath;
    } else {
      die('View does not exist');
    }
  }
}
