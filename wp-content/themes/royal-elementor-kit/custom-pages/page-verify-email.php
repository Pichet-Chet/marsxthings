<?php
/**
 * Template Name: MarsX Verify Email (ไทย)
 * Description: หน้ายืนยันอีเมลสำหรับ MarsX Things - ภาษาไทย
 */

// Process verification
$user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : 0;
$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';

$verification_result = array('success' => false, 'error' => '');
$page_status = 'error'; // error, success, already_verified, expired

if ($user_id && $token) {
    $verification_result = marsx_verify_email_token($user_id, $token);

    if ($verification_result['success']) {
        if ($verification_result['error'] === 'already_verified') {
            $page_status = 'already_verified';
        } else {
            $page_status = 'success';
        }
    } else {
        if ($verification_result['error'] === 'token_expired') {
            $page_status = 'expired';
        } else {
            $page_status = 'error';
        }
    }
} else {
    $page_status = 'error';
}

$login_url = home_url('/login/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันอีเมล - <?php bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #fff9f0 0%, #fff 50%, #f8f9fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-container {
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .verify-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 50px 40px;
        }

        .verify-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .verify-icon.success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }

        .verify-icon.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .verify-icon.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .verify-icon.info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .verify-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            fill: none;
        }

        .verify-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .verify-message {
            font-size: 1rem;
            color: #666;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 40px;
            background: linear-gradient(135deg, #f5a623 0%, #f39c12 100%);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 30px;
            background: white;
            color: #f39c12;
            text-decoration: none;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid #f39c12;
            cursor: pointer;
            font-family: inherit;
            margin-top: 15px;
        }

        .btn-secondary:hover {
            background: #fff9f0;
        }

        .logo {
            margin-bottom: 40px;
        }

        .logo h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            font-style: italic;
            color: #f39c12;
        }

        /* Hide WooCommerce elements */
        .xoo-wsc-modal,
        .xoo-wsc-container,
        .floating-cart,
        #xoo-wsc-w-container {
            display: none !important;
        }

        /* Decorative elements */
        .decor-circle {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
        }

        .decor-circle-1 {
            width: 300px;
            height: 300px;
            background: rgba(243, 156, 18, 0.05);
            top: -100px;
            right: -100px;
        }

        .decor-circle-2 {
            width: 200px;
            height: 200px;
            background: rgba(243, 156, 18, 0.08);
            bottom: -50px;
            left: -50px;
        }

        @media (max-width: 480px) {
            .verify-card {
                padding: 40px 25px;
            }

            .verify-title {
                font-size: 1.5rem;
            }

            .verify-icon {
                width: 80px;
                height: 80px;
            }

            .verify-icon svg {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="decor-circle decor-circle-1"></div>
    <div class="decor-circle decor-circle-2"></div>

    <div class="verify-container">
        <div class="logo">
            <h1>MarsX Things</h1>
        </div>

        <div class="verify-card">
            <?php if ($page_status === 'success') : ?>
                <div class="verify-icon success">
                    <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <h2 class="verify-title">ยืนยันอีเมลสำเร็จ!</h2>
                <p class="verify-message">
                    ยินดีด้วย! อีเมลของคุณได้รับการยืนยันเรียบร้อยแล้ว<br>
                    คุณสามารถเข้าสู่ระบบเพื่อเริ่มต้นช้อปปิ้งได้ทันที
                </p>
                <a href="<?php echo esc_url($login_url . '?verified=1'); ?>" class="btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    เข้าสู่ระบบ
                </a>

            <?php elseif ($page_status === 'already_verified') : ?>
                <div class="verify-icon info">
                    <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <h2 class="verify-title">อีเมลยืนยันแล้ว</h2>
                <p class="verify-message">
                    อีเมลของคุณได้รับการยืนยันเรียบร้อยแล้ว<br>
                    คุณสามารถเข้าสู่ระบบได้ทันที
                </p>
                <a href="<?php echo esc_url($login_url); ?>" class="btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    เข้าสู่ระบบ
                </a>

            <?php elseif ($page_status === 'expired') : ?>
                <div class="verify-icon warning">
                    <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h2 class="verify-title">ลิงก์หมดอายุ</h2>
                <p class="verify-message">
                    ลิงก์ยืนยันอีเมลนี้หมดอายุแล้ว<br>
                    กรุณาไปที่หน้าเข้าสู่ระบบเพื่อขอลิงก์ใหม่
                </p>
                <a href="<?php echo esc_url($login_url); ?>" class="btn-primary">
                    ไปหน้าเข้าสู่ระบบ
                </a>

            <?php else : ?>
                <div class="verify-icon error">
                    <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <h2 class="verify-title">ลิงก์ไม่ถูกต้อง</h2>
                <p class="verify-message">
                    ลิงก์ยืนยันอีเมลนี้ไม่ถูกต้องหรือถูกใช้งานไปแล้ว<br>
                    กรุณาตรวจสอบลิงก์อีกครั้ง หรือขอลิงก์ใหม่
                </p>
                <a href="<?php echo esc_url($login_url); ?>" class="btn-primary">
                    ไปหน้าเข้าสู่ระบบ
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
