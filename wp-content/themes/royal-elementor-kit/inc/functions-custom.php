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
 * Early template loading for custom pages (order-received, view-order)
 * Uses template_redirect to load templates before WordPress 404 handling
 */
add_action('template_redirect', function() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    $templates = array(
        '/en/view-order' => 'page-view-order-en.php',
        '/en/order-received' => 'page-thankyou-en.php',
        '/view-order' => 'page-view-order.php',
        '/order-received' => 'page-thankyou.php',
    );

    if (isset($templates[$uri_path])) {
        $template_path = get_stylesheet_directory() . '/custom-pages/' . $templates[$uri_path];

        if (file_exists($template_path)) {
            global $wp_query;
            $wp_query->is_404 = false;
            status_header(200);
            include $template_path;
            exit;
        }
    }
}, 1);

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
 * Set query vars for custom template URLs to prevent 404
 */
add_action('parse_request', function($wp) {
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
        // Trick WordPress into thinking this is a valid page
        $wp->query_vars['pagename'] = 'custom-template';
        $wp->query_vars['marsx_custom_page'] = $uri_path;
    }
}, 1);

/**
 * Prevent 404 for custom template URLs
 * Priority 1 to run before other filters
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
}, 1, 2);

/**
 * Load custom templates for Thank You and View Order pages
 * ใช้ template_include filter เพื่อให้ WordPress และ Elementor load assets ได้ถูกต้อง
 * Priority 9999 to ensure it runs last
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
                global $wp_query;
                $wp_query->is_404 = false;
                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                status_header(200);
                return $template_path;
            }
        }
    }

    return $template;
}, 9999);

/**
 * =========================================
 * Google Sign-In Integration
 * =========================================
 */

// Google OAuth Configuration
// ต้องเพิ่มใน wp-config.php:
// define('MARSX_GOOGLE_CLIENT_ID', 'your-client-id');
// define('MARSX_GOOGLE_CLIENT_SECRET', 'your-client-secret');
if (!defined('MARSX_GOOGLE_REDIRECT_URI')) {
    define('MARSX_GOOGLE_REDIRECT_URI', home_url('/google-callback/'));
}

/**
 * Generate Google OAuth URL
 */
function marsx_get_google_auth_url($lang = 'th') {
    $state = wp_create_nonce('marsx_google_auth') . '|' . $lang;

    $params = array(
        'client_id' => MARSX_GOOGLE_CLIENT_ID,
        'redirect_uri' => MARSX_GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'access_type' => 'online',
        'state' => base64_encode($state),
        'prompt' => 'select_account',
    );

    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

/**
 * Add google-callback to custom pages list (prevent 404)
 */
add_filter('pre_handle_404', function($preempt, $wp_query) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    if ($uri_path === '/google-callback') {
        return true;
    }

    return $preempt;
}, 5, 2);

/**
 * Handle Google OAuth Callback
 */
add_action('template_redirect', function() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    if ($uri_path !== '/google-callback') {
        return;
    }

    // Get authorization code
    $code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';
    $state = isset($_GET['state']) ? base64_decode($_GET['state']) : '';
    $error = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : '';

    // Parse state to get language and nonce
    $state_parts = explode('|', $state);
    $nonce = $state_parts[0] ?? '';
    $lang = $state_parts[1] ?? 'th';
    $is_english = ($lang === 'en');

    // Error handling function
    $redirect_with_error = function($message) use ($is_english) {
        $login_url = $is_english ? home_url('/en/login/') : home_url('/login/');
        wp_redirect($login_url . '?google_error=' . urlencode($message));
        exit;
    };

    // Check for errors
    if ($error) {
        $redirect_with_error($is_english ? 'Google login was cancelled' : 'การเข้าสู่ระบบด้วย Google ถูกยกเลิก');
    }

    // Verify nonce
    if (!wp_verify_nonce($nonce, 'marsx_google_auth')) {
        $redirect_with_error($is_english ? 'Security verification failed' : 'การตรวจสอบความปลอดภัยล้มเหลว');
    }

    if (empty($code)) {
        $redirect_with_error($is_english ? 'Authorization code not received' : 'ไม่ได้รับรหัสยืนยัน');
    }

    // Exchange code for access token
    $token_response = wp_remote_post('https://oauth2.googleapis.com/token', array(
        'body' => array(
            'client_id' => MARSX_GOOGLE_CLIENT_ID,
            'client_secret' => MARSX_GOOGLE_CLIENT_SECRET,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => MARSX_GOOGLE_REDIRECT_URI,
        ),
        'timeout' => 30,
    ));

    if (is_wp_error($token_response)) {
        $redirect_with_error($is_english ? 'Failed to connect to Google' : 'ไม่สามารถเชื่อมต่อกับ Google ได้');
    }

    $token_data = json_decode(wp_remote_retrieve_body($token_response), true);

    if (!isset($token_data['access_token'])) {
        $redirect_with_error($is_english ? 'Failed to get access token' : 'ไม่สามารถรับ access token ได้');
    }

    // Get user info from Google
    $user_response = wp_remote_get('https://www.googleapis.com/oauth2/v2/userinfo', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $token_data['access_token'],
        ),
        'timeout' => 30,
    ));

    if (is_wp_error($user_response)) {
        $redirect_with_error($is_english ? 'Failed to get user information' : 'ไม่สามารถดึงข้อมูลผู้ใช้ได้');
    }

    $google_user = json_decode(wp_remote_retrieve_body($user_response), true);

    if (!isset($google_user['email'])) {
        $redirect_with_error($is_english ? 'Email not provided by Google' : 'Google ไม่ได้ให้ข้อมูลอีเมล');
    }

    $email = sanitize_email($google_user['email']);
    $google_id = sanitize_text_field($google_user['id']);
    $name = isset($google_user['name']) ? sanitize_text_field($google_user['name']) : '';
    $first_name = isset($google_user['given_name']) ? sanitize_text_field($google_user['given_name']) : '';
    $last_name = isset($google_user['family_name']) ? sanitize_text_field($google_user['family_name']) : '';
    $picture = isset($google_user['picture']) ? esc_url_raw($google_user['picture']) : '';

    // Check if user exists by email
    $user = get_user_by('email', $email);

    if (!$user) {
        // Check if user exists by Google ID (stored in user meta)
        $users = get_users(array(
            'meta_key' => 'marsx_google_id',
            'meta_value' => $google_id,
            'number' => 1,
        ));

        if (!empty($users)) {
            $user = $users[0];
        }
    }

    if (!$user) {
        // Create new user
        $username = marsx_generate_unique_username($email, $first_name, $last_name);
        $random_password = wp_generate_password(16, true, true);

        $user_id = wp_create_user($username, $random_password, $email);

        if (is_wp_error($user_id)) {
            $redirect_with_error($is_english ? 'Failed to create account' : 'ไม่สามารถสร้างบัญชีได้');
        }

        // Update user meta
        wp_update_user(array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $name ?: $first_name,
        ));

        // Set role to customer if WooCommerce is active
        $user = get_user_by('ID', $user_id);
        if (class_exists('WooCommerce')) {
            $user->set_role('customer');
        }

        // Store Google ID
        update_user_meta($user_id, 'marsx_google_id', $google_id);
        update_user_meta($user_id, 'marsx_google_picture', $picture);
        update_user_meta($user_id, 'marsx_registered_via', 'google');

    } else {
        // Update existing user's Google info
        update_user_meta($user->ID, 'marsx_google_id', $google_id);
        update_user_meta($user->ID, 'marsx_google_picture', $picture);
    }

    // Log the user in
    wp_clear_auth_cookie();
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    // Redirect to my-account page
    $redirect_url = $is_english ? home_url('/en/my-account/') : home_url('/my-account/');
    wp_redirect($redirect_url);
    exit;
}, 5);

