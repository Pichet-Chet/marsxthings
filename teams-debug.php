<?php
/**
 * Teams Chat Notification Debug/Test
 * ทดสอบการส่งข้อความไปยัง Microsoft Teams Chat
 */
require_once dirname(__FILE__) . '/wp-load.php';

echo "<h1>Teams Chat Notification Debug</h1>";
echo "<style>
    body { font-family: 'Segoe UI', Tahoma, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; }
    h1 { color: #5558af; }
    h2 { color: #333; border-bottom: 2px solid #5558af; padding-bottom: 5px; }
    pre { background: #f5f5f5; padding: 15px; border-radius: 8px; overflow-x: auto; }
    .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; }
    .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; }
    .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; }
    .info { background: #cce5ff; color: #004085; padding: 10px; border-radius: 5px; margin: 10px 0; }
    form { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
    input, textarea { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; }
    button { background: #5558af; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
    button:hover { background: #4448a0; }
</style>";

// 1. Check Constants
echo "<h2>1. Configuration Check</h2>";
echo "<pre>";

$config_ok = true;

// Graph API (shared)
echo "=== Graph API (Shared) ===\n";
echo "MARSX_GRAPH_TENANT_ID: " . (defined('MARSX_GRAPH_TENANT_ID') ? '✅ SET' : '❌ NOT DEFINED') . "\n";
echo "MARSX_GRAPH_CLIENT_ID: " . (defined('MARSX_GRAPH_CLIENT_ID') ? '✅ SET' : '❌ NOT DEFINED') . "\n";
echo "MARSX_GRAPH_CLIENT_SECRET: " . (defined('MARSX_GRAPH_CLIENT_SECRET') ? '✅ SET (' . strlen(MARSX_GRAPH_CLIENT_SECRET) . ' chars)' : '❌ NOT DEFINED') . "\n";

if (!defined('MARSX_GRAPH_TENANT_ID') || !defined('MARSX_GRAPH_CLIENT_ID') || !defined('MARSX_GRAPH_CLIENT_SECRET')) {
    $config_ok = false;
}

echo "\n=== Teams ROPC Flow ===\n";
echo "MARSX_TEAMS_USERNAME: " . (defined('MARSX_TEAMS_USERNAME') ? '✅ ' . MARSX_TEAMS_USERNAME : '❌ NOT DEFINED') . "\n";
echo "MARSX_TEAMS_PASSWORD: " . (defined('MARSX_TEAMS_PASSWORD') ? '✅ SET (' . strlen(MARSX_TEAMS_PASSWORD) . ' chars)' : '❌ NOT DEFINED') . "\n";

if (!defined('MARSX_TEAMS_USERNAME') || !defined('MARSX_TEAMS_PASSWORD')) {
    $config_ok = false;
}

echo "\n=== Chat IDs ===\n";
echo "MARSX_TEAMS_CHAT_ID_1TO1: " . (defined('MARSX_TEAMS_CHAT_ID_1TO1') && !empty(MARSX_TEAMS_CHAT_ID_1TO1) ? '✅ ' . MARSX_TEAMS_CHAT_ID_1TO1 : '❌ NOT SET') . "\n";
echo "MARSX_TEAMS_CHAT_GROUP_ID: " . (defined('MARSX_TEAMS_CHAT_GROUP_ID') && !empty(MARSX_TEAMS_CHAT_GROUP_ID) ? '✅ ' . MARSX_TEAMS_CHAT_GROUP_ID : '⚠️ NOT SET (optional)') . "\n";

echo "</pre>";

if (!$config_ok) {
    echo "<div class='error'><strong>Configuration Missing!</strong> Please add the required constants to wp-config.php</div>";
    echo "<div class='info'><strong>Required wp-config.php entries:</strong><pre>";
    echo "// Microsoft Teams Chat (ROPC flow)
define('MARSX_TEAMS_USERNAME', 'user@yourdomain.com');
define('MARSX_TEAMS_PASSWORD', 'your-password');
define('MARSX_TEAMS_CHAT_ID_1TO1', 'chat-id-here');
define('MARSX_TEAMS_CHAT_GROUP_ID', ''); // optional";
    echo "</pre></div>";
}

// 2. Test Token Acquisition
echo "<h2>2. Token Acquisition Test</h2>";

if ($config_ok) {
    // Clear cached token first
    delete_transient('marsx_teams_access_token');

    echo "<p>Attempting to get access token using ROPC flow...</p>";

    $token = marsx_get_teams_access_token();

    if (is_wp_error($token)) {
        echo "<div class='error'>❌ Token Error: " . esc_html($token->get_error_message()) . "</div>";
    } else {
        echo "<div class='success'>✅ Token acquired successfully!</div>";
        echo "<pre>Token preview: " . substr($token, 0, 50) . "...</pre>";
    }
} else {
    echo "<div class='warning'>⚠️ Skipped - Configuration not complete</div>";
}

// 3. Send Test Message Form (2 methods)
echo "<h2>3. Send Test Message</h2>";

// Method A: Send by Chat ID (existing)
echo "<h3>Method A: Send by Chat ID</h3>";
echo "<div class='info'>ใช้ Chat ID ที่มีอยู่แล้ว (ต้องหา Chat ID ก่อน)</div>";

if (isset($_POST['send_test_chatid']) && $config_ok) {
    $chat_id = sanitize_text_field($_POST['chat_id']);
    $message = wp_kses_post($_POST['message']);

    echo "<p>Sending message to chat: <code>" . esc_html($chat_id) . "</code></p>";

    $result = marsx_send_teams_chat_message($chat_id, $message);

    if (is_wp_error($result)) {
        echo "<div class='error'>❌ Failed: " . esc_html($result->get_error_message()) . "</div>";
    } else {
        echo "<div class='success'>✅ Message sent successfully!</div>";
        echo "<pre>Response:\n" . print_r($result, true) . "</pre>";
    }
}

$default_chat_id = defined('MARSX_TEAMS_CHAT_ID_1TO1') ? MARSX_TEAMS_CHAT_ID_1TO1 : '';
$default_message = "<h2>🧪 Test Message from MarsX Things</h2>
<p>This is a test message sent at: " . current_time('Y-m-d H:i:s') . "</p>
<p><strong>Status:</strong> ✅ Working!</p>";

echo "<form method='post'>
    <label><strong>Chat ID:</strong></label>
    <input type='text' name='chat_id' value='" . esc_attr($default_chat_id) . "' placeholder='Enter Chat ID' required>

    <label><strong>Message (HTML):</strong></label>
    <textarea name='message' rows='4' required>" . esc_textarea($default_message) . "</textarea>

    <button type='submit' name='send_test_chatid'>📤 Send by Chat ID</button>
</form>";

// Method B: Send by Email (creates chat dynamically - like C#)
echo "<h3>Method B: Send by Email (เหมือน C#)</h3>";
echo "<div class='info'>ส่งโดยใช้ Email โดยตรง - ระบบจะสร้าง 1:1 Chat ให้อัตโนมัติ (เหมือนโค้ด C# ของคุณ)</div>";

if (isset($_POST['send_test_email']) && $config_ok) {
    $target_email = sanitize_email($_POST['target_email']);
    $message = wp_kses_post($_POST['message_email']);

    echo "<p>Creating/Getting 1:1 chat with: <code>" . esc_html($target_email) . "</code></p>";

    // First, create or get the chat
    $chat_id = marsx_create_or_get_1to1_chat($target_email);

    if (is_wp_error($chat_id)) {
        echo "<div class='error'>❌ Failed to create/get chat: " . esc_html($chat_id->get_error_message()) . "</div>";
    } else {
        echo "<div class='success'>✅ Got Chat ID: <code>" . esc_html($chat_id) . "</code></div>";

        // Then send the message
        echo "<p>Sending message...</p>";
        $result = marsx_send_teams_chat_message($chat_id, $message);

        if (is_wp_error($result)) {
            echo "<div class='error'>❌ Failed to send: " . esc_html($result->get_error_message()) . "</div>";
        } else {
            echo "<div class='success'>✅ Message sent successfully!</div>";
            echo "<pre>Response:\n" . print_r($result, true) . "</pre>";
        }
    }
}

$default_target = defined('MARSX_TEAMS_TARGET_EMAIL') ? MARSX_TEAMS_TARGET_EMAIL : '';

echo "<form method='post'>
    <label><strong>Target Email (recipient):</strong></label>
    <input type='email' name='target_email' value='" . esc_attr($default_target) . "' placeholder='user@domain.com' required>
    <small style='color:#666;'>อีเมลของคนที่จะรับข้อความ (ต้องเป็น user ใน Microsoft 365 organization เดียวกัน)</small>

    <label><strong>Message (HTML):</strong></label>
    <textarea name='message_email' rows='4' required>" . esc_textarea($default_message) . "</textarea>

    <button type='submit' name='send_test_email' style='background:#28a745;'>📧 Send by Email</button>
</form>";

// 4. Simulate Order Notification
echo "<h2>4. Simulate Order Notification</h2>";

if (isset($_POST['simulate_order'])) {
    $order_id = intval($_POST['order_id']);
    $order = wc_get_order($order_id);

    if ($order) {
        // Clear the notified flag to allow re-sending
        $order->delete_meta_data('_marsx_teams_notified');
        $order->save();

        echo "<p>Simulating notification for Order #" . $order_id . "...</p>";

        // Call the notification function
        marsx_notify_order_to_teams($order_id);

        // Check if it was marked as notified
        $order = wc_get_order($order_id); // Refresh
        if ($order->get_meta('_marsx_teams_notified')) {
            echo "<div class='success'>✅ Order notification sent! Check your Teams chat.</div>";
        } else {
            echo "<div class='error'>❌ Notification may have failed. Check error_log for details.</div>";
        }
    } else {
        echo "<div class='error'>❌ Order #" . $order_id . " not found</div>";
    }
}

// Get recent orders for testing
$recent_orders = wc_get_orders(array(
    'limit' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
));

echo "<form method='post'>
    <label><strong>Select Order to Test:</strong></label>
    <select name='order_id' style='width:100%; padding:10px; margin:5px 0 15px;'>";

foreach ($recent_orders as $order) {
    $notified = $order->get_meta('_marsx_teams_notified') ? ' (Already notified)' : '';
    echo "<option value='" . $order->get_id() . "'>Order #" . $order->get_order_number() . " - " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . " - " . $order->get_formatted_order_total() . $notified . "</option>";
}

echo "</select>
    <button type='submit' name='simulate_order'>🔔 Send Order Notification</button>
</form>";

// 5. Debug Info
echo "<h2>5. Debug Info</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "WooCommerce Version: " . (defined('WC_VERSION') ? WC_VERSION : 'Not installed') . "\n";
echo "Current Time: " . current_time('Y-m-d H:i:s') . "\n";
echo "Cached Token Exists: " . (get_transient('marsx_teams_access_token') ? 'Yes' : 'No') . "\n";
echo "</pre>";

echo "<div class='info'><strong>Tip:</strong> Check your WordPress error_log for detailed error messages if something fails.</div>";
