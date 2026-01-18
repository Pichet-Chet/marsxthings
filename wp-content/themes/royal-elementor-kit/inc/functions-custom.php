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
        return ' บาท';
    }
    return $symbol;
}, 10, 2);
