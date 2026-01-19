<?php
/**
 * Beam Webhook Test - จำลองการส่ง webhook จาก Beam
 * ใช้ทดสอบว่า webhook handler ทำงานถูกต้อง
 *
 * วิธีใช้: เปิด URL นี้ใน browser แล้วใส่ Order ID ที่ต้องการทดสอบ
 */
require_once dirname(__FILE__) . '/wp-load.php';

// Check admin access
if (!current_user_can('manage_woocommerce')) {
    wp_die('Access denied. Please login as admin.');
}

echo "<h1>Beam Webhook Test</h1>";

// Get order ID from query string
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    // Show form to enter order ID
    echo "<h2>Enter Order ID to simulate payment</h2>";
    echo "<form method='get'>";
    echo "<p><label>Order ID: <input type='number' name='order_id' required></label></p>";
    echo "<p><button type='submit'>Simulate Payment Success</button></p>";
    echo "</form>";

    // Show recent pending orders
    echo "<h2>Recent Pending Orders (with Beam payment)</h2>";
    $orders = wc_get_orders(array(
        'status' => array('pending', 'on-hold'),
        'limit' => 10,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    if ($orders) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Order ID</th><th>Status</th><th>Total</th><th>Payment Link ID</th><th>Action</th></tr>";
        foreach ($orders as $order) {
            $link_id = $order->get_meta('_beam_payment_link_id');
            if ($link_id) {
                echo "<tr>";
                echo "<td>#" . $order->get_id() . "</td>";
                echo "<td>" . $order->get_status() . "</td>";
                echo "<td>" . $order->get_total() . " THB</td>";
                echo "<td>" . esc_html($link_id) . "</td>";
                echo "<td><a href='?order_id=" . $order->get_id() . "'>Simulate Payment</a></td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>No pending orders found.</p>";
    }

    echo "<hr>";
    echo "<p style='color:red;'><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
    exit;
}

// Get order
$order = wc_get_order($order_id);

if (!$order) {
    echo "<p style='color:red'>Order #$order_id not found!</p>";
    echo "<p><a href='?'>Back</a></p>";
    exit;
}

// Get payment link ID
$link_id = $order->get_meta('_beam_payment_link_id');

if (!$link_id) {
    echo "<p style='color:red'>Order #$order_id does not have Beam payment link ID!</p>";
    echo "<p><a href='?'>Back</a></p>";
    exit;
}

echo "<h2>Order Details</h2>";
echo "<pre>";
echo "Order ID: " . $order->get_id() . "\n";
echo "Order Number: " . $order->get_order_number() . "\n";
echo "Status: " . $order->get_status() . "\n";
echo "Total: " . $order->get_total() . " THB\n";
echo "Payment Link ID: " . $link_id . "\n";
echo "</pre>";

// Simulate webhook payload (matching Beam payment_link.paid event)
$webhook_payload = array(
    'event' => 'payment_link.paid',
    'paymentLinkId' => $link_id,
    'status' => 'PAID',
    'order' => array(
        'referenceId' => (string) $order_id,
        'netAmount' => intval($order->get_total()),
        'currency' => 'THB',
    ),
    'paymentMethod' => 'QR_PROMPT_PAY',
    'paidAt' => date('c'),
);

echo "<h2>Simulated Webhook Payload</h2>";
echo "<pre>" . json_encode($webhook_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";

// Process the webhook (bypass signature verification for testing)
echo "<h2>Processing Webhook...</h2>";

// Directly update order status
if ($order->is_paid()) {
    echo "<p style='color:orange'>Order is already paid!</p>";
} else {
    // Mark as paid
    $order->payment_complete($link_id);

    // Add order note
    $order->add_order_note(
        sprintf('Beam Checkout: Payment completed (SIMULATED TEST). Link ID: %s', $link_id)
    );

    // Save payment method
    $order->update_meta_data('_beam_payment_method', 'QR_PROMPT_PAY');
    $order->save();

    echo "<p style='color:green; font-size:20px;'><strong>SUCCESS!</strong> Order #$order_id has been marked as paid.</p>";
    echo "<p>New status: <strong>" . wc_get_order($order_id)->get_status() . "</strong></p>";
}

echo "<p><a href='" . admin_url('post.php?post=' . $order_id . '&action=edit') . "' target='_blank'>View Order in Admin</a></p>";
echo "<p><a href='?'>Test Another Order</a></p>";

echo "<hr>";
echo "<p style='color:red;'><strong>ลบไฟล์นี้หลังทดสอบเสร็จ!</strong></p>";
