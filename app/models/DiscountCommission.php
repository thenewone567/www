<?php
/**
 * DiscountCommission Model
 * Handles customer discount credits and contractor commissions in POS
 */
class DiscountCommission
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Scan unique ID and get user type and discount/commission info
     */
    public function scanUniqueId($uniqueId)
    {
        $uniqueId = trim($uniqueId);

        if (empty($uniqueId) || strlen($uniqueId) !== 12) {
            return ['success' => false, 'message' => 'Invalid unique ID format'];
        }

        // Determine user type from prefix
        $prefix = substr($uniqueId, 0, 2);

        switch ($prefix) {
            case 'CU': // Customer
                return $this->getCustomerDiscountInfo($uniqueId);
            case 'CO': // Contractor 
                return $this->getContractorCommissionInfo($uniqueId);
            case 'US': // User/Official
                return ['success' => false, 'message' => 'Officials cannot earn discounts or commissions in POS'];
            default:
                return ['success' => false, 'message' => 'Unknown unique ID prefix'];
        }
    }

    /**
     * Get customer discount information
     */
    private function getCustomerDiscountInfo($uniqueId)
    {
        $this->db->query("
            SELECT 
                c.customer_id,
                c.unique_id,
                c.customer_name,
                c.discount_credit_balance,
                c.total_discount_earned,
                c.total_discount_used,
                COUNT(cdc.credit_id) as active_credits_count,
                SUM(CASE WHEN cdc.status = 'active' THEN cdc.credit_amount ELSE 0 END) as available_credits
            FROM customers c
            LEFT JOIN customer_discount_credits cdc ON c.customer_id = cdc.customer_id AND cdc.status = 'active'
            WHERE c.unique_id = :unique_id
            GROUP BY c.customer_id
        ");

        $this->db->bind(':unique_id', $uniqueId);
        $this->db->execute();
        $customer = $this->db->single();

        if ($customer) {
            return [
                'success' => true,
                'type' => 'customer',
                'data' => [
                    'customer_id' => $customer->customer_id,
                    'unique_id' => $customer->unique_id,
                    'name' => $customer->customer_name,
                    'discount_balance' => floatval($customer->discount_credit_balance),
                    'available_credits' => floatval($customer->available_credits ?? 0),
                    'total_earned' => floatval($customer->total_discount_earned),
                    'total_used' => floatval($customer->total_discount_used),
                    'active_credits_count' => intval($customer->active_credits_count ?? 0)
                ]
            ];
        }

        return ['success' => false, 'message' => 'Customer not found'];
    }

    /**
     * Get contractor commission information
     */
    private function getContractorCommissionInfo($uniqueId)
    {
        $this->db->query("
            SELECT 
                co.contractor_id,
                co.unique_id,
                co.contractor_name,
                co.commission_rate,
                co.current_tier_achievement,
                co.commission_type,
                co.commission_tiers,
                co.total_revenue_generated,
                co.quarterly_revenue_generated,
                co.current_quarter_start,
                co.pending_commission_balance,
                co.total_commission_earned,
                co.total_commission_paid,
                COUNT(ccc.credit_id) as pending_commissions_count,
                SUM(CASE WHEN ccc.status = 'pending' THEN ccc.commission_amount ELSE 0 END) as pending_amount
            FROM contractors co
            LEFT JOIN contractor_commission_credits ccc ON co.contractor_id = ccc.contractor_id AND ccc.status = 'pending'
            WHERE co.unique_id = :unique_id
            GROUP BY co.contractor_id
        ");

        $this->db->bind(':unique_id', $uniqueId);
        $this->db->execute();
        $contractor = $this->db->single();

        if ($contractor) {
            // Check if we need to reset quarterly data
            $this->resetQuarterlyDataIfNeeded($contractor->contractor_id);

            $commissionType = $contractor->commission_type ?? 'percentage';
            $baseCommissionRate = floatval($contractor->commission_rate ?? 1.0);
            $quarterlyRevenue = floatval($contractor->quarterly_revenue_generated ?? 0);
            $totalRevenue = floatval($contractor->total_revenue_generated ?? 0);
            $currentAchievement = intval($contractor->current_tier_achievement ?? 1);

            // Use achievement-based tier for display, but check if quarterly revenue qualifies for upgrade
            $quarterlyTierLevel = $this->calculateTierLevelFromRevenue($quarterlyRevenue);
            $currentCommissionRate = $baseCommissionRate;

            // Create tier info based on current achievement (not quarterly revenue)
            $tierInfo = [
                'name' => $this->getTierNameFromAchievement($currentAchievement),
                'rate' => $this->getTierRateFromAchievement($currentAchievement),
                'min_revenue' => $this->getTierMinRevenue($currentAchievement),
                'max_revenue' => $this->getTierMaxRevenue($currentAchievement),
                'current_revenue' => $quarterlyRevenue,
                'progress_to_next' => $this->calculateProgressToNext($quarterlyRevenue, $currentAchievement),
                'can_upgrade' => $quarterlyTierLevel > $currentAchievement
            ];

            return [
                'success' => true,
                'type' => 'contractor',
                'data' => [
                    'contractor_id' => $contractor->contractor_id,
                    'unique_id' => $contractor->unique_id,
                    'name' => $contractor->contractor_name,
                    'commission_rate' => $currentCommissionRate,
                    'base_commission_rate' => $baseCommissionRate,
                    'commission_type' => $commissionType,
                    'commission_tiers' => $contractor->commission_tiers,
                    'total_revenue' => $quarterlyRevenue, // Show quarterly revenue as "total" for frontend
                    'quarterly_revenue' => $quarterlyRevenue,
                    'lifetime_revenue' => $totalRevenue,
                    'current_achievement' => $currentAchievement,
                    'tier_info' => $tierInfo,
                    'pending_balance' => floatval($contractor->pending_commission_balance),
                    'total_earned' => floatval($contractor->total_commission_earned),
                    'total_paid' => floatval($contractor->total_commission_paid),
                    'pending_count' => intval($contractor->pending_commissions_count ?? 0),
                    'pending_amount' => floatval($contractor->pending_amount ?? 0)
                ]
            ];
        }

        return ['success' => false, 'message' => 'Contractor not found'];
    }

    /**
     * Apply discount credits to a sale
     */
    public function applyDiscountCredits($customerId, $saleAmount, $creditsToUse = null)
    {
        try {
            // Get customer's available credits
            $this->db->query("
                SELECT discount_credit_balance 
                FROM customers 
                WHERE customer_id = :customer_id
            ");
            $this->db->bind(':customer_id', $customerId);
            $this->db->execute();
            $customer = $this->db->single();

            if (!$customer) {
                return ['success' => false, 'message' => 'Customer not found'];
            }

            $availableCredits = floatval($customer->discount_credit_balance);

            // If no specific amount requested, use maximum available (up to sale amount)
            if ($creditsToUse === null) {
                $creditsToUse = min($availableCredits, $saleAmount);
            } else {
                $creditsToUse = min($creditsToUse, $availableCredits, $saleAmount);
            }

            if ($creditsToUse <= 0) {
                return ['success' => false, 'message' => 'No credits available to apply'];
            }

            // Calculate discount amount (credits are 1:1 with discount dollars)
            $discountAmount = $creditsToUse;
            $finalAmount = $saleAmount - $discountAmount;

            return [
                'success' => true,
                'original_amount' => $saleAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'credits_used' => $creditsToUse,
                'remaining_credits' => $availableCredits - $creditsToUse
            ];

        } catch (Exception $e) {
            error_log("Discount credit calculation error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error calculating discount'];
        }
    }

    /**
     * Calculate commission for contractor referral
     */
    public function calculateCommission($contractorId, $saleAmount, $customerId = null)
    {
        try {
            // Get contractor commission rate and quarterly revenue
            $this->db->query("
                SELECT commission_rate, commission_type, commission_tiers, 
                       total_revenue_generated, quarterly_revenue_generated, current_quarter_start
                FROM contractors 
                WHERE contractor_id = :contractor_id
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->execute();
            $contractor = $this->db->single();

            if (!$contractor) {
                return ['success' => false, 'message' => 'Contractor not found'];
            }

            // Reset quarterly data if needed
            $this->resetQuarterlyDataIfNeeded($contractorId);

            // Get updated quarterly revenue after potential reset
            $this->db->query("
                SELECT quarterly_revenue_generated, commission_rate, commission_type, commission_tiers
                FROM contractors 
                WHERE contractor_id = :contractor_id
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->execute();
            $updatedContractor = $this->db->single();

            // Get minimum sale amount for commission from settings
            $this->db->query("
                SELECT setting_value 
                FROM settings 
                WHERE setting_key = 'commission_minimum_sale'
            ");
            $this->db->execute();
            $minSaleResult = $this->db->single();
            $minSaleAmount = floatval($minSaleResult->setting_value ?? 50.00);

            if ($saleAmount < $minSaleAmount) {
                return [
                    'success' => false,
                    'message' => "Sale amount must be at least $" . number_format($minSaleAmount, 2) . " to earn commission"
                ];
            }

            $commissionRate = floatval($updatedContractor->commission_rate ?? 1.0);
            $commissionType = $updatedContractor->commission_type ?? 'percentage';
            $commissionTiers = $updatedContractor->commission_tiers ?? null;
            $quarterlyRevenue = floatval($updatedContractor->quarterly_revenue_generated ?? 0);

            if ($commissionType === 'tiered' && $commissionTiers) {
                // Calculate tiered commission based on quarterly revenue
                $commissionRate = $this->calculateTieredCommissionRate($commissionTiers, $quarterlyRevenue);
                $commissionAmount = ($saleAmount * $commissionRate) / 100;
            } elseif ($commissionType === 'percentage') {
                $commissionAmount = ($saleAmount * $commissionRate) / 100;
            } else {
                $commissionAmount = $commissionRate; // Fixed amount
            }

            return [
                'success' => true,
                'sale_amount' => $saleAmount,
                'commission_rate' => $commissionRate,
                'commission_type' => $commissionType,
                'commission_amount' => round($commissionAmount, 2),
                'contractor_id' => $contractorId,
                'customer_id' => $customerId,
                'quarterly_revenue' => $quarterlyRevenue
            ];

        } catch (Exception $e) {
            error_log("Commission calculation error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error calculating commission'];
        }
    }

    /**
     * Process discount and commission after sale completion
     */
    public function processSaleRewards($saleId, $saleAmount, $customerId = null, $contractorId = null, $discountApplied = 0)
    {
        try {
            $this->db->beginTransaction();

            $results = [
                'discount_credits_earned' => 0,
                'commission_earned' => 0,
                'credits_used' => $discountApplied
            ];

            // Process customer discount credits (earn credits for purchase)
            if ($customerId && $saleAmount > 0) {
                $creditsEarned = $this->processCustomerDiscountCredits($saleId, $customerId, $saleAmount, $discountApplied);
                $results['discount_credits_earned'] = $creditsEarned;
            }

            // Process contractor commission
            if ($contractorId) {
                $commissionEarned = $this->processContractorCommission($saleId, $contractorId, $saleAmount, $customerId);
                $results['commission_earned'] = $commissionEarned;
            }

            $this->db->commit();
            return ['success' => true, 'results' => $results];

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Sale rewards processing error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error processing sale rewards'];
        }
    }

    /**
     * Process customer discount credits earned from purchase
     */
    private function processCustomerDiscountCredits($saleId, $customerId, $saleAmount, $discountUsed = 0)
    {
        // Get discount credit earning rate from settings
        $this->db->query("
            SELECT setting_value 
            FROM settings 
            WHERE setting_key = 'discount_credit_rate'
        ");
        $this->db->execute();
        $rateResult = $this->db->single();
        $creditRate = floatval($rateResult->setting_value ?? 2.0);

        // Get minimum purchase amount
        $this->db->query("
            SELECT setting_value 
            FROM settings 
            WHERE setting_key = 'discount_credit_minimum_purchase'
        ");
        $this->db->execute();
        $minResult = $this->db->single();
        $minPurchase = floatval($minResult->setting_value ?? 10.00);

        if ($saleAmount < $minPurchase) {
            return 0; // No credits earned for small purchases
        }

        // Calculate credits earned (percentage of purchase amount)
        $creditsEarned = ($saleAmount * $creditRate) / 100;
        $creditsEarned = round($creditsEarned, 2);

        if ($creditsEarned > 0) {
            // Get customer unique ID
            $this->db->query("SELECT unique_id FROM customers WHERE customer_id = :customer_id");
            $this->db->bind(':customer_id', $customerId);
            $this->db->execute();
            $customer = $this->db->single();

            // Add credits to customer_discount_credits table
            $this->db->query("
                INSERT INTO customer_discount_credits 
                (customer_id, unique_id, credit_amount, earned_from_sale_id, earned_date, status, notes) 
                VALUES 
                (:customer_id, :unique_id, :credit_amount, :sale_id, CURDATE(), 'active', :notes)
            ");

            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':unique_id', $customer->unique_id);
            $this->db->bind(':credit_amount', $creditsEarned);
            $this->db->bind(':sale_id', $saleId);
            $this->db->bind(':notes', "Earned {$creditRate}% credits from purchase of $" . number_format($saleAmount, 2));

            $this->db->execute();

            // Record the transaction
            $this->recordDiscountTransaction($saleId, $customerId, $customer->unique_id, 0, $discountUsed, $creditsEarned, 'credit_earned');
        }

        return $creditsEarned;
    }

    /**
     * Process contractor commission from referral sale
     */
    private function processContractorCommission($saleId, $contractorId, $saleAmount, $customerId = null)
    {
        $commissionResult = $this->calculateCommission($contractorId, $saleAmount, $customerId);

        if (!$commissionResult['success']) {
            return 0;
        }

        $commissionAmount = $commissionResult['commission_amount'];

        if ($commissionAmount > 0) {
            // Get contractor unique ID
            $this->db->query("SELECT unique_id FROM contractors WHERE contractor_id = :contractor_id");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->execute();
            $contractor = $this->db->single();

            // Add commission to contractor_commission_credits table
            $this->db->query("
                INSERT INTO contractor_commission_credits 
                (contractor_id, unique_id, commission_amount, earned_from_sale_id, reference_customer_id, commission_rate, earned_date, status, notes) 
                VALUES 
                (:contractor_id, :unique_id, :commission_amount, :sale_id, :customer_id, :commission_rate, CURDATE(), 'pending', :notes)
            ");

            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':unique_id', $contractor->unique_id);
            $this->db->bind(':commission_amount', $commissionAmount);
            $this->db->bind(':sale_id', $saleId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':commission_rate', $commissionResult['commission_rate']);
            $this->db->bind(':notes', "Earned {$commissionResult['commission_rate']}% commission from sale of $" . number_format($saleAmount, 2));

            $this->db->execute();

            // Update contractor revenue tracking (both quarterly and lifetime)
            $this->db->query("
                UPDATE contractors 
                SET quarterly_revenue_generated = quarterly_revenue_generated + :sale_amount,
                    total_revenue_generated = total_revenue_generated + :sale_amount,
                    pending_commission_balance = pending_commission_balance + :commission_amount
                WHERE contractor_id = :contractor_id
            ");
            $this->db->bind(':sale_amount', $saleAmount);
            $this->db->bind(':commission_amount', $commissionAmount);
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->execute();

            // Check if contractor should be promoted to higher tier based on new quarterly revenue
            $this->updateContractorTierAfterSale($contractorId);
        }

        return $commissionAmount;
    }

    /**
     * Update contractor tier after a sale if they qualify for promotion
     */
    private function updateContractorTierAfterSale($contractorId)
    {
        try {
            // Get updated quarterly revenue and current achievement
            $this->db->query("
                SELECT quarterly_revenue_generated, commission_type, commission_tiers, current_tier_achievement
                FROM contractors 
                WHERE contractor_id = :contractor_id
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->execute();
            $contractor = $this->db->single();

            if ($contractor && $contractor->commission_type === 'tiered' && $contractor->commission_tiers) {
                $quarterlyRevenue = floatval($contractor->quarterly_revenue_generated);
                $currentAchievement = intval($contractor->current_tier_achievement ?? 1);

                // Calculate what tier level this quarterly revenue qualifies for
                $newTierLevel = $this->calculateTierLevelFromRevenue($quarterlyRevenue);

                // Only update if the new tier is higher than current achievement (can't go down)
                if ($newTierLevel > $currentAchievement) {
                    $newCommissionRate = $this->getTierRateFromAchievement($newTierLevel);

                    $this->db->query("
                        UPDATE contractors 
                        SET commission_rate = :new_rate,
                            current_tier_achievement = :new_achievement
                        WHERE contractor_id = :contractor_id
                    ");
                    $this->db->bind(':new_rate', $newCommissionRate);
                    $this->db->bind(':new_achievement', $newTierLevel);
                    $this->db->bind(':contractor_id', $contractorId);
                    $this->db->execute();
                }
            }
        } catch (Exception $e) {
            error_log("Tier update error: " . $e->getMessage());
        }
    }

    /**
     * Calculate tier level (1-5) from quarterly revenue
     */
    private function calculateTierLevelFromRevenue($revenue)
    {
        if ($revenue >= 1000000)
            return 5; // Diamond
        if ($revenue >= 500000)
            return 4;  // Platinum  
        if ($revenue >= 250000)
            return 3;  // Gold
        if ($revenue >= 100000)
            return 2;  // Silver
        return 1; // Bronze
    }

    /**
     * Get minimum revenue for tier level
     */
    private function getTierMinRevenue($tierLevel)
    {
        $minimums = [
            1 => 0,       // Bronze
            2 => 100000,  // Silver
            3 => 250000,  // Gold  
            4 => 500000,  // Platinum
            5 => 1000000  // Diamond
        ];

        return $minimums[$tierLevel] ?? 0;
    }

    /**
     * Get maximum revenue for tier level
     */
    private function getTierMaxRevenue($tierLevel)
    {
        $maximums = [
            1 => 99999,   // Bronze
            2 => 249999,  // Silver
            3 => 499999,  // Gold
            4 => 999999,  // Platinum
            5 => null     // Diamond (unlimited)
        ];

        return $maximums[$tierLevel] ?? null;
    }

    /**
     * Calculate progress to next tier
     */
    private function calculateProgressToNext($currentRevenue, $tierLevel)
    {
        if ($tierLevel >= 5)
            return 100; // Already at max tier

        $nextTierMin = $this->getTierMinRevenue($tierLevel + 1);
        $currentTierMin = $this->getTierMinRevenue($tierLevel);

        if ($currentRevenue >= $nextTierMin)
            return 100;

        $tierRange = $nextTierMin - $currentTierMin;
        $progress = ($currentRevenue - $currentTierMin) / $tierRange * 100;

        return max(0, min(100, $progress));
    }

    /**
     * Use discount credits for a purchase
     */
    public function useDiscountCredits($customerId, $creditsToUse, $saleId)
    {
        try {
            // Get customer's active credits (oldest first for FIFO)
            $this->db->query("
                SELECT credit_id, credit_amount 
                FROM customer_discount_credits 
                WHERE customer_id = :customer_id AND status = 'active' 
                ORDER BY earned_date ASC, credit_id ASC
            ");
            $this->db->bind(':customer_id', $customerId);
            $this->db->execute();
            $activeCredits = $this->db->resultSet();

            $remainingToUse = $creditsToUse;
            $creditsUsed = 0;

            foreach ($activeCredits as $credit) {
                if ($remainingToUse <= 0)
                    break;

                $creditAmount = floatval($credit->credit_amount);
                $useFromThisCredit = min($remainingToUse, $creditAmount);

                if ($useFromThisCredit >= $creditAmount) {
                    // Use entire credit
                    $this->db->query("
                        UPDATE customer_discount_credits 
                        SET status = 'used', updated_at = CURRENT_TIMESTAMP 
                        WHERE credit_id = :credit_id
                    ");
                    $this->db->bind(':credit_id', $credit->credit_id);
                    $this->db->execute();
                } else {
                    // Partial use - split the credit
                    $remainingAmount = $creditAmount - $useFromThisCredit;

                    // Mark original as used
                    $this->db->query("
                        UPDATE customer_discount_credits 
                        SET status = 'used', updated_at = CURRENT_TIMESTAMP 
                        WHERE credit_id = :credit_id
                    ");
                    $this->db->bind(':credit_id', $credit->credit_id);
                    $this->db->execute();

                    // Create new credit for remainder
                    $this->db->query("
                        INSERT INTO customer_discount_credits 
                        (customer_id, unique_id, credit_amount, earned_from_sale_id, earned_date, status, notes) 
                        SELECT customer_id, unique_id, :remaining_amount, earned_from_sale_id, earned_date, 'active', 
                               CONCAT('Remaining balance after partial use in sale #', :sale_id)
                        FROM customer_discount_credits 
                        WHERE credit_id = :credit_id
                    ");
                    $this->db->bind(':remaining_amount', $remainingAmount);
                    $this->db->bind(':sale_id', $saleId);
                    $this->db->bind(':credit_id', $credit->credit_id);
                    $this->db->execute();
                }

                $creditsUsed += $useFromThisCredit;
                $remainingToUse -= $useFromThisCredit;
            }

            return $creditsUsed;

        } catch (Exception $e) {
            error_log("Use discount credits error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Record discount transaction for audit trail
     */
    private function recordDiscountTransaction($saleId, $customerId, $customerUniqueId, $discountAmount, $creditsUsed, $creditsEarned, $transactionType)
    {
        $this->db->query("
            INSERT INTO discount_transactions 
            (sale_id, customer_id, customer_unique_id, discount_amount, credits_used, credits_earned, transaction_type, transaction_date, notes) 
            VALUES 
            (:sale_id, :customer_id, :customer_unique_id, :discount_amount, :credits_used, :credits_earned, :transaction_type, CURDATE(), :notes)
        ");

        $notes = '';
        switch ($transactionType) {
            case 'credit_earned':
                $notes = "Earned $" . number_format($creditsEarned, 2) . " in discount credits";
                break;
            case 'credit_used':
                $notes = "Used $" . number_format($creditsUsed, 2) . " in discount credits";
                break;
            case 'discount_applied':
                $notes = "Applied $" . number_format($discountAmount, 2) . " discount";
                break;
        }

        $this->db->bind(':sale_id', $saleId);
        $this->db->bind(':customer_id', $customerId);
        $this->db->bind(':customer_unique_id', $customerUniqueId);
        $this->db->bind(':discount_amount', $discountAmount);
        $this->db->bind(':credits_used', $creditsUsed);
        $this->db->bind(':credits_earned', $creditsEarned);
        $this->db->bind(':transaction_type', $transactionType);
        $this->db->bind(':notes', $notes);

        $this->db->execute();
    }

    /**
     * Get discount/commission settings
     */
    public function getSettings()
    {
        $this->db->query("
            SELECT setting_key, setting_value, description 
            FROM settings 
            WHERE setting_key IN (
                'discount_credit_rate',
                'commission_rate_default', 
                'discount_credit_minimum_purchase',
                'commission_minimum_sale',
                'discount_credit_expiry_days',
                'allow_stacked_discounts'
            )
        ");

        $this->db->execute();
        $settings = $this->db->resultSet();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->setting_key] = $setting->setting_value;
        }

        return $result;
    }

    /**
     * Calculate commission rate based on tier system
     * @param string $tiersJson JSON string of tier configuration
     * @param float $totalRevenue Contractor's total revenue
     * @return float Commission rate percentage
     */
    private function calculateTieredCommissionRate($tiersJson, $totalRevenue)
    {
        try {
            $tiers = json_decode($tiersJson, true);
            if (!$tiers || !is_array($tiers)) {
                return 5.0; // Default fallback rate
            }

            // Sort tiers by minimum value to ensure correct order
            usort($tiers, function ($a, $b) {
                return ($a['min'] ?? 0) <=> ($b['min'] ?? 0);
            });

            // Find the appropriate tier based on total revenue
            $applicableRate = 1.0; // Default Bronze rate

            foreach ($tiers as $tier) {
                $min = floatval($tier['min'] ?? 0);
                $max = isset($tier['max']) ? floatval($tier['max']) : PHP_FLOAT_MAX;

                if ($totalRevenue >= $min && $totalRevenue <= $max) {
                    $applicableRate = floatval($tier['rate'] ?? 1.0);
                    break;
                }
            }

            return $applicableRate;

        } catch (Exception $e) {
            error_log("Tiered commission calculation error: " . $e->getMessage());
            return 5.0; // Default fallback rate
        }
    }

    /**
     * Get current tier information for a contractor
     * @param string $tiersJson JSON string of tier configuration
     * @param float $totalRevenue Contractor's total revenue
     * @return array|null Tier information
     */
    private function getCurrentTierInfo($tiersJson, $totalRevenue)
    {
        try {
            $tiers = json_decode($tiersJson, true);
            if (!$tiers || !is_array($tiers)) {
                return null;
            }

            // Define tier names based on standard system
            $tierNames = [
                1.0 => 'Bronze',
                2.0 => 'Silver',
                3.0 => 'Gold',
                4.0 => 'Platinum',
                5.0 => 'Diamond'
            ];

            // Sort tiers by minimum value
            usort($tiers, function ($a, $b) {
                return ($a['min'] ?? 0) <=> ($b['min'] ?? 0);
            });

            // Find current tier
            foreach ($tiers as $tier) {
                $min = floatval($tier['min'] ?? 0);
                $max = isset($tier['max']) ? floatval($tier['max']) : PHP_FLOAT_MAX;
                $rate = floatval($tier['rate'] ?? 1.0);

                if ($totalRevenue >= $min && $totalRevenue <= $max) {
                    return [
                        'name' => $tierNames[$rate] ?? 'Custom',
                        'rate' => $rate,
                        'min_revenue' => $min,
                        'max_revenue' => $max === PHP_FLOAT_MAX ? null : $max,
                        'current_revenue' => $totalRevenue,
                        'progress_to_next' => $max === PHP_FLOAT_MAX ? 100 : min(100, ($totalRevenue / $max) * 100)
                    ];
                }
            }

            // Default to first tier if no match
            $firstTier = $tiers[0] ?? ['min' => 0, 'rate' => 1.0];
            $rate = floatval($firstTier['rate'] ?? 1.0);

            return [
                'name' => $tierNames[$rate] ?? 'Bronze',
                'rate' => $rate,
                'min_revenue' => floatval($firstTier['min'] ?? 0),
                'max_revenue' => isset($firstTier['max']) ? floatval($firstTier['max']) : null,
                'current_revenue' => $totalRevenue,
                'progress_to_next' => 0
            ];

        } catch (Exception $e) {
            error_log("Tier info calculation error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Reset quarterly data if we're in a new quarter
     */
    private function resetQuarterlyDataIfNeeded($contractorId)
    {
        try {
            $currentQuarterStart = $this->getCurrentQuarterStart();
            $currentQuarterLabel = $this->getCurrentQuarterLabel();

            // Get contractor's current quarter and tier info
            $this->db->query("
                SELECT current_quarter_start, current_tier_achievement, tier_earned_quarter,
                       commission_rate, commission_type, commission_tiers
                FROM contractors 
                WHERE contractor_id = :contractor_id
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->execute();
            $contractor = $this->db->single();

            $contractorQuarterStart = $contractor->current_quarter_start ?? null;

            if ($contractorQuarterStart !== $currentQuarterStart) {
                $currentTierAchievement = $contractor->current_tier_achievement ?? 1;
                $tierEarnedQuarter = $contractor->tier_earned_quarter ?? null;

                // Determine if we should preserve the tier (only for 1 quarter)
                $previousQuarterLabel = $this->getPreviousQuarterLabel($currentQuarterLabel);
                $shouldPreserveTier = ($tierEarnedQuarter === $previousQuarterLabel);

                if ($shouldPreserveTier) {
                    // Preserve tier for one more quarter (the grace period)
                    $tierRate = $this->getTierRateFromAchievement($currentTierAchievement);

                    $this->db->query("
                        UPDATE contractors 
                        SET quarterly_revenue_generated = 0.00,
                            current_quarter_start = :quarter_start,
                            commission_rate = :tier_rate
                        WHERE contractor_id = :contractor_id
                    ");
                    $this->db->bind(':quarter_start', $currentQuarterStart);
                    $this->db->bind(':tier_rate', $tierRate);
                    $this->db->bind(':contractor_id', $contractorId);
                    $this->db->execute();
                } else {
                    // Reset to Bronze after grace period expires
                    $this->db->query("
                        UPDATE contractors 
                        SET quarterly_revenue_generated = 0.00,
                            current_quarter_start = :quarter_start,
                            commission_rate = 1.00,
                            current_tier_achievement = 1,
                            tier_earned_quarter = NULL
                        WHERE contractor_id = :contractor_id
                    ");
                    $this->db->bind(':quarter_start', $currentQuarterStart);
                    $this->db->bind(':contractor_id', $contractorId);
                    $this->db->execute();
                }

                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log("Quarterly reset error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get tier rate from achievement level
     */
    private function getTierRateFromAchievement($achievementLevel)
    {
        $tierRates = [
            1 => 1.0, // Bronze
            2 => 2.0, // Silver  
            3 => 3.0, // Gold
            4 => 4.0, // Platinum
            5 => 5.0  // Diamond
        ];

        return $tierRates[$achievementLevel] ?? 1.0;
    }

    /**
     * Get tier name from achievement level
     */
    private function getTierNameFromAchievement($achievementLevel)
    {
        $tierNames = [
            1 => 'Bronze',
            2 => 'Silver',
            3 => 'Gold',
            4 => 'Platinum',
            5 => 'Diamond'
        ];

        return $tierNames[$achievementLevel] ?? 'Bronze';
    }

    /**
     * Get current quarter start date
     */
    private function getCurrentQuarterStart()
    {
        $currentMonth = date('n'); // 1-12
        $currentYear = date('Y');

        if ($currentMonth >= 1 && $currentMonth <= 3) {
            // Q1: January - March
            return $currentYear . '-01-01';
        } elseif ($currentMonth >= 4 && $currentMonth <= 6) {
            // Q2: April - June  
            return $currentYear . '-04-01';
        } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
            // Q3: July - September
            return $currentYear . '-07-01';
        } else {
            // Q4: October - December
            return $currentYear . '-10-01';
        }
    }

    /**
     * Get current quarter label (e.g., '2025-Q3')
     */
    private function getCurrentQuarterLabel()
    {
        $currentMonth = date('n'); // 1-12
        $currentYear = date('Y');

        if ($currentMonth >= 1 && $currentMonth <= 3) {
            return $currentYear . '-Q1';
        } elseif ($currentMonth >= 4 && $currentMonth <= 6) {
            return $currentYear . '-Q2';
        } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
            return $currentYear . '-Q3';
        } else {
            return $currentYear . '-Q4';
        }
    }

    /**
     * Get previous quarter label
     */
    private function getPreviousQuarterLabel($currentQuarterLabel)
    {
        if (preg_match('/(\d{4})-Q(\d)/', $currentQuarterLabel, $matches)) {
            $year = intval($matches[1]);
            $quarter = intval($matches[2]);

            if ($quarter == 1) {
                return ($year - 1) . '-Q4';
            } else {
                return $year . '-Q' . ($quarter - 1);
            }
        }

        return null;
    }
}
?>