/**
 * Generate unique username from email or name
 */
function marsx_generate_unique_username($email, $first_name = '', $last_name = '') {
    // Try using first name + last name
    if ($first_name) {
        $base_username = sanitize_user(strtolower($first_name . ($last_name ? '_' . $last_name : '')));
    } else {
        // Use email prefix
        $base_username = sanitize_user(strtolower(strstr($email, '@', true)));
    }

    $username = $base_username;
    $counter = 1;

    while (username_exists($username)) {
        $username = $base_username . $counter;
        $counter++;
    }

    return $username;
}

/**
 * =========================================
 * Google reCAPTCHA v3 Integration
 * =========================================
 */

// reCAPTCHA v3 Configuration
// ต้องเพิ่มใน wp-config.php:
// define('MARSX_RECAPTCHA_V3_SITE_KEY', 'your-site-key');
// define('MARSX_RECAPTCHA_V3_SECRET_KEY', 'your-secret-key');

/**
 * Get reCAPTCHA v3 Site Key
 * @return string
 */
function marsx_get_recaptcha_site_key() {
    return defined('MARSX_RECAPTCHA_V3_SITE_KEY') ? MARSX_RECAPTCHA_V3_SITE_KEY : '';
}

/**
 * Check if reCAPTCHA v3 is enabled
 * @return bool
 */
function marsx_is_recaptcha_enabled() {
    return defined('MARSX_RECAPTCHA_V3_SITE_KEY') && defined('MARSX_RECAPTCHA_V3_SECRET_KEY')
        && !empty(MARSX_RECAPTCHA_V3_SITE_KEY) && !empty(MARSX_RECAPTCHA_V3_SECRET_KEY);
}

/**
 * Verify reCAPTCHA v3 token
 * @param string $token - reCAPTCHA token from frontend
 * @param string $expected_action - Expected action name
 * @param float $min_score - Minimum acceptable score (default 0.5)
 * @return array ['success' => bool, 'score' => float, 'action' => string, 'error' => string]
 */
function marsx_verify_recaptcha_v3($token, $expected_action = '', $min_score = 0.5) {
    $result = array(
        'success' => false,
        'score' => 0,
        'action' => '',
        'error' => '',
    );

    // Check if reCAPTCHA is configured
    if (!marsx_is_recaptcha_enabled()) {
        $result['success'] = true; // Skip verification if not configured
        return $result;
    }

    // Check if token is provided
    if (empty($token)) {
        $result['error'] = 'reCAPTCHA token missing';
        return $result;
    }

    // Verify with Google API
    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
        'body' => array(
            'secret' => MARSX_RECAPTCHA_V3_SECRET_KEY,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ),
        'timeout' => 10,
    ));

    // Check for connection error
    if (is_wp_error($response)) {
        $result['error'] = 'Failed to connect to reCAPTCHA server';
        return $result;
    }

    // Parse response
    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!$body) {
        $result['error'] = 'Invalid response from reCAPTCHA server';
        return $result;
    }

    // Debug logging
    error_log('MarsX reCAPTCHA Response: ' . print_r($body, true));

    // Check success
    if (empty($body['success'])) {
        $result['error'] = 'reCAPTCHA verification failed';
        if (!empty($body['error-codes'])) {
            $result['error'] .= ': ' . implode(', ', $body['error-codes']);
        }
        return $result;
    }

    // Get score and action
    $result['score'] = isset($body['score']) ? floatval($body['score']) : 0;
    $result['action'] = isset($body['action']) ? $body['action'] : '';

    // Check score threshold
    if ($result['score'] < $min_score) {
        $result['error'] = 'Score too low';
        return $result;
    }

    // Check action matches (if expected_action provided)
    // ปิด action check ไว้ก่อนเพื่อ debug
    // if (!empty($expected_action) && $result['action'] !== $expected_action) {
    //     $result['error'] = 'Action mismatch';
    //     return $result;
    // }

    $result['success'] = true;
    return $result;
}

