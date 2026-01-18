<?php
/**
 * Template Name: MarsX My Account (English)
 * Description: My Account page for MarsX Things - English
 */

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/en/login/'));
    exit;
}

$current_user = wp_get_current_user();
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';

// Handle logout
if ($current_tab === 'logout') {
    wp_logout();
    wp_redirect(home_url('/en/login/'));
    exit;
}

// Handle account details update
$update_message = '';
$update_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marsx_update_account'])) {
    if (!isset($_POST['marsx_account_nonce']) || !wp_verify_nonce($_POST['marsx_account_nonce'], 'marsx_account_action')) {
        $update_message = 'Security check failed. Please try again.';
        $update_type = 'error';
    } else {
        $user_id = $current_user->ID;
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $display_name = sanitize_text_field($_POST['display_name']);
        $email = sanitize_email($_POST['email']);

        $userdata = array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $display_name,
            'user_email' => $email
        );

        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                $update_message = 'New passwords do not match.';
                $update_type = 'error';
            } else {
                $userdata['user_pass'] = $_POST['new_password'];
            }
        }

        if (empty($update_message)) {
            $result = wp_update_user($userdata);
            if (is_wp_error($result)) {
                $update_message = $result->get_error_message();
                $update_type = 'error';
            } else {
                $update_message = 'Changes saved successfully.';
                $update_type = 'success';
                $current_user = wp_get_current_user();
            }
        }
    }
}

// Handle address update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marsx_update_address'])) {
    if (!isset($_POST['marsx_address_nonce']) || !wp_verify_nonce($_POST['marsx_address_nonce'], 'marsx_address_action')) {
        $update_message = 'Security check failed. Please try again.';
        $update_type = 'error';
    } else {
        $user_id = $current_user->ID;
        $address_type = sanitize_text_field($_POST['address_type']);
        $prefix = $address_type . '_';

        update_user_meta($user_id, $prefix . 'first_name', sanitize_text_field($_POST['addr_first_name']));
        update_user_meta($user_id, $prefix . 'last_name', sanitize_text_field($_POST['addr_last_name']));
        update_user_meta($user_id, $prefix . 'company', sanitize_text_field($_POST['addr_company']));
        update_user_meta($user_id, $prefix . 'address_1', sanitize_text_field($_POST['addr_address_1']));
        update_user_meta($user_id, $prefix . 'address_2', sanitize_text_field($_POST['addr_address_2']));
        update_user_meta($user_id, $prefix . 'city', sanitize_text_field($_POST['addr_city']));
        update_user_meta($user_id, $prefix . 'postcode', sanitize_text_field($_POST['addr_postcode']));
        update_user_meta($user_id, $prefix . 'country', sanitize_text_field($_POST['addr_country']));
        update_user_meta($user_id, $prefix . 'phone', sanitize_text_field($_POST['addr_phone']));

        $update_message = 'Address saved successfully.';
        $update_type = 'success';
    }
}

$account_url = home_url('/en/my-account/');

get_header();
?>

