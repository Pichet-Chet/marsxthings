<?php
/**
 * Template Name: MarsX Cart (ไทย)
 * Description: หน้าตะกร้าสินค้าสำหรับ MarsX Things - ภาษาไทย
 */

// Make sure WooCommerce is active
if (!class_exists('WooCommerce')) {
    wp_redirect(home_url('/'));
    exit;
}

get_header();

// Get cart instance
$cart = WC()->cart;
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Cart Page Styles */
    .marsx-cart-wrapper {
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
        min-height: 60vh;
        padding: 40px 0 80px 0;
        margin-top: 150px;
    }

    .marsx-cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 30px;
    }

    /* Page Title */
    .marsx-cart-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 35px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .marsx-cart-title svg {
        color: #f39c12;
    }

    .marsx-cart-title span {
        color: #f39c12;
    }

    /* Cart Layout */
    .marsx-cart-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 35px;
        align-items: start;
    }

    /* Cart Items */
    .marsx-cart-items {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    /* Cart Item Row */
    .marsx-cart-item {
        display: grid;
        grid-template-columns: auto 1fr auto auto auto;
        gap: 20px;
        align-items: center;
        padding: 25px 30px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .marsx-cart-item:hover {
        background: #fafafa;
    }

    .marsx-cart-item:last-child {
        border-bottom: none;
    }

    /* Product Image */
    .marsx-item-image {
        width: 90px;
        height: 90px;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .marsx-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Product Info */
    .marsx-item-info {
        min-width: 0;
    }

    .marsx-item-info h4 {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .marsx-item-info h4 a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }

    .marsx-item-info h4 a:hover {
        color: #f39c12;
    }

    .marsx-item-price {
        font-size: 0.95rem;
        color: #888;
    }

    /* Quantity Control */
    .marsx-qty-control {
        display: flex;
        align-items: center;
        background: #f5f5f5;
        border-radius: 12px;
        overflow: hidden;
    }

    .marsx-qty-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: #666;
        font-size: 1.2rem;
        font-weight: 500;
    }

    .marsx-qty-btn:hover {
        background: #f39c12;
        color: white;
    }

    .marsx-qty-btn:active {
        transform: scale(0.95);
    }

    .marsx-qty-input {
        width: 50px;
        height: 40px;
        border: none;
        background: white;
        text-align: center;
        font-size: 1rem;
        font-family: inherit;
        font-weight: 600;
        color: #1a1a1a;
        -moz-appearance: textfield;
    }

    .marsx-qty-input::-webkit-outer-spin-button,
    .marsx-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .marsx-qty-input:focus {
        outline: none;
    }

    /* Item Subtotal */
    .marsx-item-subtotal {
        font-weight: 700;
        color: #f39c12;
        font-size: 1.1rem;
        min-width: 100px;
        text-align: right;
    }

    /* Remove Button */
    .marsx-item-remove {
        width: 38px;
        height: 38px;
        border: none;
        background: transparent;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: #ccc;
    }

    .marsx-item-remove:hover {
        background: #fee2e2;
        color: #ef4444;
    }

    /* Cart Summary */
    .marsx-cart-summary {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 35px;
        position: sticky;
        top: 120px;
    }

    .marsx-summary-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .marsx-summary-title svg {
        color: #f39c12;
    }

    .marsx-summary-divider {
        height: 1px;
        background: linear-gradient(90deg, #f39c12 0%, #ffecd2 100%);
        margin-bottom: 25px;
    }

    .marsx-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        font-size: 1rem;
    }

    .marsx-summary-row.total {
        border-top: 2px dashed #eee;
        margin-top: 20px;
        padding-top: 25px;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .marsx-summary-row.total .marsx-summary-value {
        color: #f39c12;
        font-size: 1.5rem;
    }

    .marsx-summary-label {
        color: #666;
    }

    .marsx-summary-value {
        color: #1a1a1a;
        font-weight: 600;
    }

    /* Checkout Button */
    .marsx-btn-checkout {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
        border: none;
        border-radius: 14px;
        color: white;
        font-size: 1.15rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none;
        margin-top: 25px;
        box-shadow: 0 8px 25px rgba(243, 156, 18, 0.3);
    }

    .marsx-btn-checkout:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(243, 156, 18, 0.4);
        color: white;
    }

    .marsx-btn-checkout:active {
        transform: translateY(-1px);
    }

    .marsx-continue-shopping {
        text-align: center;
        margin-top: 20px;
    }

    .marsx-continue-shopping a {
        color: #888;
        text-decoration: none;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color 0.2s;
    }

    .marsx-continue-shopping a:hover {
        color: #f39c12;
    }

    /* Empty Cart */
    .marsx-empty-cart {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 80px 40px;
        text-align: center;
    }

    .marsx-empty-cart-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #fff9f0 0%, #ffecd2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
    }

    .marsx-empty-cart-icon svg {
        width: 60px;
        height: 60px;
        stroke: #f39c12;
    }

    .marsx-empty-cart h3 {
        font-size: 1.6rem;
        color: #1a1a1a;
        margin-bottom: 12px;
    }

    .marsx-empty-cart p {
        color: #888;
        margin-bottom: 30px;
        font-size: 1.05rem;
    }

    .marsx-btn-shop {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 35px;
        background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
        color: white;
        text-decoration: none;
        border-radius: 14px;
        font-weight: 600;
        font-size: 1.05rem;
        transition: all 0.3s;
        box-shadow: 0 8px 25px rgba(243, 156, 18, 0.3);
    }

    .marsx-btn-shop:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(243, 156, 18, 0.4);
        color: white;
    }

    /* Cart Header */
    .marsx-cart-header {
        display: grid;
        grid-template-columns: auto 1fr auto auto auto;
        gap: 20px;
        padding: 18px 30px;
        background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        border-bottom: 1px solid #eee;
        font-weight: 600;
        font-size: 0.85rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .marsx-cart-header span:nth-child(1) { width: 90px; }
    .marsx-cart-header span:nth-child(4) { min-width: 100px; text-align: right; }
    .marsx-cart-header span:nth-child(5) { width: 38px; }

    /* Hide WooCommerce elements */
    .xoo-wsc-modal,
    .xoo-wsc-container,
    #xoo-wsc-w-container {
        display: none !important;
    }

    /* WooCommerce Notices */
    .woocommerce-message,
    .woocommerce-info,
    .woocommerce-error {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 0.95rem;
    }

    .woocommerce-message {
        background: #f0fff4;
        border: 1px solid #c6f6d5;
        color: #276749;
    }

    .woocommerce-error {
        background: #fff5f5;
        border: 1px solid #fed7d7;
        color: #c53030;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .marsx-cart-layout {
            grid-template-columns: 1fr;
        }

        .marsx-cart-summary {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .marsx-cart-wrapper {
            padding: 30px 0 60px 0;
        }

        .marsx-cart-container {
            padding: 0 15px;
        }

        .marsx-cart-title {
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .marsx-cart-header {
            display: none;
        }

        .marsx-cart-item {
            grid-template-columns: auto 1fr;
            grid-template-rows: auto auto;
            gap: 15px;
            padding: 20px;
            position: relative;
        }

        .marsx-item-image {
            width: 80px;
            height: 80px;
            grid-row: span 2;
        }

        .marsx-item-info {
            grid-column: 2;
        }

        .marsx-qty-control {
            grid-column: 2;
            justify-self: start;
        }

        .marsx-item-subtotal {
            position: absolute;
            top: 20px;
            right: 60px;
            min-width: auto;
        }

        .marsx-item-remove {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .marsx-cart-summary {
            padding: 25px;
        }

        .marsx-summary-title {
            font-size: 1.2rem;
        }
    }
</style>

<div class="marsx-cart-wrapper">
    <div class="marsx-cart-container">
        <!-- Page Title -->
        <h1 class="marsx-cart-title">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            ตะกร้า<span>สินค้า</span>
        </h1>

        <?php
        // Display WooCommerce notices
        if (function_exists('wc_print_notices')) {
            wc_print_notices();
        }
        ?>

        <?php if ($cart->is_empty()) : ?>
            <!-- Empty Cart -->
            <div class="marsx-empty-cart">
                <div class="marsx-empty-cart-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <h3>ตะกร้าสินค้าว่างเปล่า</h3>
                <p>ยังไม่มีสินค้าในตะกร้า เริ่มช้อปปิ้งกันเลย!</p>
                <a href="<?php echo home_url('/products/'); ?>" class="marsx-btn-shop">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    เลือกซื้อสินค้า
                </a>
            </div>

        <?php else : ?>
            <!-- Cart with Items -->
            <form action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" class="marsx-cart-form" id="marsx-cart-form">
                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>

                <div class="marsx-cart-layout">
                    <!-- Cart Items -->
                    <div class="marsx-cart-items">
                        <!-- Header -->
                        <div class="marsx-cart-header">
                            <span></span>
                            <span>สินค้า</span>
                            <span>จำนวน</span>
                            <span>รวม</span>
                            <span></span>
                        </div>

                        <!-- Items -->
                        <?php foreach ($cart->get_cart() as $cart_item_key => $cart_item) :
                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                $max_qty = $_product->get_max_purchase_quantity();
                                ?>
                                <div class="marsx-cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>">
                                    <!-- Image -->
                                    <div class="marsx-item-image">
                                        <?php echo $_product->get_image(); ?>
                                    </div>

                                    <!-- Info -->
                                    <div class="marsx-item-info">
                                        <h4>
                                            <?php if ($product_permalink) : ?>
                                                <a href="<?php echo esc_url($product_permalink); ?>">
                                                    <?php echo $_product->get_name(); ?>
                                                </a>
                                            <?php else : ?>
                                                <?php echo $_product->get_name(); ?>
                                            <?php endif; ?>
                                        </h4>
                                        <div class="marsx-item-price">
                                            <?php echo WC()->cart->get_product_price($_product); ?>
                                        </div>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="marsx-qty-control">
                                        <button type="button" class="marsx-qty-btn" data-action="minus">−</button>
                                        <input type="number"
                                               class="marsx-qty-input"
                                               name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]"
                                               value="<?php echo esc_attr($cart_item['quantity']); ?>"
                                               min="0"
                                               max="<?php echo esc_attr($max_qty > 0 ? $max_qty : 9999); ?>"
                                               step="1"
                                               data-key="<?php echo esc_attr($cart_item_key); ?>">
                                        <button type="button" class="marsx-qty-btn" data-action="plus">+</button>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="marsx-item-subtotal">
                                        <?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
                                    </div>

                                    <!-- Remove -->
                                    <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" class="marsx-item-remove" title="ลบสินค้า">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Cart Summary -->
                    <div class="marsx-cart-summary">
                        <h3 class="marsx-summary-title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            สรุปคำสั่งซื้อ
                        </h3>
                        <div class="marsx-summary-divider"></div>

                        <div class="marsx-summary-row">
                            <span class="marsx-summary-label">ยอดรวมสินค้า (<?php echo $cart->get_cart_contents_count(); ?> ชิ้น)</span>
                            <span class="marsx-summary-value"><?php echo $cart->get_cart_subtotal(); ?></span>
                        </div>

                        <?php if ($cart->get_cart_discount_total() > 0) : ?>
                            <div class="marsx-summary-row">
                                <span class="marsx-summary-label">ส่วนลด</span>
                                <span class="marsx-summary-value" style="color: #10b981;">-<?php echo wc_price($cart->get_cart_discount_total()); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($cart->get_fees() as $fee) : ?>
                            <div class="marsx-summary-row">
                                <span class="marsx-summary-label"><?php echo esc_html($fee->name); ?></span>
                                <span class="marsx-summary-value"><?php echo wc_price($fee->total); ?></span>
                            </div>
                        <?php endforeach; ?>

                        <?php if (wc_tax_enabled() && !$cart->display_prices_including_tax()) : ?>
                            <?php foreach ($cart->get_tax_totals() as $code => $tax) : ?>
                                <div class="marsx-summary-row">
                                    <span class="marsx-summary-label"><?php echo esc_html($tax->label); ?></span>
                                    <span class="marsx-summary-value"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="marsx-summary-row total">
                            <span class="marsx-summary-label">ยอดรวมทั้งสิ้น</span>
                            <span class="marsx-summary-value"><?php echo $cart->get_total(); ?></span>
                        </div>

                        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="marsx-btn-checkout">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"></path>
                                <path d="M12 5l7 7-7 7"></path>
                            </svg>
                            ดำเนินการชำระเงิน
                        </a>

                        <div class="marsx-continue-shopping">
                            <a href="<?php echo home_url('/products/'); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                เลือกซื้อสินค้าต่อ
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hidden submit button for form -->
                <input type="hidden" name="update_cart" value="1">
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('marsx-cart-form');
        if (!form) return;

        // Handle quantity buttons
        document.querySelectorAll('.marsx-qty-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const control = this.closest('.marsx-qty-control');
                const input = control.querySelector('.marsx-qty-input');
                const action = this.dataset.action;
                const min = parseInt(input.min) || 0;
                const max = parseInt(input.max) || 9999;
                let value = parseInt(input.value) || 0;

                if (action === 'minus' && value > min) {
                    input.value = value - 1;
                } else if (action === 'plus' && value < max) {
                    input.value = value + 1;
                }

                // Trigger change event
                input.dispatchEvent(new Event('change', { bubbles: true }));

                // Auto submit after delay
                clearTimeout(window.cartUpdateTimeout);
                window.cartUpdateTimeout = setTimeout(function() {
                    form.submit();
                }, 800);
            });
        });

        // Handle manual input change
        document.querySelectorAll('.marsx-qty-input').forEach(function(input) {
            input.addEventListener('change', function() {
                clearTimeout(window.cartUpdateTimeout);
                window.cartUpdateTimeout = setTimeout(function() {
                    form.submit();
                }, 800);
            });
        });
    });
})();
</script>

<?php get_footer(); ?>
