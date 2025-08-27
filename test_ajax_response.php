<?php
// Test the actual AJAX endpoint with curl to see the raw response
$url = 'http://localhost/purchases/cancelPurchaseAjax';

$postData = http_build_query([
    'purchase_id' => '1',  // Use a valid purchase ID
    'reason' => 'Test cancellation reason'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/x-www-form-urlencoded',
            'X-Requested-With: XMLHttpRequest'
        ],
        'content' => $postData
    ]
]);

echo "Testing AJAX endpoint: $url\n";
echo "POST data: $postData\n\n";

$response = file_get_contents($url, false, $context);

echo "Raw response:\n";
echo "==============\n";
echo $response;
echo "\n==============\n";

// Try to decode as JSON
$jsonData = json_decode($response, true);
if ($jsonData === null) {
    echo "\nJSON decode error: " . json_last_error_msg() . "\n";
    echo "Response is not valid JSON\n";
} else {
    echo "\nJSON decoded successfully:\n";
    print_r($jsonData);
}
?>
