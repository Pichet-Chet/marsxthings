# Beam Checkout Payment Flow

## Sequence Diagram

```mermaid
sequenceDiagram
    participant C as ลูกค้า
    participant W as WooCommerce
    participant B as Beam API
    participant BP as Beam Payment Page
    participant WH as Webhook Handler

    C->>W: 1. กดสั่งซื้อ (Place Order)
    W->>W: 2. สร้าง Order (Pending)
    W->>B: 3. POST /api/v1/payment-links
    B-->>W: 4. Return payment link URL
    W->>W: 5. บันทึก payment_link_id
    W-->>C: 6. Redirect to Beam

    C->>BP: 7. เปิดหน้า Beam Checkout
    BP->>BP: 8. แสดง QR Code
    C->>BP: 9. สแกน QR จ่ายเงิน
    BP-->>C: 10. Redirect to Thank You

    BP->>WH: 11. POST /beam-webhook/
    WH->>WH: 12. Verify signature
    WH->>W: 13. Update Order → Processing
    W->>C: 14. ส่ง Email ยืนยัน
```

## Flowchart

```mermaid
flowchart TD
    A[ลูกค้าเลือกสินค้า] --> B[หน้า Cart]
    B --> C[หน้า Checkout]
    C --> D{เลือก Payment}
    D --> E[ชำระผ่าน QR Code]
    E --> F[กด สั่งซื้อ]

    F --> G[WooCommerce สร้าง Order]
    G --> H[เรียก Beam API]
    H --> I[ได้ Payment Link]
    I --> J[Redirect ไป Beam]

    J --> K[แสดง QR Code]
    K --> L{ลูกค้าจ่ายเงิน?}

    L -->|ไม่จ่าย| M[Order ค้าง Pending]
    L -->|จ่ายสำเร็จ| N[Beam ส่ง Webhook]

    N --> O[Verify Signature]
    O --> P[Update Order → Processing]
    P --> Q[ส่ง Email]

    L -->|จ่ายสำเร็จ| R[Redirect Thank You]
    R --> S[แสดงหน้ายืนยัน]
```

## State Diagram

```mermaid
stateDiagram-v2
    [*] --> Pending: สร้าง Order
    Pending --> BeamCheckout: Redirect to Beam
    BeamCheckout --> Pending: ยกเลิก/หมดเวลา
    BeamCheckout --> Processing: จ่ายเงินสำเร็จ + Webhook
    Processing --> Completed: Admin จัดส่งแล้ว
    Completed --> [*]
```

---

## วิธีดู Diagram

### Option 1: Mermaid Live Editor
1. ไปที่ https://mermaid.live
2. Copy code ระหว่าง ```mermaid และ ```
3. Paste ใน editor
4. Export เป็น PNG/SVG

### Option 2: VS Code Extension
1. ติดตั้ง "Markdown Preview Mermaid Support"
2. เปิดไฟล์นี้ใน VS Code
3. กด Ctrl+Shift+V เพื่อ Preview

### Option 3: GitHub
- Push ไฟล์นี้ขึ้น GitHub
- GitHub จะ render Mermaid diagrams อัตโนมัติ

---

## Files ที่เกี่ยวข้อง

| File | หน้าที่ |
|------|--------|
| `inc/class-wc-gateway-beam.php` | Payment Gateway Class |
| `inc/functions-custom.php` | Register Gateway + Webhook |
| `custom-pages/page-checkout.php` | หน้า Checkout |
| `custom-pages/page-order-received.php` | หน้า Thank You |

## Credentials (wp-config.php)

```php
define('MARSX_BEAM_MERCHANT_ID', 'your-merchant-id');
define('MARSX_BEAM_API_KEY', 'your-api-key');
define('MARSX_BEAM_WEBHOOK_SECRET', 'your-webhook-secret');
```

## API Endpoints

| Environment | URL |
|-------------|-----|
| Playground | `https://playground.api.beamcheckout.com/api/v1` |
| Production | `https://api.beamcheckout.com/api/v1` |

## Webhook URL

```
https://marsxthings.com/beam-webhook/
```