/**
 * =========================================
 * Custom Order Number Format
 * =========================================
 * Format: MX + YYMMDD + Order ID
 * Example: MX250119-2130 (Jan 19, 2025, Order #2130)
 */

/**
 * Generate custom order number
 * @param int $order_id
 * @return string Custom formatted order number
 */
function marsx_get_custom_order_number($order_id) {
    $order = wc_get_order($order_id);

    if (!$order) {
        return $order_id;
    }

    // Get order date
    $order_date = $order->get_date_created();

    if ($order_date) {
        // Format: MX + YYMMDD + - + Order ID
        $date_part = $order_date->date('ymd');
        return 'MX' . $date_part . '-' . $order_id;
    }

    // Fallback: use current date if order date not available
    return 'MX' . date('ymd') . '-' . $order_id;
}

/**
 * Filter WooCommerce order number display
 */
add_filter('woocommerce_order_number', function($order_id, $order) {
    return marsx_get_custom_order_number($order->get_id());
}, 10, 2);

/**
 * WooCommerce Checkout reCAPTCHA v3 Validation
 */
add_action('woocommerce_checkout_process', function() {
    if (!marsx_is_recaptcha_enabled()) {
        return;
    }

    $recaptcha_token = isset($_POST['recaptcha_token']) ? sanitize_text_field($_POST['recaptcha_token']) : '';
    $recaptcha_result = marsx_verify_recaptcha_v3($recaptcha_token, 'checkout', 0.5);

    if (!$recaptcha_result['success']) {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

        $error_message = $is_english
            ? 'Security verification failed. Please refresh the page and try again.'
            : 'การตรวจสอบความปลอดภัยล้มเหลว กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง';

        wc_add_notice($error_message, 'error');
    }
});

/**
 * =========================================
 * Beam Checkout Payment Gateway Integration
 * =========================================
 */

// Beam Checkout Configuration
// ต้องเพิ่มใน wp-config.php:
// define('MARSX_BEAM_MERCHANT_ID', 'your-merchant-id');
// define('MARSX_BEAM_API_KEY', 'your-api-key');
// define('MARSX_BEAM_WEBHOOK_SECRET', 'your-webhook-secret');

/**
 * Load Beam Gateway Class - runs early on plugins_loaded
 */
add_action('plugins_loaded', 'marsx_load_beam_gateway', 0);
function marsx_load_beam_gateway() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    $gateway_file = get_stylesheet_directory() . '/inc/class-wc-gateway-beam.php';
    if (file_exists($gateway_file) && !class_exists('WC_Gateway_Beam')) {
        require_once $gateway_file;
    }
}

/**
 * Register Beam Gateway with WooCommerce - separate filter
 */
add_filter('woocommerce_payment_gateways', 'marsx_add_beam_gateway', 10);
function marsx_add_beam_gateway($gateways) {
    // Load class if not yet loaded
    if (!class_exists('WC_Gateway_Beam')) {
        $gateway_file = get_stylesheet_directory() . '/inc/class-wc-gateway-beam.php';
        if (file_exists($gateway_file)) {
            require_once $gateway_file;
        }
    }

    if (class_exists('WC_Gateway_Beam')) {
        $gateways[] = 'WC_Gateway_Beam';
    }
    return $gateways;
}

/**
 * Register Beam as offline payment method for new WooCommerce UI
 */
add_filter('woocommerce_admin_payment_gateway_suggestion_specs', function($specs) {
    $specs[] = array(
        'id' => 'beam_checkout',
        'title' => 'Beam Checkout (QR Code)',
        'content' => 'รับชำระเงินผ่าน QR Code PromptPay',
        'image' => '',
        'plugins' => array(),
        'is_visible' => true,
        'category_other' => array('TH'),
        'category_additional' => array(),
    );
    return $specs;
});

/**
 * Add Beam to offline payment methods list
 */
add_filter('woocommerce_admin_get_feature_config', function($features) {
    return $features;
});

/**
 * Add Beam Checkout to WooCommerce Settings submenu
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'woocommerce',
        'Beam Checkout Settings',
        'Beam Checkout',
        'manage_woocommerce',
        'beam-checkout-settings',
        'marsx_beam_settings_page'
    );
}, 99);

/**
 * Beam Checkout Settings Page
 */
function marsx_beam_settings_page() {
    // Make sure gateway class is loaded
    if (!class_exists('WC_Gateway_Beam')) {
        $gateway_file = get_stylesheet_directory() . '/inc/class-wc-gateway-beam.php';
        if (file_exists($gateway_file)) {
            require_once $gateway_file;
        }
    }

    if (!class_exists('WC_Gateway_Beam')) {
        echo '<div class="wrap"><h1>Beam Checkout</h1><p>Error: Gateway class not found.</p></div>';
        return;
    }

    $gateway = new WC_Gateway_Beam();

    // Handle form submission
    if (isset($_POST['save_beam_settings']) && wp_verify_nonce($_POST['beam_nonce'], 'save_beam_settings')) {
        $gateway->process_admin_options();
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
        // Reload gateway to get updated settings
        $gateway = new WC_Gateway_Beam();
    }

    ?>
    <div class="wrap">
        <h1>Beam Checkout Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('save_beam_settings', 'beam_nonce'); ?>
            <table class="form-table">
                <?php $gateway->generate_settings_html(); ?>
            </table>
            <p class="submit">
                <button type="submit" name="save_beam_settings" class="button-primary">บันทึกการตั้งค่า</button>
            </p>
        </form>
    </div>
    <?php
}

