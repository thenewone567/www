<?php
class Setting
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSettings()
    {
        $this->db->query("SELECT * FROM settings");
        $results = $this->db->resultSet();
        $settings = [];
        foreach ($results as $result) {
            $settings[$result->setting_key] = $result->setting_value;
        }
        return $settings;
    }

    public function updateSettings($data)
    {
        foreach ($data as $key => $value) {
            // Use upsert so new keys are inserted automatically
            $this->db->query("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $this->db->bind(':key', $key);
            $this->db->bind(':value', $value);
            if (!$this->db->execute()) {
                return false;
            }
        }
        return true;
    }
}
