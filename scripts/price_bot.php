<?php
/**
 * Price Bot - automated pricing script with real database integration
 * Intended to be run from CLI or as a scheduled task (cron/Task Scheduler).
 * This script fetches products from the local database, calculates
 * updated prices so the overall profit margin approaches a target, and
 * updates each product's price in the database.
 */

// Load application bootstrap to access database and models
require_once __DIR__ . "/../bootstrap.php";

// Configuration
$target_overall_margin = 0.30; // desired overall profit margin (30%)
$logFile = __DIR__ . "/price_bot.log";

// Simple logger
function bot_log($message)
{
    global $logFile;
    $ts = date('Y-m-d H:i:s');
    $line = "[$ts] $message\n";
    echo $line;
    file_put_contents($logFile, $line, FILE_APPEND);
}

// API configuration - configure these for your platform integration
// Set $api_config['dry_run'] = false to enable real API calls.
$api_config = [
    'base_url' => getenv('PRICEBOT_API_BASE') ?: '', // e.g. 'https://api.example.com'
    'auth_type' => getenv('PRICEBOT_API_AUTH_TYPE') ?: 'bearer', // 'bearer' or 'basic' or 'none'
    'api_key' => getenv('PRICEBOT_API_KEY') ?: '',
    'username' => getenv('PRICEBOT_API_USER') ?: '',
    'password' => getenv('PRICEBOT_API_PASS') ?: '',
    'timeout' => 10,
    'max_retries' => 2,
    'retry_delay' => 1, // seconds
    'dry_run' => true, // safe default: do not perform network calls until configured
];

/**
 * Perform an HTTP request using cURL with retries.
 * Returns an array: [ 'success' => bool, 'code' => int, 'body' => string, 'error' => string|null ]
 */
function http_request($method, $url, $payload = null, $extraHeaders = [])
{
    global $api_config;

    $attempts = 0;
    $max = max(1, intval($api_config['max_retries'] ?? 1));
    $delay = max(0, intval($api_config['retry_delay'] ?? 1));

    do {
        $attempts++;

        $ch = curl_init();
        $headers = array_merge(['Accept: application/json'], $extraHeaders);

        // Authentication header
        if (!empty($api_config['api_key']) && $api_config['auth_type'] === 'bearer') {
            $headers[] = 'Authorization: Bearer ' . $api_config['api_key'];
        } elseif (!empty($api_config['username']) && $api_config['auth_type'] === 'basic') {
            curl_setopt($ch, CURLOPT_USERPWD, $api_config['username'] . ':' . $api_config['password']);
        }

        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => intval($api_config['timeout'] ?? 10),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        $methodUpper = strtoupper($method);
        if (in_array($methodUpper, ['POST', 'PUT', 'PATCH'])) {
            $json = is_null($payload) ? '' : json_encode($payload);
            $opts[CURLOPT_CUSTOMREQUEST] = $methodUpper;
            $opts[CURLOPT_POSTFIELDS] = $json;
            $headers[] = 'Content-Type: application/json';
            $opts[CURLOPT_HTTPHEADER] = $headers;
        } elseif ($methodUpper === 'GET') {
            $opts[CURLOPT_HTTPGET] = true;
        }

        curl_setopt_array($ch, $opts);
        $body = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body !== false && ($code >= 200 && $code < 300)) {
            return ['success' => true, 'code' => $code, 'body' => $body, 'error' => null];
        }

        // on transient failure, wait and retry
        if ($attempts < $max) {
            sleep($delay);
        }

    } while ($attempts < $max);

    return ['success' => false, 'code' => $code ?? 0, 'body' => $body ?? null, 'error' => $err ?: 'HTTP ' . ($code ?? 0)];
}

/**
 * Fetch products from local database.
 * Returns products with id, cost, sales_per_month, profit_weighting
 */
