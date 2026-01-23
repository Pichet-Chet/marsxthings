<?php
/**
 * Beam Checkout Payment Gateway for WooCommerce
 *
 * @package MarsX
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Beam Checkout Payment Gateway Class
 */
class WC_Gateway_Beam extends WC_Payment_Gateway {

    /**
     * Beam API URL (Playground/Sandbox)
     */
    private $api_url = 'https://playground.api.beamcheckout.com/api/v1';

    /**
     * Merchant ID
     */
    private $merchant_id;

    /**
     * API Key
     */
    private $api_key;

    /**
     * Webhook Secret
     */
    private $webhook_secret;

    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'beam_checkout';
        $this->icon = get_stylesheet_directory_uri() . '/assets/images/beam-logo.png';
        $this->has_fields = false;
        $this->method_title = 'Beam Checkout';
        $this->method_description = 'รับชำระเงินผ่าน QR Code PromptPay ด้วย Beam Checkout';

        // Load settings
        $this->init_form_fields();
        $this->init_settings();

        // Get settings
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');

        // Get credentials from wp-config.php
        $this->merchant_id = defined('MARSX_BEAM_MERCHANT_ID') ? MARSX_BEAM_MERCHANT_ID : '';
        $this->api_key = defined('MARSX_BEAM_API_KEY') ? MARSX_BEAM_API_KEY : '';
        $this->webhook_secret = defined('MARSX_BEAM_WEBHOOK_SECRET') ? MARSX_BEAM_WEBHOOK_SECRET : '';

