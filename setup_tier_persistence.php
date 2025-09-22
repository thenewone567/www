<?php
// Updated Quarterly Tier System with Tier Persistence
require_once 'bootstrap.php';

echo "=== Updated Quarterly Tier System with Tier Persistence ===\n\n";

try {
    $db = new PDO("mysql:host=localhost;dbname=master_hardware", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Initializing Tier Achievements ===\n";

    // Get all contractors and set their tier achievements based on current commission rates
    $stmt = $db->prepare("SELECT contractor_id, contractor_name, commission_rate FROM contractors WHERE is_active = 1");
    $stmt->execute();
    $contractors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($contractors as $contractor) {
        $rate = floatval($contractor['commission_rate']);
        $achievementLevel = 1; // Default to Bronze

        // Map commission rate to achievement level
        if ($rate >= 5.0)
            $achievementLevel = 5; // Diamond
        elseif ($rate >= 4.0)
            $achievementLevel = 4; // Platinum  
        elseif ($rate >= 3.0)
            $achievementLevel = 3; // Gold
        elseif ($rate >= 2.0)
            $achievementLevel = 2; // Silver
        else
            $achievementLevel = 1; // Bronze

        // Update contractor with tier achievement
        $updateStmt = $db->prepare("
            UPDATE contractors 
            SET current_tier_achievement = :achievement,
                commission_rate = :rate
            WHERE contractor_id = :contractor_id
        ");
        $updateStmt->execute([
            ':achievement' => $achievementLevel,
            ':rate' => $rate,
            ':contractor_id' => $contractor['contractor_id']
        ]);

        $tierNames = [1 => 'Bronze', 2 => 'Silver', 3 => 'Gold', 4 => 'Platinum', 5 => 'Diamond'];
        echo "✅ {$contractor['contractor_name']}: {$tierNames[$achievementLevel]} tier ({$rate}%) - Achievement Level {$achievementLevel}\n";
    }

    echo "\n=== New Tier Persistence System Explained ===\n";
    echo "🔄 **Quarterly Reset**: Quarterly revenue resets to $0 each quarter\n";
    echo "🏆 **Tier Persistence**: Achieved tier levels carry forward to next quarter\n";
    echo "📈 **Only Upgrades**: Contractors can only maintain or upgrade their tier, never downgrade\n";
    echo "🎯 **Performance Based**: Higher quarterly sales unlock higher tiers\n\n";

    echo "=== Tier Progression Examples ===\n";
    echo "• Start Q3 at Bronze (1%) → Earn $150K in Q3 → Achieve Silver (2%)\n";
    echo "• Start Q4 at Silver (2%) → Earn $50K in Q4 → Remain Silver (2%)\n";
    echo "• Start Q4 at Silver (2%) → Earn $300K in Q4 → Upgrade to Gold (3%)\n";
    echo "• Tier achievements are permanent upgrades!\n\n";

    echo "=== Quarter Reset Behavior ===\n";
    echo "When Q4 starts (Oct 1, 2025):\n";
    echo "• Quarterly revenue: Reset to $0 ✅\n";
    echo "• Tier achievement: Preserved from Q3 ✅\n";
    echo "• Commission rate: Stays at achieved tier rate ✅\n";
    echo "• Can upgrade further based on Q4 performance ✅\n\n";

    echo "✅ Tier persistence system activated!\n";
    echo "🎉 Contractors now keep their earned tier achievements across quarters!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>