function fetch_products()
{
    try {
        // Create Product model instance
        require_once APPROOT . '/app/models/Product.php';
        $productModel = new Product();

        // Use the model's method to get products with pricing data
        $products = $productModel->getProductsForPriceManagement([]);

        $processedProducts = [];
        foreach ($products as $product) {
            // Extract values with correct field names from getProductsForPriceManagement
            $cost = floatval($product->cost ?? $product->current_average_cost ?? 0);
            $sellingPrice = floatval($product->price ?? $product->selling_price ?? 0);
            $productName = $product->name ?? $product->product_name ?? "Product {$product->product_id}";
            $productId = $product->product_id ?? $product->id ?? null;

            // Only include products with valid cost and current price
            if ($cost > 0 && $sellingPrice > 0 && !empty($productId)) {
                // Calculate sales per month from total_sold (simplified estimation)
                $totalSold = intval($product->total_sold ?? 0);
                $salesPerMonth = max(1, max($totalSold, 10)); // default to 10 sales/month minimum

                // Estimate profit weighting based on current margin and sales rank
                $profitWeighting = 1.0;
                if ($cost > 0 && $sellingPrice > $cost) {
                    $currentMargin = ($sellingPrice - $cost) / $sellingPrice;
                    $salesRank = intval($product->sales_rank ?? 9999);
                    // Lower sales rank = higher weight (rank 1 is best)
                    $rankWeight = ($salesRank < 100) ? 1.5 : (($salesRank < 1000) ? 1.2 : 1.0);
                    $profitWeighting = max(0.3, ($currentMargin * $rankWeight) + 0.5);
                }

                $processedProducts[] = (object) [
                    'id' => $productId,
                    'name' => $productName,
                    'cost' => $cost,
                    'current_price' => $sellingPrice,
                    'sales_per_month' => $salesPerMonth,
                    'profit_weighting' => $profitWeighting,
                    'quantity_on_hand' => intval($product->stock_quantity ?? 0)
                ];
            }
        }

        bot_log("Processed " . count($processedProducts) . " valid products from " . count($products) . " total products");
        return array_slice($processedProducts, 0, 10); // limit to first 10 for safety

    } catch (Exception $e) {
        bot_log("Error fetching products from database: " . $e->getMessage());

        // Fallback to mock data if database fails
        bot_log("Falling back to mock data");
        return [
            (object) ['id' => 'MOCK1', 'cost' => 10.00, 'sales_per_month' => 120, 'profit_weighting' => 1.0, 'name' => 'Mock Product 1', 'current_price' => 15.00],
            (object) ['id' => 'MOCK2', 'cost' => 5.00, 'sales_per_month' => 300, 'profit_weighting' => 0.8, 'name' => 'Mock Product 2', 'current_price' => 8.00],
            (object) ['id' => 'MOCK3', 'cost' => 20.00, 'sales_per_month' => 40, 'profit_weighting' => 1.5, 'name' => 'Mock Product 3', 'current_price' => 30.00],
        ];
    }
}

/**
 * Update product price in local database.
 * Returns ['success'=>bool, 'productId'=>id, 'newPrice'=>float, 'oldPrice'=>float, 'error'=>string|null]
 */
function update_product_price($productId, $newPrice)
{
    try {
        // Create Product model instance
        require_once APPROOT . '/app/models/Product.php';
        $productModel = new Product();

        // Get current price for logging
        $currentProduct = $productModel->getProductById($productId);
        if (!$currentProduct) {
            return ['success' => false, 'productId' => $productId, 'newPrice' => $newPrice, 'oldPrice' => null, 'error' => 'Product not found'];
        }

        $oldPrice = floatval($currentProduct->selling_price);

        // Update the product price using the model's method
        $success = $productModel->updateProductPrice($productId, $newPrice);

        if ($success) {
            bot_log("Database updated product {$productId} price from \${$oldPrice} to \${$newPrice}");
            return ['success' => true, 'productId' => $productId, 'newPrice' => $newPrice, 'oldPrice' => $oldPrice, 'error' => null];
        } else {
            bot_log("Failed to update product {$productId} in database");
            return ['success' => false, 'productId' => $productId, 'newPrice' => $newPrice, 'oldPrice' => $oldPrice, 'error' => 'Database update failed'];
        }

    } catch (Exception $e) {
        bot_log("Error updating product {$productId}: " . $e->getMessage());
        return ['success' => false, 'productId' => $productId, 'newPrice' => $newPrice, 'oldPrice' => null, 'error' => $e->getMessage()];
    }
}

