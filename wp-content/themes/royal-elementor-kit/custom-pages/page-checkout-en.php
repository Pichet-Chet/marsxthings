<?php
/**
 * Template Name: MarsX Checkout (English)
 * Description: Checkout page for MarsX Things - English
 */

// Make sure WooCommerce is active
if (!class_exists('WooCommerce')) {
    wp_redirect(home_url('/en/'));
    exit;
}

// Redirect to cart if cart is empty
if (WC()->cart->is_empty()) {
    wp_redirect(home_url('/en/products/cart/'));
    exit;
}

// Check if checkout is available
if (!WC()->checkout()) {
    wp_redirect(home_url('/en/products/cart/'));
    exit;
}

get_header();

// Get checkout instance
$checkout = WC()->checkout();
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Checkout Page Styles */
    .marsx-checkout-wrapper {
        font-family: 'Poppins', 'Noto Sans Thai', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
        min-height: 60vh;
        padding: 40px 0 80px 0;
        margin-top: 150px;
    }

    .marsx-checkout-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 30px;
    }

    /* Page Title */
    .marsx-checkout-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 35px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .marsx-checkout-title svg {
        color: #f39c12;
    }

    .marsx-checkout-title span {
        color: #f39c12;
    }

    /* Checkout Layout */
    .marsx-checkout-layout {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 35px;
        align-items: start;
    }

    /* Checkout Form Section */
    .marsx-checkout-form-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 35px;
    }

    .marsx-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f39c12;
    }

    .marsx-section-title svg {
        color: #f39c12;
    }

    /* Form Fields */
    .marsx-checkout-form-section .woocommerce-billing-fields,
    .marsx-checkout-form-section .woocommerce-shipping-fields,
    .marsx-checkout-form-section .woocommerce-additional-fields {
        margin-bottom: 30px;
    }

    .marsx-checkout-form-section .woocommerce-billing-fields h3,
    .marsx-checkout-form-section .woocommerce-shipping-fields h3,
    .marsx-checkout-form-section .woocommerce-additional-fields h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
    }

    .marsx-checkout-form-section .form-row {
        margin-bottom: 18px;
    }

    .marsx-checkout-form-section label {
        display: block;
        font-weight: 500;
        color: #444;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .marsx-checkout-form-section label .required {
        color: #ef4444;
    }

    .marsx-checkout-form-section .input-text,
    .marsx-checkout-form-section select,
    .marsx-checkout-form-section textarea {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        font-family: inherit;
        transition: all 0.2s;
        background: #fff;
    }

    .marsx-checkout-form-section .input-text:focus,
    .marsx-checkout-form-section select:focus,
    .marsx-checkout-form-section textarea:focus {
        outline: none;
        border-color: #f39c12;
        box-shadow: 0 0 0 4px rgba(243, 156, 18, 0.1);
    }

    .marsx-checkout-form-section textarea {
        min-height: 100px;
        resize: vertical;
    }

    /* Select2 Override */
    .marsx-checkout-form-section .select2-container--default .select2-selection--single {
        height: auto;
        padding: 10px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
    }

    .marsx-checkout-form-section .select2-container--default .select2-selection--single:focus {
        border-color: #f39c12;
    }

    .marsx-checkout-form-section .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding: 0;
    }

    .marsx-checkout-form-section .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }

    /* Two Column Fields */
    .marsx-checkout-form-section .woocommerce-billing-fields__field-wrapper,
    .marsx-checkout-form-section .woocommerce-shipping-fields__field-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .marsx-checkout-form-section .form-row-wide {
        grid-column: span 2;
    }

    .marsx-checkout-form-section .form-row-first,
    .marsx-checkout-form-section .form-row-last {
        grid-column: span 1;
    }

    /* Fix first/last name field width */
    .marsx-checkout-form-section #billing_first_name_field,
    .marsx-checkout-form-section #billing_last_name_field,
    .marsx-checkout-form-section #shipping_first_name_field,
    .marsx-checkout-form-section #shipping_last_name_field {
        width: 100% !important;
    }

    .marsx-checkout-form-section #billing_first_name_field .input-text,
    .marsx-checkout-form-section #billing_last_name_field .input-text,
    .marsx-checkout-form-section #shipping_first_name_field .input-text,
    .marsx-checkout-form-section #shipping_last_name_field .input-text {
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Order Review Section */
    .marsx-order-review {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 30px;
        position: sticky;
        top: 120px;
    }

    .marsx-order-review-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .marsx-order-review-title svg {
        color: #f39c12;
    }

    .marsx-order-divider {
        height: 1px;
        background: linear-gradient(90deg, #f39c12 0%, #ffecd2 100%);
        margin-bottom: 20px;
    }

    /* Custom Order Items */
    .marsx-order-items {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 20px;
        padding-right: 5px;
    }

    .marsx-order-items::-webkit-scrollbar {
        width: 4px;
    }

    .marsx-order-items::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    .marsx-order-items::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 2px;
    }

    .marsx-order-items::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }

    .marsx-order-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .marsx-order-item:last-child {
        border-bottom: none;
    }

    .marsx-order-item-image {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        overflow: hidden;
        background: #f8f9fa;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .marsx-order-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .marsx-order-item-details {
        flex: 1;
        min-width: 0;
    }

    .marsx-order-item-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 4px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .marsx-order-item-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: #888;
    }

    .marsx-order-item-qty {
        background: #f5f5f5;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 500;
    }

    .marsx-order-item-price {
        font-size: 0.95rem;
        font-weight: 700;
        color: #f39c12;
        white-space: nowrap;
    }

    /* Order Summary Totals */
    .marsx-order-totals {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .marsx-order-totals-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 0.9rem;
    }

    .marsx-order-totals-row:first-child {
        padding-top: 0;
    }

    .marsx-order-totals-label {
        color: #666;
    }

    .marsx-order-totals-value {
        font-weight: 600;
        color: #1a1a1a;
    }

    .marsx-order-totals-row.discount .marsx-order-totals-value {
        color: #22c55e;
    }

    .marsx-order-totals-row.total {
        border-top: 2px dashed #e5e7eb;
        margin-top: 10px;
        padding-top: 15px;
    }

    .marsx-order-totals-row.total .marsx-order-totals-label {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    .marsx-order-totals-row.total .marsx-order-totals-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #f39c12;
    }

    /* Hide default WooCommerce order table completely */
    .marsx-order-review .woocommerce-checkout-review-order-table,
    .marsx-order-review table.shop_table,
    .marsx-order-review .shop_table,
    #order_review .woocommerce-checkout-review-order-table,
    #order_review table.shop_table {
        display: none !important;
    }

    /* Order Items - Clean style without borders */
    .marsx-order-items {
        border: none !important;
        background: transparent !important;
    }

    .marsx-order-item {
        border: none !important;
        border-bottom: none !important;
        padding: 10px 0 !important;
        background: transparent !important;
    }

    /* Order Totals - Clean style without borders/background */
    .marsx-order-totals {
        background: transparent !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
    }

    .marsx-order-totals-row {
        border: none !important;
        padding: 10px 0 !important;
        background: transparent !important;
    }

    .marsx-order-totals-row.total {
        border: none !important;
        border-top: 2px dashed #e5e7eb !important;
        margin-top: 15px !important;
        padding-top: 15px !important;
    }

    /* Also hide shipping rows from WooCommerce default */
    .marsx-order-review .woocommerce-shipping-totals,
    .marsx-order-review tr.shipping {
        display: none !important;
    }

    /* Payment Section Title */
    .marsx-payment-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 25px 0 15px 0;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .marsx-payment-title svg {
        color: #f39c12;
    }

    /* Payment Methods */
    .marsx-order-review #payment {
        margin-top: 15px;
    }

    .marsx-order-review #payment .payment_methods {
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
    }

    .marsx-order-review #payment .payment_methods li {
        padding: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .marsx-order-review #payment .payment_methods li:hover,
    .marsx-order-review #payment .payment_methods li.wc_payment_method input[type="radio"]:checked + label {
        border-color: #f39c12;
        background: #fffbf5;
    }

    .marsx-order-review #payment .payment_methods li label {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        font-weight: 500;
        color: #1a1a1a;
    }

    .marsx-order-review #payment .payment_methods li input[type="radio"] {
        accent-color: #f39c12;
        width: 18px;
        height: 18px;
    }

    .marsx-order-review #payment .payment_methods li img {
        max-height: 28px;
        width: auto;
    }

    .marsx-order-review #payment .payment_box {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-top: 15px;
        font-size: 0.9rem;
        color: #666;
    }

    /* Place Order Button */
    .marsx-order-review #place_order {
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
        margin-top: 20px;
        box-shadow: 0 8px 25px rgba(243, 156, 18, 0.3);
    }

    .marsx-order-review #place_order:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(243, 156, 18, 0.4);
    }

    .marsx-order-review #place_order:active {
        transform: translateY(-1px);
    }

    /* Terms and Conditions */
    .marsx-order-review .woocommerce-terms-and-conditions-wrapper {
        margin-top: 20px;
        font-size: 0.9rem;
        color: #666;
    }

    .marsx-order-review .woocommerce-terms-and-conditions-wrapper .woocommerce-form__label-for-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
    }

    .marsx-order-review .woocommerce-terms-and-conditions-wrapper input[type="checkbox"] {
        accent-color: #f39c12;
        width: 18px;
        height: 18px;
        margin-top: 2px;
    }

    .marsx-order-review .woocommerce-terms-and-conditions-wrapper a {
        color: #f39c12;
        text-decoration: none;
    }

    .marsx-order-review .woocommerce-terms-and-conditions-wrapper a:hover {
        text-decoration: underline;
    }

    /* Coupon */
    .marsx-checkout-form-section .woocommerce-form-coupon-toggle {
        margin-bottom: 20px;
    }

    .marsx-checkout-form-section .woocommerce-form-coupon-toggle .woocommerce-info {
        padding: 15px 20px;
        background: #fff9f0;
        border: 1px solid #ffecd2;
        border-radius: 12px;
        color: #b8860b;
    }

    .marsx-checkout-form-section .checkout_coupon {
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        gap: 12px;
    }

    .marsx-checkout-form-section .checkout_coupon input[type="text"] {
        flex: 1;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
    }

    .marsx-checkout-form-section .checkout_coupon button {
        padding: 12px 25px;
        background: #1a1a1a;
        border: none;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .marsx-checkout-form-section .checkout_coupon button:hover {
        background: #333;
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

    .woocommerce-info {
        background: #fff9f0;
        border: 1px solid #ffecd2;
        color: #b8860b;
    }

    /* Back to Cart */
    .marsx-back-to-cart {
        margin-top: 20px;
        text-align: center;
    }

    .marsx-back-to-cart a {
        color: #888;
        text-decoration: none;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color 0.2s;
    }

    .marsx-back-to-cart a:hover {
        color: #f39c12;
    }

    /* Login Form */
    .marsx-checkout-form-section .woocommerce-form-login-toggle .woocommerce-info {
        padding: 15px 20px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        color: #0369a1;
        margin-bottom: 20px;
    }

    .marsx-checkout-form-section .woocommerce-form-login {
        padding: 25px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    /* Shipping Options */
    .marsx-checkout-form-section #ship-to-different-address {
        margin-top: 20px;
    }

    .marsx-checkout-form-section #ship-to-different-address label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: 600;
    }

    .marsx-checkout-form-section #ship-to-different-address input[type="checkbox"] {
        accent-color: #f39c12;
        width: 18px;
        height: 18px;
    }

    /* Hide shipping fields by default */
    .marsx-checkout-form-section .shipping_address {
        display: none;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
        animation: marsx-fadeIn 0.3s ease;
    }

    .marsx-checkout-form-section .shipping_address.show {
        display: block;
    }

    @keyframes marsx-fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* AJAX Validation Styles */
    .marsx-field-error {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        animation: marsx-shake 0.4s ease;
    }

    .marsx-field-error svg {
        flex-shrink: 0;
    }

    @keyframes marsx-shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .marsx-checkout-form-section .form-row.marsx-has-error .input-text,
    .marsx-checkout-form-section .form-row.marsx-has-error select,
    .marsx-checkout-form-section .form-row.marsx-has-error textarea {
        border-color: #dc2626 !important;
        background-color: #fef2f2 !important;
    }

    .marsx-checkout-form-section .form-row.marsx-has-error .select2-container--default .select2-selection--single {
        border-color: #dc2626 !important;
        background-color: #fef2f2 !important;
    }

    .marsx-checkout-form-section .form-row.marsx-has-error label {
        color: #dc2626;
    }

    /* Validation Loading */
    .marsx-validation-loading {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .marsx-validation-loading.active {
        opacity: 1;
        visibility: visible;
    }

    .marsx-validation-loading-content {
        text-align: center;
    }

    .marsx-validation-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #f39c12;
        border-radius: 50%;
        animation: marsx-spin 0.8s linear infinite;
        margin: 0 auto 15px;
    }

    @keyframes marsx-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .marsx-validation-loading-text {
        font-family: 'Poppins', 'Noto Sans Thai', sans-serif;
        color: #1a1a1a;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .marsx-checkout-layout {
            grid-template-columns: 1fr;
        }

        .marsx-order-review {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .marsx-checkout-wrapper {
            padding: 30px 0 60px 0;
        }

        .marsx-checkout-container {
            padding: 0 15px;
        }

        .marsx-checkout-title {
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .marsx-checkout-form-section {
            padding: 25px;
        }

        .marsx-checkout-form-section .woocommerce-billing-fields__field-wrapper,
        .marsx-checkout-form-section .woocommerce-shipping-fields__field-wrapper {
            grid-template-columns: 1fr;
        }

        .marsx-checkout-form-section .form-row-wide,
        .marsx-checkout-form-section .form-row-first,
        .marsx-checkout-form-section .form-row-last {
            grid-column: span 1;
        }

        .marsx-order-review {
            padding: 25px;
        }

        .marsx-section-title,
        .marsx-order-review-title {
            font-size: 1.2rem;
        }
    }
</style>

<div class="marsx-checkout-wrapper">
    <div class="marsx-checkout-container">
        <!-- Page Title -->
        <h1 class="marsx-checkout-title">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Checkout</span>
        </h1>

        <?php
        // Display WooCommerce notices
        if (function_exists('wc_print_notices')) {
            wc_print_notices();
        }
        ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

            <div class="marsx-checkout-layout">
                <!-- Billing & Shipping Form -->
                <div class="marsx-checkout-form-section">
                    <h2 class="marsx-section-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Shipping Details
                    </h2>

                    <?php if ($checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) : ?>
                        <div class="woocommerce-form-login-toggle">
                            <div class="woocommerce-info">
                                <a href="#" class="showlogin"><?php esc_html_e('Click here to login', 'woocommerce'); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php do_action('woocommerce_checkout_billing'); ?>

                    <?php do_action('woocommerce_checkout_shipping'); ?>

                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>

                </div>

                <!-- Order Review -->
                <div class="marsx-order-review">
                    <h3 class="marsx-order-review-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        Order Summary
                    </h3>
                    <div class="marsx-order-divider"></div>

                    <!-- Custom Order Items Display -->
                    <div class="marsx-order-items">
                        <?php
                        $cart = WC()->cart;
                        foreach ($cart->get_cart() as $cart_item_key => $cart_item) :
                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0) :
                        ?>
                            <div class="marsx-order-item">
                                <div class="marsx-order-item-image">
                                    <?php echo $_product->get_image('thumbnail'); ?>
                                </div>
                                <div class="marsx-order-item-details">
                                    <div class="marsx-order-item-name"><?php echo $_product->get_name(); ?></div>
                                    <div class="marsx-order-item-meta">
                                        <span class="marsx-order-item-qty">x<?php echo $cart_item['quantity']; ?></span>
                                        <span><?php echo WC()->cart->get_product_price($_product); ?></span>
                                    </div>
                                </div>
                                <div class="marsx-order-item-price">
                                    <?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
                                </div>
                            </div>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>

                    <!-- Order Totals -->
                    <div class="marsx-order-totals">
                        <div class="marsx-order-totals-row">
                            <span class="marsx-order-totals-label">Subtotal (<?php echo $cart->get_cart_contents_count(); ?> items)</span>
                            <span class="marsx-order-totals-value"><?php echo $cart->get_cart_subtotal(); ?></span>
                        </div>

                        <?php if ($cart->get_cart_discount_total() > 0) : ?>
                        <div class="marsx-order-totals-row discount">
                            <span class="marsx-order-totals-label">Discount</span>
                            <span class="marsx-order-totals-value">-<?php echo wc_price($cart->get_cart_discount_total()); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php foreach ($cart->get_fees() as $fee) : ?>
                        <div class="marsx-order-totals-row">
                            <span class="marsx-order-totals-label"><?php echo esc_html($fee->name); ?></span>
                            <span class="marsx-order-totals-value"><?php echo wc_price($fee->total); ?></span>
                        </div>
                        <?php endforeach; ?>

                        <?php if (wc_tax_enabled() && !$cart->display_prices_including_tax()) : ?>
                            <?php foreach ($cart->get_tax_totals() as $code => $tax) : ?>
                            <div class="marsx-order-totals-row">
                                <span class="marsx-order-totals-label"><?php echo esc_html($tax->label); ?></span>
                                <span class="marsx-order-totals-value"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="marsx-order-totals-row total">
                            <span class="marsx-order-totals-label">Total</span>
                            <span class="marsx-order-totals-value"><?php echo $cart->get_total(); ?></span>
                        </div>
                    </div>

                    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

                    <?php do_action('woocommerce_checkout_before_order_review'); ?>

                    <!-- Payment Section Title -->
                    <h4 class="marsx-payment-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Payment Method
                    </h4>

                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <?php do_action('woocommerce_checkout_order_review'); ?>
                    </div>

                    <?php do_action('woocommerce_checkout_after_order_review'); ?>

                    <div class="marsx-back-to-cart">
                        <a href="<?php echo home_url('/en/products/cart/'); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- Validation Loading Overlay -->