/**
 * Register Beam Webhook Endpoint
 */
add_action('init', 'marsx_register_beam_webhook_endpoint');
function marsx_register_beam_webhook_endpoint() {
    add_rewrite_rule('^beam-webhook/?$', 'index.php?beam_webhook=1', 'top');
}

/**
 * Add beam_webhook query var
 */
add_filter('query_vars', 'marsx_beam_webhook_query_vars');
function marsx_beam_webhook_query_vars($vars) {
    $vars[] = 'beam_webhook';
    return $vars;
}

/**
 * Prevent 404 for beam-webhook endpoint
 */
add_filter('pre_handle_404', function($preempt, $wp_query) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    if ($uri_path === '/beam-webhook') {
        return true;
    }

    return $preempt;
}, 5, 2);

/**
 * Handle Beam Webhook Requests
 */
add_action('template_redirect', 'marsx_handle_beam_webhook');
function marsx_handle_beam_webhook() {
    if (!get_query_var('beam_webhook')) {
        return;
    }

    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        status_header(405);
        echo json_encode(array('error' => 'Method not allowed'));
        exit;
    }

    // Get raw body
    $raw_body = file_get_contents('php://input');

    // Get signature header
    $signature = isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : '';

    // Log webhook request
    error_log('Beam Webhook Request Received');
    error_log('Beam Webhook Signature: ' . $signature);
    error_log('Beam Webhook Body: ' . $raw_body);

    // Process webhook
    if (class_exists('WC_Gateway_Beam')) {
        $gateway = new WC_Gateway_Beam();
        $success = $gateway->handle_webhook($raw_body, $signature);

        if ($success) {
            status_header(200);
            echo json_encode(array('status' => 'ok'));
        } else {
            status_header(400);
            echo json_encode(array('error' => 'Webhook processing failed'));
        }
    } else {
        status_header(500);
        echo json_encode(array('error' => 'Gateway not available'));
    }

    exit;
}

/**
 * Check if Beam is configured
 * @return bool
 */
function marsx_is_beam_configured() {
    return defined('MARSX_BEAM_MERCHANT_ID') && defined('MARSX_BEAM_API_KEY')
        && !empty(MARSX_BEAM_MERCHANT_ID) && !empty(MARSX_BEAM_API_KEY);
}

/**
 * =========================================
 * Microsoft Graph Email Configuration
 * =========================================
 * - ใช้ Microsoft Graph API สำหรับส่งอีเมล
 * - ต้อง config Azure AD App Registration
 *
 * เพิ่มใน wp-config.php:
 * define('MARSX_GRAPH_TENANT_ID', 'your-tenant-id');
 * define('MARSX_GRAPH_CLIENT_ID', 'your-client-id');
 * define('MARSX_GRAPH_CLIENT_SECRET', 'your-client-secret');
 * define('MARSX_GRAPH_SENDER_EMAIL', 'no-reply@yourdomain.com');
 */

/**
 * Get Microsoft Graph Access Token
 * Uses OAuth 2.0 Client Credentials flow
 * @return string|false Access token or false on failure
 */