// Calculate new prices so overall weighted margin meets target
function calculate_prices($products, $targetMargin)
{
    // First compute baseline total revenue and total cost with current cost-plus simplest markup
    // We'll compute a relative margin allocation based on profit_weighting and expected sales volume.

    // Aggregate weights and expected volumes
    $total_weighted_sales = 0.0;
    foreach ($products as $p) {
        // expected revenue weight = sales_per_month * profit_weighting
        $total_weighted_sales += ($p->sales_per_month * max(0.0001, $p->profit_weighting));
    }

    // Determine per-product margin contribution target based on weighting
    $results = [];

    // To meet overall margin, we need: (TotalRevenue - TotalCost) / TotalRevenue = target
    // Let new price for product i be: price_i = cost_i / (1 - margin_i)
    // We distribute total margin dollars proportionally to each product's weight.

    // Compute current total cost and use target to compute required total revenue
    $total_cost = 0.0;
    foreach ($products as $p) {
        $total_cost += $p->cost * $p->sales_per_month;
    }

    // Required total revenue to reach target margin
    // target = (R - C)/R => R = C / (1 - target)
    if ($targetMargin >= 0.999) {
        throw new Exception('Target margin too high');
    }
    $required_total_revenue = $total_cost / (1 - $targetMargin);
    $required_total_profit = $required_total_revenue - $total_cost;

    // Now split required_total_profit across products according to their weight
    foreach ($products as $p) {
        $weight = $p->sales_per_month * max(0.0001, $p->profit_weighting);
        $share_of_profit = ($weight / max(1e-9, $total_weighted_sales)) * $required_total_profit;

        // profit per unit needed
        $profit_per_unit = $share_of_profit / max(1e-9, $p->sales_per_month);

        // new price = cost + profit_per_unit
        $new_price = max($p->cost * 1.01, $p->cost + $profit_per_unit); // ensure at least 1% markup

        $results[] = (object) [
            'id' => $p->id,
            'name' => $p->name ?? 'Unknown',
            'old_cost' => $p->cost,
            'old_price' => $p->current_price ?? null,
            'new_price' => round($new_price, 2),
            'sales_per_month' => $p->sales_per_month,
            'profit_allocated_total' => round($share_of_profit, 2)
        ];
    }

    return $results;
}

// Main bot routine
function run_price_bot()
{
    global $target_overall_margin;

    bot_log("Initiating Price Bot run. Target overall margin={$target_overall_margin}");

    // Fetch products (from database)
    $products = fetch_products();
    bot_log("Fetched " . count($products) . " products from database");

    try {
        $calculated = calculate_prices($products, $target_overall_margin);
    } catch (Exception $ex) {
        bot_log("Price calculation failed: " . $ex->getMessage());
        return;
    }

    // Apply updates (to database)
    foreach ($calculated as $item) {
        bot_log("Preparing update for product {$item->id} ({$item->name}): new_price={$item->new_price}");
        $resp = update_product_price($item->id, $item->new_price);
        if ($resp['success']) {
            bot_log("Product {$item->id} price updated to {$item->new_price}");
        } else {
            bot_log("Failed to update product {$item->id}: " . ($resp['error'] ?? 'unknown'));
        }
    }

    bot_log("Price Bot run completed.");
}

// If run from CLI, execute. If accessed via browser for testing, still run but warn.
if (php_sapi_name() === 'cli') {
    run_price_bot();
} else {
    echo "<pre>";
    bot_log("Note: Price Bot executed from web. For production, run this script from CLI as a scheduled task.");
    run_price_bot();
    echo "</pre>";
}

?>