<div class="marsx-validation-loading" id="marsx-validation-loading">
    <div class="marsx-validation-loading-content">
        <div class="marsx-validation-spinner"></div>
        <div class="marsx-validation-loading-text">Processing...</div>
    </div>
</div>

<script>
(function($) {
    'use strict';

    // Validation messages in English
    var validationMessages = {
        required: 'This field is required.',
        email: 'Please enter a valid email address.',
        phone: 'Please enter a valid phone number.',
        postcode: 'Please enter a valid postcode.'
    };

    // Required fields configuration
    var requiredFields = [
        { id: 'billing_first_name', label: 'First Name' },
        { id: 'billing_last_name', label: 'Last Name' },
        { id: 'billing_phone', label: 'Phone', type: 'phone' },
        { id: 'billing_email', label: 'Email', type: 'email' },
        { id: 'billing_address_1', label: 'Address' },
        { id: 'billing_city', label: 'City' },
        { id: 'billing_state', label: 'Province' },
        { id: 'billing_postcode', label: 'Postcode' }
    ];

    // Create error element
    function createErrorElement(message) {
        return $('<div class="marsx-field-error">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
            '<circle cx="12" cy="12" r="10"></circle>' +
            '<line x1="12" y1="8" x2="12" y2="12"></line>' +
            '<line x1="12" y1="16" x2="12.01" y2="16"></line>' +
            '</svg>' +
            '<span>' + message + '</span>' +
            '</div>');
    }

    // Clear all errors
    function clearAllErrors() {
        $('.marsx-field-error').remove();
        $('.form-row').removeClass('marsx-has-error');
    }

    // Add error to field
    function addError(fieldId, message) {
        var $field = $('#' + fieldId);
        var $row = $field.closest('.form-row');

        if ($row.length) {
            $row.addClass('marsx-has-error');
            $row.find('.marsx-field-error').remove();
            $row.append(createErrorElement(message));
        }
    }

    // Validate email format
    function isValidEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Validate phone format
    function isValidPhone(phone) {
        var cleaned = phone.replace(/[\s\-\(\)]/g, '');
        return /^[0-9]{9,15}$/.test(cleaned);
    }

    // Validate single field
    function validateField(field) {
        var $input = $('#' + field.id);
        var value = $input.val() ? $input.val().trim() : '';

        // Check if field exists
        if ($input.length === 0) {
            return true;
        }

        // Check required
        if (!value) {
            addError(field.id, validationMessages.required);
            return false;
        }

        // Check email format
        if (field.type === 'email' && !isValidEmail(value)) {
            addError(field.id, validationMessages.email);
            return false;
        }

        // Check phone format
        if (field.type === 'phone' && !isValidPhone(value)) {
            addError(field.id, validationMessages.phone);
            return false;
        }

        return true;
    }

    // Validate all fields
    function validateAllFields() {
        var errors = [];

        clearAllErrors();

        requiredFields.forEach(function(field) {
            if (!validateField(field)) {
                errors.push(field.id);
            }
        });

        // Check payment method
        if ($('input[name="payment_method"]:checked').length === 0) {
            var $paymentSection = $('#payment .payment_methods');
            if ($paymentSection.length) {
                $paymentSection.addClass('marsx-has-error');
                if ($paymentSection.find('.marsx-field-error').length === 0) {
                    $paymentSection.after(createErrorElement('Please select a payment method.'));
                }
                errors.push('payment_method');
            }
        }

        return errors;
    }

    // Scroll to first error
    function scrollToFirstError(errors) {
        if (errors.length === 0) return;

        var firstErrorId = errors[0];
        var $firstError;

        if (firstErrorId === 'payment_method') {
            $firstError = $('#payment');
        } else {
            $firstError = $('#' + firstErrorId);
        }

        if ($firstError.length) {
            $('html, body').animate({
                scrollTop: $firstError.offset().top - 150
            }, 500, function() {
                if (firstErrorId !== 'payment_method') {
                    $firstError.focus();
                }
            });
        }
    }

    // Show loading
    function showLoading() {
        $('#marsx-validation-loading').addClass('active');
    }

    // Hide loading
    function hideLoading() {
        $('#marsx-validation-loading').removeClass('active');
    }

    // Initialize validation
    $(document).ready(function() {
        var $form = $('form.checkout');

        // Handle form submit
        $form.on('submit', function(e) {
            var errors = validateAllFields();

            if (errors.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                scrollToFirstError(errors);
                return false;
            }

            showLoading();
        });

        // Real-time validation on blur
        requiredFields.forEach(function(field) {
            $('#' + field.id).on('blur', function() {
                var $row = $(this).closest('.form-row');
                $row.find('.marsx-field-error').remove();
                $row.removeClass('marsx-has-error');
                validateField(field);
            });

            // Clear error on input
            $('#' + field.id).on('input', function() {
                var $row = $(this).closest('.form-row');
                if ($row.hasClass('marsx-has-error')) {
                    $row.find('.marsx-field-error').remove();
                    $row.removeClass('marsx-has-error');
                }
            });
        });

        // Handle WooCommerce checkout error
        $(document.body).on('checkout_error', function() {
            hideLoading();
        });

        // Handle Select2 changes
        $(document).on('change', '.form-row select', function() {
            var $row = $(this).closest('.form-row');
            if ($row.hasClass('marsx-has-error')) {
                $row.find('.marsx-field-error').remove();
                $row.removeClass('marsx-has-error');
            }
        });

        // Toggle shipping address visibility
        var $shipCheckbox = $('#ship-to-different-address-checkbox');
        var $shippingFields = $('.shipping_address');

        function toggleShippingAddress() {
            if ($shipCheckbox.is(':checked')) {
                $shippingFields.addClass('show');
            } else {
                $shippingFields.removeClass('show');
            }
        }

        // Initial state
        toggleShippingAddress();

        // Handle checkbox change
        $shipCheckbox.on('change', toggleShippingAddress);
    });

})(jQuery);
</script>

<?php get_footer(); ?>
