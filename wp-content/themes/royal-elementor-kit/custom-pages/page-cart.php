<?php
/**
 * Template Name: MarsX Cart (ไทย)
 * Description: หน้าตะกร้าสินค้าสำหรับ MarsX Things - ภาษาไทย
 */

if (!class_exists('WooCommerce')) {
    wp_redirect(home_url('/'));
    exit;
}

get_header();
$cart = WC()->cart;
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
.mcart {
    --primary: #f5a623;
    --dark: #111;
    --gray: #666;
    --light: #f7f7f7;
    --white: #fff;
    --radius: 16px;
    font-family: 'Noto Sans Thai', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--light);
    padding: 180px 20px 80px;
}

.mcart-inner {
    max-width: 1000px;
    margin: 0 auto;
}

.mcart h1 {
    font-size: 24px;
    font-weight: 600;
    color: var(--dark);
    margin: 0 0 24px 0;
}

.mcart-grid {
    display: flex;
    gap: 24px;
    align-items: flex-start;
}

.mcart-list {
    flex: 1;
    background: var(--white);
    border-radius: var(--radius);
    overflow: hidden;
}

/* Product Row */
.mcart-item {
    display: flex;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 1px solid #eee;
    gap: 24px;
}

.mcart-item:last-child {
    border-bottom: none;
}

/* Image */
.mcart-img {
    width: 72px;
    height: 72px;
    border-radius: 10px;
    overflow: hidden;
    background: var(--light);
    flex-shrink: 0;
}

.mcart-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Info */
.mcart-info {
    flex: 1;
    min-width: 0;
}

.mcart-info h3 {
    font-size: 15px;
    font-weight: 600;
    color: var(--dark);
    margin: 0 0 6px 0;
    line-height: 1.4;
}

.mcart-info h3 a {
    color: inherit;
    text-decoration: none;
}

.mcart-info h3 a:hover {
    color: var(--primary);
}

.mcart-info .price {
    font-size: 13px;
    color: var(--primary);
    font-weight: 500;
    margin: 0;
}

/* Quantity */
.mcart-qty {
    display: flex;
    align-items: center;
    background: var(--light);
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.mcart-qty button {
    width: 40px;
    height: 40px;
    border: none;
    background: none;
    font-size: 18px;
    color: var(--dark);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mcart-qty button:hover {
    color: var(--primary);
}

.mcart-qty input {
    width: 50px;
    height: 40px;
    border: none;
    background: none;
    text-align: center;
    font-size: 15px;
    font-weight: 600;
    color: var(--dark);
    -moz-appearance: textfield;
}

.mcart-qty input::-webkit-outer-spin-button,
.mcart-qty input::-webkit-inner-spin-button {
    -webkit-appearance: none;
}

/* Subtotal */
.mcart-subtotal {
    width: 160px;
    text-align: right;
    font-size: 15px;
    font-weight: 700;
    color: var(--dark);
    flex-shrink: 0;
}

.mcart-subtotal .woocommerce-Price-amount {
    color: var(--dark);
}

.mcart-subtotal small {
    display: block;
    font-size: 11px;
    color: var(--gray);
    font-weight: 400;
    margin-top: 2px;
}

/* Remove Button */
.mcart-remove {
    width: 32px;
    height: 32px;
    border: none;
    background: none;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    flex-shrink: 0;
    text-decoration: none;
}

.mcart-remove:hover {
    background: #fee2e2;
    color: #ef4444;
}

/* Summary */
.mcart-summary {
    width: 320px;
    background: var(--white);
    border-radius: var(--radius);
    padding: 24px;
    flex-shrink: 0;
}

.mcart-summary h2 {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin: 0 0 20px 0;
    padding-bottom: 16px;
    border-bottom: 1px solid #eee;
}

.mcart-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    font-size: 14px;
}

.mcart-row .label { color: var(--gray); }
.mcart-row .val { font-weight: 500; color: var(--dark); }

.mcart-row.total {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 2px dashed #eee;
}

.mcart-row.total .label {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
}

.mcart-row.total .val {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary);
}

.mcart-summary .mcart-row .val .woocommerce-Price-amount {
    color: inherit;
}

.mcart-btn {
    display: block;
    width: 100%;
    padding: 14px;
    margin-top: 20px;
    background: var(--primary);
    border: none;
    border-radius: 10px;
    color: var(--white);
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
}

.mcart-btn:hover {
    background: #e5941d;
    color: var(--white);
}

.mcart-continue {
    display: block;
    text-align: center;
    margin-top: 12px;
    font-size: 13px;
    color: var(--gray);
    text-decoration: none;
}

.mcart-continue:hover {
    color: var(--primary);
}

/* Empty Cart */
.mcart-empty {
    background: var(--white);
    border-radius: var(--radius);
    padding: 60px 40px;
    text-align: center;
    max-width: 400px;
    margin: 40px auto;
}

.mcart-empty svg {
    width: 60px;
    height: 60px;
    stroke: #ccc;
    margin-bottom: 20px;
}

.mcart-empty h2 {
    font-size: 20px;
    color: var(--dark);
    margin: 0 0 8px 0;
}

.mcart-empty p {
    font-size: 14px;
    color: var(--gray);
    margin: 0 0 24px 0;
}

/* Loading */
.mcart-loading {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: 0.2s;
}

.mcart-loading.show {
    opacity: 1;
    visibility: visible;
}

