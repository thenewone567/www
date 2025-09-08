<?php
class Company
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Fetch company by id (default 1)
     */
    public function getCompany($id = 1)
    {
        $this->db->query('SELECT * FROM companies WHERE company_id = :id LIMIT 1');
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Insert or update a company record. $data is an associative array of column=>value
     */
    public function saveCompany($id = 1, $data = [])
    {
        // Normalize keys and only allow strings/numeric values
        if (empty($data) || !is_array($data)) {
            return false;
        }

        // Check if company exists
        $exists = $this->getCompany($id);

        if ($exists) {
            $sets = [];
            foreach ($data as $k => $v) {
                $sets[] = "`$k` = :$k";
            }
            $sql = 'UPDATE companies SET ' . implode(', ', $sets) . ' WHERE company_id = :id';
            $this->db->query($sql);
            foreach ($data as $k => $v) {
                $this->db->bind(':' . $k, $v);
            }
            $this->db->bind(':id', $id);
            $result = $this->db->execute();
        } else {
            $cols = [];
            $place = [];
            foreach ($data as $k => $v) {
                $cols[] = "`$k`";
                $place[] = ':' . $k;
            }
            // include company_id if provided
            $sql = 'INSERT INTO companies (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $place) . ')';
            $this->db->query($sql);
            foreach ($data as $k => $v) {
                $this->db->bind(':' . $k, $v);
            }
            $result = $this->db->execute();
        }

        // Clear company cache after successful save
        if ($result && function_exists('clear_company_cache')) {
            clear_company_cache();
        }

        return $result;
    }
}
