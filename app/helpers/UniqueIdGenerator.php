<?php

/**
 * UniqueIdGenerator Class
 * Handles generation and management of 12-digit unique tracking IDs
 * for users, contractors, and customers
 * 
 * Format: XX + yymmddms + RR = 12 characters
 * Where: XX=prefix, yy=year, mm=month, dd=day, ms=milliseconds, RR=random
 */
class UniqueIdGenerator
{
    private $db;

    // ID prefixes for different entity types
    const PREFIXES = [
        'user' => 'US',
        'customer' => 'CU',
        'contractor' => 'CO'
    ];

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Generate a unique 12-digit ID for the specified entity type
     * Format: XX + 8-digit yymmddms + 2-digit random = 12 characters
     * Where: yy=year, mm=month, dd=day, ms=milliseconds
     * 
     * @param string $entityType The type of entity (user, customer, contractor)
     * @return string 12-character unique ID
     */
    public function generateUniqueId($entityType)
    {
        $prefix = self::PREFIXES[$entityType] ?? 'XX';
        $attempts = 0;
        $maxAttempts = 100;

        do {
            // Generate yymmddms format (8 digits)
            // yy = last 2 digits of year
            // mm = month (01-12)
            // dd = day (01-31)
            // ms = milliseconds component (00-99)
            $now = new DateTime();
            $year = substr($now->format('Y'), -2);  // Last 2 digits of year
            $month = $now->format('m');             // Month with leading zero
            $day = $now->format('d');               // Day with leading zero

            // Get milliseconds from microtime (0-999, take last 2 digits)
            $microtime = microtime(true);
            $milliseconds = str_pad((int) (($microtime * 1000) % 100), 2, '0', STR_PAD_LEFT);

            $timestampPart = $year . $month . $day . $milliseconds;

            // Generate 2-digit random number
            $randomPart = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);

            // Combine parts
            $uniqueId = $prefix . $timestampPart . $randomPart;

            $attempts++;

            // Check if this ID already exists
            if (!$this->idExists($uniqueId)) {
                return $uniqueId;
            }

            // Add small delay to ensure different timestamps
            usleep(1000); // 1ms delay

        } while ($attempts < $maxAttempts);

        // Fallback: use microseconds and counter if all attempts failed
        $microTime = substr(str_replace('.', '', (string) microtime(true)), -8);
        $counter = str_pad($attempts, 2, '0', STR_PAD_LEFT);