function marsx_get_graph_access_token() {
    // Check for cached token
    $cached_token = get_transient('marsx_graph_access_token');
    if ($cached_token) {
        return $cached_token;
    }

    $token_url = 'https://login.microsoftonline.com/' . MARSX_GRAPH_TENANT_ID . '/oauth2/v2.0/token';

    $response = wp_remote_post($token_url, array(
        'timeout' => 30,
        'body' => array(
            'client_id' => MARSX_GRAPH_CLIENT_ID,
            'client_secret' => MARSX_GRAPH_CLIENT_SECRET,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ),
    ));

    if (is_wp_error($response)) {
        error_log('MarsX Graph Token Error: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['access_token'])) {
        // Cache token for 50 minutes (tokens expire in 60 minutes)
        set_transient('marsx_graph_access_token', $body['access_token'], 50 * 60);
        return $body['access_token'];
    }

    error_log('MarsX Graph Token Error: ' . print_r($body, true));
    return false;
}

/**
 * Send email via Microsoft Graph API
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $html_body HTML email body
 * @return bool Success or failure
 */
function marsx_send_email_graph($to, $subject, $html_body) {
    $access_token = marsx_get_graph_access_token();
    if (!$access_token) {
        error_log('MarsX Graph: Failed to get access token');
        return false;
    }

    $send_url = 'https://graph.microsoft.com/v1.0/users/' . MARSX_GRAPH_SENDER_EMAIL . '/sendMail';

    $email_data = array(
        'message' => array(
            'subject' => $subject,
            'body' => array(
                'contentType' => 'HTML',
                'content' => $html_body,
            ),
            'toRecipients' => array(
                array(
                    'emailAddress' => array(
                        'address' => $to,
                    ),
                ),
            ),
            'from' => array(
                'emailAddress' => array(
                    'address' => MARSX_GRAPH_SENDER_EMAIL,
                    'name' => 'MarsX Things',
                ),
            ),
        ),
        'saveToSentItems' => false,
    );

    $response = wp_remote_post($send_url, array(
        'timeout' => 30,
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode($email_data),
    ));

    if (is_wp_error($response)) {
        error_log('MarsX Graph Send Error: ' . $response->get_error_message());
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);

    // 202 = Accepted (email queued for sending)
    if ($response_code === 202) {
        return true;
    }

    $body = wp_remote_retrieve_body($response);
    error_log('MarsX Graph Send Error (Code ' . $response_code . '): ' . $body);
    return false;
}

/**
 * =========================================
 * Email Verification System
 * =========================================
 * - ระบบยืนยันอีเมลสำหรับการสมัครสมาชิก
 * - Verification link หมดอายุใน 24 ชั่วโมง
 * - Google Sign-in ไม่ต้องยืนยันอีเมล (Auto Sign-in)
 */

// Verification token expiry (24 hours)
define('MARSX_EMAIL_VERIFICATION_EXPIRY', 24 * 60 * 60);

/**
 * Generate email verification token
 * @param int $user_id
 * @return string Verification token
 */
function marsx_generate_verification_token($user_id) {
    $token = wp_generate_password(64, false, false);
    $expiry = time() + MARSX_EMAIL_VERIFICATION_EXPIRY;

    update_user_meta($user_id, 'marsx_email_verification_token', $token);
    update_user_meta($user_id, 'marsx_email_verification_expiry', $expiry);
    update_user_meta($user_id, 'marsx_email_verified', 'no');

    return $token;
}

/**
 * Check if user's email is verified
 * @param int $user_id
 * @return bool
 */
function marsx_is_email_verified($user_id) {
    // Google users are auto-verified
    $registered_via = get_user_meta($user_id, 'marsx_registered_via', true);
    if ($registered_via === 'google') {
        return true;
    }

    $verified = get_user_meta($user_id, 'marsx_email_verified', true);
    return $verified === 'yes';
}

/**
 * Get verification URL
 * @param int $user_id
 * @param string $token
 * @param string $lang
 * @return string
 */
function marsx_get_verification_url($user_id, $token, $lang = 'th') {
    $base_url = $lang === 'en' ? home_url('/en/verify-email/') : home_url('/verify-email/');
    return add_query_arg(array(
        'user_id' => $user_id,
        'token' => $token,
    ), $base_url);
}

/**
 * Send verification email
 * @param int $user_id
 * @param string $lang
 * @return bool
 */
function marsx_send_verification_email($user_id, $lang = 'th') {
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return false;
    }

    $token = marsx_generate_verification_token($user_id);
    $verification_url = marsx_get_verification_url($user_id, $token, $lang);

    $is_english = ($lang === 'en');

    // Email subject
    $subject = $is_english
        ? 'Verify Your Email - ' . get_bloginfo('name')
        : 'ยืนยันอีเมลของคุณ - ' . get_bloginfo('name');

    // Logo URL
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/marsx-logo.png';
    $site_url = home_url('/');

    // Email content
    $name = $user->first_name ?: $user->display_name;
    $year = date('Y');

    // Get email template
    $message = marsx_get_email_template($is_english, $name, $verification_url, $logo_url, $site_url, $year);

    // Send via Microsoft Graph
    return marsx_send_email_graph($user->user_email, $subject, $message);
}

/**
 * Get beautiful email template
 * @param bool $is_english
 * @param string $name
 * @param string $verification_url
 * @param string $logo_url
 * @param string $site_url
 * @param string $year
 * @return string HTML email content
 */
