<?php
class NotificationsController extends Controller
{
    public $notificationModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->notificationModel = $this->model('Notification');
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            flash('notification_message', 'User not found');
            redirect('users/login');
            return;
        }
        $notifications = $this->notificationModel->getNotifications($_SESSION['user_id']);
        if (!$notifications) {
            $notifications = [];
            flash('notification_message', 'No notifications found');
        }
        $data = [
            'notifications' => $notifications
        ];
        $this->view('notifications/index', $data);
    }

    public function markAsRead($id)
    {
        if ($this->notificationModel->markAsRead($id)) {
            redirect('notifications');
        } else {
            die('Something went wrong');
        }
    }
}
