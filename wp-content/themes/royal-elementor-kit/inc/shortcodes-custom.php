<?php
/**
 * MarsX Custom Shortcodes
 *
 * Shortcode: [marsx_user_menu]
 * แสดง Avatar + Email พร้อม Dropdown Menu
 */

// ป้องกันการเข้าถึงโดยตรง
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper function: ตรวจสอบว่าอยู่ในหน้าภาษาอังกฤษหรือไม่
 */
function marsx_is_english_page() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    return (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);
}

/**
 * โหลด CSS สำหรับ shortcodes ผ่าน wp_head (โหลดทุกหน้า)
 */
function marsx_shortcodes_enqueue_styles() {
    ?>
    <style id="marsx-shortcodes-css">
    /* MarsX User Menu Styles */
    .marsx-user-menu,
    .elementor-widget-container .marsx-user-menu,
    .elementor-shortcode .marsx-user-menu {
        position: relative !important;
        display: inline-block !important;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }

    .marsx-user-menu *,
    .marsx-user-menu *::before,
    .marsx-user-menu *::after {
        box-sizing: border-box !important;
    }

    .marsx-um-trigger {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 5px !important;
        background: transparent !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
    }

    .marsx-um-trigger:hover {
        opacity: 0.8 !important;
    }

    .marsx-um-avatar,
    .marsx-um-trigger img,
    .marsx-um-trigger .avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
    }

    .marsx-um-info {
        display: flex !important;
        flex-direction: column !important;
        line-height: 1.3 !important;
    }

    .marsx-um-name {
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        color: #1a1a1a !important;
    }

    .marsx-um-email {
        font-size: 0.8rem !important;
        color: #888 !important;
        max-width: 150px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .marsx-um-fullname {
        font-size: 0.75rem !important;
        color: #666 !important;
        max-width: 150px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .marsx-um-arrow {
        color: #888 !important;
        transition: transform 0.2s !important;
    }

    .marsx-user-menu:hover .marsx-um-arrow {
        transform: rotate(180deg) !important;
    }

    /* Dropdown - Critical Styles */
    .marsx-um-dropdown,
    .elementor-widget-container .marsx-um-dropdown,
    .elementor-shortcode .marsx-um-dropdown,
    div.marsx-um-dropdown {
        position: absolute !important;
        top: calc(100% + 8px) !important;
        right: 0 !important;
        left: auto !important;
        background: white !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
        min-width: 260px !important;
        width: auto !important;
        max-width: 300px !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(-10px) !important;
        transition: all 0.2s ease !important;
        z-index: 999999 !important;
        overflow: visible !important;
        display: block !important;
        flex-wrap: nowrap !important;
        flex-direction: column !important;
        float: none !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
        pointer-events: none !important;
    }

    /* Bridge element to prevent gap hover issue */
    .marsx-um-dropdown::before {
        content: '' !important;
        position: absolute !important;
        top: -12px !important;
        left: 0 !important;
        right: 0 !important;
        height: 12px !important;
        background: transparent !important;
    }

    .marsx-user-menu:hover .marsx-um-dropdown,
    .elementor-widget-container .marsx-user-menu:hover .marsx-um-dropdown,
    .elementor-shortcode .marsx-user-menu:hover .marsx-um-dropdown {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    .marsx-um-dropdown-header {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 20px !important;
        background: linear-gradient(135deg, #fff9f0 0%, #fff 100%) !important;
    }

    .marsx-um-dropdown-header img {
        width: 50px !important;
        height: 50px !important;
        border-radius: 50% !important;
        border: 2px solid var(--e-global-color-primary) !important;
    }

    .marsx-um-dropdown-name {
        font-weight: 600 !important;
        font-size: 1rem !important;
        color: #1a1a1a !important;
    }

    .marsx-um-dropdown-email {
        font-size: 0.85rem !important;
        color: #888 !important;
    }

    .marsx-um-dropdown-divider {
        height: 1px !important;
        background: #f0f0f0 !important;
        width: 100% !important;
        display: block !important;
    }

    .marsx-um-dropdown-item,
    .marsx-um-dropdown .marsx-um-dropdown-item,
    a.marsx-um-dropdown-item {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 14px 20px !important;
        color: #333 !important;
        text-decoration: none !important;
        transition: all 0.2s !important;
        font-size: 0.95rem !important;
        width: 100% !important;
        white-space: nowrap !important;
        float: none !important;
        position: relative !important;
        background: white !important;
    }

    .marsx-um-dropdown-item:hover {
        background: #f8f9fa !important;
        color: var(--e-global-color-primary) !important;
    }

    .marsx-um-dropdown-item svg {
        color: #888 !important;
        transition: color 0.2s !important;
        flex-shrink: 0 !important;
        width: 18px !important;
        height: 18px !important;
    }

    .marsx-um-dropdown-item:hover svg {
        color: var(--e-global-color-primary) !important;
    }

    .marsx-um-logout,
    a.marsx-um-logout {
        color: #e74c3c !important;
    }

    .marsx-um-logout:hover {
        background: #fff5f5 !important;
        color: #c0392b !important;
    }

    .marsx-um-logout svg {
        color: #e74c3c !important;
    }

    /* Cart Icon Styles */
    .marsx-cart-icon {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        color: #333 !important;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
        transition: all 0.2s !important;
    }

    .marsx-cart-icon:hover {
        color: #333 !important;
    }

    .marsx-cart-icon-wrapper {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 40px !important;
        height: 40px !important;
        background: var(--e-global-color-primary) !important;
        border-radius: 50% !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
        transition: all 0.2s !important;
    }

    .marsx-cart-icon:hover .marsx-cart-icon-wrapper {
        transform: scale(1.05) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
    }

    .marsx-cart-svg {
        color: white !important;
        transition: transform 0.2s !important;
    }

    .marsx-cart-icon:hover .marsx-cart-svg {
        transform: scale(1.1) !important;
    }

    .marsx-cart-count {
        position: absolute !important;
        top: -2px !important;
        right: -2px !important;
        background: white !important;
        color: var(--e-global-color-primary) !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        min-width: 16px !important;
        height: 16px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 3px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }

    .marsx-cart-total {
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        color: #333 !important;
    }

    .marsx-cart-icon:hover .marsx-cart-total {
        color: var(--e-global-color-primary) !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .marsx-um-info {
            display: none !important;
        }

        .marsx-um-trigger {
            padding: 6px !important;
            border-radius: 50% !important;
        }

        .marsx-um-arrow {
            display: none !important;
        }

        .marsx-um-dropdown {
            right: -10px !important;
            min-width: 240px !important;
        }

        .marsx-cart-total {
            display: none !important;
        }
    }

    /* ===== Add to Cart Bar (Inline) ===== */
    .marsx-sticky-cart {
        background: #fff !important;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
        border: 1px solid #eee !important;
        border-radius: 12px !important;
        padding: 12px 20px !important;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
        margin: 20px 0 !important;
    }

    .marsx-sticky-cart-inner {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        max-width: 1200px !important;
        margin: 0 auto !important;
        gap: 20px !important;
    }

    .marsx-sticky-cart-product {
        display: flex !important;
        align-items: center !important;
        gap: 15px !important;
        flex: 1 !important;
        min-width: 0 !important;
    }

    .marsx-sticky-cart-image {
        width: 50px !important;
        height: 50px !important;
        border-radius: 8px !important;
        object-fit: cover !important;
        border: 1px solid #eee !important;
        flex-shrink: 0 !important;
    }

    .marsx-sticky-cart-title {
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        color: #1a1a1a !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 300px !important;
    }

    .marsx-sticky-cart-actions {
        display: flex !important;
        align-items: center !important;
        gap: 20px !important;
        flex-shrink: 0 !important;
    }

    .marsx-sticky-cart-qty {
        display: flex !important;
        align-items: center !important;
        gap: 0 !important;
        border: 1px solid #ddd !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }

    .marsx-sticky-cart-qty-btn {
        width: 36px !important;
        height: 36px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #f8f9fa !important;
        border: none !important;
        cursor: pointer !important;
        font-size: 1.2rem !important;
        color: #333 !important;
        transition: all 0.2s !important;
    }

    .marsx-sticky-cart-qty-btn:hover {
        background: #e9ecef !important;
        color: var(--e-global-color-primary, #ff6b35) !important;
    }

    .marsx-sticky-cart-qty-input {
        width: 50px !important;
        height: 36px !important;
        text-align: center !important;
        border: none !important;
        border-left: 1px solid #ddd !important;
        border-right: 1px solid #ddd !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        color: #333 !important;
        -moz-appearance: textfield !important;
    }

    .marsx-sticky-cart-qty-input::-webkit-outer-spin-button,
    .marsx-sticky-cart-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }

    .marsx-sticky-cart-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        padding: 12px 32px !important;
        background: var(--e-global-color-primary, #ff6b35) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    .marsx-sticky-cart-btn:hover {
        filter: brightness(0.9) !important;
        transform: translateY(-1px) !important;
    }

    .marsx-sticky-cart-btn:disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        transform: none !important;
    }

    .marsx-sticky-cart-btn svg {
        width: 18px !important;
        height: 18px !important;
    }

    .marsx-sticky-cart-btn.loading::after {
        content: '' !important;
        width: 16px !important;
        height: 16px !important;
        border: 2px solid #fff !important;
        border-top-color: transparent !important;
        border-radius: 50% !important;
        animation: marsx-spin 0.8s linear infinite !important;
        margin-left: 8px !important;
    }

    @keyframes marsx-spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive for Sticky Cart */
    @media (max-width: 768px) {
        .marsx-sticky-cart {
            padding: 10px 15px !important;
        }

        .marsx-sticky-cart-image {
            width: 40px !important;
            height: 40px !important;
        }

        .marsx-sticky-cart-title {
            display: none !important;
        }

        .marsx-sticky-cart-qty-btn {
            width: 32px !important;
            height: 32px !important;
        }

        .marsx-sticky-cart-qty-input {
            width: 40px !important;
            height: 32px !important;
        }

        .marsx-sticky-cart-btn {
            padding: 10px 20px !important;
            font-size: 0.85rem !important;
        }
    }

    @media (max-width: 480px) {
        .marsx-sticky-cart-product {
            flex: 0 !important;
        }

        .marsx-sticky-cart-actions {
            flex: 1 !important;
            justify-content: flex-end !important;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'marsx_shortcodes_enqueue_styles', 999);

/**
 * Shortcode: [marsx_user_menu]
 *
 * Parameters:
 * - show_email: yes/no (default: yes)
 * - show_name: yes/no (default: no)
 * - show_fullname: yes/no (default: yes) - แสดงชื่อ-นามสกุลใต้ email
 * - avatar_size: ขนาด avatar (default: 40)
 * - login_url: URL หน้า login (default: /login/)
 * - login_text: ข้อความปุ่ม login (default: เข้าสู่ระบบ)
 * - max_width: ความกว้างสูงสุดของ email/name (default: 150px)
 *
 * ตัวอย่างการใช้งาน:
 * [marsx_user_menu]
 * [marsx_user_menu show_email="no" show_name="yes"]
 * [marsx_user_menu avatar_size="50" login_url="/en/login/" login_text="Login"]
 * [marsx_user_menu max_width="200px"]
 */
function marsx_user_menu_shortcode($atts) {
    // Default attributes
    $atts = shortcode_atts(array(
        'show_email'    => 'yes',
        'show_name'     => 'no',
        'show_fullname' => 'yes',
        'avatar_size'   => 40,
        'login_url'     => '/login/',
        'login_text'    => 'เข้าสู่ระบบ',
        'max_width'     => '150px',
    ), $atts, 'marsx_user_menu');

    // Start output buffering
    ob_start();

    // เพิ่ม CSS (จะโหลดครั้งเดียว)
    marsx_user_menu_styles();

    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $avatar = get_avatar($user->ID, $atts['avatar_size'], '', $user->display_name, array('class' => 'marsx-um-avatar'));
        $email = $user->user_email;
        $name = $user->display_name;
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $fullname = trim($first_name . ' ' . $last_name);
        $max_width = esc_attr($atts['max_width']);

        // ตรวจสอบภาษาจาก URL
        $is_english = marsx_is_english_page();

        // กำหนด URL ตามภาษา
        $account_url = $is_english ? home_url('/en/my-account/') : home_url('/my-account/');
        $logout_url = $is_english ? wp_logout_url(home_url('/en/login/')) : wp_logout_url(home_url('/login/'));

        // กำหนดข้อความตามภาษา
        $text_my_account = $is_english ? 'My Account' : 'บัญชีของฉัน';
        $text_orders = $is_english ? 'Orders' : 'คำสั่งซื้อ';
        $text_settings = $is_english ? 'Settings' : 'ตั้งค่า';
        $text_logout = $is_english ? 'Logout' : 'ออกจากระบบ';
        ?>
        <div class="marsx-user-menu">
            <div class="marsx-um-trigger">
                <?php echo $avatar; ?>
                <div class="marsx-um-info">
                    <?php if ($atts['show_name'] === 'yes') : ?>
                        <span class="marsx-um-name"><?php echo esc_html($name); ?></span>
                    <?php endif; ?>
                    <?php if ($atts['show_email'] === 'yes') : ?>
                        <span class="marsx-um-email" style="max-width: <?php echo $max_width; ?>;"><?php echo esc_html($email); ?></span>
                    <?php endif; ?>
                    <?php if ($atts['show_fullname'] === 'yes' && !empty($fullname)) : ?>
                        <span class="marsx-um-fullname" style="max-width: <?php echo $max_width; ?>;"><?php echo esc_html($fullname); ?></span>
                    <?php endif; ?>
                </div>
                <svg class="marsx-um-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
            <div class="marsx-um-dropdown">
                <div class="marsx-um-dropdown-header">
                    <?php echo get_avatar($user->ID, 50, '', $user->display_name); ?>
                    <div>
                        <div class="marsx-um-dropdown-name"><?php echo esc_html($name); ?></div>
                        <div class="marsx-um-dropdown-email"><?php echo esc_html($email); ?></div>
                    </div>
                </div>
                <div class="marsx-um-dropdown-divider"></div>
                <a href="<?php echo esc_url($account_url); ?>" class="marsx-um-dropdown-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <?php echo esc_html($text_my_account); ?>
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=orders'); ?>" class="marsx-um-dropdown-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <?php echo esc_html($text_orders); ?>
                </a>
                <a href="<?php echo $is_english ? home_url('/en/track-shipping/') : home_url('/track-shipping/'); ?>" class="marsx-um-dropdown-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <?php echo $is_english ? 'Track Shipping' : 'ติดตามพัสดุ'; ?>
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=account-details'); ?>" class="marsx-um-dropdown-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <?php echo esc_html($text_settings); ?>
                </a>
                <div class="marsx-um-dropdown-divider"></div>
                <a href="<?php echo esc_url($logout_url); ?>" class="marsx-um-dropdown-item marsx-um-logout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <?php echo esc_html($text_logout); ?>
                </a>
            </div>
        </div>
        <?php
    }
    // ไม่แสดงอะไรถ้าไม่ได้ login

    return ob_get_clean();
}
add_shortcode('marsx_user_menu', 'marsx_user_menu_shortcode');


/**
 * CSS Styles for User Menu
 */
function marsx_user_menu_styles() {
    static $styles_loaded = false;
    if ($styles_loaded) return;
    $styles_loaded = true;
    ?>
    <style>
    /* MarsX User Menu Styles */
    .marsx-user-menu,
    .elementor-widget-container .marsx-user-menu,
    .elementor-shortcode .marsx-user-menu {
        position: relative !important;
        display: inline-block !important;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }

    /* Reset Elementor flex/grid on dropdown */
    .marsx-user-menu *,
    .marsx-user-menu *::before,
    .marsx-user-menu *::after {
        box-sizing: border-box;
    }

    .marsx-um-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .marsx-um-trigger:hover {
        opacity: 0.8;
    }

    .marsx-um-avatar,
    .marsx-um-trigger img,
    .marsx-um-trigger .avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        object-fit: cover;
    }

    .marsx-um-info {
        display: flex;
        flex-direction: column;
        line-height: 1.3;
    }

    .marsx-um-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1a1a1a;
    }

    .marsx-um-email {
        font-size: 0.8rem;
        color: #888;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .marsx-um-fullname {
        font-size: 0.75rem;
        color: #666;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .marsx-um-arrow {
        color: #888;
        transition: transform 0.2s;
    }

    .marsx-user-menu:hover .marsx-um-arrow {
        transform: rotate(180deg);
    }

    /* Dropdown */
    .marsx-um-dropdown,
    .elementor-widget-container .marsx-um-dropdown,
    .elementor-shortcode .marsx-um-dropdown {
        position: absolute !important;
        top: calc(100% + 8px) !important;
        right: 0 !important;
        left: auto !important;
        background: white !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
        min-width: 260px !important;
        width: auto !important;
        max-width: 300px !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(-10px) !important;
        transition: all 0.2s ease !important;
        z-index: 99999 !important;
        overflow: visible !important;
        display: block !important;
        flex-wrap: nowrap !important;
        flex-direction: column !important;
        float: none !important;
        margin: 0 !important;
        padding: 0 !important;
        pointer-events: none !important;
    }

    /* Bridge element to prevent gap hover issue */
    .marsx-um-dropdown::before {
        content: '' !important;
        position: absolute !important;
        top: -12px !important;
        left: 0 !important;
        right: 0 !important;
        height: 12px !important;
        background: transparent !important;
    }

    .marsx-user-menu:hover .marsx-um-dropdown,
    .elementor-widget-container .marsx-user-menu:hover .marsx-um-dropdown {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    .marsx-um-dropdown-item,
    .marsx-um-dropdown .marsx-um-dropdown-item {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 14px 20px !important;
        color: #333 !important;
        text-decoration: none !important;
        transition: all 0.2s !important;
        font-size: 0.95rem !important;
        width: 100% !important;
        white-space: nowrap !important;
    }

    .marsx-um-dropdown-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px;
        background: linear-gradient(135deg, #fff9f0 0%, #fff 100%);
    }

    .marsx-um-dropdown-header img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--e-global-color-primary);
    }

    .marsx-um-dropdown-name {
        font-weight: 600;
        font-size: 1rem;
        color: #1a1a1a;
    }

    .marsx-um-dropdown-email {
        font-size: 0.85rem;
        color: #888;
    }

    .marsx-um-dropdown-divider {
        height: 1px;
        background: #f0f0f0;
    }

    .marsx-um-dropdown-item:hover {
        background: #f8f9fa !important;
        color: var(--e-global-color-primary) !important;
    }

    .marsx-um-dropdown-item svg {
        color: #888 !important;
        transition: color 0.2s !important;
        flex-shrink: 0 !important;
    }

    .marsx-um-dropdown-item:hover svg {
        color: var(--e-global-color-primary) !important;
    }

    .marsx-um-logout {
        color: #e74c3c;
    }

    .marsx-um-logout:hover {
        background: #fff5f5;
        color: #c0392b;
    }

    .marsx-um-logout svg {
        color: #e74c3c;
    }

    /* Login Button (เมื่อไม่ได้ login) */
    .marsx-um-login-btn {
        display: inline-block;
        padding: 10px 28px;
        background: var(--e-global-color-primary);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .marsx-um-login-btn:hover {
        background: var(--e-global-color-primary);
        filter: brightness(0.9);
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .marsx-um-info {
            display: none;
        }

        .marsx-um-trigger {
            padding: 6px;
            border-radius: 50%;
        }

        .marsx-um-arrow {
            display: none;
        }

        .marsx-um-dropdown {
            right: -10px;
            min-width: 240px;
        }
    }
    </style>
    <?php
}

/**
 * Shortcode: [marsx_sticky_cart]
 *
 * Sticky Add to Cart Bar สำหรับหน้า Single Product
 *
 * Parameters:
 * - show_image: yes/no (default: yes) - แสดงรูปสินค้า
 * - show_title: yes/no (default: yes) - แสดงชื่อสินค้า
 * - show_qty: yes/no (default: yes) - แสดงตัวเลือกจำนวน
 * - button_text: ข้อความปุ่ม (default: ADD TO CART)
 * - button_text_th: ข้อความปุ่มภาษาไทย (default: เพิ่มลงตะกร้า)
 * - scroll_offset: ระยะ scroll ก่อนแสดง bar (default: 300)
 *
 * ตัวอย่างการใช้งาน:
 * [marsx_sticky_cart]
 * [marsx_sticky_cart show_image="no" button_text="BUY NOW"]
 * [marsx_sticky_cart scroll_offset="500"]
 */
function marsx_sticky_cart_shortcode($atts) {
    // ตรวจสอบว่า WooCommerce active
    if (!class_exists('WooCommerce')) {
        return '';
    }

    global $product, $post;

    // พยายามดึง product จากหลายแหล่ง
    if (!$product || !is_a($product, 'WC_Product')) {
        if ($post && $post->post_type === 'product') {
            $product = wc_get_product($post->ID);
        }
    }

    // ถ้ายังไม่มี product ให้ return ว่าง (แต่ไม่ block ถ้าอยู่ใน template)
    if (!$product) {
        // สำหรับ debug - แสดง placeholder ใน Elementor editor
        if (defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return '<div style="padding:20px;background:#f5f5f5;text-align:center;border-radius:8px;">📦 Sticky Cart Bar จะแสดงในหน้า Single Product</div>';
        }
        return '';
    }

    // Default attributes
    $atts = shortcode_atts(array(
        'show_image'     => 'yes',
        'show_title'     => 'yes',
        'show_qty'       => 'yes',
        'button_text'    => 'ADD TO CART',
        'button_text_th' => 'เพิ่มลงตะกร้า',
        'scroll_offset'  => 300,
    ), $atts, 'marsx_sticky_cart');

    // ตรวจสอบภาษา
    $is_english = marsx_is_english_page();
    $button_text = $is_english ? $atts['button_text'] : $atts['button_text_th'];

    // ข้อมูลสินค้า
    $product_id = $product->get_id();
    $product_title = $product->get_name();
    $product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'thumbnail');
    $product_image_url = $product_image ? $product_image[0] : wc_placeholder_img_src('thumbnail');

    // Start output buffering
    ob_start();
    ?>
    <div id="marsx-sticky-cart" class="marsx-sticky-cart">
        <div class="marsx-sticky-cart-inner">
            <div class="marsx-sticky-cart-product">
                <?php if ($atts['show_image'] === 'yes') : ?>
                    <img src="<?php echo esc_url($product_image_url); ?>" alt="<?php echo esc_attr($product_title); ?>" class="marsx-sticky-cart-image">
                <?php endif; ?>
                <?php if ($atts['show_title'] === 'yes') : ?>
                    <span class="marsx-sticky-cart-title"><?php echo esc_html($product_title); ?></span>
                <?php endif; ?>
            </div>

            <div class="marsx-sticky-cart-actions">
                <?php if ($atts['show_qty'] === 'yes') : ?>
                    <div class="marsx-sticky-cart-qty">
                        <button type="button" class="marsx-sticky-cart-qty-btn" data-action="minus">−</button>
                        <input type="number" class="marsx-sticky-cart-qty-input" value="1" min="1" max="<?php echo esc_attr($product->get_stock_quantity() ?: 99); ?>">
                        <button type="button" class="marsx-sticky-cart-qty-btn" data-action="plus">+</button>
                    </div>
                <?php endif; ?>

                <button type="button" class="marsx-sticky-cart-btn" data-product-id="<?php echo esc_attr($product_id); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <?php echo esc_html($button_text); ?>
                </button>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const stickyCart = document.getElementById('marsx-sticky-cart');
        if (!stickyCart) return;

        const qtyInput = stickyCart.querySelector('.marsx-sticky-cart-qty-input');
        const minusBtn = stickyCart.querySelector('[data-action="minus"]');
        const plusBtn = stickyCart.querySelector('[data-action="plus"]');
        const addToCartBtn = stickyCart.querySelector('.marsx-sticky-cart-btn');
        const productId = addToCartBtn?.dataset.productId;

        // Quantity buttons
        if (minusBtn && qtyInput) {
            minusBtn.addEventListener('click', function() {
                let val = parseInt(qtyInput.value) || 1;
                if (val > 1) {
                    qtyInput.value = val - 1;
                }
            });
        }

        if (plusBtn && qtyInput) {
            plusBtn.addEventListener('click', function() {
                let val = parseInt(qtyInput.value) || 1;
                let max = parseInt(qtyInput.max) || 99;
                if (val < max) {
                    qtyInput.value = val + 1;
                }
            });
        }

        // Add to Cart AJAX
        if (addToCartBtn && productId) {
            addToCartBtn.addEventListener('click', function() {
                if (addToCartBtn.disabled) return;

                const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

                addToCartBtn.disabled = true;
                addToCartBtn.classList.add('loading');

                // WooCommerce AJAX add to cart
                const formData = new FormData();
                formData.append('action', 'marsx_sticky_cart_add');
                formData.append('product_id', productId);
                formData.append('quantity', qty);

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // ถ้า AJAX handler ไม่รองรับ ใช้วิธี redirect
                        window.location.href = '?add-to-cart=' + productId + '&quantity=' + qty;
                    } else {
                        // Update cart fragments
                        if (data.fragments) {
                            jQuery.each(data.fragments, function(key, value) {
                                jQuery(key).replaceWith(value);
                            });
                        }

                        // Trigger WooCommerce event
                        jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, jQuery(addToCartBtn)]);

                        // Reset button
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.remove('loading');

                        // Show success feedback
                        const originalText = addToCartBtn.innerHTML;
                        addToCartBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> <?php echo $is_english ? "Added!" : "เพิ่มแล้ว!"; ?>';
                        setTimeout(() => {
                            addToCartBtn.innerHTML = originalText;
                        }, 2000);
                    }
                })
                .catch(error => {
                    // Fallback: redirect with add-to-cart parameter
                    window.location.href = '?add-to-cart=' + productId + '&quantity=' + qty;
                });
            });
        }
    })();
    </script>
    <?php

    return ob_get_clean();
}
add_shortcode('marsx_sticky_cart', 'marsx_sticky_cart_shortcode');

/**
 * AJAX handler for WooCommerce add to cart (Sticky Cart)
 * Note: ใช้ action name ที่ต่างจาก woocommerce-custom.php
 */
function marsx_sticky_cart_ajax_add_to_cart() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce') || !function_exists('WC')) {
        wp_send_json(array('error' => true, 'message' => 'WooCommerce not active'));
        wp_die();
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) && !empty($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : 1;
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $variation = array();

    if (!$product_id) {
        wp_send_json(array('error' => true, 'message' => 'Invalid product'));
        wp_die();
    }

    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && 'publish' === $product_status && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation)) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX::get_refreshed_fragments();
    } else {
        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id),
        );

        wp_send_json($data);
    }

    wp_die();
}
add_action('wp_ajax_marsx_sticky_cart_add', 'marsx_sticky_cart_ajax_add_to_cart');
add_action('wp_ajax_nopriv_marsx_sticky_cart_add', 'marsx_sticky_cart_ajax_add_to_cart');
