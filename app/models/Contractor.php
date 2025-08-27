<?php
/**
 * Contractor Model
 * Handles contractor/vendor management
 */
class Contractor
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all contractors
     * @return array
     */
    public function getAllContractors()
    {
        $this->db->query('SELECT * FROM contractors WHERE is_active = 1 ORDER BY contractor_name');
        return $this->db->resultSet();
    }

    /**
     * Get contractor by ID
     * @param int $contractorId
     * @return object|null
     */
    public function getContractorById($contractorId)
    {
        $this->db->query('SELECT * FROM contractors WHERE contractor_id = :contractor_id');
        $this->db->bind(':contractor_id', $contractorId);
        return $this->db->single();
    }

    /**
     * Get contractor by email
     * @param string $email
     * @return object|null
     */
    public function getContractorByEmail($email)
    {
        $this->db->query('SELECT * FROM contractors WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Add new contractor
     * @param array $data
     * @return bool
     */
    public function addContractor($data)
    {
        $sql = 'INSERT INTO contractors (
            contractor_name, company_name, email, phone, address, city, state, postal_code, 
            specialization, license_number, commission_type, commission_rate, 
            is_active, created_at
        ) VALUES (
            :contractor_name, :company_name, :email, :phone, :address, :city, :state, :postal_code,
            :specialization, :license_number, :commission_type, :commission_rate,
            :is_active, NOW()
        )';

        $this->db->query($sql);

        // Use contact_person as contractor_name if no specific contractor_name
        $contractorName = $data['contact_person'] ?? $data['contractor_name'] ?? '';

        $this->db->bind(':contractor_name', $contractorName);
        $this->db->bind(':company_name', $data['company_name'] ?? '');
        $this->db->bind(':email', $data['email'] ?? '');
        $this->db->bind(':phone', $data['phone'] ?? '');
        $this->db->bind(':address', $data['address'] ?? '');
        $this->db->bind(':city', $data['city'] ?? '');
        $this->db->bind(':state', $data['state'] ?? '');
        $this->db->bind(':postal_code', $data['zip_code'] ?? '');
        $this->db->bind(':specialization', $data['specialty'] ?? '');
        $this->db->bind(':license_number', $data['license_number'] ?? '');
        $this->db->bind(':commission_type', $data['commission_type'] ?? 'percentage');
        $this->db->bind(':commission_rate', $data['commission_value'] ?? 0);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);

        return $this->db->execute();
    }

    /**
     * Update contractor
     * @param int $contractorId
     * @param array $data
     * @return bool
     */
    public function updateContractor($contractorId, $data)
    {
        $this->db->query('UPDATE contractors SET 
                         contractor_name = :contractor_name,
                         company_name = :company_name,
                         email = :email,
                         phone = :phone,
                         address = :address,
                         city = :city,
                         state = :state,
                         zip_code = :zip_code,
                         contract_type = :contract_type,
                         specialization = :specialization,
                         hourly_rate = :hourly_rate,
                         notes = :notes,
                         updated_at = CURRENT_TIMESTAMP
                         WHERE contractor_id = :contractor_id');

        $this->db->bind(':contractor_id', $contractorId);
        $this->db->bind(':contractor_name', $data['contractor_name']);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':city', $data['city'] ?? null);
        $this->db->bind(':state', $data['state'] ?? null);
        $this->db->bind(':zip_code', $data['zip_code'] ?? null);
        $this->db->bind(':contract_type', $data['contract_type'] ?? 'contractor');
        $this->db->bind(':specialization', $data['specialization'] ?? null);
        $this->db->bind(':hourly_rate', $data['hourly_rate'] ?? null);
        $this->db->bind(':notes', $data['notes'] ?? null);

        return $this->db->execute();
    }

    /**
     * Delete/deactivate contractor
     * @param int $contractorId
     * @return bool
     */
    public function deleteContractor($contractorId)
    {
        $this->db->query('UPDATE contractors SET is_active = 0 WHERE contractor_id = :contractor_id');
        $this->db->bind(':contractor_id', $contractorId);
        return $this->db->execute();
    }

    /**
     * Get contractors by type
     * @param string $type
     * @return array
     */
    public function getContractorsByType($type)
    {
        $this->db->query('SELECT * FROM contractors WHERE contract_type = :contract_type AND is_active = 1 ORDER BY contractor_name');
        $this->db->bind(':contract_type', $type);
        return $this->db->resultSet();
    }

    /**
     * Search contractors
     * @param string $searchTerm
     * @return array
     */
    public function searchContractors($searchTerm)
    {
        $this->db->query('SELECT * FROM contractors 
                         WHERE (contractor_name LIKE :search OR company_name LIKE :search OR email LIKE :search OR specialization LIKE :search) 
                         AND is_active = 1 
                         ORDER BY contractor_name');
        $searchPattern = '%' . $searchTerm . '%';
        $this->db->bind(':search', $searchPattern);
        return $this->db->resultSet();
    }

    /**
     * Get total contractor count
     * @return int
     */
    public function getTotalContractors()
    {
        $this->db->query('SELECT COUNT(*) as count FROM contractors WHERE is_active = 1');
        $result = $this->db->single();
        return $result ? (int) $result->count : 0;
    }

    /**
     * Get contractor statistics
     * @return object
     */
    public function getContractorStats()
    {
        $this->db->query('SELECT 
                         COUNT(*) as total_contractors,
                         COUNT(CASE WHEN contract_type = "contractor" THEN 1 END) as general_contractors,
                         COUNT(CASE WHEN contract_type = "vendor" THEN 1 END) as vendors,
                         COUNT(CASE WHEN contract_type = "supplier" THEN 1 END) as suppliers,
                         COUNT(CASE WHEN contract_type = "freelancer" THEN 1 END) as freelancers,
                         AVG(hourly_rate) as avg_hourly_rate
                         FROM contractors WHERE is_active = 1');
        return $this->db->single();
    }
}
?>