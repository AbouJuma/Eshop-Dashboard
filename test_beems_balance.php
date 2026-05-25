<?php

// Test the correct Beems Balance API endpoint you provided
$username = '48a8c40076933db5';
$password = 'ZjE4NjQxMzBhODIwMmQzNjZjMWE5YjJmODY3YzEyZmM0NzliODI1NDE3Y2U0NjAzYmUyOWE3NWU4ODcxYzVkYg==';
$Url = 'https://apisms.beem.africa/public/v1/vendors/balance';

echo "Testing Beems Balance API with OneMile credentials:\n";
echo "Endpoint: $Url\n";
echo "Username: $username\n\n";

$ch = curl_init($Url);
error_reporting(E_ALL);
ini_set('display_errors', 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt_array($ch, array(
    CURLOPT_HTTPGET => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization:Basic ' . base64_encode("$username:$password"),
        'Content-Type: application/json'
    ),
));

// Send the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if($response === FALSE){
    echo "Curl Error: " . curl_error($ch) . "\n";
    die(curl_error($ch));
}

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200) {
    echo "✅ SUCCESS! Real balance data retrieved!\n";
    $data = json_decode($response, true);
    echo "Balance Data:\n";
    print_r($data);
} else {
    echo "❌ Error: HTTP Code $httpCode\n";
}

curl_close($ch);
?>
