<?php
// Simple integration-style test for Product::getProductsForPriceManagement
// Define minimal constants expected by the app model when run in isolation
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);
if (!defined('APPROOT'))
    define('APPROOT', realpath(__DIR__ . '/../'));

require_once APPROOT . DS . 'app' . DS . 'models' . DS . 'Product.php';

// Minimal Mock DB that records the last query and returns supplied result
class MockDB
{
    public $lastQuery = '';
    public $bindings = [];
    private $rows = [];

    public function __construct($rows = [])
    {
        $this->rows = $rows;
    }

    public function query($sql)
    {
        $this->lastQuery = $sql;
    }

    public function bind($param, $value, $type = null)
    {
        $this->bindings[$param] = $value;
    }

    public function execute()
    {
        return true;
    }

    public function resultSet()
    {
        return $this->rows;
    }

    public function single()
    {
        return null;
    }
}

function assertEqual($a, $b, $message = '')
{
    if ($a === $b) {
        echo "PASS: $message\n";
    } else {
        echo "FAIL: $message\nExpected: " . var_export($b, true) . "\nGot: " . var_export($a, true) . "\n";
        exit(1);
    }
}

// Prepare fake rows to be returned by DB
$fakeRows = [
    (object) [
        'product_id'       => 101,
        'name'             => 'Test Screwdriver',
        'description'      => 'A good screwdriver',
        'sku'              => 'SD-001',
        'category'         => 'Tools',
        'price'            => 12.50,
        'cost'             => 6.00,
        'stock_quantity'   => 25,
        'image_path'       => 'uploads/images/sd-001.jpg',
        'price_updated_at' => '2025-08-22 12:00:00'
    ]
];

$mockDb = new MockDB($fakeRows);
$productModel = new Product($mockDb);

// Test 1: basic call returns our fake row
$results = $productModel->getProductsForPriceManagement(['limit' => 10, 'offset' => 0]);
assertEqual(is_array($results), true, 'Result is an array');
assertEqual(count($results), 1, 'One row returned');
assertEqual($results[0]->product_id, 101, 'Product ID matches');

// Test 2: category_id filter binds parameter
$results = $productModel->getProductsForPriceManagement(['category_id' => 5, 'limit' => 5, 'offset' => 0]);
assertEqual(isset($mockDb->bindings[':category_id']), true, 'category_id bound');
assertEqual($mockDb->bindings[':category_id'], 5, 'category_id value bound correctly');

// Test 3: pagination binds offset and limit
$productModel->getProductsForPriceManagement(['limit' => 15, 'offset' => 30]);
assertEqual($mockDb->bindings[':offset'], 30, 'offset bound correctly');
assertEqual($mockDb->bindings[':limit'], 15, 'limit bound correctly');

echo "All tests passed.\n";
