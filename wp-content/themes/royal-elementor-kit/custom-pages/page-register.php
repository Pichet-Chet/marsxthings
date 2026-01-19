<?php
/**
 * Template Name: MarsX Register (ไทย)
 * Description: หน้าสมัครสมาชิกสำหรับ MarsX Things - ภาษาไทย
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/my-account/'));
    exit;
}

// Handle registration
$register_error = '';
$register_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marsx_register'])) {
    // Verify nonce
    if (!isset($_POST['marsx_register_nonce']) || !wp_verify_nonce($_POST['marsx_register_nonce'], 'marsx_register_action')) {
        $register_error = 'การตรวจสอบความปลอดภัยล้มเหลว กรุณาลองใหม่อีกครั้ง';
    } else {
        // Verify reCAPTCHA v3
        $recaptcha_token = isset($_POST['recaptcha_token']) ? sanitize_text_field($_POST['recaptcha_token']) : '';
        $recaptcha_result = marsx_verify_recaptcha_v3($recaptcha_token, 'register', 0.5);

        if (!$recaptcha_result['success']) {
            $register_error = 'การตรวจสอบความปลอดภัยล้มเหลว กรุณาลองใหม่อีกครั้ง';
        } else {
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validation
        if (empty($first_name) || empty($last_name)) {
            $register_error = 'กรุณากรอกชื่อและนามสกุล';
        } elseif (empty($email) || !is_email($email)) {
            $register_error = 'กรุณากรอกอีเมลที่ถูกต้อง';
        } elseif (email_exists($email)) {
            $register_error = 'อีเมลนี้ถูกใช้งานแล้ว';
        } elseif (strlen($password) < 12) {
            $register_error = 'รหัสผ่านต้องมีอย่างน้อย 12 ตัวอักษร';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $register_error = 'รหัสผ่านต้องมีตัวอักษรพิมพ์ใหญ่อย่างน้อย 1 ตัว';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $register_error = 'รหัสผ่านต้องมีตัวอักษรพิมพ์เล็กอย่างน้อย 1 ตัว';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $register_error = 'รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว';
        } elseif (!preg_match('/[!@#$%^&*]/', $password)) {
            $register_error = 'รหัสผ่านต้องมีอักขระพิเศษ (!@#$%^&*) อย่างน้อย 1 ตัว';
        } elseif ($password !== $confirm_password) {
            $register_error = 'รหัสผ่านไม่ตรงกัน';
        } else {
            // Create user
            $username = sanitize_user(strtolower($first_name . '.' . $last_name));
            $username_base = $username;
            $counter = 1;

            // Ensure unique username
            while (username_exists($username)) {
                $username = $username_base . $counter;
                $counter++;
            }

            $user_id = wp_create_user($username, $password, $email);

            if (is_wp_error($user_id)) {
                $register_error = $user_id->get_error_message();
            } else {
                // Update user meta
                wp_update_user(array(
                    'ID' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $first_name . ' ' . $last_name
                ));

                // Set user role (customer for WooCommerce)
                $user = new WP_User($user_id);
                $user->set_role('customer');

                // Auto login
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);

                // Redirect to my account
                wp_redirect(home_url('/my-account/'));
                exit;
            }
        }
        }
    }
}

$login_url = home_url('/login/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - <?php bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
    <?php if (marsx_is_recaptcha_enabled()) : ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr(marsx_get_recaptcha_site_key()); ?>"></script>
    <?php endif; ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .register-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Left Side - Mars Background */
        .register-left {
            flex: 0 0 50%;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 60px;
            overflow: hidden;
        }

        .register-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('<?php echo get_template_directory_uri(); ?>/assets/images/login-cover.jpg') center/cover no-repeat;
            z-index: -2;
        }

        /* White curved overlay */
        .register-left::after {
            content: '';
            position: absolute;
            top: -10%;
            right: -50px;
            width: 150px;
            height: 120%;
            background: white;
            border-radius: 100% 0 0 100% / 50%;
            z-index: 1;
        }

        .register-left-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 450px;
            padding-bottom: 60px;
        }

        .register-left-content h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            font-style: italic;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .register-left-content .tagline {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-style: italic;
            opacity: 0.95;
            margin-bottom: 40px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .register-left-content p {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .register-left-content a {
            color: #f39c12;
            text-decoration: none;
            font-weight: 600;
        }

        .register-left-content a:hover {
            text-decoration: underline;
        }

        /* Right Side - Register Form */
        .register-right {
            flex: 0 0 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 60px;
            background: white;
            position: relative;
            overflow: hidden;
        }

        /* Language Switch */
        .lang-switch {
            position: absolute;
            top: 25px;
            right: 30px;
            z-index: 10;
        }

        .lang-switch a {
            display: block;
        }

        .lang-switch img {
            width: 32px;
            height: 24px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }

        .lang-switch img:hover {
            transform: scale(1.1);
        }

        .register-form-wrapper {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 2;
        }

        .register-form-wrapper h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .register-form-wrapper .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: #fff;
        }

        .form-group input:focus {
            outline: none;
            border-color: #f39c12;
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #666;
        }

        /* Password Strength Section */
        .password-options {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .password-options-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .password-requirements {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #666;
        }

        .requirement-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .requirement-checkbox.checked {
            background: #22c55e;
            border-color: #22c55e;
        }

        .requirement-checkbox svg {
            width: 12px;
            height: 12px;
            stroke: white;
            stroke-width: 3;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .requirement-checkbox.checked svg {
            opacity: 1;
        }

        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #999;
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 38%;
            height: 1px;
            background: #e5e5e5;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .btn-google {
            width: 100%;
            padding: 14px;
            background: white;
            border: 2px solid #f39c12;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #f39c12;
            transition: background 0.3s, color 0.3s;
        }

        .btn-google:hover {
            background: #fff9f0;
        }

        .btn-google svg {
            width: 22px;
            height: 22px;
        }

        .error-message {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .success-message {
            background: #f0fff4;
            border: 1px solid #c6f6d5;
            color: #276749;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        /* Hide WooCommerce cart and other floating widgets */
        .xoo-wsc-modal,
        .xoo-wsc-container,
        .floating-cart,
        .cart-icon-float,
        .woocommerce-cart-icon,
        [class*="cart-float"],
        [class*="floating-cart"],
        .xoo-wsc-basket,
        .cart-contents-count,
        .widget_shopping_cart,
        #xoo-wsc-w-container {
            display: none !important;
        }

        /* Decorative arcs - Right side */
        .decor-arcs {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 300px;
            height: 300px;
            overflow: hidden;
            pointer-events: none;
        }

        .decor-arc {
            position: absolute;
            border: 3px solid #f5a623;
            border-radius: 50%;
            background: transparent;
        }

        .decor-arc-1 {
            width: 350px;
            height: 350px;
            bottom: -180px;
            right: -180px;
        }

        .decor-arc-2 {
            width: 280px;
            height: 280px;
            bottom: -140px;
            right: -140px;
        }

        .decor-arc-3 {
            width: 210px;
            height: 210px;
            bottom: -100px;
            right: -100px;
        }

        /* Orange filled circle */
        .decor-circle-filled {
            position: absolute;
            width: 180px;
            height: 180px;
            bottom: -50px;
            right: -50px;
            background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
            border-radius: 50%;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .register-left {
                flex: 0 0 45%;
                padding: 40px;
            }
            .register-right {
                flex: 0 0 55%;
                padding: 40px;
            }
        }

        @media (max-width: 992px) {
            .register-container {
                flex-direction: column;
            }

            .register-left {
                flex: none;
                min-height: 300px;
                padding: 40px;
                justify-content: flex-end;
            }

            .register-left::after {
                display: none;
            }

            .register-left-content {
                padding-bottom: 30px;
            }

            .register-left-content h1 {
                font-size: 2.2rem;
            }

            .register-right {
                flex: none;
                width: 100%;
                padding: 50px 30px;
            }

            .decor-arcs {
                width: 200px;
                height: 200px;
            }

            .decor-arc-1 {
                width: 250px;
                height: 250px;
                bottom: -130px;
                right: -130px;
            }

            .decor-arc-2 {
                width: 200px;
                height: 200px;
                bottom: -100px;
                right: -100px;
            }

            .decor-arc-3 {
                width: 150px;
                height: 150px;
                bottom: -70px;
                right: -70px;
            }

            .decor-circle-filled {
                width: 120px;
                height: 120px;
                bottom: -30px;
                right: -30px;
            }
        }

        @media (max-width: 480px) {
            .register-left {
                min-height: 250px;
                padding: 30px;
            }

            .register-left-content h1 {
                font-size: 1.8rem;
            }

            .register-left-content .tagline {
                font-size: 1rem;
                margin-bottom: 25px;
            }

            .register-right {
                padding: 40px 20px;
            }

            .register-form-wrapper h2 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-group input {
                padding: 12px 14px;
            }

            .lang-switch {
                top: 15px;
                right: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Mars Background -->
        <div class="register-left">
            <div class="register-left-content">
                <h1>MarsX Things</h1>
                <p class="tagline">— A platform built for your future.</p>
                <p>หากคุณมีบัญชีอยู่แล้ว<br>สามารถ <a href="<?php echo esc_url($login_url); ?>">เข้าสู่ระบบที่นี่ !</a></p>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="register-right">
            <div class="decor-arcs">
                <div class="decor-arc decor-arc-1"></div>
                <div class="decor-arc decor-arc-2"></div>
                <div class="decor-arc decor-arc-3"></div>
            </div>
            <div class="decor-circle-filled"></div>

            <div class="lang-switch">
                <a href="<?php echo home_url('/en/register/'); ?>" title="English">
                    <img src="https://flagcdn.com/w40/gb.png" alt="English">
                </a>
            </div>

            <div class="register-form-wrapper">
                <h2>สมัครสมาชิก</h2>
                <p class="subtitle">สร้างบัญชีใหม่เพื่อเริ่มต้นช้อปปิ้งกับเรา</p>

                <?php if ($register_error) : ?>
                    <div class="error-message"><?php echo esc_html($register_error); ?></div>
                <?php endif; ?>

                <form method="post" action="" id="register-form">
                    <?php wp_nonce_field('marsx_register_action', 'marsx_register_nonce'); ?>
                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                    <input type="hidden" name="marsx_register" value="1">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">ชื่อ</label>
                            <input type="text" id="first_name" name="first_name" placeholder="ชื่อของคุณ" value="<?php echo isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">นามสกุล</label>
                            <input type="text" id="last_name" name="last_name" placeholder="นามสกุลของคุณ" value="<?php echo isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">อีเมล</label>
                        <input type="email" id="email" name="email" placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">รหัสผ่าน</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="สร้างรหัสผ่านที่แข็งแรง" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')" aria-label="แสดง/ซ่อนรหัสผ่าน">
                                <svg class="eye-open" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="ยืนยันรหัสผ่านอีกครั้ง" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')" aria-label="แสดง/ซ่อนรหัสผ่าน">
                                <svg class="eye-open-confirm" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed-confirm" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Password Options Section -->
                    <div class="password-options">
                        <div class="password-options-title">การกำหนดรัหสผ่าน</div>

                        <div class="password-requirements">
                            <div class="requirement-item">
                                <div class="requirement-checkbox" id="req-length">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <span>อย่างน้อย 12 ตัวอักษร</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-checkbox" id="req-uppercase">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <span>ตัวอักษรพิมพ์ใหญ่ (A-Z)</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-checkbox" id="req-lowercase">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <span>ตัวอักษรพิมพ์เล็ก (a-z)</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-checkbox" id="req-numbers">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <span>ตัวเลข (0-9)</span>
                            </div>
                            <div class="requirement-item">
                                <div class="requirement-checkbox" id="req-symbols">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <span>สัญลักษณ์ (!@#$%^&*)</span>
                            </div>
                        </div>
                    </div>

                    

                    <button type="submit" name="marsx_register" class="btn-register" id="btn-register">สมัครสมาชิก</button>
                </form>

                <div class="divider">หรือ</div>

                <button type="button" class="btn-google" onclick="location.href='<?php echo esc_url(home_url('/wp-login.php?loginSocial=google')); ?>'">
                    <svg viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    สมัครด้วย Google
                </button>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const isMain = fieldId === 'password';
            const eyeOpen = document.querySelector(isMain ? '.eye-open' : '.eye-open-confirm');
            const eyeClosed = document.querySelector(isMain ? '.eye-closed' : '.eye-closed-confirm');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }

        // Password validation
        const passwordInput = document.getElementById('password');
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqLowercase = document.getElementById('req-lowercase');
        const reqNumbers = document.getElementById('req-numbers');
        const reqSymbols = document.getElementById('req-symbols');

        function checkRequirement(element, isValid) {
            if (isValid) {
                element.classList.add('checked');
            } else {
                element.classList.remove('checked');
            }
        }

        passwordInput.addEventListener('input', function() {
            const password = this.value;

            checkRequirement(reqLength, password.length >= 12);
            checkRequirement(reqUppercase, /[A-Z]/.test(password));
            checkRequirement(reqLowercase, /[a-z]/.test(password));
            checkRequirement(reqNumbers, /[0-9]/.test(password));
            checkRequirement(reqSymbols, /[!@#$%^&*]/.test(password));
        });

        <?php if (marsx_is_recaptcha_enabled()) : ?>
        // reCAPTCHA v3 - Get token before form submit
        (function() {
            var form = document.getElementById('register-form');
            var tokenField = document.getElementById('recaptcha_token');
            var siteKey = '<?php echo esc_js(marsx_get_recaptcha_site_key()); ?>';

            function handleSubmit(e) {
                e.preventDefault();
                e.stopPropagation();

                grecaptcha.ready(function() {
                    grecaptcha.execute(siteKey, {action: 'register'})
                        .then(function(token) {
                            tokenField.value = token;
                            form.removeEventListener('submit', handleSubmit);
                            HTMLFormElement.prototype.submit.call(form);
                        })
                        .catch(function(error) {
                            console.error('reCAPTCHA error:', error);
                            alert('reCAPTCHA Error: ' + error.message);
                        });
                });
            }

            form.addEventListener('submit', handleSubmit);
        })();
        <?php endif; ?>
    </script>

    <?php wp_footer(); ?>
</body>
</html>
