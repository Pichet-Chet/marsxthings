<?php
/**
 * MarsX Custom 404 Page
 */

get_header();

// Detect language
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$is_english = (strpos($request_uri, '/en/') !== false || strpos($request_uri, '/en') === 0);

// Text translations
$texts = $is_english ? array(
    'title' => '404',
    'subtitle' => 'Page Not Found',
    'message' => 'Sorry, the page you are looking for doesn\'t exist or has been moved.',
    'home_btn' => 'Back to Home',
    'shop_btn' => 'Browse Products',
    'home_url' => home_url('/en/'),
    'shop_url' => home_url('/en/products/'),
) : array(
    'title' => '404',
    'subtitle' => 'ไม่พบหน้าที่คุณต้องการ',
    'message' => 'ขออภัย หน้าที่คุณกำลังมองหาไม่มีอยู่หรือถูกย้ายไปแล้ว',
    'home_btn' => 'กลับหน้าหลัก',
    'shop_btn' => 'เลือกซื้อสินค้า',
    'home_url' => home_url('/'),
    'shop_url' => home_url('/products/'),
);
?>

<style>
    .marsx-404-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 120px 20px 60px;
        background: linear-gradient(135deg, #fefefe 0%, #f8f9fa 100%);
    }

    .marsx-404-container {
        text-align: center;
        max-width: 600px;
    }

    .marsx-404-icon {
        margin-bottom: 30px;
    }

    .marsx-404-icon svg {
        width: 180px;
        height: 180px;
        color: #f39c12;
    }

    .marsx-404-title {
        font-family: 'Poppins', 'Noto Sans Thai', sans-serif;
        font-size: 8rem;
        font-weight: 800;
        color: #f39c12;
        line-height: 1;
        margin: 0 0 10px 0;
        text-shadow: 4px 4px 0 #ffecd2;
    }

    .marsx-404-subtitle {
        font-family: 'Noto Sans Thai', 'Poppins', sans-serif;
        font-size: 1.8rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 20px 0;
    }

    .marsx-404-message {
        font-family: 'Noto Sans Thai', 'Poppins', sans-serif;
        font-size: 1.1rem;
        color: #666;
        margin: 0 0 40px 0;
        line-height: 1.6;
    }

    .marsx-404-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .marsx-404-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        border-radius: 12px;
        font-family: 'Noto Sans Thai', 'Poppins', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .marsx-404-btn-primary {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
    }

    .marsx-404-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
        color: white;
    }

    .marsx-404-btn-secondary {
        background: white;
        color: #1a1a1a;
        border: 2px solid #e0e0e0;
    }

    .marsx-404-btn-secondary:hover {
        border-color: #f39c12;
        color: #f39c12;
        transform: translateY(-2px);
    }

    .marsx-404-btn svg {
        width: 20px;
        height: 20px;
    }

    .marsx-404-decoration {
        margin-top: 60px;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .marsx-404-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #f39c12;
        opacity: 0.3;
    }

    .marsx-404-dot:nth-child(2) {
        opacity: 0.5;
    }

    .marsx-404-dot:nth-child(3) {
        opacity: 0.7;
    }

    .marsx-404-dot:nth-child(4) {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .marsx-404-title {
            font-size: 5rem;
        }

        .marsx-404-subtitle {
            font-size: 1.4rem;
        }

        .marsx-404-message {
            font-size: 1rem;
        }

        .marsx-404-icon svg {
            width: 120px;
            height: 120px;
        }

        .marsx-404-actions {
            flex-direction: column;
            align-items: center;
        }

        .marsx-404-btn {
            width: 100%;
            max-width: 280px;
            justify-content: center;
        }
    }
</style>

<div class="marsx-404-wrapper">
    <div class="marsx-404-container">
        <!-- Icon -->
        <div class="marsx-404-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                <line x1="9" y1="9" x2="9.01" y2="9"></line>
                <line x1="15" y1="9" x2="15.01" y2="9"></line>
            </svg>
        </div>

        <!-- Title -->
        <h1 class="marsx-404-title"><?php echo esc_html($texts['title']); ?></h1>

        <!-- Subtitle -->
        <h2 class="marsx-404-subtitle"><?php echo esc_html($texts['subtitle']); ?></h2>

        <!-- Message -->
        <p class="marsx-404-message"><?php echo esc_html($texts['message']); ?></p>

        <!-- Action Buttons -->
        <div class="marsx-404-actions">
            <a href="<?php echo esc_url($texts['home_url']); ?>" class="marsx-404-btn marsx-404-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <?php echo esc_html($texts['home_btn']); ?>
            </a>
            <a href="<?php echo esc_url($texts['shop_url']); ?>" class="marsx-404-btn marsx-404-btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <?php echo esc_html($texts['shop_btn']); ?>
            </a>
        </div>

        <!-- Decoration -->
        <div class="marsx-404-decoration">
            <div class="marsx-404-dot"></div>
            <div class="marsx-404-dot"></div>
            <div class="marsx-404-dot"></div>
            <div class="marsx-404-dot"></div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
