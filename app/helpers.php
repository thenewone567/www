<?php
// Simple page redirect
function redirect($page)
{
  header('Location: ' . URLROOT . '/' . ltrim($page, '/'));
  exit();
}

// Flash message helper
// EXAMPLE - flash('register_success', 'You are now registered');
// DISPLAY IN VIEW - echo flash('register_success');
function flash($name = '', $message = '', $class = 'alert alert-success')
{
  if (!empty($name)) {
    if (!empty($message) && empty($_SESSION[$name])) {
      if (!empty($_SESSION[$name])) {
        unset($_SESSION[$name]);
      }

      if (!empty($_SESSION[$name . '_class'])) {
        unset($_SESSION[$name . '_class']);
      }

      $_SESSION[$name] = $message;
      $_SESSION[$name . '_class'] = $class;
    } elseif (empty($message) && !empty($_SESSION[$name])) {
      $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
      echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
      unset($_SESSION[$name]);
      unset($_SESSION[$name . '_class']);
    }
  }
}

function isLoggedIn()
{
  // Use enhanced session manager if available
  if (class_exists('SessionManager')) {
    return SessionManager::isLoggedIn();
  }

  // Fallback to basic check
  return isset($_SESSION['user_id']);
}

// Replacement for deprecated FILTER_SANITIZE_STRING
function sanitizePost($inputArray = null)
{
  if ($inputArray === null) {
    $inputArray = $_POST;
  }

  $sanitized = [];
  foreach ($inputArray as $key => $value) {
    if (is_array($value)) {
      $sanitized[$key] = sanitizePost($value);
    } else {
      // Remove HTML tags and encode special characters
      $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
  }

  return $sanitized;
}

/**
 * Format currency in Indian Rupees with Indian number formatting
 * @param float $amount The amount to format
 * @param int $decimals Number of decimal places (default: 2)
 * @return string Formatted currency string
 */
function formatCurrency($amount, $decimals = 2)
{
  return '₹' . formatIndianNumber($amount, $decimals);
}

/**
 * Format numbers in Indian numbering system (lakhs, crores)
 * @param float $number The number to format
 * @param int $decimals Number of decimal places (default: 2)
 * @return string Formatted number string
 */
function formatIndianNumber($number, $decimals = 2)
{
  if ($number == 0) {
    return number_format(0, $decimals);
  }

  $isNegative = $number < 0;
  $number = abs($number);
  if ($number >= 10000000) {
    // Crores
    $formatted = number_format($number / 10000000, $decimals) . ' Cr';
  } elseif ($number >= 100000) {
    // Lakhs
    $formatted = number_format($number / 100000, $decimals) . ' L';
  } elseif ($number >= 1000) {
    // Thousands with Indian comma placement
    $formatted = formatWithIndianCommas($number, $decimals);
  } else {
    $formatted = number_format($number, $decimals);
  }

  return $isNegative ? '-' . $formatted : $formatted;
}

/**
 * Format numbers with Indian comma placement (xx,xx,xxx)
 * @param float $number The number to format
 * @param int $decimals Number of decimal places
 * @return string Formatted number string
 */
function formatWithIndianCommas($number, $decimals = 2)
{
  $number = number_format($number, $decimals, '.', '');
  $parts = explode('.', $number);
  $integerPart = $parts[0];
  $decimalPart = isset($parts[1]) ? $parts[1] : '';
  // Add commas in Indian format
  $length = strlen($integerPart);
  if ($length > 3) {
    $lastThree = substr($integerPart, -3);

    if (!function_exists('company_initials')) {
      function company_initials()
      {
        $name = company_name();
        $parts = preg_split('/\s+/', trim($name));
        $letters = '';
        foreach ($parts as $p) {
          if ($p !== '') {
            $letters .= mb_strtoupper(mb_substr($p, 0, 1));
          }
          if (strlen($letters) >= 2)
            break;
        }
        return $letters !== '' ? $letters : 'C';
      }
    }
    $remaining = substr($integerPart, 0, -3);
    $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
    $integerPart = $remaining . ',' . $lastThree;
  }

  return $decimalPart ? $integerPart . '.' . $decimalPart : $integerPart;
}

/**
 * Format currency for display without denomination suffix
 * @param float $amount The amount to format
 * @param int $decimals Number of decimal places (default: 2)
 * @return string Formatted currency string
 */
function formatCurrencySimple($amount, $decimals = 2)
{
  return '₹' . formatWithIndianCommas($amount, $decimals);
}

/**
 * Demonstrate inventory costing calculation
 * @param array $purchases Array of purchase records with quantity and unit_price
 * @param int $quantitySold Quantity sold using FIFO method
 * @return array Cost calculation details
 */
function demonstrateInventoryCosting($purchases, $quantitySold = 0)
{
  $totalInventoryValue = 0;
  $totalQuantity = 0;
  $costOfGoodsSold = 0;
  $remainingQuantity = $quantitySold;
  $fifoLayers = [];

  // Sort purchases by date (oldest first for FIFO)
  usort($purchases, function ($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
  });

  // Calculate FIFO cost layers
  foreach ($purchases as $purchase) {
    $layerRemaining = $purchase['quantity'];

    // If we need to sell from this layer
    if ($remainingQuantity > 0) {
      $soldFromThisLayer = min($remainingQuantity, $layerRemaining);
      $costOfGoodsSold += $soldFromThisLayer * $purchase['unit_price'];
      $layerRemaining -= $soldFromThisLayer;
      $remainingQuantity -= $soldFromThisLayer;
    }

    // Add remaining inventory from this layer
    if ($layerRemaining > 0) {
      $fifoLayers[] = [
        'date' => $purchase['date'],
        'quantity' => $layerRemaining,
        'unit_price' => $purchase['unit_price'],
        'layer_value' => $layerRemaining * $purchase['unit_price']
      ];
      $totalInventoryValue += $layerRemaining * $purchase['unit_price'];
      $totalQuantity += $layerRemaining;
    }
  }

  $averageCost = $totalQuantity > 0 ? $totalInventoryValue / $totalQuantity : 0;

  return [
    'total_inventory_value' => $totalInventoryValue,
    'total_quantity' => $totalQuantity,
    'average_cost' => $averageCost,
    'cost_of_goods_sold' => $costOfGoodsSold,
    'fifo_layers' => $fifoLayers
  ];
}

/**
 * Calculate average price for inventory costing
 * @param float $currentInventory Current Inventory quantity
 * @param float $currentPrice Current Inventory price per unit
 * @param float $newQuantity New purchase quantity
 * @param float $newPrice New purchase price per unit
 * @return array Result with total quantity, total value, and average price
 */
function calculateAveragePrice($currentInventory, $currentPrice, $newQuantity, $newPrice)
{
  $currentValue = $currentInventory * $currentPrice;
  $newValue = $newQuantity * $newPrice;

  $totalQuantity = $currentInventory + $newQuantity;
  $totalValue = $currentValue + $newValue;

  $averagePrice = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;

  return [
    'total_quantity' => $totalQuantity,
    'total_value' => $totalValue,
    'average_price' => $averagePrice,
    'current_value' => $currentValue,
    'new_value' => $newValue
  ];
}

/**
 * Calculate separate batch costing
 * @param array $existingBatches Array of existing batches [quantity, price, batch_number]
 * @param float $newQuantity New purchase quantity
 * @param float $newPrice New purchase price per unit
 * @param string $newBatchNumber New batch number
 * @return array Result with updated batches and total inventory value
 */
function calculateSeparateBatches($existingBatches, $newQuantity, $newPrice, $newBatchNumber)
{
  $totalValue = 0;
  $totalQuantity = 0;

  // Calculate existing batches value
  foreach ($existingBatches as $batch) {
    $batchValue = $batch['quantity'] * $batch['price'];
    $totalValue += $batchValue;
    $totalQuantity += $batch['quantity'];
  }

  // Add new batch
  $newBatchValue = $newQuantity * $newPrice;
  $totalValue += $newBatchValue;
  $totalQuantity += $newQuantity;

  $newBatch = [
    'quantity' => $newQuantity,
    'price' => $newPrice,
    'batch_number' => $newBatchNumber,
    'value' => $newBatchValue
  ];

  return [
    'total_quantity' => $totalQuantity,
    'total_value' => $totalValue,
    'new_batch' => $newBatch,
    'existing_batches' => $existingBatches,
    'weighted_average_price' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0
  ];
}

/**
 * Generate example for costing method comparison
 * @param float $currentInventory Current Inventory quantity
 * @param float $currentPrice Current Inventory price per unit
 * @param float $newQuantity New purchase quantity
 * @param float $newPrice New purchase price per unit
 * @return array Comparison of both methods
 */
function getCostingMethodExample($currentInventory, $currentPrice, $newQuantity, $newPrice)
{
  $averageMethod = calculateAveragePrice($currentInventory, $currentPrice, $newQuantity, $newPrice);

  $existingBatches = [
    [
      'quantity' => $currentInventory,
      'price' => $currentPrice,
      'batch_number' => 'BATCH-001'
    ]
  ];

  $separateMethod = calculateSeparateBatches($existingBatches, $newQuantity, $newPrice, 'BATCH-002');

  return [
    'average_method' => $averageMethod,
    'separate_method' => $separateMethod,
    'price_difference' => abs($averageMethod['average_price'] - $separateMethod['weighted_average_price']),
    'method_comparison' => [
      'average_shows_single_price' => $averageMethod['average_price'],
      'separate_maintains_individual_costs' => true,
      'inventory_value_same' => ($averageMethod['total_value'] == $separateMethod['total_value'])
    ]
  ];
}

// === Company Branding Helpers (company_name, company_logo, company_initials) ===
if (!function_exists('company_name')) {
  function company_name()
  {
    static $cached = null;
    if ($cached !== null)
      return $cached;
    try {
      if (class_exists('Setting')) {
        $settings = (new Setting())->getSettings();
        if (!empty($settings['company_name'])) {
          $cached = $settings['company_name'];
          return $cached;
        }
      }
    } catch (Exception $e) {
    }
    $cached = defined('SITENAME') ? SITENAME : 'Company';
    return $cached;
  }
}

if (!function_exists('company_logo')) {
  function company_logo()
  {
    static $cachedLogo = null;
    if ($cachedLogo !== null)
      return $cachedLogo;
    $fallbacks = [
      'uploads/logo/mlogo.png',
      'uploads/logos/mlogo.png'
    ];
    try {
      if (class_exists('Setting')) {
        $settings = (new Setting())->getSettings();
        if (!empty($settings['company_logo'])) {
          $path = $settings['company_logo'];
          $fsPath = APPROOT . DS . str_replace(['/', '\\'], DS, $path);
          if (file_exists($fsPath)) {
            $cachedLogo = $path;
            return $cachedLogo;
          }
          if (preg_match('/^https?:\/\//i', $path)) {
            $cachedLogo = $path;
            return $cachedLogo;
          }
        }
      }
    } catch (Exception $e) {
    }
    foreach ($fallbacks as $f) {
      if (file_exists(APPROOT . DS . str_replace(['/', '\\'], DS, $f))) {
        $cachedLogo = $f;
        return $cachedLogo;
      }
    }
    $cachedLogo = '';
    return $cachedLogo;
  }
}

