<?php
/**
 * Template Name: MarsX Lost Password (English)
 * Description: Lost password page for MarsX Things - English
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/en/'));
    exit;
}

// Handle lost password
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marsx_lost_password'])) {
    // Verify nonce
    if (!isset($_POST['marsx_lost_password_nonce']) || !wp_verify_nonce($_POST['marsx_lost_password_nonce'], 'marsx_lost_password_action')) {
        $message = 'Security check failed. Please try again.';
        $message_type = 'error';
    } else {
        $user_login = sanitize_text_field($_POST['user_login']);

        if (empty($user_login)) {
            $message = 'Please enter your email or username.';
            $message_type = 'error';
        } else {
            // Use WordPress retrieve_password function
            $result = retrieve_password($user_login);

            if (is_wp_error($result)) {
                $message = $result->get_error_message();
                $message_type = 'error';
            } else {
                $message = 'A password reset link has been sent to your email. Please check your inbox.';
                $message_type = 'success';
            }
        }
    }
}

$login_url = home_url('/en/login/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Left Side - Mars Background */
        .login-left {
            flex: 0 0 50%;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 60px;
            overflow: hidden;
        }

        .login-left::before {
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
        .login-left::after {
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

        .login-left-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 450px;
            padding-bottom: 60px;
        }

        .login-left-content h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            font-style: italic;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .login-left-content .tagline {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-style: italic;
            opacity: 0.95;
            margin-bottom: 40px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .login-left-content p {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .login-left-content a {
            color: #f39c12;
            text-decoration: none;
            font-weight: 600;
        }

        .login-left-content a:hover {
            text-decoration: underline;
        }

        /* Right Side - Form */
        .login-right {
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

        .login-form-wrapper {
            width: 100%;
            max-width: 380px;
            position: relative;
            z-index: 2;
        }

        .login-form-wrapper h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .login-form-wrapper .subtitle {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 15px 18px;
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

        .btn-submit {
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

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-to-login {
            text-align: center;
            margin-top: 30px;
            font-size: 0.95rem;
            color: #666;
        }

        .back-to-login a {
            color: #f39c12;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        .message {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .message.error {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
        }

        .message.success {
            background: #f0fff4;
            border: 1px solid #c6f6d5;
            color: #276749;
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
            .login-left {
                flex: 0 0 45%;
                padding: 40px;
            }
            .login-right {
                flex: 0 0 55%;
                padding: 40px;
            }
        }

        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
            }

            .login-left {
                flex: none;
                min-height: 350px;
                padding: 40px;
                justify-content: flex-end;
            }

            .login-left::after {
                display: none;
            }

            .login-left-content {
                padding-bottom: 30px;
            }

            .login-left-content h1 {
                font-size: 2.2rem;
            }

            .login-right {
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
            .login-left {
                min-height: 280px;
                padding: 30px;
            }

            .login-left-content h1 {
                font-size: 1.8rem;
            }

            .login-left-content .tagline {
                font-size: 1rem;
                margin-bottom: 25px;
            }

            .login-right {
                padding: 40px 20px;
            }

            .login-form-wrapper h2 {
                font-size: 2rem;
                margin-bottom: 10px;
            }

            .form-group input {
                padding: 14px 16px;
            }

            .lang-switch {
                top: 15px;
                right: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Mars Background -->
        <div class="login-left">
            <div class="login-left-content">
                <h1>MarsX Things</h1>
                <p class="tagline">— A platform built for your future.</p>
                <p>Remember your password?<br><a href="<?php echo esc_url($login_url); ?>">Back to login</a></p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="login-right">
            <div class="decor-arcs">
                <div class="decor-arc decor-arc-1"></div>
                <div class="decor-arc decor-arc-2"></div>
                <div class="decor-arc decor-arc-3"></div>
            </div>
            <div class="decor-circle-filled"></div>

            <div class="lang-switch">
                <a href="<?php echo home_url('/lost-password/'); ?>" title="ภาษาไทย">
                    <img src="https://flagcdn.com/w40/th.png" alt="Thai">
                </a>
            </div>

            <div class="login-form-wrapper">
                <h2>Forgot Password?</h2>
                <p class="subtitle">Enter your email or username. We'll send you a link to reset your password.</p>

                <?php if ($message) : ?>
                    <div class="message <?php echo esc_attr($message_type); ?>"><?php echo esc_html($message); ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <?php wp_nonce_field('marsx_lost_password_action', 'marsx_lost_password_nonce'); ?>

                    <div class="form-group">
                        <label for="user_login">Email or Username</label>
                        <input type="text" id="user_login" name="user_login" placeholder="name@example.com" required>
                    </div>

                    <button type="submit" name="marsx_lost_password" class="btn-submit">Send Reset Link</button>
                </form>

                <div class="back-to-login">
                    <a href="<?php echo esc_url($login_url); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
