<?php
/**
 * Beam Debug V5 - Check is_available and enabled status
 */
require_once dirname(__FILE__) . '/wp-load.php';

echo "<h1>Beam Debug V5 - Availability Check</h1>";

// 1. Constants
echo "<h2>1. Constants</h2>";
echo "<pre>";
echo "MARSX_BEAM_MERCHANT_ID: " . (defined('MARSX_BEAM_MERCHANT_ID') ? MARSX_BEAM_MERCHANT_ID : 'NOT DEFINED') . "\n";
echo "MARSX_BEAM_API_KEY: " . (defined('MARSX_BEAM_API_KEY') ? 'SET (' . strlen(MARSX_BEAM_API_KEY) . ' chars)' : 'NOT DEFINED') . "\n";
echo "</pre>";

// 2. Gateway settings from database
echo "<h2>2. Gateway Settings (from database)</h2>";
echo "<pre>";
$settings = get_option('woocommerce_beam_checkout_settings');
if ($settings) {
    echo "Settings found:\n";
    print_r($settings);
} else {
    echo "NO SETTINGS FOUND IN DATABASE\n";
    echo "This means the gateway has never been saved.\n";
}
echo "</pre>";

// 3. Gateway instance check
echo "<h2>3. Gateway Instance</h2>";
if (class_exists('WC_Gateway_Beam')) {
    $gateway = new WC_Gateway_Beam();
    echo "<pre>";
    echo "Gateway ID: " . $gateway->id . "\n";
    echo "enabled (from settings): '" . $gateway->enabled . "'\n";
    echo "title: '" . $gateway->title . "'\n";

    // Use reflection to get private properties
    $reflection = new ReflectionClass($gateway);

    $merchant_prop = $reflection->getProperty('merchant_id');
    $merchant_prop->setAccessible(true);
    echo "merchant_id: '" . $merchant_prop->getValue($gateway) . "'\n";

    $api_prop = $reflection->getProperty('api_key');
    $api_prop->setAccessible(true);
    echo "api_key length: " . strlen($api_prop->getValue($gateway)) . "\n";

    echo "\nis_available(): " . ($gateway->is_available() ? 'YES' : 'NO') . "\n";
    echo "</pre>";
} else {
    echo "<pre>WC_Gateway_Beam class not found</pre>";
}

// 4. All registered payment gateways
echo "<h2>4. All Payment Gateways</h2>";
if (function_exists('WC') && WC()->payment_gateways) {
    $gateways = WC()->payment_gateways()->payment_gateways();
    echo "<pre>";
    foreach ($gateways as $id => $gw) {
        $available = method_exists($gw, 'is_available') ? ($gw->is_available() ? 'YES' : 'NO') : 'N/A';
        echo $id . " => enabled: " . $gw->enabled . ", is_available: " . $available . "\n";
    }
    echo "</pre>";
} else {
    echo "<pre>WooCommerce not loaded</pre>";
}

// 5. Try to manually set enabled
echo "<h2>5. Manual Enable Test</h2>";
echo "<pre>";
$test_settings = array(
    'enabled' => 'yes',
    'title' => 'ชำระผ่าน QR Code',
    'title_en' => 'Pay via QR Code',
    'description' => 'สแกน QR Code เพื่อชำระเงินผ่าน PromptPay',
    'description_en' => 'Scan QR Code to pay via PromptPay',
);
echo "To manually enable, run this in database or save from admin:\n";
echo "Option name: woocommerce_beam_checkout_settings\n";
echo "</pre>";

echo "<hr>";
echo "<p style='color:red;'><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