.mcart-spinner {
    width: 36px;
    height: 36px;
    border: 3px solid #eee;
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 900px) {
    .mcart-grid {
        flex-direction: column;
    }
    .mcart-summary {
        width: 100%;
    }
}

@media (max-width: 640px) {
    .mcart { padding: 20px 16px 40px; }
    .mcart h1 { font-size: 20px; }

    .mcart-item {
        flex-wrap: wrap;
        gap: 12px;
        padding: 16px;
    }

    .mcart-img {
        width: 60px;
        height: 60px;
    }

    .mcart-info {
        flex: 1;
        min-width: calc(100% - 110px);
    }

    .mcart-qty {
        order: 3;
    }

    .mcart-subtotal {
        order: 4;
        width: auto;
        flex: 1;
        text-align: right;
    }

    .mcart-remove {
        position: absolute;
        top: 12px;
        right: 12px;
    }

    .mcart-item {
        position: relative;
        padding-right: 44px;
    }
}

/* Hide WooCommerce defaults */
.xoo-wsc-modal, .xoo-wsc-container, #xoo-wsc-w-container { display: none !important; }
</style>

<div class="mcart-loading" id="loading">
    <div class="mcart-spinner"></div>
</div>

<div class="mcart">
    <div class="mcart-inner">

        <?php if ($cart->is_empty()) : ?>

        <div class="mcart-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <h2>ตะกร้าว่างเปล่า</h2>
            <p>คุณยังไม่ได้เพิ่มสินค้าลงตะกร้า</p>
            <a href="<?php echo home_url('/products/'); ?>" class="mcart-btn">เลือกซื้อสินค้า</a>
        </div>

        <?php else : ?>

        <h1>ตะกร้าสินค้า</h1>

        <form action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" id="cart-form">
            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>

            <div class="mcart-grid">
                <div class="mcart-list">
                    <?php foreach ($cart->get_cart() as $key => $item) :
                        $product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $key);
                        if (!$product || !$product->exists() || $item['quantity'] <= 0) continue;
                        $link = $product->is_visible() ? $product->get_permalink($item) : '';
                        $max = $product->get_max_purchase_quantity();
                    ?>
                    <div class="mcart-item">
                        <div class="mcart-img"><?php echo $product->get_image('thumbnail'); ?></div>

                        <div class="mcart-info">
                            <h3><?php if ($link) : ?><a href="<?php echo esc_url($link); ?>"><?php endif; ?><?php echo $product->get_name(); ?><?php if ($link) : ?></a><?php endif; ?></h3>
                            <p class="price"><?php echo WC()->cart->get_product_price($product); ?></p>
                        </div>

                        <div class="mcart-qty">
                            <button type="button" data-act="minus">−</button>
                            <input type="number" name="cart[<?php echo esc_attr($key); ?>][qty]" value="<?php echo esc_attr($item['quantity']); ?>" min="0" max="<?php echo esc_attr($max > 0 ? $max : 999); ?>">
                            <button type="button" data-act="plus">+</button>
                        </div>

                        <div class="mcart-subtotal"><?php echo WC()->cart->get_product_subtotal($product, $item['quantity']); ?></div>

                        <a href="<?php echo esc_url(wc_get_cart_remove_url($key)); ?>" class="mcart-remove">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mcart-summary">
                    <h2>สรุปคำสั่งซื้อ</h2>

                    <div class="mcart-row">
                        <span class="label">ยอดรวม (<?php echo $cart->get_cart_contents_count(); ?> ชิ้น)</span>
                        <span class="val"><?php echo $cart->get_cart_subtotal(); ?></span>
                    </div>

                    <?php if ($cart->get_cart_discount_total() > 0) : ?>
                    <div class="mcart-row">
                        <span class="label">ส่วนลด</span>
                        <span class="val" style="color:#22c55e">-<?php echo wc_price($cart->get_cart_discount_total()); ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="mcart-row total">
                        <span class="label">ยอดรวมทั้งสิ้น</span>
                        <span class="val"><?php echo $cart->get_total(); ?></span>
                    </div>

                    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="mcart-btn">ดำเนินการชำระเงิน</a>
                    <a href="<?php echo home_url('/products/'); ?>" class="mcart-continue">← เลือกซื้อสินค้าต่อ</a>
                </div>
            </div>

            <input type="hidden" name="update_cart" value="1">
        </form>

        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    const form = document.getElementById('cart-form');
    const load = document.getElementById('loading');
    if (!form) return;

    document.querySelectorAll('.mcart-qty button').forEach(btn => {
        btn.onclick = function(e) {
            e.preventDefault();
            const input = this.parentElement.querySelector('input');
            let v = parseInt(input.value) || 0;
            const min = parseInt(input.min) || 0;
            const max = parseInt(input.max) || 999;

            if (this.dataset.act === 'minus' && v > min) v--;
            else if (this.dataset.act === 'plus' && v < max) v++;
            input.value = v;

            clearTimeout(window.t);
            window.t = setTimeout(() => {
                load.classList.add('show');
                form.submit();
            }, 500);
        };
    });

    document.querySelectorAll('.mcart-qty input').forEach(input => {
        input.onchange = () => {
            clearTimeout(window.t);
            window.t = setTimeout(() => {
                load.classList.add('show');
                form.submit();
            }, 500);
        };
    });
})();
</script>

<?php get_footer(); ?>
