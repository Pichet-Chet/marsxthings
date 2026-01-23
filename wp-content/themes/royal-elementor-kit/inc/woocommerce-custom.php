<?php
/**
 * =========================================
 * MarsX WooCommerce Custom Functions
 * =========================================
 *
 * ไฟล์นี้รวมฟังก์ชันที่เกี่ยวข้องกับ WooCommerce ทั้งหมด:
 * - Cart Icon Shortcode
 * - Cart Fragments (AJAX update)
 * - Toast Notification สำหรับ Add to Cart
 * - AJAX Add to Cart Handler
 */

// ป้องกันการเข้าถึงโดยตรง
if (!defined('ABSPATH')) {
    exit;
}

// ตรวจสอบว่า WooCommerce active
if (!class_exists('WooCommerce')) {
    return;
}


/**
 * ========================================
 * Cart Icon Shortcode
 * ========================================
 */

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
                <path d="M6 6h15l-1.5 9h-12z"></path>
                <circle cx="9" cy="20" r="1.5"></circle>
                <circle cx="17" cy="20" r="1.5"></circle>
                <path d="M3 3l2.5 2.5"></path>
                <path d="M5.5 5.5L6 6"></path>
                <path d="M1 1l3 3" stroke-width="2.5"></path>
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
        color: #333;
    }

    .marsx-cart-icon-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: var(--e-global-color-primary);
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.2s;
    }

    .marsx-cart-icon:hover .marsx-cart-icon-wrapper {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .marsx-cart-svg {
        color: white;
        transition: transform 0.2s;
    }

    .marsx-cart-icon:hover .marsx-cart-svg {
        transform: scale(1.1);
    }

    .marsx-cart-count {
        position: absolute;
        top: -2px;
        right: -2px;
        background: white;
        color: var(--e-global-color-primary);
        font-size: 10px;
        font-weight: 700;
        min-width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .marsx-cart-total {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
    }

    .marsx-cart-icon:hover .marsx-cart-total {
        color: var(--e-global-color-primary);
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
 * ========================================
 * Cart Fragments (AJAX Update)
 * ========================================
 */

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


/**
 * ========================================
 * Toast Notification สำหรับ Add to Cart
 * ========================================
 */

/**
 * บันทึกข้อมูลสินค้าที่เพิ่มลงตะกร้า (สำหรับ non-AJAX add to cart - fallback)
 */
add_action('woocommerce_add_to_cart', 'marsx_save_added_product_for_toast', 10, 6);
function marsx_save_added_product_for_toast($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    // ไม่บันทึกถ้าเป็น AJAX request
    if (wp_doing_ajax()) {
        return;
    }

    if (!WC()->session) {
        return;
    }

    $product = wc_get_product($product_id);
    if ($product) {
        WC()->session->set('marsx_just_added_product', array(
            'name' => $product->get_name(),
            'id' => $product_id,
            'quantity' => $quantity,
            'time' => time()
        ));
    }
}


/**
 * ========================================
 * AJAX Add to Cart Handler
 * ========================================
 */

// เปิด AJAX Add to Cart สำหรับ Single Product Page
add_theme_support('wc-product-gallery-zoom');
add_theme_support('wc-product-gallery-lightbox');
add_theme_support('wc-product-gallery-slider');

// ป้องกัน redirect หลัง add to cart (ใช้ AJAX แทน)
add_filter('woocommerce_add_to_cart_redirect', function($url) {
    if (wp_doing_ajax()) {
        return false;
    }
    return $url;
});

/**
 * Custom AJAX Handler สำหรับ Add to Cart (Single Product)
 */
add_action('wp_ajax_marsx_add_to_cart', 'marsx_ajax_add_to_cart');
add_action('wp_ajax_nopriv_marsx_add_to_cart', 'marsx_ajax_add_to_cart');

function marsx_ajax_add_to_cart() {
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount(absint($_POST['quantity']));
    $variation_id = !empty($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $variation = array();

    // ดึง variation attributes ถ้ามี
    if ($variation_id > 0) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $variation[$key] = sanitize_text_field($value);
            }
        }
    }

    // เพิ่มสินค้าลงตะกร้า
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation)) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        // ดึง cart fragments
        WC_AJAX::get_refreshed_fragments();
    } else {
        // ดึง WooCommerce notices
        $notices = wc_get_notices('error');
        $error_message = !empty($notices) ? strip_tags($notices[0]['notice']) : 'ไม่สามารถเพิ่มสินค้าได้';
        wc_clear_notices();

        wp_send_json_error(array(
            'error' => true,
            'message' => $error_message
        ));
    }

    wp_die();
}


