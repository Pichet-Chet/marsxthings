<?php
/**
 * Beam Gateway Debug Test
 * เข้าถึงที่: https://marsxthings.com/beam-test.php
 * ลบไฟล์นี้หลังทดสอบเสร็จ!
 */

// Load WordPress
require_once dirname(__FILE__) . '/wp-load.php';

echo "<h1>Beam Gateway Debug</h1>";

// 1. Check constants
echo "<h2>1. Constants Check</h2>";
echo "<pre>";
echo "MARSX_BEAM_MERCHANT_ID: " . (defined('MARSX_BEAM_MERCHANT_ID') ? MARSX_BEAM_MERCHANT_ID : 'NOT DEFINED') . "\n";
echo "MARSX_BEAM_API_KEY: " . (defined('MARSX_BEAM_API_KEY') ? 'SET (' . strlen(MARSX_BEAM_API_KEY) . ' chars)' : 'NOT DEFINED') . "\n";
echo "MARSX_BEAM_WEBHOOK_SECRET: " . (defined('MARSX_BEAM_WEBHOOK_SECRET') ? 'SET' : 'NOT DEFINED') . "\n";
echo "</pre>";

// 2. Check if class file exists
echo "<h2>2. Class File Check</h2>";
$class_file = get_stylesheet_directory() . '/inc/class-wc-gateway-beam.php';
echo "<pre>";
echo "File path: " . $class_file . "\n";
echo "File exists: " . (file_exists($class_file) ? 'YES' : 'NO') . "\n";
if (file_exists($class_file)) {
    echo "File size: " . filesize($class_file) . " bytes\n";
}
echo "</pre>";

// 3. Check if class is loaded
echo "<h2>3. Class Load Check</h2>";
echo "<pre>";
echo "WC_Payment_Gateway exists: " . (class_exists('WC_Payment_Gateway') ? 'YES' : 'NO') . "\n";
echo "WC_Gateway_Beam exists: " . (class_exists('WC_Gateway_Beam') ? 'YES' : 'NO') . "\n";
echo "</pre>";

// 4. Check WooCommerce payment gateways
echo "<h2>4. Registered Payment Gateways</h2>";
if (function_exists('WC')) {
    $gateways = WC()->payment_gateways()->payment_gateways();
    echo "<pre>";
    foreach ($gateways as $id => $gateway) {
        echo $id . " => " . get_class($gateway) . " (enabled: " . ($gateway->enabled === 'yes' ? 'yes' : 'no') . ")\n";
    }
    echo "</pre>";
} else {
    echo "<p>WooCommerce not loaded</p>";
}

// 5. Try to manually load and instantiate
echo "<h2>5. Manual Load Test</h2>";
if (file_exists($class_file) && !class_exists('WC_Gateway_Beam')) {
    require_once $class_file;
    echo "<pre>Manually loaded class file</pre>";
}

if (class_exists('WC_Gateway_Beam')) {
    try {
        $test_gateway = new WC_Gateway_Beam();
        echo "<pre>";
        echo "Gateway ID: " . $test_gateway->id . "\n";
        echo "Gateway Title: " . $test_gateway->method_title . "\n";
        echo "Enabled: " . $test_gateway->enabled . "\n";
        echo "</pre>";
    } catch (Exception $e) {
        echo "<pre>Error: " . $e->getMessage() . "</pre>";
    }
} else {
    echo "<pre>WC_Gateway_Beam class still not available</pre>";
}

// 6. Check for PHP errors in error log
echo "<h2>6. Recent PHP Errors (if accessible)</h2>";
$error_log = ini_get('error_log');
echo "<pre>Error log path: " . ($error_log ?: 'default') . "</pre>";

echo "<hr><p><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