<style>
    /* Reset and spacing for Elementor header */
    .marsx-account-wrapper {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        background: #f8f9fa;
        min-height: 50vh;
        padding: 40px 0 60px 0;
        margin-top: 150px;
    }
    .marsx-account-container { max-width: 1200px; margin: 0 auto; padding: 0 30px; display: flex; gap: 40px; align-items: flex-start; }

    /* Sidebar */
    .marsx-account-sidebar { flex: 0 0 280px; position: sticky; top: 120px; }
    .marsx-sidebar-menu { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
    .marsx-sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 18px 25px; color: #333; text-decoration: none; font-weight: 500; transition: all 0.3s; border-left: 4px solid transparent; }
    .marsx-sidebar-menu a:hover { background: #fff9f0; color: #f39c12; }
    .marsx-sidebar-menu a.active { background: #fff9f0; color: #f39c12; border-left-color: #f39c12; }
    .marsx-sidebar-menu a svg { width: 22px; height: 22px; stroke: currentColor; fill: none; }
    .marsx-sidebar-menu a.logout-link { color: #e74c3c; }
    .marsx-sidebar-menu a.logout-link:hover { background: #fff5f5; }

    /* Content */
    .marsx-account-content { flex: 1; }
    .marsx-content-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 35px; }
    .marsx-content-title { font-size: 1.8rem; font-weight: 700; color: #1a1a1a; margin-bottom: 10px; }
    .marsx-content-subtitle { color: #666; font-size: 0.95rem; margin-bottom: 30px; }

    /* Dashboard */
    .marsx-dashboard-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 35px; }
    .marsx-stat-card { background: linear-gradient(135deg, #fff9f0 0%, #fff 100%); border: 1px solid #ffecd2; border-radius: 12px; padding: 25px; text-align: center; }
    .marsx-stat-number { font-size: 2.2rem; font-weight: 700; color: #f39c12; }
    .marsx-stat-label { color: #666; font-size: 0.9rem; margin-top: 5px; }
    .marsx-welcome-message { background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%); border-radius: 12px; padding: 30px; color: white; margin-bottom: 30px; }
    .marsx-welcome-message h3 { font-size: 1.4rem; margin-bottom: 10px; }
    .marsx-welcome-message p { opacity: 0.95; line-height: 1.6; }

    /* Orders Table */
    .marsx-orders-table { width: 100%; border-collapse: collapse; }
    .marsx-orders-table th, .marsx-orders-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
    .marsx-orders-table th { background: #f8f9fa; font-weight: 600; color: #333; font-size: 0.9rem; }
    .marsx-orders-table tr:hover { background: #fafafa; }
    .marsx-order-status { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
    .marsx-order-status.completed { background: #d4edda; color: #155724; }
    .marsx-order-status.processing { background: #fff3cd; color: #856404; }
    .marsx-order-status.pending { background: #e2e3e5; color: #383d41; }
    .marsx-order-status.cancelled { background: #f8d7da; color: #721c24; }
    .marsx-btn-view { color: #f39c12; text-decoration: none; font-weight: 500; }
    .marsx-btn-view:hover { text-decoration: underline; }
    .marsx-no-orders { text-align: center; padding: 50px; color: #666; }
    .marsx-no-orders svg { width: 60px; height: 60px; stroke: #ddd; margin-bottom: 15px; }

    /* Forms */
    .marsx-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .marsx-form-group { margin-bottom: 20px; }
    .marsx-form-group.full-width { grid-column: span 2; }
    .marsx-form-group label { display: block; font-size: 0.9rem; color: #333; margin-bottom: 8px; font-weight: 500; }
    .marsx-form-group input, .marsx-form-group select { width: 100%; padding: 14px 16px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s, box-shadow 0.3s; }
    .marsx-form-group input:focus, .marsx-form-group select:focus { outline: none; border-color: #f39c12; box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1); }
    .marsx-btn-submit { padding: 14px 35px; background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%); border: none; border-radius: 30px; color: white; font-size: 1rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
    .marsx-btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4); }

    /* Addresses */
    .marsx-addresses-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .marsx-address-card { border: 1px solid #e0e0e0; border-radius: 12px; padding: 25px; }
    .marsx-address-card h4 { font-size: 1.1rem; color: #1a1a1a; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
    .marsx-address-card h4 svg { width: 20px; height: 20px; stroke: #f39c12; }
    .marsx-address-card p { color: #666; line-height: 1.8; font-size: 0.95rem; }
    .marsx-address-card .marsx-btn-edit { display: inline-flex; align-items: center; gap: 5px; margin-top: 15px; color: #f39c12; text-decoration: none; font-weight: 500; font-size: 0.9rem; }
    .marsx-address-card .marsx-btn-edit:hover { text-decoration: underline; }

    /* Messages */
    .marsx-message { padding: 14px 18px; border-radius: 10px; margin-bottom: 25px; font-size: 0.9rem; }
    .marsx-message.error { background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; }
    .marsx-message.success { background: #f0fff4; border: 1px solid #c6f6d5; color: #276749; }

    /* Back to Shop */
    .marsx-back-to-shop { margin-top: 25px; }
    .marsx-back-to-shop a { display: inline-flex; align-items: center; gap: 8px; color: #f39c12; text-decoration: none; font-weight: 500; }
    .marsx-back-to-shop a:hover { text-decoration: underline; }

    /* Responsive */
    @media (max-width: 992px) {
        .marsx-account-container { flex-direction: column; }
        .marsx-account-sidebar { flex: none; width: 100%; }
        .marsx-sidebar-menu { display: flex; overflow-x: auto; }
        .marsx-sidebar-menu a { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; padding: 15px 20px; }
        .marsx-sidebar-menu a.active { border-bottom-color: #f39c12; }
        .marsx-dashboard-stats { grid-template-columns: 1fr; }
        .marsx-addresses-grid { grid-template-columns: 1fr; }
        .marsx-form-row { grid-template-columns: 1fr; }
        .marsx-form-group.full-width { grid-column: span 1; }
    }
    @media (max-width: 480px) {
        .marsx-account-wrapper { padding: 20px 0; }
        .marsx-account-container { padding: 0 15px; }
        .marsx-content-card { padding: 25px 20px; }
        .marsx-content-title { font-size: 1.5rem; }
    }
</style>

<div class="marsx-account-wrapper">
    <div class="marsx-account-container">
        <aside class="marsx-account-sidebar">
            <nav class="marsx-sidebar-menu">
                <a href="<?php echo esc_url($account_url); ?>" class="<?php echo $current_tab === 'dashboard' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=orders'); ?>" class="<?php echo $current_tab === 'orders' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    Orders
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=addresses'); ?>" class="<?php echo $current_tab === 'addresses' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    Addresses
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=account-details'); ?>" class="<?php echo $current_tab === 'account-details' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Account Details
                </a>
                <a href="<?php echo esc_url($account_url . '?tab=logout'); ?>" class="logout-link">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </a>
            </nav>
        </aside>

        <main class="marsx-account-content">
            <?php if ($update_message) : ?>
                <div class="marsx-message <?php echo esc_attr($update_type); ?>"><?php echo esc_html($update_message); ?></div>
            <?php endif; ?>

            <?php if ($current_tab === 'dashboard') : ?>
                <div class="marsx-content-card">
                    <div class="marsx-welcome-message">
                        <h3>Hello, <?php echo esc_html($current_user->display_name); ?>!</h3>
                        <p>Welcome to your account. You can manage your orders, shipping addresses, and account details from here.</p>
                    </div>

                    <?php
                    $order_count = 0;
                    $total_spent = 0;
                    if (class_exists('WooCommerce')) {
                        $orders = wc_get_orders(array('customer_id' => $current_user->ID, 'limit' => -1));
                        $order_count = count($orders);
                        foreach ($orders as $order) {
                            $total_spent += $order->get_total();
                        }
                    }
                    ?>

                    <div class="marsx-dashboard-stats">
                        <div class="marsx-stat-card">
                            <div class="marsx-stat-number"><?php echo $order_count; ?></div>
                            <div class="marsx-stat-label">Total Orders</div>
                        </div>
                        <div class="marsx-stat-card">
                            <div class="marsx-stat-number">฿<?php echo number_format($total_spent, 0); ?></div>
                            <div class="marsx-stat-label">Total Spent</div>
                        </div>
                        <div class="marsx-stat-card">
                            <div class="marsx-stat-number"><?php echo esc_html(date('M d, Y', strtotime($current_user->user_registered))); ?></div>
                            <div class="marsx-stat-label">Member Since</div>
                        </div>
                    </div>

                    <div class="marsx-back-to-shop">
                        <a href="<?php echo home_url('/en/products/'); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            Back to Shop
                        </a>
                    </div>
                </div>

            <?php elseif ($current_tab === 'orders') : ?>
                <div class="marsx-content-card">
                    <h2 class="marsx-content-title">My Orders</h2>
                    <p class="marsx-content-subtitle">View your order history and status</p>

                    <?php if (class_exists('WooCommerce')) :
                        $orders = wc_get_orders(array('customer_id' => $current_user->ID, 'limit' => 10, 'orderby' => 'date', 'order' => 'DESC'));
                        if ($orders) : ?>
                            <table class="marsx-orders-table">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order) :
                                        $status = $order->get_status();
                                        $status_labels = array('completed' => 'Completed', 'processing' => 'Processing', 'pending' => 'Pending', 'cancelled' => 'Cancelled', 'on-hold' => 'On Hold', 'refunded' => 'Refunded');
                                    ?>
                                        <tr>
                                            <td>#<?php echo $order->get_order_number(); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order->get_date_created())); ?></td>
                                            <td><span class="marsx-order-status <?php echo esc_attr($status); ?>"><?php echo isset($status_labels[$status]) ? $status_labels[$status] : $status; ?></span></td>
                                            <td>฿<?php echo number_format($order->get_total(), 0); ?></td>
                                            <td><a href="<?php echo $order->get_view_order_url(); ?>" class="marsx-btn-view">View</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="marsx-no-orders">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                <p>You haven't placed any orders yet.</p>
                            </div>
                        <?php endif;
                    endif; ?>
                </div>

            <?php elseif ($current_tab === 'addresses') : ?>
                <div class="marsx-content-card">
                    <h2 class="marsx-content-title">My Addresses</h2>
                    <p class="marsx-content-subtitle">Manage your shipping and billing addresses</p>

                    <?php
                    $billing_address = array(
                        'first_name' => get_user_meta($current_user->ID, 'billing_first_name', true),
                        'last_name' => get_user_meta($current_user->ID, 'billing_last_name', true),
                        'company' => get_user_meta($current_user->ID, 'billing_company', true),
                        'address_1' => get_user_meta($current_user->ID, 'billing_address_1', true),
                        'address_2' => get_user_meta($current_user->ID, 'billing_address_2', true),
                        'city' => get_user_meta($current_user->ID, 'billing_city', true),
                        'postcode' => get_user_meta($current_user->ID, 'billing_postcode', true),
                        'country' => get_user_meta($current_user->ID, 'billing_country', true),
                        'phone' => get_user_meta($current_user->ID, 'billing_phone', true)
                    );
                    $shipping_address = array(
                        'first_name' => get_user_meta($current_user->ID, 'shipping_first_name', true),
                        'last_name' => get_user_meta($current_user->ID, 'shipping_last_name', true),
                        'company' => get_user_meta($current_user->ID, 'shipping_company', true),
                        'address_1' => get_user_meta($current_user->ID, 'shipping_address_1', true),
                        'address_2' => get_user_meta($current_user->ID, 'shipping_address_2', true),
                        'city' => get_user_meta($current_user->ID, 'shipping_city', true),
                        'postcode' => get_user_meta($current_user->ID, 'shipping_postcode', true),
                        'country' => get_user_meta($current_user->ID, 'shipping_country', true),
                        'phone' => get_user_meta($current_user->ID, 'shipping_phone', true)
                    );
                    $edit_address = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : '';
                    ?>

                    <?php if ($edit_address === 'billing' || $edit_address === 'shipping') :
                        $addr = $edit_address === 'billing' ? $billing_address : $shipping_address;
                        $addr_title = $edit_address === 'billing' ? 'Billing Address' : 'Shipping Address';
                    ?>
                        <h3 style="margin-bottom: 25px;">Edit <?php echo $addr_title; ?></h3>
                        <form method="post" action="">
                            <?php wp_nonce_field('marsx_address_action', 'marsx_address_nonce'); ?>
                            <input type="hidden" name="address_type" value="<?php echo esc_attr($edit_address); ?>">
                            <div class="marsx-form-row">
                                <div class="marsx-form-group">
                                    <label>First Name</label>
                                    <input type="text" name="addr_first_name" value="<?php echo esc_attr($addr['first_name']); ?>">
                                </div>
                                <div class="marsx-form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="addr_last_name" value="<?php echo esc_attr($addr['last_name']); ?>">
                                </div>
                            </div>
                            <div class="marsx-form-group">
                                <label>Company (Optional)</label>
                                <input type="text" name="addr_company" value="<?php echo esc_attr($addr['company']); ?>">
                            </div>
                            <div class="marsx-form-group">
                                <label>Address</label>
                                <input type="text" name="addr_address_1" value="<?php echo esc_attr($addr['address_1']); ?>">
                            </div>
                            <div class="marsx-form-group">
                                <label>Address Line 2</label>
                                <input type="text" name="addr_address_2" value="<?php echo esc_attr($addr['address_2']); ?>">
                            </div>
                            <div class="marsx-form-row">
                                <div class="marsx-form-group">
                                    <label>City</label>
                                    <input type="text" name="addr_city" value="<?php echo esc_attr($addr['city']); ?>">
                                </div>
                                <div class="marsx-form-group">
                                    <label>Postcode</label>
                                    <input type="text" name="addr_postcode" value="<?php echo esc_attr($addr['postcode']); ?>">
                                </div>
                            </div>
                            <div class="marsx-form-row">
                                <div class="marsx-form-group">
                                    <label>Country</label>
                                    <select name="addr_country">
                                        <option value="TH" <?php selected($addr['country'], 'TH'); ?>>Thailand</option>
                                    </select>
                                </div>
                                <div class="marsx-form-group">
                                    <label>Phone</label>
                                    <input type="tel" name="addr_phone" value="<?php echo esc_attr($addr['phone']); ?>">
                                </div>
                            </div>
                            <button type="submit" name="marsx_update_address" class="marsx-btn-submit">Save Address</button>
                            <a href="<?php echo esc_url($account_url . '?tab=addresses'); ?>" style="margin-left: 15px; color: #666;">Cancel</a>
                        </form>
                    <?php else : ?>
                        <div class="marsx-addresses-grid">
                            <div class="marsx-address-card">
                                <h4><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> Billing Address</h4>
                                <?php if ($billing_address['address_1']) : ?>
                                    <p><?php echo esc_html($billing_address['first_name'] . ' ' . $billing_address['last_name']); ?><br>
                                    <?php echo esc_html($billing_address['address_1']); ?><br>
                                    <?php echo esc_html($billing_address['city'] . ' ' . $billing_address['postcode']); ?><br>
                                    <?php if ($billing_address['phone']) echo 'Phone: ' . esc_html($billing_address['phone']); ?></p>
                                <?php else : ?>
                                    <p>No address specified.</p>
                                <?php endif; ?>
                                <a href="<?php echo esc_url($account_url . '?tab=addresses&edit=billing'); ?>" class="marsx-btn-edit">Edit</a>
                            </div>
                            <div class="marsx-address-card">
                                <h4><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg> Shipping Address</h4>
                                <?php if ($shipping_address['address_1']) : ?>
                                    <p><?php echo esc_html($shipping_address['first_name'] . ' ' . $shipping_address['last_name']); ?><br>
                                    <?php echo esc_html($shipping_address['address_1']); ?><br>
                                    <?php echo esc_html($shipping_address['city'] . ' ' . $shipping_address['postcode']); ?><br>
                                    <?php if ($shipping_address['phone']) echo 'Phone: ' . esc_html($shipping_address['phone']); ?></p>
                                <?php else : ?>
                                    <p>No address specified.</p>
                                <?php endif; ?>
                                <a href="<?php echo esc_url($account_url . '?tab=addresses&edit=shipping'); ?>" class="marsx-btn-edit">Edit</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($current_tab === 'account-details') : ?>
                <div class="marsx-content-card">
                    <h2 class="marsx-content-title">Account Details</h2>
                    <p class="marsx-content-subtitle">Edit your personal information and password</p>

                    <form method="post" action="">
                        <?php wp_nonce_field('marsx_account_action', 'marsx_account_nonce'); ?>
                        <div class="marsx-form-row">
                            <div class="marsx-form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>">
                            </div>
                            <div class="marsx-form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>">
                            </div>
                        </div>
                        <div class="marsx-form-group">
                            <label>Display Name</label>
                            <input type="text" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>">
                        </div>
                        <div class="marsx-form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>">
                        </div>

                        <h3 style="margin: 35px 0 20px; font-size: 1.2rem; color: #1a1a1a;">Change Password</h3>
                        <div class="marsx-form-group">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="new_password" placeholder="••••••••">
                        </div>
                        <div class="marsx-form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="••••••••">
                        </div>

                        <button type="submit" name="marsx_update_account" class="marsx-btn-submit">Save Changes</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php get_footer(); ?>
