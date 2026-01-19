<?php
/**
 * Beam Debug V3 - ตรวจสอบ Constants โดยตรง
 */
require_once dirname(__FILE__) . '/wp-load.php';

echo "<h1>Beam Debug V3</h1>";

// 1. ตรวจสอบ Constants โดยตรง
echo "<h2>1. Constants Check (Direct)</h2>";
echo "<pre>";
echo "defined('MARSX_BEAM_MERCHANT_ID'): " . (defined('MARSX_BEAM_MERCHANT_ID') ? 'YES' : 'NO') . "\n";
echo "defined('MARSX_BEAM_API_KEY'): " . (defined('MARSX_BEAM_API_KEY') ? 'YES' : 'NO') . "\n";
echo "defined('MARSX_BEAM_WEBHOOK_SECRET'): " . (defined('MARSX_BEAM_WEBHOOK_SECRET') ? 'YES' : 'NO') . "\n";
echo "\n";

if (defined('MARSX_BEAM_MERCHANT_ID')) {
    echo "MARSX_BEAM_MERCHANT_ID value: '" . MARSX_BEAM_MERCHANT_ID . "'\n";
} else {
    echo "MARSX_BEAM_MERCHANT_ID: NOT DEFINED\n";
}

if (defined('MARSX_BEAM_API_KEY')) {
    echo "MARSX_BEAM_API_KEY length: " . strlen(MARSX_BEAM_API_KEY) . " chars\n";
} else {
    echo "MARSX_BEAM_API_KEY: NOT DEFINED\n";
}
echo "</pre>";

// 2. ตรวจสอบ wp-config.php path
echo "<h2>2. wp-config.php Location</h2>";
echo "<pre>";
echo "ABSPATH: " . ABSPATH . "\n";
$wp_config_path = ABSPATH . 'wp-config.php';
echo "wp-config.php path: " . $wp_config_path . "\n";
echo "wp-config.php exists: " . (file_exists($wp_config_path) ? 'YES' : 'NO') . "\n";
echo "</pre>";

// 3. ตรวจสอบว่ามี constants อื่นที่ defined ใน wp-config.php ไหม
echo "<h2>3. Other WP Constants (จาก wp-config.php)</h2>";
echo "<pre>";
echo "DB_NAME: " . (defined('DB_NAME') ? 'YES - ' . DB_NAME : 'NO') . "\n";
echo "WP_DEBUG: " . (defined('WP_DEBUG') ? 'YES - ' . (WP_DEBUG ? 'true' : 'false') : 'NO') . "\n";
echo "MARSX_GOOGLE_CLIENT_ID: " . (defined('MARSX_GOOGLE_CLIENT_ID') ? 'YES (set)' : 'NO') . "\n";
echo "</pre>";

// 4. ตรวจสอบ Gateway class
echo "<h2>4. Gateway Class Check</h2>";
echo "<pre>";
echo "class_exists('WC_Payment_Gateway'): " . (class_exists('WC_Payment_Gateway') ? 'YES' : 'NO') . "\n";
echo "class_exists('WC_Gateway_Beam'): " . (class_exists('WC_Gateway_Beam') ? 'YES' : 'NO') . "\n";
echo "</pre>";

// 5. ลองสร้าง instance และตรวจสอบค่า
echo "<h2>5. Gateway Instance Test</h2>";
if (class_exists('WC_Gateway_Beam')) {
    $gateway = new WC_Gateway_Beam();
    echo "<pre>";
    echo "Gateway ID: " . $gateway->id . "\n";
    echo "Gateway Title: " . $gateway->method_title . "\n";
    echo "Enabled: " . $gateway->enabled . "\n";

    // ใช้ reflection เพื่อดู private properties
    $reflection = new ReflectionClass($gateway);
    $merchant_id_prop = $reflection->getProperty('merchant_id');
    $merchant_id_prop->setAccessible(true);
    $merchant_id = $merchant_id_prop->getValue($gateway);

    $api_key_prop = $reflection->getProperty('api_key');
    $api_key_prop->setAccessible(true);
    $api_key = $api_key_prop->getValue($gateway);

    echo "merchant_id (private): '" . $merchant_id . "'\n";
    echo "api_key length (private): " . strlen($api_key) . " chars\n";
    echo "is_available(): " . ($gateway->is_available() ? 'YES' : 'NO') . "\n";
    echo "</pre>";
} else {
    echo "<pre>WC_Gateway_Beam class not available</pre>";
}

// 6. ตรวจสอบ Registered Payment Gateways
echo "<h2>6. Registered Payment Gateways</h2>";
if (function_exists('WC') && WC()->payment_gateways) {
    $gateways = WC()->payment_gateways()->payment_gateways();
    echo "<pre>";
    foreach ($gateways as $id => $gw) {
        echo $id . " => " . get_class($gw) . " (enabled: " . ($gw->enabled === 'yes' ? 'yes' : 'no') . ")\n";
    }
    echo "</pre>";
} else {
    echo "<pre>WooCommerce not loaded</pre>";
}

echo "<hr>";
echo "<p style='color:red;'><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
