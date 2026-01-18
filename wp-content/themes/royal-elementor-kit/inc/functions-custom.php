<?php
/**
 * MarsX Custom Functions
 *
 * ไฟล์รวม custom functions ทั้งหมดของ MarsX
 */

// ป้องกันการเข้าถึงโดยตรง
if (!defined('ABSPATH')) {
    exit;
}

/**
 * เปลี่ยน Currency Symbol จาก ฿ เป็น บาท (มีเว้นวรรคนำหน้า)
 */
add_filter('woocommerce_currency_symbol', function($symbol, $currency) {
    if ($currency === 'THB') {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);
        return $is_english ? ' Baht' : ' บาท';
    }
    return $symbol;
}, 10, 2);

/**
 * Payment method translations Thai to English
 */
function marsx_get_payment_translations() {
    return array(
        'โอนเงินเข้าบัญชีธนาคาร' => 'Bank Transfer',
        'ชำระเงินปลายทาง' => 'Cash on Delivery',
        'บัตรเครดิต/เดบิต' => 'Credit/Debit Card',
        'พร้อมเพย์' => 'PromptPay',
        'QR Code' => 'QR Code Payment',
    );
}

/**
 * Translate payment method title to English
 */
function marsx_translate_payment_method($title) {
    $translations = marsx_get_payment_translations();
    return isset($translations[$title]) ? $translations[$title] : $title;
}

/**
 * แปลชื่อ Payment Gateway เป็นภาษาอังกฤษเมื่ออยู่ในหน้า /en/
 */
add_filter('woocommerce_gateway_title', function($title, $gateway_id) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

    if ($is_english) {
        return marsx_translate_payment_method($title);
    }

    return $title;
}, 10, 2);

/**
 * แปลคำอธิบาย Payment Gateway เป็นภาษาอังกฤษเมื่ออยู่ในหน้า /en/
 */
add_filter('woocommerce_gateway_description', function($description, $gateway_id) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

    if ($is_english) {
        // แปลคำอธิบาย payment methods
        $translations = array(
            'ชำระเงินเมื่อรับสินค้า' => 'Pay when you receive the goods',
            'โอนเงินเข้าบัญชีธนาคารของเรา' => 'Transfer money to our bank account',
            'ชำระผ่านบัตรเครดิตหรือเดบิต' => 'Pay via credit or debit card',
        );

        if (isset($translations[$description])) {
            return $translations[$description];
        }
    }

    return $description;
}, 10, 2);

/**
 * แปลปุ่ม Place Order เป็นภาษาอังกฤษเมื่ออยู่ในหน้า /en/
 */
add_filter('woocommerce_order_button_text', function($text) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

    if ($is_english) {
        return 'Place Order';
    }

    return $text;
});

/**
 * แก้ไข Checkout URL ให้ไปที่ custom checkout page
 */
add_filter('woocommerce_get_checkout_url', function($url) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

    if ($is_english) {
        return home_url('/en/products/checkout/');
    }

    // สำหรับภาษาไทย ให้ไปที่ custom checkout page
    return home_url('/products/checkout/');
});

/**
 * Redirect WooCommerce default order-received to custom Thank You page
 */
add_action('template_redirect', function() {
    // Check if this is WooCommerce order-received endpoint
    if (!function_exists('is_wc_endpoint_url') || !is_wc_endpoint_url('order-received')) {
        return;
    }

    // Get order ID from URL
    global $wp;
    $order_id = absint($wp->query_vars['order-received']);

    if (!$order_id) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    // Verify order key
    $order_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    if ($order_key !== $order->get_order_key()) return;

    // Detect language from session or current URL
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false);

    // Also check WC session for language preference
    if (WC()->session) {
        $checkout_lang = WC()->session->get('marsx_checkout_lang');
        if ($checkout_lang === 'en') {
            $is_english = true;
        }
    }

    // Build redirect URL
    $redirect_url = $is_english
        ? home_url('/en/order-received/?order_id=' . $order_id . '&key=' . $order->get_order_key())
        : home_url('/order-received/?order_id=' . $order_id . '&key=' . $order->get_order_key());

    wp_safe_redirect($redirect_url);
    exit;
}, 1);

/**
 * Store checkout language in session
 */
add_action('woocommerce_before_checkout_process', function() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

    if (WC()->session) {
        WC()->session->set('marsx_checkout_lang', $is_english ? 'en' : 'th');
    }
});

/**
 * Custom View Order URL for My Account orders
 */
add_filter('woocommerce_get_view_order_url', function($url, $order) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

    $order_id = $order->get_id();

    if ($is_english) {
        return home_url('/en/view-order/?order_id=' . $order_id);
    }

    return home_url('/view-order/?order_id=' . $order_id);
}, 10, 2);

/**
 * Prevent 404 for custom template URLs
 */
add_filter('pre_handle_404', function($preempt, $wp_query) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    $custom_pages = array(
        '/en/view-order',
        '/en/order-received',
        '/view-order',
        '/order-received',
    );

    if (in_array($uri_path, $custom_pages)) {
        return true; // Prevent 404
    }

    return $preempt;
}, 10, 2);

/**
 * Load custom templates for Thank You and View Order pages
 * ใช้ template_include filter เพื่อให้ WordPress และ Elementor load assets ได้ถูกต้อง
 */
add_filter('template_include', function($template) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    $templates = array(
        '/en/view-order' => 'page-view-order-en.php',
        '/en/order-received' => 'page-thankyou-en.php',
        '/view-order' => 'page-view-order.php',
        '/order-received' => 'page-thankyou.php',
    );

    foreach ($templates as $url_pattern => $template_file) {
        if ($uri_path === $url_pattern) {
            $template_path = get_stylesheet_directory() . '/custom-pages/' . $template_file;

            if (file_exists($template_path)) {
                status_header(200);
                return $template_path;
            }
        }
    }

    return $template;
}, 99);
