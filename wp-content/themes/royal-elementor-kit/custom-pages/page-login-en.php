<?php
/**
 * Template Name: MarsX Login (English)
 * Description: Login page for MarsX Things - English
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/en/'));
    exit;
}

// Handle login
$login_error = '';

// Check for Google login error
if (isset($_GET['google_error'])) {
    $login_error = sanitize_text_field($_GET['google_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marsx_login'])) {
    // Verify nonce
    if (!isset($_POST['marsx_login_nonce']) || !wp_verify_nonce($_POST['marsx_login_nonce'], 'marsx_login_action')) {
        $login_error = 'Security check failed. Please try again.';
    } else {
        $creds = array(
            'user_login'    => sanitize_text_field($_POST['email']),
            'user_password' => $_POST['password'],
            'remember'      => isset($_POST['remember'])
        );

        $user = wp_signon($creds, is_ssl());

        if (is_wp_error($user)) {
            $login_error = 'Invalid email or password.';
        } else {
            wp_redirect(home_url('/en/my-account/'));
            exit;
        }
    }
}

$register_url = home_url('/en/register/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php bloginfo('name'); ?></title>
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

        /* Right Side - Login Form */
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
            font-size: 3rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 35px;
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

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #f39c12;
            cursor: pointer;
        }

        .remember-me span {
            color: #555;
        }

        .forgot-password {
            color: #f39c12;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-login {
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
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 28px 0;
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
            padding: 15px;
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
            margin-bottom: 25px;
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
                font-size: 2.2rem;
                margin-bottom: 25px;
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
                <p>Don't have an account yet?<br><a href="<?php echo esc_url($register_url); ?>">Register here!</a></p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="decor-arcs">
                <div class="decor-arc decor-arc-1"></div>
                <div class="decor-arc decor-arc-2"></div>
                <div class="decor-arc decor-arc-3"></div>
            </div>
            <div class="decor-circle-filled"></div>

            <div class="lang-switch">
                <a href="<?php echo home_url('/login/'); ?>" title="ภาษาไทย">
                    <img src="https://flagcdn.com/w40/th.png" alt="Thai">
                </a>
            </div>

            <div class="login-form-wrapper">
                <h2>Welcome!</h2>

                <?php if ($login_error) : ?>
                    <div class="error-message"><?php echo esc_html($login_error); ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <?php wp_nonce_field('marsx_login_action', 'marsx_login_nonce'); ?>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" placeholder="name@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Show/Hide password">
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

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="<?php echo esc_url(home_url('/en/lost-password/')); ?>" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" name="marsx_login" class="btn-login">Login</button>
                </form>

                <div class="divider">or</div>

                <a href="<?php echo esc_url(marsx_get_google_auth_url('en')); ?>" class="btn-google">
                    <svg viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Login with Google
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.querySelector('.eye-open');
            const eyeClosed = document.querySelector('.eye-closed');

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
    </script>

    <?php wp_footer(); ?>
</body>
</html>
