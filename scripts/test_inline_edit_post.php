<?php
// Quick test script to POST to suppliers/linkProduct endpoint for debugging inline edit
$base = 'http://localhost';
$path = '/suppliers/linkProduct';
$url = $base . $path;

$ps_id = 26; // example
$product_id = 110;
$purchase_price = '1700.00';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'ps_id' => $ps_id,
    'product_id' => $product_id,
    'purchase_price' => $purchase_price
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
// include cookies/session if needed
$res = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    echo "CURL error: $err\n";
} else {
    echo "HTTP $code\n";
    echo $res . "\n";
}
