<?php
// Example: How to call your API from another app

// 1. Using PHP cURL
function callHardwareStoreAPI($endpoint, $params = [])
{
    $baseUrl = 'http://localhost/api/'; // Your system's URL
    $url = $baseUrl . $endpoint;

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Example usage:
$result = callHardwareStoreAPI('getProducts.php');
if ($result['status'] == 200) {
    $products = $result['data'];
    // Process products...
}
?>