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
