<?php
class NotificationsController extends Controller {
public $notificationModel;

    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->notificationModel = $this->model('Notification');
    }

    public function index(){
        $notifications = $this->notificationModel->getNotifications($_SESSION['user_id']);
        $data = [
            'notifications' => $notifications
        ];
        $this->view('notifications/index', $data);
    }

    public function markAsRead($id){
        if($this->notificationModel->markAsRead($id)){
            redirect('notifications');
        } else {
            die('Something went wrong');
        }
    }
}
