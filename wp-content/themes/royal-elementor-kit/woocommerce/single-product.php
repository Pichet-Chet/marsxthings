<?php
/**
 * MarsX Custom Single Product Template
 *
 * Override WooCommerce default single product page
 * Based on reference design with:
 * - Breadcrumb navigation
 * - Product gallery with arrows
 * - Product info (title, price, description, specs)
 * - Color swatches
 * - Sticky add to cart bar
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('shop');

// ตรวจสอบภาษา
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

?>

<div id="marsx-single-product" class="marsx-single-product-page">

    <?php while (have_posts()) : the_post(); ?>

        <?php wc_get_template_part('content', 'single-product-custom'); ?>

    <?php endwhile; ?>

</div>

<?php

get_footer('shop');