function marsx_get_email_template($is_english, $name, $verification_url, $logo_url, $site_url, $year) {
    // Text translations
    $texts = $is_english ? array(
        'greeting' => "Hello <strong>{$name}</strong>,",
        'welcome' => "Welcome to MarsX Things!",
        'message' => "Thank you for registering. Please verify your email address by clicking the button below to activate your account.",
        'button' => "Verify Email Address",
        'expire' => "This link will expire in <strong>24 hours</strong>.",
        'ignore' => "If you didn't create an account, please ignore this email.",
        'help' => "Need help? Contact us at",
        'rights' => "All rights reserved.",
        'follow' => "Follow us"
    ) : array(
        'greeting' => "สวัสดีคุณ <strong>{$name}</strong>,",
        'welcome' => "ยินดีต้อนรับสู่ MarsX Things!",
        'message' => "ขอบคุณที่สมัครสมาชิก กรุณายืนยันอีเมลของคุณโดยคลิกปุ่มด้านล่างเพื่อเปิดใช้งานบัญชี",
        'button' => "ยืนยันอีเมล",
        'expire' => "ลิงก์นี้จะหมดอายุภายใน <strong>24 ชั่วโมง</strong>",
        'ignore' => "หากคุณไม่ได้สมัครสมาชิก กรุณาเพิกเฉยอีเมลนี้",
        'help' => "ต้องการความช่วยเหลือ? ติดต่อเราที่",
        'rights' => "สงวนลิขสิทธิ์",
        'follow' => "ติดตามเรา"
    );

    return '<!DOCTYPE html>
<html lang="' . ($is_english ? 'en' : 'th') . '">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
</head>
<body style="margin: 0; padding: 0; font-family: \'Segoe UI\', \'Noto Sans Thai\', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Main Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width: 600px; width: 100%;">

                    <!-- Header with Logo -->
                    <tr>
                        <td align="center" style="padding: 30px 0;">
                            <a href="' . esc_url($site_url) . '" style="text-decoration: none;">
                                <img src="' . esc_url($logo_url) . '" alt="MarsX Things" width="180" style="max-width: 180px; height: auto; display: block;">
                            </a>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%); border-radius: 20px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);">

                                <!-- Orange Top Border -->
                                <tr>
                                    <td style="background: linear-gradient(90deg, #f5a623 0%, #f39c12 50%, #e67e22 100%); height: 6px; border-radius: 20px 20px 0 0;"></td>
                                </tr>

                                <!-- Icon -->
                                <tr>
                                    <td align="center" style="padding: 40px 40px 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="background: linear-gradient(135deg, #fff9f0 0%, #fff5e6 100%); border-radius: 50%; width: 80px; height: 80px; text-align: center; vertical-align: middle;">
                                                    <span style="font-size: 36px;">&#9993;</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Welcome Text -->
                                <tr>
                                    <td align="center" style="padding: 0 40px 10px;">
                                        <h1 style="margin: 0; font-size: 26px; font-weight: 700; color: #1a1a1a;">' . $texts['welcome'] . '</h1>
                                    </td>
                                </tr>

                                <!-- Greeting -->
                                <tr>
                                    <td align="center" style="padding: 20px 40px 10px;">
                                        <p style="margin: 0; font-size: 16px; color: #333; line-height: 1.6;">' . $texts['greeting'] . '</p>
                                    </td>
                                </tr>

                                <!-- Message -->
                                <tr>
                                    <td align="center" style="padding: 10px 40px 30px;">
                                        <p style="margin: 0; font-size: 15px; color: #555; line-height: 1.7;">' . $texts['message'] . '</p>
                                    </td>
                                </tr>

                                <!-- Button -->
                                <tr>
                                    <td align="center" style="padding: 0 40px 30px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="border-radius: 50px; background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%); box-shadow: 0 8px 25px rgba(243, 156, 18, 0.35);">
                                                    <a href="' . esc_url($verification_url) . '" style="display: inline-block; padding: 16px 50px; font-size: 16px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 50px;">' . $texts['button'] . '</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Expire Note -->
                                <tr>
                                    <td align="center" style="padding: 0 40px 15px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" style="background-color: #fff9f0; border-radius: 10px; border-left: 4px solid #f39c12;">
                                            <tr>
                                                <td style="padding: 12px 20px;">
                                                    <p style="margin: 0; font-size: 13px; color: #92400e;">&#9203; ' . $texts['expire'] . '</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Ignore Note -->
                                <tr>
                                    <td align="center" style="padding: 10px 40px 40px;">
                                        <p style="margin: 0; font-size: 13px; color: #888;">' . $texts['ignore'] . '</p>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 30px 20px;">
                            <p style="margin: 0 0 10px; font-size: 13px; color: #888;">' . $texts['help'] . ' <a href="mailto:support@marsxthings.com" style="color: #f39c12; text-decoration: none;">support@marsxthings.com</a></p>
                            <p style="margin: 0; font-size: 12px; color: #aaa;">&copy; ' . $year . ' MarsX Things. ' . $texts['rights'] . '</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

/**
 * Verify email token
 * @param int $user_id
 * @param string $token
 * @return array ['success' => bool, 'error' => string]
 */
function marsx_verify_email_token($user_id, $token) {
    $result = array('success' => false, 'error' => '');

    $user = get_user_by('ID', $user_id);
    if (!$user) {
        $result['error'] = 'invalid_user';
        return $result;
    }

    // Check if already verified
    if (marsx_is_email_verified($user_id)) {
        $result['success'] = true;
        $result['error'] = 'already_verified';
        return $result;
    }

    // Get stored token and expiry
    $stored_token = get_user_meta($user_id, 'marsx_email_verification_token', true);
    $expiry = get_user_meta($user_id, 'marsx_email_verification_expiry', true);

    // Check token
    if (empty($stored_token) || $token !== $stored_token) {
        $result['error'] = 'invalid_token';
        return $result;
    }

    // Check expiry
    if (time() > $expiry) {
        $result['error'] = 'token_expired';
        return $result;
    }

    // Mark as verified
    update_user_meta($user_id, 'marsx_email_verified', 'yes');
    delete_user_meta($user_id, 'marsx_email_verification_token');
    delete_user_meta($user_id, 'marsx_email_verification_expiry');

    $result['success'] = true;
    return $result;
}

/**
 * Resend verification email
 * @param string $email
 * @param string $lang
 * @return array ['success' => bool, 'error' => string]
 */
function marsx_resend_verification_email($email, $lang = 'th') {
    $result = array('success' => false, 'error' => '');

    $user = get_user_by('email', $email);
    if (!$user) {
        $result['error'] = 'user_not_found';
        return $result;
    }

    // Check if already verified
    if (marsx_is_email_verified($user->ID)) {
        $result['error'] = 'already_verified';
        return $result;
    }

    // Send new verification email
    $sent = marsx_send_verification_email($user->ID, $lang);

    if ($sent) {
        $result['success'] = true;
    } else {
        $result['error'] = 'email_failed';
    }

    return $result;
}

/**
 * Add verify-email to custom pages list (prevent 404)
 */
add_filter('pre_handle_404', function($preempt, $wp_query) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    if ($uri_path === '/verify-email' || $uri_path === '/en/verify-email') {
        return true;
    }

    return $preempt;
}, 5, 2);

/**
 * Load verify-email template
 */
