<?php
class Notification {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getNotifications($user_id){
        $this->db->query("SELECT * FROM notifications WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function addNotification($data){
        $this->db->query("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':message', $data['message']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function markAsRead($id){
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE notification_id = :id");
        $this->db->bind(':id', $id);
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
