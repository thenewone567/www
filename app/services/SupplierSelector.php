<?php

/**
 * Smart Supplier Selection Service
 * 
 * Replaces the binary "primary supplier" concept with intelligent
 * supplier selection based on configurable business rules.
 */
class SupplierSelector
{
    private $db;

    // Default weights for supplier selection
    private $defaultWeights = [
        'price' => 40,      // 40% weight for price competitiveness
        'delivery' => 30,   // 30% weight for delivery speed/reliability  
        'quality' => 30     // 30% weight for quality/reliability
    ];

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get optimal supplier for a product based on context
     * 
     * @param int $productId Product to source
     * @param int $quantity Order quantity (affects volume discounts)
     * @param string $urgency 'urgent', 'normal', 'bulk'
     * @param array $customWeights Override default weights
     * @return object|null Supplier with selection reasoning or null if none found
     */
    public function getOptimalSupplier($productId, $quantity = 1, $urgency = 'normal', $customWeights = null)
    {
        // Get all active suppliers for this product
        $suppliers = $this->getAllSuppliersWithMetrics($productId);

        if (empty($suppliers)) {
            return null;
        }

        // Use custom weights or adjust defaults based on urgency
        $weights = $customWeights ?: $this->getWeightsForUrgency($urgency);

        // Score each supplier
        $scoredSuppliers = [];
        foreach ($suppliers as $supplier) {
            $score = $this->calculateSupplierScore($supplier, $quantity, $weights);
            $supplier->selection_score = $score['total_score'];
            $supplier->selection_reasoning = $score['reasoning'];
            $scoredSuppliers[] = $supplier;
        }

        // Sort by score (highest first)
        usort($scoredSuppliers, function ($a, $b) {
            return $b->selection_score <=> $a->selection_score;
        });

        return $scoredSuppliers[0];
    }

