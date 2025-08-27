<?php
/**
 * Enhanced Contractor Model
 * Handles contractor/vendor management with commission tracking
 */
class Contractor
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all contractors with commission details
     * @return array
     */
    public function getAllContractors()
    {
        $this->db->query("
            SELECT 
                contractor_id, contractor_name, company_name, email, phone, address,
                city, state, postal_code, contract_type, specialization, 
                commission_type, commission_rate, commission_min_amount, commission_max_amount,
                commission_tiers, total_jobs_completed, total_revenue_generated, 
                total_commission_earned, is_active, is_verified, created_at
            FROM contractors 
            ORDER BY contractor_name ASC
        ");
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
     * Add new contractor with commission structure
     * @param array $data
     * @return bool
     */
    public function addContractor($data)
    {
        $this->db->query("
            INSERT INTO contractors (
                contractor_name, company_name, email, phone, address, city, state, postal_code,
                contract_type, specialization, license_number, license_expiry,
                commission_type, commission_rate, commission_min_amount, commission_max_amount,
                commission_tiers, preferred_payment_method, tax_id, tax_classification,
                is_active, is_verified
            ) VALUES (
                :contractor_name, :company_name, :email, :phone, :address, :city, :state, :postal_code,
                :contract_type, :specialization, :license_number, :license_expiry,
                :commission_type, :commission_rate, :commission_min_amount, :commission_max_amount,
                :commission_tiers, :preferred_payment_method, :tax_id, :tax_classification,
                :is_active, :is_verified
            )
        ");

        // Bind values
        $this->db->bind(':contractor_name', $data['contractor_name']);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':city', $data['city'] ?? null);
        $this->db->bind(':state', $data['state'] ?? null);
        $this->db->bind(':postal_code', $data['postal_code'] ?? null);
        $this->db->bind(':contract_type', $data['contract_type'] ?? 'individual');
        $this->db->bind(':specialization', $data['specialization'] ?? null);
        $this->db->bind(':license_number', $data['license_number'] ?? null);
        $this->db->bind(':license_expiry', $data['license_expiry'] ?? null);
        $this->db->bind(':commission_type', $data['commission_type'] ?? 'percentage');
        $this->db->bind(':commission_rate', $data['commission_rate'] ?? 0.00);
        $this->db->bind(':commission_min_amount', $data['commission_min_amount'] ?? 0.00);
        $this->db->bind(':commission_max_amount', $data['commission_max_amount'] ?? null);
        $this->db->bind(':commission_tiers', $data['commission_tiers'] ?? null);
        $this->db->bind(':preferred_payment_method', $data['preferred_payment_method'] ?? 'bank_transfer');
        $this->db->bind(':tax_id', $data['tax_id'] ?? null);
        $this->db->bind(':tax_classification', $data['tax_classification'] ?? 'individual');
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':is_verified', $data['is_verified'] ?? 0);

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
        $this->db->query("
            UPDATE contractors SET 
                contractor_name = :contractor_name,
                company_name = :company_name,
                email = :email,
                phone = :phone,
                address = :address,
                city = :city,
                state = :state,
                postal_code = :postal_code,
                contract_type = :contract_type,
                specialization = :specialization,
                commission_type = :commission_type,
                commission_rate = :commission_rate,
                commission_min_amount = :commission_min_amount,
                commission_max_amount = :commission_max_amount,
                commission_tiers = :commission_tiers,
                is_active = :is_active,
                is_verified = :is_verified,
                updated_at = CURRENT_TIMESTAMP
            WHERE contractor_id = :contractor_id
        ");

        // Bind values
        $this->db->bind(':contractor_id', $contractorId);
        $this->db->bind(':contractor_name', $data['contractor_name']);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':city', $data['city'] ?? null);
        $this->db->bind(':state', $data['state'] ?? null);
        $this->db->bind(':postal_code', $data['postal_code'] ?? null);
        $this->db->bind(':contract_type', $data['contract_type']);
        $this->db->bind(':specialization', $data['specialization']);
        $this->db->bind(':commission_type', $data['commission_type']);
        $this->db->bind(':commission_rate', $data['commission_rate']);
        $this->db->bind(':commission_min_amount', $data['commission_min_amount']);
        $this->db->bind(':commission_max_amount', $data['commission_max_amount']);
        $this->db->bind(':commission_tiers', $data['commission_tiers']);
        $this->db->bind(':is_active', $data['is_active']);
        $this->db->bind(':is_verified', $data['is_verified']);

        return $this->db->execute();
    }

    /**
     * Delete contractor
     * @param int $contractorId
     * @return bool
     */
    public function deleteContractor($contractorId)
    {
        $this->db->query('DELETE FROM contractors WHERE contractor_id = :contractor_id');
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
        $this->db->query('SELECT * FROM contractors WHERE contract_type = :type AND is_active = 1 ORDER BY contractor_name ASC');
        $this->db->bind(':type', $type);
        return $this->db->resultSet();
    }

    /**
     * Get contractors by specialization
     * @param string $specialization
     * @return array
     */
    public function getContractorsBySpecialization($specialization)
    {
        $this->db->query('SELECT * FROM contractors WHERE specialization = :specialization AND is_active = 1 ORDER BY contractor_name ASC');
        $this->db->bind(':specialization', $specialization);
        return $this->db->resultSet();
    }

    /**
     * Search contractors
     * @param string $search
     * @return array
     */
    public function searchContractors($search)
    {
        $this->db->query("
            SELECT * FROM contractors 
            WHERE (contractor_name LIKE :search 
                OR company_name LIKE :search 
                OR email LIKE :search 
                OR specialization LIKE :search)
            AND is_active = 1
            ORDER BY contractor_name ASC
        ");
        $this->db->bind(':search', '%' . $search . '%');
        return $this->db->resultSet();
    }

    /**
     * Calculate commission for a contractor
     * @param int $contractorId
     * @param float $amount
     * @return array
     */
    public function calculateCommission($contractorId, $amount)
    {
        $contractor = $this->getContractorById($contractorId);
        if (!$contractor) {
            return ['rate' => 0, 'amount' => 0];
        }

        $commission = ['rate' => 0, 'amount' => 0];

        switch ($contractor->commission_type) {
            case 'percentage':
                $commission['rate'] = $contractor->commission_rate;
                $commission['amount'] = ($amount * $contractor->commission_rate) / 100;
                break;

            case 'fixed':
                $commission['rate'] = 0;
                $commission['amount'] = $contractor->commission_rate;
                break;

            case 'tiered':
                if ($contractor->commission_tiers) {
                    $tiers = json_decode($contractor->commission_tiers, true);
                    foreach ($tiers as $tier) {
                        if ($amount >= $tier['min'] && ($tier['max'] === null || $amount <= $tier['max'])) {
                            $commission['rate'] = $tier['rate'];
                            $commission['amount'] = ($amount * $tier['rate']) / 100;
                            break;
                        }
                    }
                }
                break;
        }

        // Apply min/max limits
        if ($contractor->commission_min_amount && $commission['amount'] < $contractor->commission_min_amount) {
            $commission['amount'] = $contractor->commission_min_amount;
        }

        if ($contractor->commission_max_amount && $commission['amount'] > $contractor->commission_max_amount) {
            $commission['amount'] = $contractor->commission_max_amount;
        }

        return $commission;
    }

    /**
     * Record commission transaction
     * @param int $contractorId
     * @param float $amount
     * @param int|null $orderId
     * @param int|null $jobId
     * @param string $transactionType
     * @return bool
     */
    public function recordCommissionTransaction($contractorId, $amount, $orderId = null, $jobId = null, $transactionType = 'sale')
    {
        $commission = $this->calculateCommission($contractorId, $amount);

        $this->db->query("
            INSERT INTO commission_transactions (
                contractor_id, order_id, job_id, transaction_type,
                transaction_amount, commission_rate_applied, commission_amount,
                transaction_date
            ) VALUES (
                :contractor_id, :order_id, :job_id, :transaction_type,
                :transaction_amount, :commission_rate_applied, :commission_amount,
                CURDATE()
            )
        ");

        $this->db->bind(':contractor_id', $contractorId);
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':job_id', $jobId);
        $this->db->bind(':transaction_type', $transactionType);
        $this->db->bind(':transaction_amount', $amount);
        $this->db->bind(':commission_rate_applied', $commission['rate']);
        $this->db->bind(':commission_amount', $commission['amount']);

        if ($this->db->execute()) {
            // Update contractor's total commission earned
            $this->updateContractorCommissionTotal($contractorId, $commission['amount']);
            return true;
        }

        return false;
    }

    /**
     * Update contractor's total commission earned
     * @param int $contractorId
     * @param float $commissionAmount
     * @return bool
     */
    public function updateContractorCommissionTotal($contractorId, $commissionAmount)
    {
        $this->db->query("
            UPDATE contractors 
            SET total_commission_earned = total_commission_earned + :commission_amount,
                total_jobs_completed = total_jobs_completed + 1
            WHERE contractor_id = :contractor_id
        ");

        $this->db->bind(':contractor_id', $contractorId);
        $this->db->bind(':commission_amount', $commissionAmount);

        return $this->db->execute();
    }

    /**
     * Get commission history for a contractor
     * @param int $contractorId
     * @param int $limit
     * @return array
     */
    public function getCommissionHistory($contractorId, $limit = 50)
    {
        $this->db->query("
            SELECT ct.*, c.contractor_name 
            FROM commission_transactions ct
            JOIN contractors c ON ct.contractor_id = c.contractor_id
            WHERE ct.contractor_id = :contractor_id
            ORDER BY ct.transaction_date DESC, ct.created_at DESC
            LIMIT :limit
        ");

        $this->db->bind(':contractor_id', $contractorId);
        $this->db->bind(':limit', $limit);

        return $this->db->resultSet();
    }

    /**
     * Get total contractors count
     * @return int
     */
    public function getTotalContractors()
    {
        $this->db->query('SELECT COUNT(*) as count FROM contractors WHERE is_active = 1');
        return $this->db->single()->count;
    }

    /**
     * Get contractor statistics
     * @return object
     */
    public function getContractorStats()
    {
        $this->db->query("
            SELECT 
                COUNT(*) as total_contractors,
                COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_contractors,
                COUNT(CASE WHEN is_active = 0 THEN 1 END) as inactive_contractors,
                COUNT(CASE WHEN is_verified = 1 THEN 1 END) as verified_contractors,
                AVG(commission_rate) as average_commission_rate,
                SUM(total_commission_earned) as total_commissions_paid,
                SUM(total_jobs_completed) as total_jobs_completed
            FROM contractors
        ");
        return $this->db->single();
    }

    /**
     * Get commission summary by date range
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getCommissionSummary($startDate = null, $endDate = null)
    {
        $whereClause = "WHERE 1=1";
        if ($startDate) {
            $whereClause .= " AND ct.transaction_date >= :start_date";
        }
        if ($endDate) {
            $whereClause .= " AND ct.transaction_date <= :end_date";
        }

        $this->db->query("
            SELECT 
                c.contractor_name,
                c.specialization,
                COUNT(ct.commission_id) as transaction_count,
                SUM(ct.transaction_amount) as total_sales,
                SUM(ct.commission_amount) as total_commission,
                AVG(ct.commission_rate_applied) as avg_commission_rate
            FROM contractors c
            LEFT JOIN commission_transactions ct ON c.contractor_id = ct.contractor_id
            {$whereClause}
            GROUP BY c.contractor_id, c.contractor_name, c.specialization
            ORDER BY total_commission DESC
        ");

        if ($startDate) {
            $this->db->bind(':start_date', $startDate);
        }
        if ($endDate) {
            $this->db->bind(':end_date', $endDate);
        }

        return $this->db->resultSet();
    }
}
?>