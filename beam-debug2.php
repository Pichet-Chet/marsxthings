<?php
/**
 * Beam Debug V2 - ตรวจสอบทุกขั้นตอน
 */
require_once dirname(__FILE__) . '/wp-load.php';

echo "<h1>Beam Debug V2</h1>";

// 1. ตรวจสอบ theme path
echo "<h2>1. Theme Path Check</h2>";
echo "<pre>";
echo "get_stylesheet_directory(): " . get_stylesheet_directory() . "\n";
echo "get_template_directory(): " . get_template_directory() . "\n";
echo "</pre>";

// 2. ตรวจสอบว่า functions-custom.php ถูก include หรือไม่
echo "<h2>2. Functions Check</h2>";
echo "<pre>";
echo "function_exists('marsx_load_beam_gateway'): " . (function_exists('marsx_load_beam_gateway') ? 'YES' : 'NO') . "\n";
echo "function_exists('marsx_add_beam_gateway'): " . (function_exists('marsx_add_beam_gateway') ? 'YES' : 'NO') . "\n";
echo "</pre>";

// 3. ตรวจสอบ class
echo "<h2>3. Class Check</h2>";
echo "<pre>";
echo "class_exists('WC_Payment_Gateway'): " . (class_exists('WC_Payment_Gateway') ? 'YES' : 'NO') . "\n";
echo "class_exists('WC_Gateway_Beam'): " . (class_exists('WC_Gateway_Beam') ? 'YES' : 'NO') . "\n";
echo "</pre>";

// 4. ตรวจสอบ registered gateways
echo "<h2>4. Registered Gateways</h2>";
if (function_exists('WC') && WC()->payment_gateways) {
    $gateways = WC()->payment_gateways()->payment_gateways();
    echo "<pre>";
    foreach ($gateways as $id => $gateway) {
        echo $id . " => " . get_class($gateway) . "\n";
    }
    echo "</pre>";
} else {
    echo "<p>WooCommerce not ready</p>";
}

// 5. ตรวจสอบ filter
echo "<h2>5. Filter Check</h2>";
global $wp_filter;
echo "<pre>";
if (isset($wp_filter['woocommerce_payment_gateways'])) {
    echo "woocommerce_payment_gateways filter EXISTS\n";
    foreach ($wp_filter['woocommerce_payment_gateways']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            if (is_array($callback['function'])) {
                echo "Priority $priority: " . (is_object($callback['function'][0]) ? get_class($callback['function'][0]) : $callback['function'][0]) . "::" . $callback['function'][1] . "\n";
            } else {
                echo "Priority $priority: " . $callback['function'] . "\n";
            }
        }
    }
} else {
    echo "woocommerce_payment_gateways filter NOT FOUND\n";
}
echo "</pre>";

// 6. ลอง manual add และ check
echo "<h2>6. Manual Test</h2>";
$gateway_file = get_stylesheet_directory() . '/inc/class-wc-gateway-beam.php';
echo "<pre>";
echo "Gateway file: $gateway_file\n";
echo "File exists: " . (file_exists($gateway_file) ? 'YES' : 'NO') . "\n";

if (file_exists($gateway_file) && !class_exists('WC_Gateway_Beam')) {
    require_once $gateway_file;
    echo "Manually loaded\n";
}

if (class_exists('WC_Gateway_Beam')) {
    $test = new WC_Gateway_Beam();
    echo "Gateway ID: " . $test->id . "\n";
    echo "Merchant ID: " . ($test->merchant_id ?? 'NOT SET') . "\n";
}
echo "</pre>";

echo "<hr><p><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