        return $prefix . $microTime . $counter;
    }

    /**
     * Check if a unique ID already exists in any table
     * 
     * @param string $uniqueId The ID to check
     * @return bool True if ID exists, false otherwise
     */
    private function idExists($uniqueId)
    {
        try {
            $this->db->query("
                SELECT COUNT(*) as count FROM (
                    SELECT unique_id FROM users WHERE unique_id = :unique_id
                    UNION ALL
                    SELECT unique_id FROM customers WHERE unique_id = :unique_id
                    UNION ALL  
                    SELECT unique_id FROM contractors WHERE unique_id = :unique_id
                ) AS combined_ids
            ");

            $this->db->bind(':unique_id', $uniqueId);
            $result = $this->db->single();

            return $result->count > 0;

        } catch (Exception $e) {
            error_log('UniqueIdGenerator::idExists error: ' . $e->getMessage());
            return true; // Assume exists to be safe
        }
    }

    /**
     * Assign unique IDs to existing records that don't have them
     * 
     * @return array Summary of assigned IDs
     */
    public function assignUniqueIdsToExistingRecords()
    {
        $summary = [
            'users' => 0,
            'customers' => 0,
            'contractors' => 0,
            'errors' => []
        ];

        // Update users
        try {
            $this->db->query("SELECT user_id FROM users WHERE unique_id IS NULL OR unique_id = ''");
            $users = $this->db->resultSet();

            foreach ($users as $user) {
                $uniqueId = $this->generateUniqueId('user');
                $this->db->query("UPDATE users SET unique_id = :unique_id WHERE user_id = :user_id");
                $this->db->bind(':unique_id', $uniqueId);
                $this->db->bind(':user_id', $user->user_id);

                if ($this->db->execute()) {
                    $summary['users']++;
                }
            }
        } catch (Exception $e) {
            $summary['errors'][] = 'Users: ' . $e->getMessage();
        }

        // Update customers
        try {
            $this->db->query("SELECT customer_id FROM customers WHERE unique_id IS NULL OR unique_id = ''");
            $customers = $this->db->resultSet();

            foreach ($customers as $customer) {
                $uniqueId = $this->generateUniqueId('customer');
                $this->db->query("UPDATE customers SET unique_id = :unique_id WHERE customer_id = :customer_id");
                $this->db->bind(':unique_id', $uniqueId);
                $this->db->bind(':customer_id', $customer->customer_id);

                if ($this->db->execute()) {
                    $summary['customers']++;
                }
            }
        } catch (Exception $e) {
            $summary['errors'][] = 'Customers: ' . $e->getMessage();
        }

        // Update contractors
        try {
            $this->db->query("SELECT contractor_id FROM contractors WHERE unique_id IS NULL OR unique_id = ''");
            $contractors = $this->db->resultSet();

            foreach ($contractors as $contractor) {
                $uniqueId = $this->generateUniqueId('contractor');
                $this->db->query("UPDATE contractors SET unique_id = :unique_id WHERE contractor_id = :contractor_id");
                $this->db->bind(':unique_id', $uniqueId);
                $this->db->bind(':contractor_id', $contractor->contractor_id);

                if ($this->db->execute()) {
                    $summary['contractors']++;
                }
            }
        } catch (Exception $e) {
            $summary['errors'][] = 'Contractors: ' . $e->getMessage();
        }

        return $summary;
    }

    /**
     * Get entity information by unique ID
     * 
     * @param string $uniqueId The unique ID to lookup
     * @return object|null Entity information or null if not found
     */
    public function getEntityByUniqueId($uniqueId)
    {
        try {
            // Determine entity type by prefix
            $prefix = substr($uniqueId, 0, 2);
            $entityType = array_search($prefix, self::PREFIXES);

            if (!$entityType) {
                return null;
            }

            switch ($entityType) {
                case 'user':
                    $this->db->query("
                        SELECT user_id as id, unique_id, username, name, email, 
                               is_active, created_at, 'user' as entity_type
                        FROM users WHERE unique_id = :unique_id
                    ");
                    break;

                case 'customer':
                    $this->db->query("
                        SELECT customer_id as id, unique_id, customer_name as name, 
                               contact_info, credit_limit, created_at, 'customer' as entity_type
                        FROM customers WHERE unique_id = :unique_id
                    ");
                    break;

                case 'contractor':
                    $this->db->query("
                        SELECT contractor_id as id, unique_id, contractor_name as name,
                               contact_info, commission_rate, status, created_at, 'contractor' as entity_type
                        FROM contractors WHERE unique_id = :unique_id
                    ");
                    break;
            }

            $this->db->bind(':unique_id', $uniqueId);
            return $this->db->single();

        } catch (Exception $e) {
            error_log('UniqueIdGenerator::getEntityByUniqueId error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate unique ID format
     * Expected format: XX + yymmddms + RR (12 characters)
     * 
     * @param string $uniqueId The ID to validate
     * @return bool True if valid format, false otherwise
     */
    public function validateUniqueIdFormat($uniqueId)
    {
        // Must be exactly 12 characters
        if (strlen($uniqueId) !== 12) {
            return false;
        }

        // First 2 characters must be valid prefix
        $prefix = substr($uniqueId, 0, 2);
        if (!in_array($prefix, self::PREFIXES)) {
            return false;
        }

        // Remaining 10 characters must be digits (yymmddms + random)
        $remaining = substr($uniqueId, 2);
        if (!ctype_digit($remaining)) {
            return false;
        }

        return true;
    }

    /**
     * Get statistics about unique IDs in the system
     * 
     * @return array Statistics summary
     */
    public function getUniqueIdStatistics()
    {
        try {
            $stats = [];

            // Count users with unique IDs
            $this->db->query("SELECT COUNT(*) as count FROM users WHERE unique_id IS NOT NULL AND unique_id != ''");
            $stats['users_with_ids'] = $this->db->single()->count;

            $this->db->query("SELECT COUNT(*) as count FROM users");
            $stats['total_users'] = $this->db->single()->count;

            // Count customers with unique IDs
            $this->db->query("SELECT COUNT(*) as count FROM customers WHERE unique_id IS NOT NULL AND unique_id != ''");
            $stats['customers_with_ids'] = $this->db->single()->count;

            $this->db->query("SELECT COUNT(*) as count FROM customers");
            $stats['total_customers'] = $this->db->single()->count;

            // Count contractors with unique IDs
            $this->db->query("SELECT COUNT(*) as count FROM contractors WHERE unique_id IS NOT NULL AND unique_id != ''");
            $stats['contractors_with_ids'] = $this->db->single()->count;

            $this->db->query("SELECT COUNT(*) as count FROM contractors");
            $stats['total_contractors'] = $this->db->single()->count;

            return $stats;

        } catch (Exception $e) {
            error_log('UniqueIdGenerator::getUniqueIdStatistics error: ' . $e->getMessage());
            return [];
        }
    }
}
