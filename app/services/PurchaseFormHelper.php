<?php

/**
 * Purchase Form Helper Service
 * 
 * Provides smart supplier recommendations for purchase order forms
 * Replaces primary supplier defaults with context-aware suggestions
 */
class PurchaseFormHelper
{
    private $db;
    private $supplierSelector;

    public function __construct($database)
    {
        $this->db = $database;
        require_once dirname(__DIR__) . '/services/SupplierSelector.php';
        $this->supplierSelector = new SupplierSelector($database);
    }

    /**
     * Get suppliers with smart recommendations for a product
     * 
     * @param int $productId Product to get suppliers for
     * @param int $quantity Expected order quantity (default 10)
     * @param string $urgency Order urgency: 'normal', 'urgent', 'bulk'
     * @return array Array of suppliers with recommendation flags
     */
    public function getSuppliersWithRecommendations($productId, $quantity = 10, $urgency = 'normal')
    {
        // Get all suppliers for the product
        $this->db->query("
            SELECT 
                ps.supplier_id,
                s.supplier_name,
                ps.purchase_price,
                ps.lead_time_days,
                ps.min_order_quantity,
                ps.quality_rating,
                s.supplier_tier,
                ps.is_primary,
                ps.is_active
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.product_id = :product_id 
            AND ps.is_active = 1 
            AND s.status = 'active'
            ORDER BY ps.purchase_price ASC
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        $suppliers = $this->db->resultSet();

        if (empty($suppliers)) {
            return [];
        }

        // Get smart recommendation
        $recommendation = $this->supplierSelector->getRecommendationWithAlternatives(
            $productId,
            $quantity,
            $urgency
        );

        $recommendedSupplierId = null;
        $recommendationReasoning = '';

        if ($recommendation && $recommendation['recommended']) {
            $recommendedSupplierId = $recommendation['recommended']->supplier_id;
            $recommendationReasoning = $recommendation['recommended']->selection_reasoning;
        }

        // Add recommendation flags to each supplier
        $suppliersWithFlags = [];
        foreach ($suppliers as $supplier) {
            $supplier->is_recommended = ($supplier->supplier_id == $recommendedSupplierId);
            $supplier->recommendation_reasoning = $supplier->is_recommended ? $recommendationReasoning : '';

            // Add recommendation badge text
            if ($supplier->is_recommended) {
                $supplier->recommendation_badge = 'RECOMMENDED';
                $supplier->recommendation_class = 'recommended';
            } elseif ($supplier->is_primary) {
                $supplier->recommendation_badge = 'LEGACY PRIMARY';
                $supplier->recommendation_class = 'legacy-primary';
            } else {
                $supplier->recommendation_badge = '';
                $supplier->recommendation_class = '';
            }

            $suppliersWithFlags[] = $supplier;
        }

        // Sort: recommended first, then by price
        usort($suppliersWithFlags, function ($a, $b) {
            if ($a->is_recommended && !$b->is_recommended)
                return -1;
            if (!$a->is_recommended && $b->is_recommended)
                return 1;
            return $a->purchase_price <=> $b->purchase_price;
        });

        return $suppliersWithFlags;
    }

    /**
     * Get urgency context based on stock levels and user input
     * 
     * @param object $product Product object with stock information
     * @param string $userUrgency User-specified urgency override
     * @return string Calculated urgency level
     */
    public function calculateUrgency($product, $userUrgency = null)
    {
        if ($userUrgency) {
            return $userUrgency; // User override
        }

        $currentStock = $product->current_stock ?? $product->stock_quantity ?? 0;
        $reorderLevel = $product->reorder_level ?? 10;

        if ($currentStock <= 0) {
            return 'urgent'; // Out of stock
        } elseif ($currentStock <= $reorderLevel) {
            return 'urgent'; // Below reorder level
        } elseif ($currentStock <= ($reorderLevel * 2)) {
            return 'normal'; // Getting low but not urgent
        } else {
            return 'normal'; // Good stock levels
        }
    }

    /**
     * Get recommended order quantity based on stock and demand
     * 
     * @param object $product Product object
     * @param int $userQuantity User-specified quantity override
     * @return int Recommended quantity
     */
    public function calculateRecommendedQuantity($product, $userQuantity = null)
    {
        if ($userQuantity && $userQuantity > 0) {
            return $userQuantity; // User override
        }

        $currentStock = $product->current_stock ?? $product->stock_quantity ?? 0;
        $reorderLevel = $product->reorder_level ?? 20;
        $maxStock = $reorderLevel * 3; // 3x reorder level as max stock

        $recommendedQuantity = max(10, $maxStock - $currentStock);

        // Cap at reasonable limits
        return min($recommendedQuantity, 100);
    }
}
?>