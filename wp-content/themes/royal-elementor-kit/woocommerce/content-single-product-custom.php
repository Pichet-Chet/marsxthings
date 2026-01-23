<?php
/**
 * MarsX Custom Single Product Content
 *
 * Layout ตามภาพตัวอย่าง:
 * - Breadcrumb
 * - 2 columns: Gallery | Product Info
 * - Product specs, color swatches
 * - Add to Cart Bar
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

// ตรวจสอบภาษา
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

// ข้อความตามภาษา
$text_add_to_cart = $is_english ? 'ADD TO CART' : 'เพิ่มลงตะกร้า';
$text_description = $is_english ? 'Description' : 'รายละเอียด';
$text_product_size = $is_english ? 'Product Size' : 'ขนาดสินค้า';
$text_shipping = $is_english ? 'Shipping' : 'การจัดส่ง';
$text_qty = $is_english ? 'Qty' : 'จำนวน';

// Product data
$product_id = $product->get_id();
$product_title = $product->get_name();
$product_price = $product->get_price_html();
$product_short_desc = $product->get_short_description();
$product_desc = $product->get_description();

// Get product images
$attachment_ids = $product->get_gallery_image_ids();
$main_image_id = $product->get_image_id();
$all_images = array();
if ($main_image_id) {
    $all_images[] = $main_image_id;
}
$all_images = array_merge($all_images, $attachment_ids);

// Get categories for breadcrumb
$terms = get_the_terms($product_id, 'product_cat');
$category_name = '';
$category_link = '';
if ($terms && !is_wp_error($terms)) {
    $main_term = $terms[0];
    $category_name = $main_term->name;
    $category_link = get_term_link($main_term);
}

// Check if product is on sale
$is_on_sale = $product->is_on_sale();

// Get product attributes/specs (if any)
$attributes = $product->get_attributes();
?>

<div id="product-<?php echo $product_id; ?>" <?php wc_product_class('marsx-product-single', $product); ?>>

    <!-- Breadcrumb -->
    <nav class="marsx-breadcrumb">
        <a href="<?php echo esc_url($is_english ? home_url('/en/products/') : home_url('/products/')); ?>">
            <?php echo $is_english ? 'Products' : 'สินค้า'; ?>
        </a>
        <span class="separator">›</span>
        <?php if ($category_name && $category_link) : ?>
            <a href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_name); ?></a>
            <span class="separator">›</span>
        <?php endif; ?>
        <span class="current"><?php echo esc_html($product_title); ?></span>
    </nav>

    <!-- Main Content -->
    <div class="marsx-product-container">

        <!-- Left: Product Gallery -->
        <div class="marsx-product-gallery">
            <?php if (!empty($all_images)) : ?>
                <div class="marsx-gallery-main">
                    <?php if ($is_on_sale) : ?>
                        <span class="marsx-sale-badge"><?php echo $is_english ? 'HOTSALE' : 'ลดราคา'; ?></span>
                    <?php endif; ?>

                    <div class="marsx-gallery-slider">
                        <?php foreach ($all_images as $index => $image_id) :
                            $image_url = wp_get_attachment_image_url($image_id, 'large');
                            $image_full = wp_get_attachment_image_url($image_id, 'full');
                        ?>
                            <div class="marsx-gallery-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                <a href="<?php echo esc_url($image_full); ?>" data-lightbox="product-gallery">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_title); ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($all_images) > 1) : ?>
                        <button class="marsx-gallery-nav prev" aria-label="Previous">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                        <button class="marsx-gallery-nav next" aria-label="Next">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (count($all_images) > 1) : ?>
                    <div class="marsx-gallery-thumbs">
                        <?php foreach ($all_images as $index => $image_id) :
                            $thumb_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                        ?>
                            <button class="marsx-thumb <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                <img src="<?php echo esc_url($thumb_url); ?>" alt="">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="marsx-gallery-main">
                    <?php echo wc_placeholder_img(); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Product Info -->
        <div class="marsx-product-info">

            <?php if ($is_on_sale) : ?>
                <span class="marsx-badge-inline"><?php echo $is_english ? 'HOTSALE' : 'ลดราคา'; ?></span>
            <?php endif; ?>

            <h1 class="marsx-product-title"><?php echo esc_html($product_title); ?></h1>

            <?php if ($product_short_desc) : ?>
                <div class="marsx-product-short-desc">
                    <?php echo wp_kses_post($product_short_desc); ?>
                </div>
            <?php endif; ?>

            <div class="marsx-product-price">
                <?php echo $product_price; ?>
            </div>

            <?php if ($product_desc) : ?>
                <div class="marsx-product-description">
                    <h3><?php echo esc_html($text_description); ?>:</h3>
                    <div class="desc-content">
                        <?php echo wp_kses_post($product_desc); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // Custom Fields - Product Specs (ACF หรือ custom meta)
            $specs = array(
                'height' => get_post_meta($product_id, '_product_height', true),
                'width' => get_post_meta($product_id, '_product_width', true),
                'depth' => get_post_meta($product_id, '_product_depth', true),
                'weight' => $product->get_weight(),
            );

            // ถ้ามี specs
            $has_specs = !empty($specs['height']) || !empty($specs['width']) || !empty($specs['depth']) || !empty($specs['weight']);
            ?>

            <?php if ($has_specs) : ?>
                <div class="marsx-product-specs">
                    <h3><?php echo esc_html($text_product_size); ?>:</h3>
                    <ul>
                        <?php if (!empty($specs['height'])) : ?>
                            <li><strong><?php echo $is_english ? 'Height' : 'ความสูง'; ?>:</strong> <?php echo esc_html($specs['height']); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($specs['width'])) : ?>
                            <li><strong><?php echo $is_english ? 'Width' : 'ความกว้าง'; ?>:</strong> <?php echo esc_html($specs['width']); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($specs['depth'])) : ?>
                            <li><strong><?php echo $is_english ? 'Depth' : 'ความลึก'; ?>:</strong> <?php echo esc_html($specs['depth']); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($specs['weight'])) : ?>
                            <li><strong><?php echo $is_english ? 'Weight' : 'น้ำหนัก'; ?>:</strong> <?php echo esc_html($specs['weight']); ?> <?php echo esc_html(get_option('woocommerce_weight_unit')); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            // Shipping info (custom field)
            $shipping_info = get_post_meta($product_id, '_shipping_info', true);
            if ($shipping_info) :
            ?>
                <div class="marsx-shipping-info">
                    <h3><?php echo esc_html($text_shipping); ?>:</h3>
                    <div class="shipping-content">
                        <?php echo wp_kses_post($shipping_info); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // Color/Variation Swatches (ถ้าเป็น variable product)
            if ($product->is_type('variable')) :
                $available_variations = $product->get_available_variations();
                $variation_attributes = $product->get_variation_attributes();
            ?>
                <div class="marsx-variations">
                    <?php foreach ($variation_attributes as $attribute_name => $options) :
                        $attribute_label = wc_attribute_label($attribute_name);
                    ?>
                        <div class="marsx-variation-group">
                            <label><?php echo esc_html($attribute_label); ?>:</label>
                            <div class="marsx-swatches" data-attribute="<?php echo esc_attr(sanitize_title($attribute_name)); ?>">
                                <?php foreach ($options as $option) : ?>
                                    <button type="button" class="marsx-swatch" data-value="<?php echo esc_attr($option); ?>" title="<?php echo esc_attr($option); ?>">
                                        <?php echo esc_html($option); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <!-- Add to Cart Bar -->
    <div class="marsx-add-to-cart-bar">
        <div class="marsx-cart-bar-inner">
            <div class="marsx-cart-bar-product">
                <?php
                $thumb_url = wp_get_attachment_image_url($main_image_id, 'thumbnail');
                if (!$thumb_url) {
                    $thumb_url = wc_placeholder_img_src('thumbnail');
                }
                ?>
                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($product_title); ?>" class="marsx-cart-bar-image">
                <span class="marsx-cart-bar-title"><?php echo esc_html($product_title); ?></span>
            </div>

            <div class="marsx-cart-bar-actions">
                <label class="marsx-qty-label"><?php echo esc_html($text_qty); ?>:</label>
                <div class="marsx-qty-wrapper">
                    <button type="button" class="marsx-qty-btn minus">−</button>
                    <input type="number" class="marsx-qty-input" value="1" min="1" max="<?php echo esc_attr($product->get_stock_quantity() ?: 99); ?>">
                    <button type="button" class="marsx-qty-btn plus">+</button>
                </div>

                <button type="button" class="marsx-add-to-cart-btn" data-product-id="<?php echo esc_attr($product_id); ?>">
                    <?php echo esc_html($text_add_to_cart); ?>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Gallery Slider
        const slides = document.querySelectorAll('.marsx-gallery-slide');
        const thumbs = document.querySelectorAll('.marsx-thumb');
        const prevBtn = document.querySelector('.marsx-gallery-nav.prev');
        const nextBtn = document.querySelector('.marsx-gallery-nav.next');
        let currentIndex = 0;

        function showSlide(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            currentIndex = index;

            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
            thumbs.forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });
        }

        if (prevBtn) prevBtn.addEventListener('click', () => showSlide(currentIndex - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => showSlide(currentIndex + 1));

        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                showSlide(parseInt(thumb.dataset.index));
            });
        });

        // Quantity buttons
        const qtyInput = document.querySelector('.marsx-qty-input');
        const minusBtn = document.querySelector('.marsx-qty-btn.minus');
        const plusBtn = document.querySelector('.marsx-qty-btn.plus');

        if (minusBtn && qtyInput) {
            minusBtn.addEventListener('click', () => {
                let val = parseInt(qtyInput.value) || 1;
                if (val > 1) qtyInput.value = val - 1;
            });
        }

        if (plusBtn && qtyInput) {
            plusBtn.addEventListener('click', () => {
                let val = parseInt(qtyInput.value) || 1;
                let max = parseInt(qtyInput.max) || 99;
                if (val < max) qtyInput.value = val + 1;
            });
        }

        // Add to Cart AJAX
        const addToCartBtn = document.querySelector('.marsx-add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                if (this.disabled) return;

                const productId = this.dataset.productId;
                const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

                this.disabled = true;
                this.classList.add('loading');
                const originalText = this.textContent;
                this.textContent = '<?php echo $is_english ? "Adding..." : "กำลังเพิ่ม..."; ?>';

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
                        window.location.href = '?add-to-cart=' + productId + '&quantity=' + qty;
                    } else {
                        if (data.fragments && typeof jQuery !== 'undefined') {
                            jQuery.each(data.fragments, function(key, value) {
                                jQuery(key).replaceWith(value);
                            });
                            jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash]);
                        }

                        this.textContent = '<?php echo $is_english ? "Added!" : "เพิ่มแล้ว!"; ?>';
                        setTimeout(() => {
                            this.textContent = originalText;
                            this.disabled = false;
                            this.classList.remove('loading');
                        }, 2000);
                    }
                })
                .catch(() => {
                    window.location.href = '?add-to-cart=' + productId + '&quantity=' + qty;
                });
            });
        }

        // Variation swatches
        const swatches = document.querySelectorAll('.marsx-swatch');
        swatches.forEach(swatch => {
            swatch.addEventListener('click', function() {
                const parent = this.closest('.marsx-swatches');
                parent.querySelectorAll('.marsx-swatch').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
})();
</script>
