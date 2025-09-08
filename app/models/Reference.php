<?php

/**
 * Reference Model
 * Handles customer references and commission system operations
 */
class Reference
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all customer references with contractor and customer details
     */
    public function getAllReferences()
    {
        try {
            $this->db->query("
                SELECT 
                    cr.*,
                    c.contractor_name,
                    c.contact_info as contractor_contact,
                    c.commission_rate as contractor_default_rate,
                    cust.customer_name,
                    cust.contact_info as customer_contact,
                    COUNT(com.commission_id) as total_commissions,
                    COALESCE(SUM(com.commission_amount), 0) as total_commission_earned
                FROM customer_references cr
                INNER JOIN contractors c ON cr.contractor_id = c.contractor_id
                INNER JOIN customers cust ON cr.customer_id = cust.customer_id
                LEFT JOIN commissions com ON cr.reference_id = com.reference_id AND com.status != 'cancelled'
                GROUP BY cr.reference_id
                ORDER BY cr.created_at DESC
            ");

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getAllReferences error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get references by contractor ID
     */
    public function getReferencesByContractor($contractorId)
    {
        try {
            $this->db->query("
                SELECT 
                    cr.*,
                    cust.customer_name,
                    cust.contact_info as customer_contact,
                    COUNT(com.commission_id) as total_commissions,
                    COALESCE(SUM(com.commission_amount), 0) as total_commission_earned
                FROM customer_references cr
                INNER JOIN customers cust ON cr.customer_id = cust.customer_id
                LEFT JOIN commissions com ON cr.reference_id = com.reference_id AND com.status != 'cancelled'
                WHERE cr.contractor_id = :contractor_id
                GROUP BY cr.reference_id
                ORDER BY cr.created_at DESC
            ");

            $this->db->bind(':contractor_id', $contractorId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getReferencesByContractor error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new customer reference
     */
    public function createReference($contractorId, $customerId, $notes = '')
    {
        try {
            $this->db->query("
                INSERT INTO customer_references (contractor_id, customer_id, notes, status)
                VALUES (:contractor_id, :customer_id, :notes, 'active')
            ");

            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':notes', $notes);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log('createReference error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all contractors for reference creation
     */
    public function getAllContractors()
    {
        try {
            $this->db->query("
                SELECT 
                    contractor_id,
                    contractor_name,
                    contact_info,
                    commission_rate,
                    status
                FROM contractors 
                WHERE status = 'active'
                ORDER BY contractor_name ASC
            ");

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getAllContractors error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all customers for reference creation
     */
    public function getAllCustomers()
    {
        try {
            $this->db->query("
                SELECT 
                    customer_id,
                    customer_name,
                    contact_info,
                    status
                FROM customers 
                WHERE status = 'active'
                ORDER BY customer_name ASC
            ");

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getAllCustomers error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get customers not yet referred by a specific contractor
     */
    public function getAvailableCustomersForContractor($contractorId)
    {
        try {
            $this->db->query("
                SELECT 
                    c.customer_id,
                    c.customer_name,
                    c.contact_info,
                    c.status
                FROM customers c
                WHERE c.status = 'active'
                AND c.customer_id NOT IN (
                    SELECT cr.customer_id 
                    FROM customer_references cr 
                    WHERE cr.contractor_id = :contractor_id
                )
                ORDER BY c.customer_name ASC
            ");

            $this->db->bind(':contractor_id', $contractorId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getAvailableCustomersForContractor error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create commission for a sale
     */
    public function createCommission($referenceId, $saleId, $saleAmount, $commissionRate = null)
    {
        try {
            // Get reference details
            $this->db->query("
                SELECT cr.*, c.commission_rate as contractor_default_rate
                FROM customer_references cr
                INNER JOIN contractors c ON cr.contractor_id = c.contractor_id
                WHERE cr.reference_id = :reference_id
            ");
            $this->db->bind(':reference_id', $referenceId);
            $reference = $this->db->single();

            if (!$reference) {
                throw new Exception('Reference not found');
            }

            // Use provided rate or contractor's default rate
            $rate = $commissionRate ?? $reference->contractor_default_rate ?? 5.00;
            $commissionAmount = ($saleAmount * $rate) / 100;

            // Insert commission record
            $this->db->query("
                INSERT INTO commissions (
                    reference_id, sale_id, contractor_id, customer_id,
                    sale_amount, commission_rate, commission_amount, status
                ) VALUES (
                    :reference_id, :sale_id, :contractor_id, :customer_id,
                    :sale_amount, :commission_rate, :commission_amount, 'pending'
                )
            ");

            $this->db->bind(':reference_id', $referenceId);
            $this->db->bind(':sale_id', $saleId);
            $this->db->bind(':contractor_id', $reference->contractor_id);
            $this->db->bind(':customer_id', $reference->customer_id);
            $this->db->bind(':sale_amount', $saleAmount);
            $this->db->bind(':commission_rate', $rate);
            $this->db->bind(':commission_amount', $commissionAmount);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log('createCommission error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get commission summary from view
     */
    public function getCommissionSummary($limit = 50)
    {
        try {
            $this->db->query("
                SELECT * FROM commission_summary 
                ORDER BY commission_date DESC 
                LIMIT :limit
            ");
            $this->db->bind(':limit', $limit);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getCommissionSummary error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update commission status
     */
    public function updateCommissionStatus($commissionId, $status, $paymentDate = null)
    {
        try {
            $sql = "UPDATE commissions SET status = :status, updated_at = CURRENT_TIMESTAMP";
            $params = [':status' => $status, ':commission_id' => $commissionId];

            if ($status === 'paid' && $paymentDate) {
                $sql .= ", payment_date = :payment_date";
                $params[':payment_date'] = $paymentDate;
            }

            $sql .= " WHERE commission_id = :commission_id";

            $this->db->query($sql);
            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('updateCommissionStatus error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get contractor commission statistics
     */
    public function getContractorStats($contractorId)
    {
        try {
            $this->db->query("
                SELECT 
                    COUNT(DISTINCT cr.reference_id) as total_references,
                    COUNT(DISTINCT com.commission_id) as total_commissions,
                    COALESCE(SUM(CASE WHEN com.status = 'pending' THEN com.commission_amount END), 0) as pending_amount,
                    COALESCE(SUM(CASE WHEN com.status = 'approved' THEN com.commission_amount END), 0) as approved_amount,
                    COALESCE(SUM(CASE WHEN com.status = 'paid' THEN com.commission_amount END), 0) as paid_amount,
                    COALESCE(SUM(com.commission_amount), 0) as total_earned
                FROM customer_references cr
                LEFT JOIN commissions com ON cr.reference_id = com.reference_id AND com.status != 'cancelled'
                WHERE cr.contractor_id = :contractor_id
            ");

            $this->db->bind(':contractor_id', $contractorId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('getContractorStats error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a reference (and cascade delete related commissions)
     */
    public function deleteReference($referenceId)
    {
        try {
            $this->db->query("DELETE FROM customer_references WHERE reference_id = :reference_id");
            $this->db->bind(':reference_id', $referenceId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('deleteReference error: ' . $e->getMessage());
            return false;
        }
    }

    // ===============================
    // TARGET-BASED COMMISSION METHODS
    // ===============================

    /**
     * Get all commission target tiers
     */
    public function getCommissionTargetTiers()
    {
        try {
            $this->db->query("
                SELECT * FROM commission_target_tiers 
                WHERE is_active = TRUE 
                ORDER BY min_monthly_sales ASC
            ");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getCommissionTargetTiers error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate target-based commission tier for given sales amount
     */
    public function calculateCommissionTier($monthlySales)
    {
        try {
            $this->db->query("
                SELECT * FROM commission_target_tiers 
                WHERE is_active = TRUE 
                AND min_monthly_sales <= :monthly_sales 
                AND (max_monthly_sales IS NULL OR max_monthly_sales >= :monthly_sales)
                ORDER BY min_monthly_sales DESC 
                LIMIT 1
            ");
            $this->db->bind(':monthly_sales', $monthlySales);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('calculateCommissionTier error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add a sale transaction for target-based commission calculation
     */
    public function addSaleTransaction($contractorId, $saleAmount, $transactionType = 'referred_sale', $customerId = null, $saleId = null, $transactionDate = null)
    {
        try {
            if (!$transactionDate) {
                $transactionDate = date('Y-m-d');
            }

            $month = date('Y-m', strtotime($transactionDate));

            // Get or create monthly summary
            $summaryId = $this->getOrCreateMonthlySummary($contractorId, $month);

            if (!$summaryId) {
                throw new Exception('Failed to get monthly summary');
            }

            // Calculate current tier for this transaction
            $currentSales = $this->getMonthlyTotalSales($contractorId, $month);
            $newTotalSales = $currentSales + $saleAmount;
            $tier = $this->calculateCommissionTier($newTotalSales);

            $commissionPercentage = $tier ? $tier->commission_percentage : 1.00;
            $commissionAmount = ($saleAmount * $commissionPercentage) / 100;

            // Insert transaction record
            $this->db->query("
                INSERT INTO commission_transactions (
                    monthly_summary_id, contractor_id, sale_id, customer_id,
                    transaction_type, sale_amount, transaction_date,
                    commission_percentage, commission_amount, notes
                ) VALUES (
                    :monthly_summary_id, :contractor_id, :sale_id, :customer_id,
                    :transaction_type, :sale_amount, :transaction_date,
                    :commission_percentage, :commission_amount, :notes
                )
            ");

            $this->db->bind(':monthly_summary_id', $summaryId);
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':sale_id', $saleId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':transaction_type', $transactionType);
            $this->db->bind(':sale_amount', $saleAmount);
            $this->db->bind(':transaction_date', $transactionDate);
            $this->db->bind(':commission_percentage', $commissionPercentage);
            $this->db->bind(':commission_amount', $commissionAmount);
            $this->db->bind(':notes', "Auto-generated for $transactionType");

            if ($this->db->execute()) {
                // Update monthly summary
                $this->updateMonthlySummary($contractorId, $month);
                return $this->db->lastInsertId();
            }

            return false;
        } catch (Exception $e) {
            error_log('addSaleTransaction error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get or create monthly commission summary for contractor
     */
    private function getOrCreateMonthlySummary($contractorId, $month)
    {
        try {
            // Try to get existing summary
            $this->db->query("
                SELECT summary_id FROM monthly_commission_summary 
                WHERE contractor_id = :contractor_id AND month = :month
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':month', $month);

            $existing = $this->db->single();
            if ($existing) {
                return $existing->summary_id;
            }

            // Create new summary
            $this->db->query("
                INSERT INTO monthly_commission_summary (contractor_id, month, status)
                VALUES (:contractor_id, :month, 'calculating')
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':month', $month);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (Exception $e) {
            error_log('getOrCreateMonthlySummary error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update monthly summary calculations
     */
    private function updateMonthlySummary($contractorId, $month)
    {
        try {
            // Get current month's totals
            $this->db->query("
                SELECT 
                    SUM(CASE WHEN transaction_type = 'referred_sale' THEN sale_amount ELSE 0 END) as total_referred_sales,
                    SUM(CASE WHEN transaction_type = 'own_purchase' THEN sale_amount ELSE 0 END) as total_own_purchases,
                    SUM(sale_amount) as total_monthly_sales,
                    SUM(commission_amount) as total_commission_amount
                FROM commission_transactions ct
                INNER JOIN monthly_commission_summary mcs ON ct.monthly_summary_id = mcs.summary_id
                WHERE mcs.contractor_id = :contractor_id AND mcs.month = :month
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':month', $month);

            $totals = $this->db->single();

            if ($totals) {
                $totalSales = $totals->total_monthly_sales ?? 0;
                $tier = $this->calculateCommissionTier($totalSales);

                // Update monthly summary
                $this->db->query("
                    UPDATE monthly_commission_summary SET
                        total_referred_sales = :total_referred_sales,
                        total_own_purchases = :total_own_purchases,
                        total_monthly_sales = :total_monthly_sales,
                        achieved_tier_id = :achieved_tier_id,
                        commission_percentage = :commission_percentage,
                        total_commission_amount = :total_commission_amount,
                        calculation_date = CURRENT_TIMESTAMP,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE contractor_id = :contractor_id AND month = :month
                ");

                $this->db->bind(':total_referred_sales', $totals->total_referred_sales ?? 0);
                $this->db->bind(':total_own_purchases', $totals->total_own_purchases ?? 0);
                $this->db->bind(':total_monthly_sales', $totalSales);
                $this->db->bind(':achieved_tier_id', $tier ? $tier->tier_id : null);
                $this->db->bind(':commission_percentage', $tier ? $tier->commission_percentage : 1.00);
                $this->db->bind(':total_commission_amount', $totals->total_commission_amount ?? 0);
                $this->db->bind(':contractor_id', $contractorId);
                $this->db->bind(':month', $month);

                return $this->db->execute();
            }

            return false;
        } catch (Exception $e) {
            error_log('updateMonthlySummary error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get monthly total sales for a contractor
     */
    private function getMonthlyTotalSales($contractorId, $month)
    {
        try {
            $this->db->query("
                SELECT COALESCE(total_monthly_sales, 0) as total_sales
                FROM monthly_commission_summary 
                WHERE contractor_id = :contractor_id AND month = :month
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':month', $month);

            $result = $this->db->single();
            return $result ? $result->total_sales : 0;
        } catch (Exception $e) {
            error_log('getMonthlyTotalSales error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get target-based commission summary from view
     */
    public function getTargetCommissionSummary($limit = 50)
    {
        try {
            $this->db->query("
                SELECT * FROM target_commission_summary 
                ORDER BY month DESC, total_monthly_sales DESC 
                LIMIT :limit
            ");
            $this->db->bind(':limit', $limit);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getTargetCommissionSummary error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get contractor's target performance for current month
     */
    public function getContractorMonthlyPerformance($contractorId, $month = null)
    {
        try {
            if (!$month) {
                $month = date('Y-m');
            }

            $this->db->query("
                SELECT * FROM target_commission_summary 
                WHERE contractor_id = :contractor_id AND month = :month
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':month', $month);

            return $this->db->single();
        } catch (Exception $e) {
            error_log('getContractorMonthlyPerformance error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Finalize monthly commissions for a specific month
     */
    public function finalizeMonthlyCommissions($month)
    {
        try {
            $this->db->query("
                UPDATE monthly_commission_summary 
                SET status = 'finalized', updated_at = CURRENT_TIMESTAMP
                WHERE month = :month AND status = 'calculating'
            ");
            $this->db->bind(':month', $month);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('finalizeMonthlyCommissions error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark monthly commission as paid
     */
    public function markCommissionAsPaid($summaryId)
    {
        try {
            $this->db->query("
                UPDATE monthly_commission_summary 
                SET status = 'paid', payment_date = CURRENT_DATE, updated_at = CURRENT_TIMESTAMP
                WHERE summary_id = :summary_id
            ");
            $this->db->bind(':summary_id', $summaryId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('markCommissionAsPaid error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate all commission transactions for a specific month
     * Useful when tier rates change
     */
    public function recalculateMonthlyCommissions($contractorId, $month)
    {
        try {
            // Get all transactions for the month
            $this->db->query("
                SELECT ct.* FROM commission_transactions ct
                INNER JOIN monthly_commission_summary mcs ON ct.monthly_summary_id = mcs.summary_id
                WHERE mcs.contractor_id = :contractor_id AND mcs.month = :month
                ORDER BY ct.transaction_date ASC
            ");
            $this->db->bind(':contractor_id', $contractorId);
            $this->db->bind(':month', $month);

            $transactions = $this->db->resultSet();

            // Recalculate commission for each transaction based on cumulative sales
            $cumulativeSales = 0;

            foreach ($transactions as $transaction) {
                $cumulativeSales += $transaction->sale_amount;
                $tier = $this->calculateCommissionTier($cumulativeSales);

                $newCommissionPercentage = $tier ? $tier->commission_percentage : 1.00;
                $newCommissionAmount = ($transaction->sale_amount * $newCommissionPercentage) / 100;

                // Update transaction
                $this->db->query("
                    UPDATE commission_transactions 
                    SET commission_percentage = :commission_percentage,
                        commission_amount = :commission_amount
                    WHERE transaction_id = :transaction_id
                ");
                $this->db->bind(':commission_percentage', $newCommissionPercentage);
                $this->db->bind(':commission_amount', $newCommissionAmount);
                $this->db->bind(':transaction_id', $transaction->transaction_id);
                $this->db->execute();
            }

            // Update monthly summary
            $this->updateMonthlySummary($contractorId, $month);

            return true;
        } catch (Exception $e) {
            error_log('recalculateMonthlyCommissions error: ' . $e->getMessage());
            return false;
        }
    }

    // Get monthly performance data for the interface
    public function getMonthlyPerformance()
    {
        try {
            $sql = "SELECT 
                        mcs.*,
                        u.name as contractor_name,
                        CONCAT(MONTHNAME(STR_TO_DATE(SUBSTRING(mcs.month_year, 6, 2), '%m')), ' ', SUBSTRING(mcs.month_year, 1, 4)) as month_display
                    FROM monthly_commission_summary mcs
                    LEFT JOIN users u ON mcs.contractor_id = u.id
                    ORDER BY mcs.month_year DESC, mcs.contractor_id";

            $this->db->query($sql);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('getMonthlyPerformance error: ' . $e->getMessage());
            return [];
        }
    }

    // Create a new commission tier
    public function createCommissionTier($data)
    {
        try {
            $sql = "INSERT INTO commission_target_tiers (tier_name, min_sales, commission_rate, created_at) 
                    VALUES (:tier_name, :min_sales, :commission_rate, NOW())";

            $this->db->query($sql);
            $this->db->bind(':tier_name', $data['tier_name']);
            $this->db->bind(':min_sales', $data['min_sales']);
            $this->db->bind(':commission_rate', $data['commission_rate']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('createCommissionTier error: ' . $e->getMessage());
            return false;
        }
    }

    // Update an existing commission tier
    public function updateCommissionTier($data)
    {
        try {
            $sql = "UPDATE commission_target_tiers 
                    SET tier_name = :tier_name, min_sales = :min_sales, commission_rate = :commission_rate, updated_at = NOW()
                    WHERE tier_id = :tier_id";

            $this->db->query($sql);
            $this->db->bind(':tier_id', $data['tier_id']);
            $this->db->bind(':tier_name', $data['tier_name']);
            $this->db->bind(':min_sales', $data['min_sales']);
            $this->db->bind(':commission_rate', $data['commission_rate']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('updateCommissionTier error: ' . $e->getMessage());
            return false;
        }
    }

    // Delete a commission tier
    public function deleteCommissionTier($tierId)
    {
        try {
            // Check if tier is being used in any monthly summaries
            $checkSql = "SELECT COUNT(*) as count FROM monthly_commission_summary WHERE tier_achieved = (SELECT tier_name FROM commission_target_tiers WHERE tier_id = :tier_id)";
            $this->db->query($checkSql);
            $this->db->bind(':tier_id', $tierId);
            $result = $this->db->single();

            if ($result->count > 0) {
                throw new Exception('Cannot delete tier that is being used in commission records');
            }

            $sql = "DELETE FROM commission_target_tiers WHERE tier_id = :tier_id";
            $this->db->query($sql);
            $this->db->bind(':tier_id', $tierId);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('deleteCommissionTier error: ' . $e->getMessage());
            return false;
        }
    }
}
