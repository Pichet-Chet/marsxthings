<?php
/**
 * Template Name: MarsX Login
 * Description: Custom login page for MarsX Things
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/my-account/'));
    exit;
}

// Handle login
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marsx_login'])) {
    $creds = array(
        'user_login'    => sanitize_text_field($_POST['email']),
        'user_password' => $_POST['password'],
        'remember'      => isset($_POST['remember'])
    );

    $user = wp_signon($creds, is_ssl());

    if (is_wp_error($user)) {
        $login_error = $user->get_error_message();
    } else {
        wp_redirect(home_url('/my-account/'));
        exit;
    }
}

// Get site info
$site_name = get_bloginfo('name');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo esc_html($site_name); ?></title>
    <?php wp_head(); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
        }

        /* Left Side - Mars Image */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('<?php echo get_template_directory_uri(); ?>/assets/images/mars-bg.jpg') center/cover no-repeat;
            opacity: 0.8;
        }

        .mars-planet {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle at 30% 30%, #e67e22 0%, #d35400 50%, #a04000 100%);
            border-radius: 50%;
            right: -150px;
            box-shadow:
                inset -30px -30px 60px rgba(0,0,0,0.4),
                0 0 100px rgba(230, 126, 34, 0.3);
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 40px;
            max-width: 500px;
        }

        .login-left-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-left-content .tagline {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .login-left-content p {
            font-size: 0.95rem;
            opacity: 0.8;
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
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: white;
            position: relative;
        }

        .lang-switch {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .lang-switch img {
            width: 30px;
            height: 20px;
            border-radius: 3px;
            cursor: pointer;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .login-form-wrapper h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
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
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #888;
            font-size: 1.2rem;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #f39c12;
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
            padding: 14px;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border: none;
            border-radius: 25px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(243, 156, 18, 0.4);
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #888;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #e0e0e0;
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
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.3s, border-color 0.3s;
        }

        .btn-google:hover {
            background: #f8f8f8;
            border-color: #ccc;
        }

        .btn-google img {
            width: 20px;
            height: 20px;
        }

        .signup-link {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #666;
        }

        .signup-link a {
            color: #f39c12;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #fff3f3;
            border: 1px solid #ffcdd2;
            color: #c62828;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        /* Decorative circles */
        .decor-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .decor-circle-1 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            right: -50px;
            opacity: 0.8;
        }

        .decor-circle-2 {
            width: 80px;
            height: 80px;
            bottom: 120px;
            right: 100px;
            opacity: 0.6;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-left {
                display: none;
            }

            .login-right {
                flex: none;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .login-right {
                padding: 20px;
            }

            .login-form-wrapper h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side -->
        <div class="login-left">
            <div class="mars-planet"></div>
            <div class="login-left-content">
                <h1>MarsX Things</h1>
                <p class="tagline">— A platform built for your future.</p>
                <p>If you don't have an account<br>You can <a href="<?php echo esc_url(home_url('/register/')); ?>">Register here!</a></p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="login-right">
            <div class="decor-circle decor-circle-1"></div>
            <div class="decor-circle decor-circle-2"></div>

            <div class="lang-switch">
                <?php if (function_exists('pll_the_languages')) : ?>
                    <?php pll_the_languages(array('show_flags' => 1, 'show_names' => 0)); ?>
                <?php endif; ?>
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
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" name="marsx_login" class="btn-login">Login</button>
                </form>

                <div class="divider">or continue with</div>

                <button type="button" class="btn-google" onclick="location.href='<?php echo esc_url(home_url('/wp-login.php?loginSocial=google')); ?>'">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Sign in with Google
                </button>

                <p class="signup-link">
                    Don't have an account? <a href="<?php echo esc_url(home_url('/register/')); ?>">Register here!</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>

    <?php wp_footer(); ?>
</body>
</html>
