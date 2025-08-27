<?php
/**
 * Mobile API Test Script
 * Test the mobile API endpoints
 */

require_once '../bootstrap.php';

echo "=== MOBILE API TESTING ===\n\n";

$baseUrl = 'http://localhost/mobile-api';

function makeRequest($url, $data = null, $method = 'GET', $token = null)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $headers = ['Content-Type: application/json'];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST' || $method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_error($ch)) {
        return ['error' => curl_error($ch)];
    }

    curl_close($ch);

    return [
        'http_code' => $httpCode,
        'response'  => json_decode($response, true)
    ];
}

// Test 1: Customer Login
echo "1. Testing Customer Login:\n";
$loginData = [
    'action'   => 'customer_login',
    'email'    => 'lucky@test.com', // Use an existing customer email
    'password' => 'password123'   // You'll need to set this password
];

$result = makeRequest($baseUrl . '/auth.php', $loginData, 'POST');
echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

$customerToken = null;
if (isset($result['response']['success']) && $result['response']['success']) {
    $customerToken = $result['response']['token'];
    echo "✓ Customer login successful - Token received\n\n";
} else {
    echo "✗ Customer login failed\n\n";
}

// Test 2: Customer Dashboard (with token)
if ($customerToken) {
    echo "2. Testing Customer Dashboard:\n";
    $result = makeRequest($baseUrl . '/customer/dashboard', null, 'GET', $customerToken);
    echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
}

// Test 3: Contractor Login
echo "3. Testing Contractor Login:\n";
$loginData = [
    'action'   => 'contractor_login',
    'email'    => 'john.smith@test.com', // Use an existing contractor email
    'password' => 'password123'        // You'll need to set this password
];

$result = makeRequest($baseUrl . '/auth.php', $loginData, 'POST');
echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

$contractorToken = null;
if (isset($result['response']['success']) && $result['response']['success']) {
    $contractorToken = $result['response']['token'];
    echo "✓ Contractor login successful - Token received\n\n";
} else {
    echo "✗ Contractor login failed\n\n";
}

// Test 4: Contractor Dashboard (with token)
if ($contractorToken) {
    echo "4. Testing Contractor Dashboard:\n";
    $result = makeRequest($baseUrl . '/contractor/dashboard', null, 'GET', $contractorToken);
    echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
}

// Test 5: Token Validation
if ($customerToken) {
    echo "5. Testing Token Validation:\n";
    $tokenData = [
        'action' => 'validate_token',
        'token'  => $customerToken
    ];

    $result = makeRequest($baseUrl . '/auth.php', $tokenData, 'POST');
    echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
}

// Test 6: Unauthorized Request
echo "6. Testing Unauthorized Request:\n";
$result = makeRequest($baseUrl . '/customer/dashboard', null, 'GET', 'invalid-token');
echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

echo "=== MOBILE API TESTING COMPLETE ===\n";
echo "\nNotes:\n";
echo "- Make sure customers and contractors have proper passwords set\n";
echo "- Update test emails to match your database\n";
echo "- API is ready for mobile app integration\n";
echo "- See README.md for integration examples\n";
?>