        // Save admin options
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // Handle language-based title/description
        add_filter('woocommerce_gateway_title', array($this, 'filter_gateway_title'), 10, 2);
        add_filter('woocommerce_gateway_description', array($this, 'filter_gateway_description'), 10, 2);
    }

    /**
     * Initialize form fields for admin settings
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => 'เปิดใช้งาน',
                'type'    => 'checkbox',
                'label'   => 'เปิดใช้งาน Beam Checkout',
                'default' => 'no',
            ),
            'title' => array(
                'title'       => 'ชื่อที่แสดง (ไทย)',
                'type'        => 'text',
                'description' => 'ชื่อวิธีการชำระเงินที่ลูกค้าเห็นในหน้า Checkout',
                'default'     => 'ชำระผ่าน QR Code',
                'desc_tip'    => true,
            ),
            'title_en' => array(
                'title'       => 'ชื่อที่แสดง (English)',
                'type'        => 'text',
                'description' => 'Payment method title for English pages',
                'default'     => 'Pay via QR Code',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'คำอธิบาย (ไทย)',
                'type'        => 'textarea',
                'description' => 'คำอธิบายวิธีการชำระเงิน',
                'default'     => 'สแกน QR Code เพื่อชำระเงินผ่าน PromptPay (ระบบจะพาไปหน้าชำระเงินของ Beam Checkout)',
            ),
            'description_en' => array(
                'title'       => 'คำอธิบาย (English)',
                'type'        => 'textarea',
                'description' => 'Payment method description for English pages',
                'default'     => 'Scan QR Code to pay via PromptPay (You will be redirected to Beam Checkout)',
            ),
        );
    }

    /**
     * Filter gateway title based on language
     */
    public function filter_gateway_title($title, $gateway_id) {
        if ($gateway_id !== $this->id) {
            return $title;
        }

        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

        if ($is_english) {
            return $this->get_option('title_en', 'Pay via QR Code');
        }

        return $title;
    }

    /**
     * Filter gateway description based on language
     */
    public function filter_gateway_description($description, $gateway_id) {
        if ($gateway_id !== $this->id) {
            return $description;
        }

        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

        if ($is_english) {
            return $this->get_option('description_en', 'Scan QR Code to pay via PromptPay');
        }

        return $description;
    }

    /**
     * Check if gateway is available (for checkout page)
     */
    public function is_available() {
        // Always allow in admin for configuration
        if (is_admin()) {
            return parent::is_available();
        }

        if ($this->enabled !== 'yes') {
            return false;
        }

        // Check if credentials are configured
        if (empty($this->merchant_id) || empty($this->api_key)) {
            return false;
        }

        return true;
    }

    /**
     * Admin Panel Options - show warning if credentials not set
     */
    public function admin_options() {
        // Show warning if credentials not configured
        if (empty($this->merchant_id) || empty($this->api_key)) {
            echo '<div class="notice notice-warning"><p>';
            echo '<strong>Beam Checkout:</strong> กรุณาเพิ่ม credentials ใน wp-config.php:<br>';
            echo '<code>define(\'MARSX_BEAM_MERCHANT_ID\', \'your-merchant-id\');</code><br>';
            echo '<code>define(\'MARSX_BEAM_API_KEY\', \'your-api-key\');</code><br>';
            echo '<code>define(\'MARSX_BEAM_WEBHOOK_SECRET\', \'your-webhook-secret\');</code>';
            echo '</p></div>';
        } else {
            echo '<div class="notice notice-success"><p>';
            echo '<strong>Beam Checkout:</strong> Credentials ถูกตั้งค่าเรียบร้อยแล้ว (Merchant ID: ' . esc_html($this->merchant_id) . ')';
            echo '</p></div>';
        }

        parent::admin_options();
    }

    /**
     * Process the payment
     *
     * @param int $order_id Order ID
     * @return array Result of payment processing
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        if (!$order) {
            wc_add_notice('ไม่พบข้อมูลคำสั่งซื้อ', 'error');
            return array('result' => 'failure');
        }

        // Create Beam purchase
        $beam_response = $this->create_beam_purchase($order);

        if (is_wp_error($beam_response)) {
            $error_message = $beam_response->get_error_message();
            wc_add_notice('เกิดข้อผิดพลาดในการสร้างลิงก์ชำระเงิน: ' . $error_message, 'error');
            return array('result' => 'failure');
        }

        // Check for id and url (Payment Links API v1 response)
        // API returns 'id' not 'paymentLinkId'
        $link_id = isset($beam_response['paymentLinkId']) ? $beam_response['paymentLinkId'] : (isset($beam_response['id']) ? $beam_response['id'] : '');
        $payment_url = isset($beam_response['url']) ? $beam_response['url'] : '';

        if (empty($link_id) || empty($payment_url)) {
            wc_add_notice('ไม่สามารถสร้างลิงก์ชำระเงินได้', 'error');
            return array('result' => 'failure');
        }

        // Save Beam payment link ID to order meta
        $order->update_meta_data('_beam_payment_link_id', $link_id);
        $order->update_meta_data('_beam_payment_url', $payment_url);
        $order->save();

        // Add order note
        $order->add_order_note(
            sprintf('Beam Checkout: Payment link created. ID: %s', $link_id)
        );

        // Return success with redirect to Beam payment page
        return array(
            'result'   => 'success',
            'redirect' => $payment_url,
        );
    }

    /**
     * Create Beam payment link via API
     *
     * @param WC_Order $order WooCommerce order
     * @return array|WP_Error Beam API response or error
     */
    private function create_beam_purchase($order) {
        $order_id = $order->get_id();
        $order_total = intval($order->get_total()); // Amount in THB (smallest unit)

        // Determine redirect URL based on checkout language
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

        // Also check session
        if (WC()->session) {
            $checkout_lang = WC()->session->get('marsx_checkout_lang');
            if ($checkout_lang === 'en') {
                $is_english = true;
            }
        }

        $redirect_url = $is_english
            ? home_url('/en/order-received/?order_id=' . $order_id . '&key=' . $order->get_order_key())
            : home_url('/order-received/?order_id=' . $order_id . '&key=' . $order->get_order_key());

        // Build order description
        $order_number = $order->get_order_number();
        $description = sprintf('Order #%s', $order_number);

        // Build request body for Payment Links API v1
        $body = array(
            'order' => array(
                'currency' => 'THB',
                'netAmount' => $order_total,
                'description' => $description,
                'referenceId' => (string) $order_id,
            ),
            'redirectUrl' => $redirect_url,
            'linkSettings' => array(
                'qrPromptPay' => array('isEnabled' => true),
                'card' => array('isEnabled' => false),
                'eWallets' => array('isEnabled' => false),
                'mobileBanking' => array('isEnabled' => false),
            ),
        );

        // Build API URL for Payment Links endpoint
        $api_url = $this->api_url . '/payment-links';

        // Build authorization header (Basic Auth)
        $auth = base64_encode($this->merchant_id . ':' . $this->api_key);

        // Make API request
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($body),
            'timeout' => 30,
        ));

        // Check for connection error
        if (is_wp_error($response)) {
            error_log('Beam API Error: ' . $response->get_error_message());
            return $response;
        }

        // Get response code
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        // Log for debugging
        error_log('Beam API Response Code: ' . $response_code);
        error_log('Beam API Response Body: ' . $response_body);

        // Parse response
        $data = json_decode($response_body, true);

        // Check for API error
        if ($response_code !== 200 && $response_code !== 201) {
            $error_message = isset($data['message']) ? $data['message'] : 'Unknown error';
            return new WP_Error('beam_api_error', $error_message);
        }

        return $data;
    }

    /**
     * Handle Beam webhook
     *
     * @param string $raw_body Raw POST body
     * @param string $signature X-Hub-Signature header value
     * @return bool Success or failure
     */
    public function handle_webhook($raw_body, $signature) {
        // Verify signature
        if (!$this->verify_webhook_signature($raw_body, $signature)) {
            error_log('Beam Webhook: Invalid signature');
            return false;
        }

        // Parse payload
        $payload = json_decode($raw_body, true);

        if (!$payload) {
            error_log('Beam Webhook: Invalid JSON payload');
            return false;
        }

        error_log('Beam Webhook Payload: ' . print_r($payload, true));

        // Get payment info (support both old purchaseId and new paymentLinkId)
        $payment_link_id = isset($payload['paymentLinkId']) ? $payload['paymentLinkId'] : '';
        $purchase_id = isset($payload['purchaseId']) ? $payload['purchaseId'] : '';
        $status = isset($payload['status']) ? $payload['status'] : '';
        $state = isset($payload['state']) ? $payload['state'] : $status;

        // Get order reference from payload
        $reference_id = '';
        if (isset($payload['order']['referenceId'])) {
            $reference_id = $payload['order']['referenceId'];
        } elseif (isset($payload['merchantReferenceId'])) {
            $reference_id = $payload['merchantReferenceId'];
        }

        $link_id = !empty($payment_link_id) ? $payment_link_id : $purchase_id;

        if (empty($link_id)) {
            error_log('Beam Webhook: Missing paymentLinkId/purchaseId');
            return false;
        }

        // Find order by reference ID (order ID)
        $order = null;

        if (!empty($reference_id)) {
            $order = wc_get_order($reference_id);
        }

        // If not found, try to find by payment link ID in meta
        if (!$order) {
            $orders = wc_get_orders(array(
                'meta_key' => '_beam_payment_link_id',
                'meta_value' => $link_id,
                'limit' => 1,
            ));

            if (!empty($orders)) {
                $order = $orders[0];
            }
        }

        // Fallback: try old purchase_id meta
        if (!$order && !empty($purchase_id)) {
            $orders = wc_get_orders(array(
                'meta_key' => '_beam_purchase_id',
                'meta_value' => $purchase_id,
                'limit' => 1,
            ));

            if (!empty($orders)) {
                $order = $orders[0];
            }
        }

        if (!$order) {
            error_log('Beam Webhook: Order not found for linkId: ' . $link_id);
            return false;
        }

        // Check if order is already completed
        if ($order->is_paid()) {
            error_log('Beam Webhook: Order already paid');
            return true;
        }

        // Handle payment complete (support both 'complete' and 'PAID' status)
        if ($state === 'complete' || $state === 'PAID') {
            // Update order status
            $order->payment_complete($link_id);

            // Add order note
            $order->add_order_note(
                sprintf('Beam Checkout: Payment completed. Link ID: %s', $link_id)
            );

            // Save payment details
            if (isset($payload['paymentMethod'])) {
                $order->update_meta_data('_beam_payment_method', $payload['paymentMethod']);
            }

            if (isset($payload['paymentId'])) {
                $order->update_meta_data('_beam_payment_id', $payload['paymentId']);
            }

            $order->save();

            error_log('Beam Webhook: Order #' . $order->get_id() . ' marked as paid');
            return true;
        }

        // Handle failed state (optional, Beam doesn't send webhook for failed)
        if ($state === 'failed') {
            $order->update_status('failed', 'Beam Checkout: Payment failed');
            return true;
        }

        return true;
    }

    /**
     * Verify webhook signature
     *
     * @param string $raw_body Raw POST body
     * @param string $signature X-Hub-Signature header value
     * @return bool Valid or not
     */
    private function verify_webhook_signature($raw_body, $signature) {
        if (empty($this->webhook_secret)) {
            error_log('Beam Webhook: Webhook secret not configured');
            return false;
        }

        if (empty($signature)) {
            error_log('Beam Webhook: No signature provided');
            return false;
        }

        // Decode base64 secret
        $secret = base64_decode($this->webhook_secret);

        // Calculate expected signature
        $expected = hash_hmac('sha256', $raw_body, $secret);

        // Remove 'sha256=' prefix if present
        $received = str_replace('sha256=', '', $signature);

        // Compare signatures
        $valid = hash_equals($expected, $received);

        if (!$valid) {
            error_log('Beam Webhook: Signature mismatch. Expected: ' . $expected . ', Received: ' . $received);
        }

        return $valid;
    }
}