add_filter('template_include', function($template) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri_path = parse_url($request_uri, PHP_URL_PATH);
    $uri_path = rtrim($uri_path, '/');

    $templates = array(
        '/en/verify-email' => 'page-verify-email-en.php',
        '/verify-email' => 'page-verify-email.php',
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

/**
 * Block unverified users from logging in (except Google users)
 */
add_filter('authenticate', function($user, $username, $password) {
    // Skip if already an error or no user
    if (is_wp_error($user) || !$user) {
        return $user;
    }

    // Check if email is verified
    if (!marsx_is_email_verified($user->ID)) {
        // Store the email for resend functionality
        $error = new WP_Error();
        $error->add('email_not_verified', $user->user_email);
        return $error;
    }

    return $user;
}, 30, 3);

/**
 * =========================================
 * Microsoft Teams Chat Notification
 * =========================================
 * ใช้ Resource Owner Password Credentials (ROPC) flow
 * เพื่อให้สามารถส่ง Chat ในนามของ user ได้
 *
 * เพิ่มใน wp-config.php:
 * define('MARSX_TEAMS_USERNAME', 'user@domain.com');
 * define('MARSX_TEAMS_PASSWORD', 'password');
 * define('MARSX_TEAMS_CHAT_ID_1TO1', 'chat-id');
 * define('MARSX_TEAMS_CHAT_GROUP_ID', 'group-chat-id');
 */

/**
 * Get Microsoft Graph Access Token using ROPC flow (for Teams Chat)
 * Uses username/password to get delegated token
 * @return string|WP_Error Access token or error
 */
function marsx_get_teams_access_token() {
    // Check for cached token
    $cached_token = get_transient('marsx_teams_access_token');
    if ($cached_token) {
        return $cached_token;
    }

    // Check required constants
    if (!defined('MARSX_TEAMS_USERNAME') || !defined('MARSX_TEAMS_PASSWORD')) {
        return new WP_Error('config_error', 'Teams username/password not configured');
    }

    $token_url = 'https://login.microsoftonline.com/' . MARSX_GRAPH_TENANT_ID . '/oauth2/v2.0/token';

    $response = wp_remote_post($token_url, array(
        'timeout' => 30,
        'body' => array(
            'client_id' => MARSX_GRAPH_CLIENT_ID,
            'client_secret' => MARSX_GRAPH_CLIENT_SECRET,
            'scope' => 'https://graph.microsoft.com/Chat.ReadWrite https://graph.microsoft.com/User.Read',
            'grant_type' => 'password',
            'username' => MARSX_TEAMS_USERNAME,
            'password' => MARSX_TEAMS_PASSWORD,
        ),
    ));

    if (is_wp_error($response)) {
        error_log('MarsX Teams Token Error: ' . $response->get_error_message());
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['access_token'])) {
        // Cache token for 50 minutes (tokens expire in 60 minutes)
        set_transient('marsx_teams_access_token', $body['access_token'], 50 * 60);
        return $body['access_token'];
    }

    $error_msg = isset($body['error_description']) ? $body['error_description'] : 'Unknown error';
    error_log('MarsX Teams Token Error: ' . $error_msg);
    return new WP_Error('token_error', $error_msg);
}

/**
 * Create or get existing 1:1 Chat with a user by email
 * Similar to C# implementation using ChatType.OneOnOne
 *
 * @param string $target_email The email of user to chat with
 * @return string|WP_Error Chat ID or error
 */
function marsx_create_or_get_1to1_chat($target_email) {
    // Check if MARSX_TEAMS_USERNAME is defined
    if (!defined('MARSX_TEAMS_USERNAME') || empty(MARSX_TEAMS_USERNAME)) {
        return new WP_Error('config_error', 'MARSX_TEAMS_USERNAME not configured in wp-config.php');
    }

    // Get access token using ROPC flow
    $access_token = marsx_get_teams_access_token();
    if (is_wp_error($access_token)) {
        return $access_token;
    }

    // The sender is MARSX_TEAMS_USERNAME (the authenticated user)
    $sender_email = MARSX_TEAMS_USERNAME;

    // Graph API endpoint for creating chats
    $endpoint = "https://graph.microsoft.com/v1.0/chats";

    // Build the payload - same structure as C# code
    $payload = array(
        'chatType' => 'oneOnOne',
        'members' => array(
            array(
                '@odata.type' => '#microsoft.graph.aadUserConversationMember',
                'roles' => array('owner'),
                'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('{$sender_email}')"
            ),
            array(
                '@odata.type' => '#microsoft.graph.aadUserConversationMember',
                'roles' => array('owner'),
                'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('{$target_email}')"
            )
        )
    );

    // Send the request
    $response = wp_remote_post($endpoint, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($payload),
        'timeout' => 30
    ));

    if (is_wp_error($response)) {
        error_log('MarsX Teams: Failed to create chat - ' . $response->get_error_message());
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // 201 = Created new chat, 200 = Returned existing chat
    if ($response_code === 201 || $response_code === 200) {
        if (isset($response_body['id'])) {
            error_log('MarsX Teams: Got chat ID for ' . $target_email . ': ' . $response_body['id']);
            return $response_body['id'];
        }
    }

    $error_msg = isset($response_body['error']['message']) ? $response_body['error']['message'] : 'Unknown error';
    error_log('MarsX Teams: Failed to create chat (' . $response_code . ') - ' . $error_msg);
    return new WP_Error('teams_chat_error', $error_msg);
}

/**
 * Send 1:1 message to a user by email (creates chat if needed)
 * This is similar to the C# implementation
 *
 * @param string $target_email The email of user to send message to
 * @param string $message The message content (HTML supported)
 * @return array|WP_Error Response or error
 */
function marsx_send_teams_message_to_user($target_email, $message) {
    // Create or get existing 1:1 chat
    $chat_id = marsx_create_or_get_1to1_chat($target_email);
    if (is_wp_error($chat_id)) {
        return $chat_id;
    }

    // Send the message to this chat
    return marsx_send_teams_chat_message($chat_id, $message);
}

/**
 * Send message to Microsoft Teams Chat using Graph API
 * Supports both 1:1 Chat and Group Chat
 *
 * @param string $chat_id The Chat ID (1:1 or Group)
 * @param string $message The message content (HTML supported)
 * @return array|WP_Error Response or error
 */
function marsx_send_teams_chat_message($chat_id, $message) {
    // Get access token using ROPC flow
    $access_token = marsx_get_teams_access_token();
    if (is_wp_error($access_token)) {
        error_log('MarsX Teams: Failed to get access token - ' . $access_token->get_error_message());
        return $access_token;
    }

    // Graph API endpoint for sending chat messages
    $endpoint = "https://graph.microsoft.com/v1.0/chats/{$chat_id}/messages";

    // Build the message payload
    $payload = array(
        'body' => array(
            'contentType' => 'html',
            'content' => $message
        )
    );

    // Send the request
    $response = wp_remote_post($endpoint, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($payload),
        'timeout' => 30
    ));

    if (is_wp_error($response)) {
        error_log('MarsX Teams: Failed to send message - ' . $response->get_error_message());
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if ($response_code !== 201) {
        $error_msg = isset($response_body['error']['message']) ? $response_body['error']['message'] : 'Unknown error';
        error_log('MarsX Teams: API error (' . $response_code . ') - ' . $error_msg);
        return new WP_Error('teams_api_error', $error_msg);
    }

    return $response_body;
}

/**
 * Format and send order notification to Teams Chat
 * ส่งโดยใช้ Email (สร้าง 1:1 Chat อัตโนมัติ)
 *
 * @param int $order_id WooCommerce Order ID
 * @return void
 */
function marsx_notify_order_to_teams($order_id) {
    // Get the order
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    // Prevent duplicate notifications
    if ($order->get_meta('_marsx_teams_notified')) {
        return;
    }

    // Get recipient emails from wp-config (comma-separated for multiple recipients)
    $notify_emails = defined('MARSX_TEAMS_NOTIFY_EMAILS') ? MARSX_TEAMS_NOTIFY_EMAILS : '';

    // If no emails configured, skip
    if (empty($notify_emails)) {
        error_log('MarsX Teams: No notify emails configured (MARSX_TEAMS_NOTIFY_EMAILS)');
        return;
    }

    // Order details
    $order_number = $order->get_order_number();
    $order_date = $order->get_date_created()->date_i18n('d/m/Y H:i');
    $order_total = $order->get_formatted_order_total();
    $payment_method = $order->get_payment_method_title();

    // Customer details
    $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    $customer_email = $order->get_billing_email();
    $customer_phone = $order->get_billing_phone();

    // Shipping address
    $shipping_address = $order->get_formatted_shipping_address();
    if (empty($shipping_address)) {
        $shipping_address = $order->get_formatted_billing_address();
    }
    $shipping_address = str_replace('<br/>', ', ', $shipping_address);

    // Order items
    $items_html = '';
    foreach ($order->get_items() as $item) {
        $product_name = $item->get_name();
        $quantity = $item->get_quantity();
        $total = wc_price($item->get_total());
        $items_html .= "<li>{$product_name} x {$quantity} - {$total}</li>";
    }

    // Build the message HTML
    $message = "
    <h2>🛒 คำสั่งซื้อใหม่ #{$order_number}</h2>
    <hr>
    <p><strong>📅 วันที่:</strong> {$order_date}</p>
    <p><strong>👤 ลูกค้า:</strong> {$customer_name}</p>
    <p><strong>📧 อีเมล:</strong> {$customer_email}</p>
    <p><strong>📱 โทรศัพท์:</strong> {$customer_phone}</p>
    <hr>
    <p><strong>📦 สินค้า:</strong></p>
    <ul>{$items_html}</ul>
    <hr>
    <p><strong>📍 ที่อยู่จัดส่ง:</strong><br>{$shipping_address}</p>
    <p><strong>💳 ชำระเงินโดย:</strong> {$payment_method}</p>
    <hr>
    <h3>💰 ยอดรวม: {$order_total}</h3>
    ";

    // Parse emails (comma-separated)
    $emails = array_map('trim', explode(',', $notify_emails));
    $success_count = 0;

    // Send to each recipient by email (creates 1:1 chat automatically)
    foreach ($emails as $target_email) {
        if (empty($target_email) || !is_email($target_email)) {
            continue;
        }

        $result = marsx_send_teams_message_to_user($target_email, $message);
        if (is_wp_error($result)) {
            error_log('MarsX Teams: Failed to send to ' . $target_email . ' - ' . $result->get_error_message());
        } else {
            error_log('MarsX Teams: Order #' . $order_number . ' notification sent to ' . $target_email);
            $success_count++;
        }
    }

    // Mark as notified if at least one succeeded
    if ($success_count > 0) {
        $order->update_meta_data('_marsx_teams_notified', current_time('mysql'));
        $order->save();
    }
}

// Hook into WooCommerce order completion
add_action('woocommerce_thankyou', 'marsx_notify_order_to_teams', 10, 1);
add_action('woocommerce_order_status_processing', 'marsx_notify_order_to_teams', 10, 1);
add_action('woocommerce_order_status_completed', 'marsx_notify_order_to_teams', 10, 1);

/**
 * =========================================
 * WP-Admin Custom Styling
 * =========================================
 */
require_once get_stylesheet_directory() . '/inc/admin-custom.php';

/**
 * =========================================
 * WP-Admin PJAX Navigation
 * =========================================
 */
// require_once get_stylesheet_directory() . '/inc/admin-pjax.php';
