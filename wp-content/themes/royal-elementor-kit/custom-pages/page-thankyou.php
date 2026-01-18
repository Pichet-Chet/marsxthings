<?php
/**
 * Template Name: MarsX Thank You (ไทย)
 * Description: หน้าขอบคุณหลังสั่งซื้อสำเร็จ - ภาษาไทย
 */

// Make sure WooCommerce is active
if (!class_exists('WooCommerce')) {
    wp_redirect(home_url('/'));
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
$order_key = isset($_GET['key']) ? wc_clean(wp_unslash($_GET['key'])) : '';

// Try to get order
$order = false;
if ($order_id) {
    $order = wc_get_order($order_id);
}

// Validate order exists and key matches
if (!$order || ($order_key && $order->get_order_key() !== $order_key)) {
    // Try fallback - get latest order for logged in user
    if (is_user_logged_in()) {
        $orders = wc_get_orders(array(
            'customer_id' => get_current_user_id(),
            'limit' => 1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        if (!empty($orders)) {
            $order = $orders[0];
        }
    }
}

get_header();
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Thank You Page Styles */
    .marsx-thankyou-wrapper {
        font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
        min-height: 60vh;
        padding: 40px 0 80px 0;
        margin-top: 150px;
    }

    .marsx-thankyou-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 30px;
    }

    /* Success Header */
    .marsx-success-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .marsx-success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        animation: marsx-pulse 2s ease-in-out infinite;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
    }

    .marsx-success-icon svg {
        width: 50px;
        height: 50px;
        color: white;
    }

    @keyframes marsx-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .marsx-success-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
    }

    .marsx-success-subtitle {
        font-size: 1.1rem;
        color: #666;
    }

    /* Order Card */
    .marsx-order-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .marsx-order-header {
        background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
        color: white;
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .marsx-order-number {
        font-size: 1.3rem;
        font-weight: 700;
    }

    .marsx-order-date {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .marsx-order-status {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        background: rgba(255,255,255,0.2);
    }

    .marsx-order-body {
        padding: 30px;
    }

    .marsx-section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f39c12;
    }

    .marsx-section-title svg {
        color: #f39c12;
        width: 22px;
        height: 22px;
    }

    /* Order Items */
    .marsx-order-items {
        margin-bottom: 30px;
    }

    .marsx-order-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .marsx-order-item:last-child {
        border-bottom: none;
    }

    .marsx-item-image {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        object-fit: cover;
        background: #f8f9fa;
        flex-shrink: 0;
    }

    .marsx-item-details {
        flex: 1;
    }

    .marsx-item-name {
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .marsx-item-meta {
        font-size: 0.9rem;
        color: #666;
    }

    .marsx-item-price {
        font-weight: 600;
        color: #f39c12;
        font-size: 1.1rem;
    }

    /* Order Totals */
    .marsx-order-totals {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .marsx-total-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .marsx-total-row:last-child {
        border-bottom: none;
        padding-top: 15px;
        margin-top: 5px;
        border-top: 2px solid #e5e7eb;
    }

    .marsx-total-label {
        color: #666;
    }

    .marsx-total-value {
        font-weight: 600;
        color: #1a1a1a;
    }

    .marsx-total-row:last-child .marsx-total-label,
    .marsx-total-row:last-child .marsx-total-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    .marsx-total-row:last-child .marsx-total-value {
        color: #f39c12;
    }

    /* Info Grid */
    .marsx-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .marsx-info-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
    }

    .marsx-info-box h4 {
        font-size: 0.95rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .marsx-info-box h4 svg {
        width: 18px;
        height: 18px;
        color: #f39c12;
    }

    .marsx-info-box p {
        color: #1a1a1a;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    /* Action Buttons */
    .marsx-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 35px;
    }

    .marsx-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 30px;
        background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
        border: none;
        border-radius: 30px;
        color: white;
        font-size: 1rem;
        font-weight: 600;
        font-family: inherit;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .marsx-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        color: white;
    }

    .marsx-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 30px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 30px;
        color: #333;
        font-size: 1rem;
        font-weight: 600;
        font-family: inherit;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .marsx-btn-secondary:hover {
        border-color: #f39c12;
        color: #f39c12;
    }

    /* Error State */
    .marsx-error-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 60px 40px;
        text-align: center;
    }

    .marsx-error-icon {
        width: 80px;
        height: 80px;
        background: #fef2f2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }

    .marsx-error-icon svg {
        width: 40px;
        height: 40px;
        color: #ef4444;
    }

    .marsx-error-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
    }

    .marsx-error-message {
        color: #666;
        margin-bottom: 30px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .marsx-thankyou-wrapper {
            padding: 30px 0 60px 0;
            margin-top: 120px;
        }

        .marsx-thankyou-container {
            padding: 0 15px;
        }

        .marsx-success-title {
            font-size: 1.8rem;
        }

        .marsx-order-header {
            flex-direction: column;
            text-align: center;
        }

        .marsx-order-body {
            padding: 20px;
        }

        .marsx-info-grid {
            grid-template-columns: 1fr;
        }

        .marsx-order-item {
            flex-wrap: wrap;
        }

        .marsx-actions {
            flex-direction: column;
        }

        .marsx-btn-primary,
        .marsx-btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="marsx-thankyou-wrapper">
    <div class="marsx-thankyou-container">
        <?php if ($order) : ?>
            <!-- Success Header -->
            <div class="marsx-success-header">
                <div class="marsx-success-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <h1 class="marsx-success-title">ขอบคุณสำหรับคำสั่งซื้อ!</h1>
                <p class="marsx-success-subtitle">เราได้รับคำสั่งซื้อของคุณเรียบร้อยแล้ว และจะดำเนินการจัดส่งให้เร็วที่สุด</p>
            </div>

            <!-- Order Card -->
            <div class="marsx-order-card">
                <div class="marsx-order-header">
                    <div>
                        <div class="marsx-order-number">คำสั่งซื้อ #<?php echo $order->get_order_number(); ?></div>
                        <div class="marsx-order-date">วันที่สั่งซื้อ: <?php echo date('d/m/Y H:i', strtotime($order->get_date_created())); ?></div>
                    </div>
                    <?php
                    $status = $order->get_status();
                    $status_labels = array(
                        'completed' => 'เสร็จสิ้น',
                        'processing' => 'กำลังดำเนินการ',
                        'pending' => 'รอดำเนินการ',
                        'on-hold' => 'รอการชำระเงิน',
                        'cancelled' => 'ยกเลิก',
                        'refunded' => 'คืนเงินแล้ว'
                    );
                    ?>
                    <span class="marsx-order-status"><?php echo isset($status_labels[$status]) ? $status_labels[$status] : $status; ?></span>
                </div>

                <div class="marsx-order-body">
                    <!-- Order Items -->
                    <div class="marsx-order-items">
                        <h3 class="marsx-section-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            รายการสินค้า
                        </h3>
                        <?php foreach ($order->get_items() as $item_id => $item) :
                            $product = $item->get_product();
                            $image_url = $product ? wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') : '';
                        ?>
                            <div class="marsx-order-item">
                                <?php if ($image_url) : ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($item->get_name()); ?>" class="marsx-item-image">
                                <?php else : ?>
                                    <div class="marsx-item-image" style="display: flex; align-items: center; justify-content: center;">
                                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    </div>
                                <?php endif; ?>
                                <div class="marsx-item-details">
                                    <div class="marsx-item-name"><?php echo esc_html($item->get_name()); ?></div>
                                    <div class="marsx-item-meta">จำนวน: <?php echo $item->get_quantity(); ?> ชิ้น</div>
                                </div>
                                <div class="marsx-item-price">฿<?php echo number_format($item->get_total(), 0); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Totals -->
                    <div class="marsx-order-totals">
                        <div class="marsx-total-row">
                            <span class="marsx-total-label">ยอดรวมสินค้า</span>
                            <span class="marsx-total-value">฿<?php echo number_format($order->get_subtotal(), 0); ?></span>
                        </div>
                        <?php if ($order->get_shipping_total() > 0) : ?>
                        <div class="marsx-total-row">
                            <span class="marsx-total-label">ค่าจัดส่ง</span>
                            <span class="marsx-total-value">฿<?php echo number_format($order->get_shipping_total(), 0); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($order->get_total_discount() > 0) : ?>
                        <div class="marsx-total-row">
                            <span class="marsx-total-label">ส่วนลด</span>
                            <span class="marsx-total-value" style="color: #10b981;">-฿<?php echo number_format($order->get_total_discount(), 0); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="marsx-total-row">
                            <span class="marsx-total-label">ยอดรวมทั้งสิ้น</span>
                            <span class="marsx-total-value">฿<?php echo number_format($order->get_total(), 0); ?></span>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="marsx-info-grid">
                        <div class="marsx-info-box">
                            <h4>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                ที่อยู่จัดส่ง
                            </h4>
                            <p>
                                <?php echo esc_html($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name()); ?><br>
                                <?php echo esc_html($order->get_shipping_address_1()); ?>
                                <?php if ($order->get_shipping_address_2()) echo '<br>' . esc_html($order->get_shipping_address_2()); ?><br>
                                <?php echo esc_html($order->get_shipping_city() . ' ' . $order->get_shipping_postcode()); ?>
                                <?php if ($order->get_billing_phone()) : ?><br>โทร: <?php echo esc_html($order->get_billing_phone()); ?><?php endif; ?>
                            </p>
                        </div>
                        <div class="marsx-info-box">
                            <h4>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                ช่องทางการชำระเงิน
                            </h4>
                            <p><?php echo esc_html($order->get_payment_method_title()); ?></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="marsx-actions">
                        <a href="<?php echo home_url('/products/'); ?>" class="marsx-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            เลือกซื้อสินค้าต่อ
                        </a>
                        <?php if (is_user_logged_in()) : ?>
                        <a href="<?php echo home_url('/my-account/?tab=orders'); ?>" class="marsx-btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            ดูคำสั่งซื้อทั้งหมด
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else : ?>
            <!-- Error State -->
            <div class="marsx-error-card">
                <div class="marsx-error-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h2 class="marsx-error-title">ไม่พบข้อมูลคำสั่งซื้อ</h2>
                <p class="marsx-error-message">ไม่สามารถเข้าถึงข้อมูลคำสั่งซื้อได้ กรุณาตรวจสอบลิงก์อีกครั้ง หรือเข้าสู่ระบบเพื่อดูคำสั่งซื้อของคุณ</p>
                <div class="marsx-actions">
                    <a href="<?php echo home_url('/products/'); ?>" class="marsx-btn-primary">กลับไปหน้าร้านค้า</a>
                    <?php if (!is_user_logged_in()) : ?>
                    <a href="<?php echo home_url('/login/'); ?>" class="marsx-btn-secondary">เข้าสู่ระบบ</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