/**
 * ========================================
 * Toast Notification CSS & JavaScript
 * ========================================
 */

/**
 * เพิ่ม CSS สำหรับ Toast และซ่อน WooCommerce Default Notice
 */
function marsx_woocommerce_toast_styles() {
    ?>
    <style>
    /* ========================================
       ซ่อน WooCommerce Default Notice
       (ใช้ Toast แทน)
       ======================================== */
    .woocommerce-message,
    .woocommerce-store-notice,
    body .woocommerce-message,
    body .woocommerce-info.wc-forward,
    .woocommerce-notices-wrapper .woocommerce-message,
    /* ซ่อนลิงก์ "ดูตะกร้าสินค้า" ข้างปุ่ม add to cart */
    .added_to_cart.wc-forward,
    a.added_to_cart,
    .single_add_to_cart_button + .added_to_cart {
        display: none !important;
    }

    /* ========================================
       MarsX Toast Notification Styles
       ======================================== */
    .marsx-toast-container {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        z-index: 999999 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
        pointer-events: none !important;
    }

    .marsx-toast {
        background: white !important;
        padding: 16px 20px !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        animation: marsx-toast-in 0.3s ease !important;
        max-width: 380px !important;
        pointer-events: auto !important;
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }

    .marsx-toast.success {
        border-left: 4px solid #22c55e !important;
    }

    .marsx-toast.error {
        border-left: 4px solid #ef4444 !important;
    }

    .marsx-toast.hiding {
        animation: marsx-toast-out 0.3s ease forwards !important;
    }

    .marsx-toast-icon {
        flex-shrink: 0 !important;
        width: 24px !important;
        height: 24px !important;
    }

    .marsx-toast-icon.success {
        color: #22c55e !important;
    }

    .marsx-toast-icon.error {
        color: #ef4444 !important;
    }

    .marsx-toast-content {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
    }

    .marsx-toast-message {
        font-size: 0.95rem !important;
        color: #333 !important;
        line-height: 1.4 !important;
    }

    .marsx-toast-link {
        font-size: 0.85rem !important;
        color: var(--e-global-color-primary, #f97316) !important;
        text-decoration: none !important;
        font-weight: 500 !important;
        transition: opacity 0.2s !important;
    }

    .marsx-toast-link:hover {
        opacity: 0.8 !important;
        text-decoration: underline !important;
    }

    .marsx-toast-close {
        flex-shrink: 0 !important;
        width: 20px !important;
        height: 20px !important;
        border: none !important;
        background: transparent !important;
        cursor: pointer !important;
        color: #999 !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: color 0.2s !important;
    }

    .marsx-toast-close:hover {
        color: #333 !important;
    }

    @keyframes marsx-toast-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes marsx-toast-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Toast Responsive */
    @media (max-width: 480px) {
        .marsx-toast-container {
            top: 10px !important;
            right: 10px !important;
            left: 10px !important;
        }

        .marsx-toast {
            max-width: 100% !important;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'marsx_woocommerce_toast_styles', 999);


/**
 * เพิ่ม JavaScript สำหรับ Toast Notification
 */
function marsx_add_to_cart_toast_script() {
    // ไม่โหลดใน Elementor editor, admin, หรือ customizer
    if (is_admin() ||
        (defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->editor->is_edit_mode()) ||
        (defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->preview->is_preview_mode()) ||
        is_customize_preview()) {
        return;
    }

    // ตรวจสอบว่ามีสินค้าที่เพิ่งเพิ่มลงตะกร้า (จาก redirect)
    $just_added = null;
    if (WC()->session) {
        $just_added = WC()->session->get('marsx_just_added_product');
        // ลบข้อมูลหลังอ่านแล้ว (แสดงครั้งเดียว)
        if ($just_added) {
            // ตรวจสอบว่าไม่เกิน 30 วินาที
            if (isset($just_added['time']) && (time() - $just_added['time']) < 30) {
                WC()->session->set('marsx_just_added_product', null);
            } else {
                $just_added = null;
                WC()->session->set('marsx_just_added_product', null);
            }
        }
    }

    // ตรวจสอบภาษา
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

    // กำหนด URL ตะกร้าตามภาษา
    $cart_url = $is_english ? home_url('/en/products/cart/') : home_url('/products/cart/');

    // ข้อความตามภาษา
    $text_added = $is_english ? 'Added to cart' : 'เพิ่มลงตะกร้าแล้ว';
    $text_view_cart = $is_english ? 'View Cart' : 'ดูตะกร้า';
    $text_product = $is_english ? 'Product' : 'สินค้า';
    ?>
    <script>
    (function() {
        // รอให้ DOM พร้อม
        document.addEventListener('DOMContentLoaded', function() {
            // สร้าง container สำหรับ toast (ถ้ายังไม่มี)
            if (!document.querySelector('.marsx-toast-container')) {
                var container = document.createElement('div');
                container.className = 'marsx-toast-container';
                document.body.appendChild(container);
            }

            // แสดง Toast สำหรับสินค้าที่เพิ่งเพิ่ม (จาก redirect/non-AJAX)
            <?php if ($just_added && !empty($just_added['name'])) : ?>
            setTimeout(function() {
                if (typeof window.marsxShowToast === 'function') {
                    window.marsxShowToast('<?php echo esc_js($just_added['name']); ?> <?php echo esc_js($text_added); ?>', 'success', true);
                }
            }, 100);
            <?php endif; ?>
        });

        // Function แสดง Toast
        window.marsxShowToast = function(message, type, showCartLink) {
            type = type || 'success';
            showCartLink = showCartLink !== false;

            var container = document.querySelector('.marsx-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'marsx-toast-container';
                document.body.appendChild(container);
            }

            var toast = document.createElement('div');
            toast.className = 'marsx-toast ' + type;

            // Icon SVG
            var iconSvg = type === 'success'
                ? '<svg class="marsx-toast-icon success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
                : '<svg class="marsx-toast-icon error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';

            // สร้าง HTML
            var linkHtml = showCartLink ? '<a href="<?php echo esc_js($cart_url); ?>" class="marsx-toast-link"><?php echo esc_js($text_view_cart); ?> →</a>' : '';

            toast.innerHTML = iconSvg +
                '<div class="marsx-toast-content">' +
                    '<span class="marsx-toast-message">' + message + '</span>' +
                    linkHtml +
                '</div>' +
                '<button class="marsx-toast-close" onclick="this.parentElement.classList.add(\'hiding\'); setTimeout(function(){ this.parentElement.remove(); }.bind(this), 300);">' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
                '</button>';

            container.appendChild(toast);

            // Auto-hide หลัง 3 วินาที
            setTimeout(function() {
                if (toast && toast.parentElement) {
                    toast.classList.add('hiding');
                    setTimeout(function() {
                        if (toast && toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }
            }, 3000);
        };

        // รอ jQuery พร้อม (สำหรับ WooCommerce events)
        if (typeof jQuery !== 'undefined') {
            jQuery(function($) {
                // ไม่ทำงานใน Elementor editor
                if (window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode()) {
                    return;
                }
                if ($('body').hasClass('elementor-editor-active') || $('body').hasClass('elementor-editor-preview')) {
                    return;
                }

                // WooCommerce AJAX add to cart event (สำหรับหน้า shop/archive)
                $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
                    // ตรวจสอบว่าไม่ได้อยู่ใน editor
                    if (window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode()) {
                        return;
                    }

                    // ถ้า $button เป็น null หมายถึงมาจาก single product AJAX (แสดง toast แล้ว)
                    if (!$button || !$button.length) {
                        return;
                    }

                    // พยายามดึงชื่อสินค้า
                    var productName = '';

                    // ลองจาก product card
                    var $product = $button.closest('.product, .elementor-widget-woocommerce-products, li');
                    productName = $product.find('.woocommerce-loop-product__title, .product-title, h2.title, .elementor-heading-title').first().text().trim();

                    // ลองจาก data attribute
                    if (!productName) {
                        productName = $button.data('product_name') || $button.attr('data-product_name') || '';
                    }

                    // ลองจาก aria-label
                    if (!productName) {
                        var ariaLabel = $button.attr('aria-label') || '';
                        if (ariaLabel) {
                            // ตัด "Add to cart: " หรือ "หยิบใส่ตะกร้า: " ออก
                            productName = ariaLabel.replace(/^(Add to cart:|หยิบใส่ตะกร้า:)\s*/i, '');
                        }
                    }

                    // Fallback
                    if (!productName) {
                        productName = '<?php echo esc_js($text_product); ?>';
                    }

                    // แสดง Toast
                    marsxShowToast(productName + ' <?php echo esc_js($text_added); ?>', 'success', true);
                });

                // AJAX Add to Cart สำหรับ Single Product Page
                $('form.cart').on('submit', function(e) {
                    e.preventDefault();

                    var $form = $(this);
                    var $button = $form.find('button[type="submit"]');

                    // ป้องกันกดซ้ำ
                    if ($button.hasClass('loading')) {
                        return;
                    }

                    // ตรวจสอบว่า form valid
                    if (!$form[0].checkValidity()) {
                        $form[0].reportValidity();
                        return;
                    }

                    // ดึงข้อมูลจาก form
                    var productId = $button.val() || $form.find('input[name="add-to-cart"]').val() || $form.find('button[name="add-to-cart"]').val();
                    var quantity = $form.find('input[name="quantity"]').val() || 1;
                    var variationId = $form.find('input[name="variation_id"]').val() || 0;

                    // ถ้าไม่มี product ID ให้ submit form ปกติ
                    if (!productId) {
                        $form.off('submit').submit();
                        return;
                    }

                    // ดึงชื่อสินค้าจากหน้า
                    var productName = $('.product_title').text().trim() ||
                                     $('h1.entry-title').text().trim() ||
                                     $('h1').first().text().trim() ||
                                     '<?php echo esc_js($text_product); ?>';

                    // แสดง loading state
                    var originalText = $button.html();
                    $button.prop('disabled', true).addClass('loading').html('<span class="spinner"></span> กำลังเพิ่ม...');

                    // ใช้ custom AJAX URL
                    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

                    // รวบรวม form data รวม variation attributes
                    var formData = {
                        action: 'marsx_add_to_cart',
                        product_id: productId,
                        quantity: quantity,
                        variation_id: variationId
                    };

                    // เพิ่ม variation attributes
                    $form.find('select[name^="attribute_"], input[name^="attribute_"]').each(function() {
                        formData[$(this).attr('name')] = $(this).val();
                    });

                    // ส่ง AJAX request
                    $.ajax({
                        type: 'POST',
                        url: ajaxUrl,
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.error) {
                                // แสดง error toast
                                marsxShowToast(response.message || 'เกิดข้อผิดพลาด', 'error', false);
                                return;
                            }

                            // อัพเดท cart fragments
                            if (response.fragments) {
                                $.each(response.fragments, function(key, value) {
                                    $(key).replaceWith(value);
                                });
                            }

                            // Trigger event สำหรับ update cart widgets
                            // ใช้ wc_fragments_refreshed แทน added_to_cart เพื่อหลีกเลี่ยง error จาก Elementor
                            $(document.body).trigger('wc_fragments_refreshed');

                            // แสดง Toast สำเร็จ
                            marsxShowToast(productName + ' <?php echo esc_js($text_added); ?>', 'success', true);
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', status, error);
                            // ถ้า AJAX ไม่ทำงาน ให้ submit form ปกติ
                            $form.off('submit').submit();
                        },
                        complete: function() {
                            // คืนค่า button
                            $button.prop('disabled', false).removeClass('loading').html(originalText);
                        }
                    });
                });
            });
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'marsx_add_to_cart_toast_script', 99);


/**
 * ========================================
 * Load Custom Single Product CSS
 * ========================================
 */
function marsx_single_product_styles() {
    if (is_product()) {
        wp_enqueue_style(
            'marsx-single-product',
            get_stylesheet_directory_uri() . '/woocommerce/single-product-custom.css',
            array(),
            filemtime(get_stylesheet_directory() . '/woocommerce/single-product-custom.css')
        );
    }
}
add_action('wp_enqueue_scripts', 'marsx_single_product_styles');
