<?php
// Quarterly Tier Reset System
require_once 'bootstrap.php';

echo "=== Quarterly Tier System Implementation ===\n\n";

// Function to get current quarter start date
function getCurrentQuarterStart()
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

// Function to reset quarterly data if needed
function resetQuarterlyDataIfNeeded($contractorId, $currentQuarterStart)
{
    global $db;

    // Check if contractor's quarter start date is different from current quarter
    $stmt = $db->prepare("SELECT current_quarter_start FROM contractors WHERE contractor_id = ?");
    $stmt->execute([$contractorId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $contractorQuarterStart = $result['current_quarter_start'];

    if ($contractorQuarterStart !== $currentQuarterStart) {
        // Reset quarterly revenue and update quarter start date
        $updateStmt = $db->prepare("
            UPDATE contractors 
            SET quarterly_revenue_generated = 0.00,
                current_quarter_start = ?,
                commission_rate = 1.00
            WHERE contractor_id = ?
        ");
        $updateStmt->execute([$currentQuarterStart, $contractorId]);

        echo "✅ Reset contractor {$contractorId} for new quarter starting {$currentQuarterStart}\n";
        return true;
    }

    return false;
}

// Function to calculate tier based on quarterly revenue
function calculateQuarterlyTier($quarterlyRevenue)
{
    $tiers = [
        ["name" => "Bronze", "min" => 0, "max" => 100000, "rate" => 1],
        ["name" => "Silver", "min" => 100000, "max" => 250000, "rate" => 2],
        ["name" => "Gold", "min" => 250000, "max" => 500000, "rate" => 3],
        ["name" => "Platinum", "min" => 500000, "max" => 1000000, "rate" => 4],
        ["name" => "Diamond", "min" => 1000000, "max" => null, "rate" => 5]
    ];

    foreach ($tiers as $tier) {
        if ($quarterlyRevenue >= $tier['min'] && ($tier['max'] === null || $quarterlyRevenue < $tier['max'])) {
            return $tier;
        }
    }

    // Default to Bronze if no match
    return $tiers[0];
}

// Get current quarter start
$currentQuarterStart = getCurrentQuarterStart();
echo "Current Quarter Start: {$currentQuarterStart}\n";
echo "Current Date: " . date('Y-m-d') . "\n\n";

// Initialize database connection
try {
    $db = new PDO("mysql:host=localhost;dbname=master_hardware", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all contractors
    $stmt = $db->prepare("SELECT contractor_id, contractor_name, quarterly_revenue_generated, current_quarter_start FROM contractors WHERE is_active = 1");
    $stmt->execute();
    $contractors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "=== Processing Contractors ===\n";

    foreach ($contractors as $contractor) {
        echo "\n--- {$contractor['contractor_name']} ---\n";

        // Check if we need to reset for new quarter
        $wasReset = resetQuarterlyDataIfNeeded($contractor['contractor_id'], $currentQuarterStart);

        if ($wasReset) {
            $quarterlyRevenue = 0.00;
        } else {
            $quarterlyRevenue = $contractor['quarterly_revenue_generated'];
        }

        // Calculate current tier based on quarterly revenue
        $currentTier = calculateQuarterlyTier($quarterlyRevenue);

        echo "Quarterly Revenue: $" . number_format($quarterlyRevenue, 2) . "\n";
        echo "Current Tier: {$currentTier['name']} ({$currentTier['rate']}%)\n";

        // Update the contractor's commission rate based on quarterly tier
        $updateStmt = $db->prepare("
            UPDATE contractors 
            SET commission_rate = ?,
                current_quarter_start = ?
            WHERE contractor_id = ?
        ");
        $updateStmt->execute([$currentTier['rate'], $currentQuarterStart, $contractor['contractor_id']]);

        echo "✅ Updated tier to {$currentTier['name']} with {$currentTier['rate']}% commission\n";
    }

    echo "\n=== Quarterly Tier System Activated ===\n";
    echo "✅ All contractors reset to Bronze tier for current quarter\n";
    echo "✅ Quarterly revenue tracking enabled\n";
    echo "✅ Automatic tier progression based on quarterly performance\n";
    echo "✅ Next reset will be on the next quarter start date\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>