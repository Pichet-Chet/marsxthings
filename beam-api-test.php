<?php
/**
 * Beam API Test - ทดสอบการเชื่อมต่อกับ Beam API
 */
require_once dirname(__FILE__) . '/wp-load.php';

echo "<h1>Beam API Test</h1>";

// 1. Check credentials
echo "<h2>1. Credentials</h2>";
echo "<pre>";
$merchant_id = defined('MARSX_BEAM_MERCHANT_ID') ? MARSX_BEAM_MERCHANT_ID : '';
$api_key = defined('MARSX_BEAM_API_KEY') ? MARSX_BEAM_API_KEY : '';

echo "Merchant ID: " . $merchant_id . "\n";
echo "API Key length: " . strlen($api_key) . " chars\n";
echo "</pre>";

if (empty($merchant_id) || empty($api_key)) {
    echo "<p style='color:red'>Credentials not set!</p>";
    exit;
}

// 2. Test API call - Payment Links API v1
echo "<h2>2. Test API Call (Payment Links API v1)</h2>";

$api_url = 'https://playground.api.beamcheckout.com/api/v1/payment-links';
$auth = base64_encode($merchant_id . ':' . $api_key);

// Build test request for Payment Links API v1
$test_body = array(
    'order' => array(
        'currency' => 'THB',
        'netAmount' => 100,
        'description' => 'Test Order',
        'referenceId' => 'test-' . time(),
    ),
    'redirectUrl' => home_url('/order-received/?order_id=test'),
    'linkSettings' => array(
        'qrPromptPay' => array('isEnabled' => true),
        'card' => array('isEnabled' => false),
        'eWallets' => array('isEnabled' => false),
        'mobileBanking' => array('isEnabled' => false),
    ),
);

echo "<pre>";
echo "API URL: " . $api_url . "\n";
echo "Auth header: Basic " . substr($auth, 0, 20) . "...\n";
echo "\nRequest body:\n";
echo json_encode($test_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "</pre>";

// Make API request
echo "<h2>3. API Response</h2>";

$response = wp_remote_post($api_url, array(
    'headers' => array(
        'Authorization' => 'Basic ' . $auth,
        'Content-Type' => 'application/json',
    ),
    'body' => json_encode($test_body),
    'timeout' => 30,
));

echo "<pre>";
if (is_wp_error($response)) {
    echo "WP Error: " . $response->get_error_message() . "\n";
} else {
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    echo "Response Code: " . $response_code . "\n";
    echo "\nResponse Body:\n";

    $data = json_decode($response_body, true);
    if ($data) {
        print_r($data);
    } else {
        echo $response_body;
    }
}
echo "</pre>";

echo "<hr>";
echo "<p style='color:red;'><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
