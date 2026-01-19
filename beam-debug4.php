<?php
/**
 * Beam Debug V4 - ตรวจสอบไฟล์ class
 */
require_once dirname(__FILE__) . '/wp-load.php';

echo "<h1>Beam Debug V4 - File Check</h1>";

// 1. ตรวจสอบ path
$gateway_file = get_stylesheet_directory() . '/inc/class-wc-gateway-beam.php';

echo "<h2>1. Gateway File Check</h2>";
echo "<pre>";
echo "get_stylesheet_directory(): " . get_stylesheet_directory() . "\n";
echo "Gateway file path: " . $gateway_file . "\n";
echo "File exists: " . (file_exists($gateway_file) ? 'YES' : 'NO') . "\n";

if (file_exists($gateway_file)) {
    echo "File size: " . filesize($gateway_file) . " bytes\n";
    echo "File modified: " . date('Y-m-d H:i:s', filemtime($gateway_file)) . "\n";
    echo "\n--- First 500 chars of file ---\n";
    echo htmlspecialchars(substr(file_get_contents($gateway_file), 0, 500));
}
echo "</pre>";

// 2. ตรวจสอบ functions-custom.php
$custom_file = get_stylesheet_directory() . '/inc/functions-custom.php';

echo "<h2>2. Functions Custom File Check</h2>";
echo "<pre>";
echo "Functions file path: " . $custom_file . "\n";
echo "File exists: " . (file_exists($custom_file) ? 'YES' : 'NO') . "\n";

if (file_exists($custom_file)) {
    echo "File size: " . filesize($custom_file) . " bytes\n";

    // ค้นหา marsx_load_beam_gateway function
    $content = file_get_contents($custom_file);
    if (strpos($content, 'marsx_load_beam_gateway') !== false) {
        echo "Contains 'marsx_load_beam_gateway': YES\n";
    } else {
        echo "Contains 'marsx_load_beam_gateway': NO\n";
    }

    if (strpos($content, 'class-wc-gateway-beam.php') !== false) {
        echo "Contains 'class-wc-gateway-beam.php': YES\n";
    } else {
        echo "Contains 'class-wc-gateway-beam.php': NO\n";
    }
}
echo "</pre>";

// 3. ลอง manual require
echo "<h2>3. Manual Require Test</h2>";
echo "<pre>";
if (file_exists($gateway_file)) {
    echo "Attempting to require file...\n";

    // Check if WC_Payment_Gateway exists first
    if (!class_exists('WC_Payment_Gateway')) {
        echo "ERROR: WC_Payment_Gateway class does not exist!\n";
    } else {
        echo "WC_Payment_Gateway exists: YES\n";

        // Try to include the file
        try {
            require_once $gateway_file;
            echo "File required successfully\n";
            echo "WC_Gateway_Beam exists now: " . (class_exists('WC_Gateway_Beam') ? 'YES' : 'NO') . "\n";

            if (class_exists('WC_Gateway_Beam')) {
                $test = new WC_Gateway_Beam();
                echo "Gateway ID: " . $test->id . "\n";
            }
        } catch (Error $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            echo "Line: " . $e->getLine() . "\n";
        }
    }
} else {
    echo "Gateway file does not exist!\n";
}
echo "</pre>";

// 4. ตรวจสอบว่า function marsx_load_beam_gateway ถูกเรียกไหม
echo "<h2>4. Function Check</h2>";
echo "<pre>";
echo "function_exists('marsx_load_beam_gateway'): " . (function_exists('marsx_load_beam_gateway') ? 'YES' : 'NO') . "\n";
echo "function_exists('marsx_add_beam_gateway'): " . (function_exists('marsx_add_beam_gateway') ? 'YES' : 'NO') . "\n";
echo "</pre>";

echo "<hr>";
echo "<p style='color:red;'><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
