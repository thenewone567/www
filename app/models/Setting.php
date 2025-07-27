<?php
class Setting {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getSettings(){
        $this->db->query("SELECT * FROM settings");
        $results = $this->db->resultSet();
        $settings = [];
        foreach($results as $result){
            $settings[$result->setting_key] = $result->setting_value;
        }
        return $settings;
    }

    public function updateSettings($data){
        foreach($data as $key => $value){
            $this->db->query("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
            $this->db->bind(':key', $key);
            $this->db->bind(':value', $value);
            $this->db->execute();
        }
        return true;
    }
}
