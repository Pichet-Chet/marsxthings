<?php
/**
 * Template Name: MarsX Track Shipping (ไทย)
 * Description: หน้าติดตามพัสดุสำหรับ MarsX Things - ภาษาไทย
 */

get_header();
?>

<style>
/* Track Shipping Page Styles */
.marsx-track-shipping {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px 80px;
    font-family: 'Noto Sans Thai', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
}

.marsx-track-header {
    text-align: center;
    margin-bottom: 40px;
}

.marsx-track-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 12px;
}

.marsx-track-header p {
    font-size: 1rem;
    color: #666;
    margin: 0;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.marsx-track-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.marsx-info-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
}

.marsx-info-card-icon {
    width: 48px;
    height: 48px;
    background: var(--e-global-color-primary, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.marsx-info-card-icon svg {
    width: 24px;
    height: 24px;
    color: white;
}

.marsx-info-card h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px;
}

.marsx-info-card p {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
    line-height: 1.6;
}

.marsx-track-iframe-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid #eee;
}

.marsx-track-iframe-header {
    background: linear-gradient(135deg, var(--e-global-color-primary, #ff6b35) 0%, #ff8c5a 100%);
    padding: 20px 24px;
    color: white;
}

.marsx-track-iframe-header h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.marsx-track-iframe-header h2 svg {
    width: 24px;
    height: 24px;
}

.marsx-track-iframe {
    width: 100%;
    height: 700px;
    border: none;
    display: block;
}

.marsx-track-note {
    margin-top: 30px;
    padding: 20px 24px;
    background: #fff9f0;
    border-radius: 12px;
    border-left: 4px solid var(--e-global-color-primary, #ff6b35);
}

.marsx-track-note h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.marsx-track-note ul {
    margin: 0;
    padding-left: 20px;
    color: #555;
    font-size: 0.9rem;
    line-height: 1.8;
}

.marsx-track-contact {
    margin-top: 40px;
    text-align: center;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 12px;
}

.marsx-track-contact h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 12px;
}

.marsx-track-contact p {
    font-size: 0.9rem;
    color: #666;
    margin: 0 0 16px;
}

.marsx-track-contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--e-global-color-primary, #ff6b35);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.marsx-track-contact-btn:hover {
    filter: brightness(0.9);
    color: white;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .marsx-track-shipping {
        padding: 20px 15px 60px;
    }

    .marsx-track-header h1 {
        font-size: 1.5rem;
    }

    .marsx-track-iframe {
        height: 500px;
    }

    .marsx-info-card {
        padding: 20px;
    }
}
</style>

<div class="marsx-track-shipping">

    <!-- Header -->
    <div class="marsx-track-header">
        <h1>ติดตามพัสดุ</h1>
        <p>ตรวจสอบสถานะการจัดส่งสินค้าของคุณ โดยกรอกหมายเลขพัสดุในช่องค้นหาด้านล่าง</p>
    </div>

    <!-- Info Cards -->
    <div class="marsx-track-info">
        <div class="marsx-info-card">
            <div class="marsx-info-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
            </div>
            <h3>จัดส่งทั่วประเทศ</h3>
            <p>รองรับการติดตามพัสดุจากทุกบริษัทขนส่งชั้นนำ ทั้ง Kerry, Flash, J&T, Thailand Post และอื่นๆ</p>
        </div>

        <div class="marsx-info-card">
            <div class="marsx-info-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <h3>อัพเดทแบบเรียลไทม์</h3>
            <p>ข้อมูลสถานะพัสดุอัพเดทตลอด 24 ชั่วโมง ติดตามได้ทุกที่ทุกเวลา</p>
        </div>

        <div class="marsx-info-card">
            <div class="marsx-info-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h3>ตรวจสอบง่าย</h3>
            <p>เพียงกรอกหมายเลขพัสดุ ระบบจะแสดงสถานะและประวัติการจัดส่งทันที</p>
        </div>
    </div>

    <!-- Tracking iFrame -->
    <div class="marsx-track-iframe-container">
        <div class="marsx-track-iframe-header">
            <h2>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                </svg>
                ค้นหาพัสดุ
            </h2>
        </div>
        <iframe
            src="https://www.business-idea.co.th/tracking"
            class="marsx-track-iframe"
            title="ติดตามพัสดุ"
            loading="lazy"
            allowfullscreen>
        </iframe>
    </div>

    <!-- Note -->
    <div class="marsx-track-note">
        <h4>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            หมายเหตุ
        </h4>
        <ul>
            <li>หมายเลขพัสดุจะถูกส่งไปยังอีเมลของคุณหลังจากสินค้าถูกจัดส่ง</li>
            <li>คุณสามารถดูหมายเลขพัสดุได้ในหน้า "คำสั่งซื้อของฉัน"</li>
            <li>หากไม่พบข้อมูลพัสดุ กรุณารอ 1-2 ชั่วโมงหลังได้รับหมายเลขติดตาม</li>
            <li>สำหรับสินค้าสั่งทำพิเศษ อาจใช้เวลาจัดส่ง 7-14 วันทำการ</li>
        </ul>
    </div>

    <!-- Contact -->
    <div class="marsx-track-contact">
        <h3>มีปัญหาในการติดตามพัสดุ?</h3>
        <p>ทีมงานพร้อมช่วยเหลือคุณ ติดต่อเราได้ทันที</p>
        <a href="<?php echo home_url('/contact/'); ?>" class="marsx-track-contact-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            ติดต่อเรา
        </a>
    </div>

</div>

<?php get_footer(); ?>