    /**
     * Get all suppliers with calculated metrics
     */
    private function getAllSuppliersWithMetrics($productId)
    {
        $this->db->query("
            SELECT 
                ps.ps_id,
                ps.supplier_id,
                s.supplier_name,
                ps.purchase_price,
                ps.lead_time_days,
                ps.min_order_quantity,
                ps.quality_rating,
                ps.delivery_rating,
                s.reliability_score,
                s.supplier_tier,
                
                -- Calculate price rank (1 = cheapest)
                RANK() OVER (PARTITION BY ps.product_id ORDER BY ps.purchase_price ASC) as price_rank,
                
                -- Calculate delivery rank (1 = fastest)  
                RANK() OVER (PARTITION BY ps.product_id ORDER BY ps.lead_time_days ASC) as delivery_rank,
                
                -- Get min/max prices for normalization
                MIN(ps.purchase_price) OVER (PARTITION BY ps.product_id) as min_price,
                MAX(ps.purchase_price) OVER (PARTITION BY ps.product_id) as max_price,
                
                -- Count total suppliers for this product
                COUNT(*) OVER (PARTITION BY ps.product_id) as supplier_count
                
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id 
            AND ps.is_active = 1 
            AND s.status = 'active'
            ORDER BY ps.purchase_price ASC
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Calculate weighted score for a supplier
     */
    private function calculateSupplierScore($supplier, $quantity, $weights)
    {
        $scores = [
            'price' => $this->calculatePriceScore($supplier),
            'delivery' => $this->calculateDeliveryScore($supplier),
            'quality' => $this->calculateQualityScore($supplier)
        ];

        // Apply quantity-based adjustments
        $scores = $this->applyQuantityAdjustments($scores, $supplier, $quantity);

        // Calculate weighted total
        $totalScore = 0;
        $reasoning = [];

        foreach ($scores as $category => $score) {
            $weight = $weights[$category] / 100;
            $weightedScore = $score * $weight;
            $totalScore += $weightedScore;

            $reasoning[] = ucfirst($category) . ": " . round($score, 1) . "/100 (weight: {$weights[$category]}%)";
        }

        return [
            'total_score' => round($totalScore, 2),
            'component_scores' => $scores,
            'reasoning' => implode(', ', $reasoning)
        ];
    }

    /**
     * Calculate price competitiveness score (0-100)
     */
    private function calculatePriceScore($supplier)
    {
        if ($supplier->supplier_count == 1) {
            return 100; // Only supplier gets full score
        }

        // Score based on price rank (1st = 100, 2nd = 80, 3rd = 60, etc.)
        $baseScore = max(0, 100 - (($supplier->price_rank - 1) * 20));

        // Bonus for significant price advantage
        if ($supplier->price_rank == 1 && $supplier->max_price > 0) {
            $priceAdvantage = (($supplier->max_price - $supplier->purchase_price) / $supplier->max_price) * 100;
            if ($priceAdvantage > 10) {
                $baseScore += min(20, $priceAdvantage); // Up to 20 bonus points
            }
        }

        return min(100, $baseScore);
    }

    /**
     * Calculate delivery performance score (0-100)  
     */
    private function calculateDeliveryScore($supplier)
    {
        $score = 50; // Base score

        // Lead time scoring
        if ($supplier->lead_time_days <= 3) {
            $score += 30; // Very fast
        } elseif ($supplier->lead_time_days <= 7) {
            $score += 20; // Fast
        } elseif ($supplier->lead_time_days <= 14) {
            $score += 10; // Normal
        }
        // Over 14 days gets no bonus

        // Delivery rating bonus
        if ($supplier->delivery_rating) {
            $score += ($supplier->delivery_rating - 3) * 10; // 3=normal, 4=+10, 5=+20
        }

        // Supplier tier bonus
        if ($supplier->supplier_tier == 'Gold') {
            $score += 15;
        } elseif ($supplier->supplier_tier == 'Silver') {
            $score += 10;
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate quality/reliability score (0-100)
     */
    private function calculateQualityScore($supplier)
    {
        $score = 50; // Base score

        // Quality rating (1-5 scale)
        if ($supplier->quality_rating) {
            $score += ($supplier->quality_rating - 3) * 15; // 3=normal, 4=+15, 5=+30
        }

        // Reliability score
        if ($supplier->reliability_score) {
            $score += ($supplier->reliability_score / 100) * 20; // 0-100 scale to 0-20 points
        }

        // Supplier tier quality bonus
        if ($supplier->supplier_tier == 'Gold') {
            $score += 20;
        } elseif ($supplier->supplier_tier == 'Silver') {
            $score += 10;
        }

        return max(0, min(100, $score));
    }

    /**
     * Apply quantity-based score adjustments
     */
    private function applyQuantityAdjustments($scores, $supplier, $quantity)
    {
        // Penalty if quantity is below minimum order
        if ($quantity < $supplier->min_order_quantity) {
            $shortfall = ($supplier->min_order_quantity - $quantity) / $supplier->min_order_quantity;
            $penalty = min(30, $shortfall * 50); // Up to 30 point penalty
            $scores['price'] -= $penalty;
        }

        // Bonus for bulk orders (encourage volume suppliers)
        if ($quantity >= 50) {
            $scores['price'] += 5; // Bulk order price advantage
        }

        return $scores;
    }

    /**
     * Get scoring weights based on urgency context
     */
    private function getWeightsForUrgency($urgency)
    {
        switch ($urgency) {
            case 'urgent':
                return ['price' => 20, 'delivery' => 60, 'quality' => 20]; // Prioritize speed

            case 'bulk':
                return ['price' => 60, 'delivery' => 20, 'quality' => 20]; // Prioritize cost

            case 'normal':
            default:
                return $this->defaultWeights; // Balanced approach
        }
    }

    /**
     * Get supplier recommendation with full context
     */
    public function getRecommendationWithAlternatives($productId, $quantity = 1, $urgency = 'normal')
    {
        $optimal = $this->getOptimalSupplier($productId, $quantity, $urgency);

        if (!$optimal) {
            return null;
        }

        // Get top 3 alternatives
        $allSuppliers = $this->getAllSuppliersWithMetrics($productId);
        $weights = $this->getWeightsForUrgency($urgency);

        $scoredSuppliers = [];
        foreach ($allSuppliers as $supplier) {
            $score = $this->calculateSupplierScore($supplier, $quantity, $weights);
            $supplier->selection_score = $score['total_score'];
            $supplier->selection_reasoning = $score['reasoning'];
            $scoredSuppliers[] = $supplier;
        }

        usort($scoredSuppliers, function ($a, $b) {
            return $b->selection_score <=> $a->selection_score;
        });

        return [
            'recommended' => $scoredSuppliers[0],
            'alternatives' => array_slice($scoredSuppliers, 1, 2),
            'context' => [
                'urgency' => $urgency,
                'quantity' => $quantity,
                'weights' => $weights
            ]
        ];
    }
}
?>