<?php
  /*
   * Base Controller
   * Loads the models and views
   */
  class Controller {
    public $notificationModel;

    public function __construct() {
        if(isLoggedIn()){
            $this->notificationModel = $this->model('Notification');
        }
    }

    // Load model
    public function model($model){
      // Require model file
      require_once '../app/models/' . $model . '.php';

      // Instatiate model
      return new $model();
    }

    // Load view
    public function view($view, $data = []){
        if(isLoggedIn()){
            $data['notifications'] = $this->notificationModel->getNotifications($_SESSION['user_id']);
        }
      // Check for view file
      if(file_exists('../app/views/' . $view . '.php')){
        require_once '../app/views/' . $view . '.php';
      } else {
        // View does not exist
        die('View does not exist');
      }
    }
  }
