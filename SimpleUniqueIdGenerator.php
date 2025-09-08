<?php
/**
 * Simple Unique ID Generator for direct database operations
 */
class SimpleUniqueIdGenerator
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=master_hardware', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Generate a unique ID for the specified entity type
     * Format: XX + 8-digit yymmddms + 2-digit random = 12 characters
     */
    public function generateUniqueId($entityType)
    {
        $prefix = $this->getPrefix($entityType);
        $maxAttempts = 100;
        $attempts = 0;

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

        } while ($this->idExists($uniqueId, $entityType) && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            throw new Exception("Failed to generate unique ID after {$maxAttempts} attempts");
        }

        return $uniqueId;
    }

    /**
     * Check if ID exists in the database
     */
    private function idExists($uniqueId, $entityType)
    {
        $table = $this->getTableName($entityType);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE unique_id = ?");
        $stmt->execute([$uniqueId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get prefix for entity type
     */
    private function getPrefix($entityType)
    {
        switch (strtolower($entityType)) {
            case 'user':
                return 'US';
            case 'customer':
                return 'CU';
            case 'contractor':
                return 'CO';
            default:
                return 'XX';
        }
    }

    /**
     * Get table name for entity type
     */
    private function getTableName($entityType)
    {
        switch (strtolower($entityType)) {
            case 'user':
                return 'users';
            case 'customer':
                return 'customers';
            case 'contractor':
                return 'contractors';
            default:
                throw new Exception("Unknown entity type: {$entityType}");
        }
    }

    /**
     * Validate unique ID format
     * Expected format: XX + yymmddms + RR (12 characters)
     */
    public function validateUniqueIdFormat($uniqueId)
    {
        // Check length
        if (strlen($uniqueId) !== 12) {
            return false;
        }

        // Check prefix
        $prefix = substr($uniqueId, 0, 2);
        if (!in_array($prefix, ['US', 'CU', 'CO'])) {
            return false;
        }

        // Check if remaining 10 characters are numeric (yymmddms + random)
        $numberPart = substr($uniqueId, 2);
        if (!ctype_digit($numberPart)) {
            return false;
        }

        return true;
    }
}
?>