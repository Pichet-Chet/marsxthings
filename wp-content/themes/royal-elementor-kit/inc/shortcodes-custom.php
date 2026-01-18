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
 * Shortcode: [marsx_user_menu]
 *
 * Parameters:
 * - show_email: yes/no (default: yes)
 * - show_name: yes/no (default: no)
 * - show_fullname: yes/no (default: yes) - แสดงชื่อ-นามสกุลใต้ email
 * - avatar_size: ขนาด avatar (default: 40)
 * - login_url: URL หน้า login (default: /login/)
 * - login_text: ข้อความปุ่ม login (default: เข้าสู่ระบบ)
 *
 * ตัวอย่างการใช้งาน:
 * [marsx_user_menu]
 * [marsx_user_menu show_email="no" show_name="yes"]
 * [marsx_user_menu avatar_size="50" login_url="/en/login/" login_text="Login"]
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
        $account_url = home_url('/my-account/');
        $logout_url = wp_logout_url(home_url('/login/'));
        ?>
        <div class="marsx-user-menu">
            <div class="marsx-um-trigger">
                <?php echo $avatar; ?>
                <div class="marsx-um-info">
                    <?php if ($atts['show_name'] === 'yes') : ?>
                        <span class="marsx-um-name"><?php echo esc_html($name); ?></span>
                    <?php endif; ?>
                    <?php if ($atts['show_email'] === 'yes') : ?>
                        <span class="marsx-um-email"><?php echo esc_html($email); ?></span>
                    <?php endif; ?>
                    <?php if ($atts['show_fullname'] === 'yes' && !empty($fullname)) : ?>
                        <span class="marsx-um-fullname"><?php echo esc_html($fullname); ?></span>
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
                    บัญชีของฉัน
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=orders'); ?>" class="marsx-um-dropdown-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    คำสั่งซื้อ
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=account-details'); ?>" class="marsx-um-dropdown-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    ตั้งค่า
                </a>
                <div class="marsx-um-dropdown-divider"></div>
                <a href="<?php echo esc_url($logout_url); ?>" class="marsx-um-dropdown-item marsx-um-logout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    ออกจากระบบ
                </a>
            </div>
        </div>
        <?php
    } else {
        // ไม่ได้ login - แสดงปุ่ม login
        ?>
        <a href="<?php echo esc_url(home_url($atts['login_url'])); ?>" class="marsx-um-login-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <?php echo esc_html($atts['login_text']); ?>
        </a>
        <?php
    }

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
    .marsx-user-menu {
        position: relative;
        display: inline-block;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
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
    .marsx-um-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        min-width: 260px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
        z-index: 9999;
        overflow: hidden;
    }

    .marsx-user-menu:hover .marsx-um-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
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
        border: 2px solid #f39c12;
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

    .marsx-um-dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .marsx-um-dropdown-item:hover {
        background: #f8f9fa;
        color: #f39c12;
    }

    .marsx-um-dropdown-item svg {
        color: #888;
        transition: color 0.2s;
    }

    .marsx-um-dropdown-item:hover svg {
        color: #f39c12;
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
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .marsx-um-login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(243, 156, 18, 0.4);
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
 * Shortcode: [marsx_cart_icon]
 *
 * แสดงไอคอนตะกร้าพร้อมจำนวนสินค้า
 *
 * Parameters:
 * - cart_url: URL หน้าตะกร้า (default: /products/cart/)
 * - icon_size: ขนาดไอคอน (default: 24)
 * - show_count: yes/no แสดงจำนวนหรือไม่ (default: yes)
 * - show_total: yes/no แสดงยอดรวมหรือไม่ (default: no)
 *
 * ตัวอย่างการใช้งาน:
 * [marsx_cart_icon]
 * [marsx_cart_icon cart_url="/en/products/cart/" show_total="yes"]
 * [marsx_cart_icon icon_size="28" show_count="yes"]
 */
function marsx_cart_icon_shortcode($atts) {
    // Default attributes
    $atts = shortcode_atts(array(
        'cart_url'   => '/products/cart/',
        'icon_size'  => 24,
        'show_count' => 'yes',
        'show_total' => 'no',
    ), $atts, 'marsx_cart_icon');

    // Start output buffering
    ob_start();

    // เพิ่ม CSS (จะโหลดครั้งเดียว)
    marsx_cart_icon_styles();

    // ดึงจำนวนสินค้าในตะกร้า
    $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $cart_total = WC()->cart ? WC()->cart->get_cart_total() : '฿0';
    $cart_url = home_url($atts['cart_url']);
    $icon_size = intval($atts['icon_size']);
    ?>
    <a href="<?php echo esc_url($cart_url); ?>" class="marsx-cart-icon">
        <div class="marsx-cart-icon-wrapper">
            <svg class="marsx-cart-svg" width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <?php if ($atts['show_count'] === 'yes' && $cart_count > 0) : ?>
                <span class="marsx-cart-count"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </div>
        <?php if ($atts['show_total'] === 'yes') : ?>
            <span class="marsx-cart-total"><?php echo $cart_total; ?></span>
        <?php endif; ?>
    </a>
    <?php

    return ob_get_clean();
}
add_shortcode('marsx_cart_icon', 'marsx_cart_icon_shortcode');


/**
 * CSS Styles for Cart Icon
 */
function marsx_cart_icon_styles() {
    static $cart_styles_loaded = false;
    if ($cart_styles_loaded) return;
    $cart_styles_loaded = true;
    ?>
    <style>
    /* MarsX Cart Icon Styles */
    .marsx-cart-icon {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #333;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        transition: all 0.2s;
    }

    .marsx-cart-icon:hover {
        color: #f39c12;
    }

    .marsx-cart-icon-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .marsx-cart-svg {
        transition: transform 0.2s;
    }

    .marsx-cart-icon:hover .marsx-cart-svg {
        transform: scale(1.1);
    }

    .marsx-cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
        color: white;
        font-size: 11px;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        box-shadow: 0 2px 6px rgba(243, 156, 18, 0.4);
    }

    .marsx-cart-total {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
    }

    .marsx-cart-icon:hover .marsx-cart-total {
        color: #f39c12;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .marsx-cart-total {
            display: none;
        }
    }
    </style>
    <?php
}


/**
 * AJAX Fragment สำหรับ update จำนวนตะกร้า
 * (ทำให้จำนวนอัพเดทอัตโนมัติเมื่อเพิ่มสินค้า)
 */
function marsx_cart_count_fragments($fragments) {
    $cart_count = WC()->cart->get_cart_contents_count();

    // อัพเดท badge นับจำนวน
    if ($cart_count > 0) {
        $fragments['.marsx-cart-count'] = '<span class="marsx-cart-count">' . $cart_count . '</span>';
    } else {
        $fragments['.marsx-cart-count'] = '';
    }

    // อัพเดทยอดรวม
    $fragments['.marsx-cart-total'] = '<span class="marsx-cart-total">' . WC()->cart->get_cart_total() . '</span>';

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'marsx_cart_count_fragments');
