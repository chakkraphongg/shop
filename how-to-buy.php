<?php
/**
 * DEKROYSHOP - How to Buy & Ordering Protocol Guide
 * BMW Corporate-Automotive Light Canvas Design Language
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cart.php';

$pageTitle = 'วิธีการสั่งซื้อ & คำถามที่พบบ่อย (Acquisition Protocol)';
$metaDescription = 'ขั้นตอนการสั่งซื้อไอเทม วิธีการชำระเงิน และการรับไอเทมเข้าตัวละครที่ DEKROYSHOP ผ่านแฟนเพจ Facebook';
require_once __DIR__ . '/includes/frontend-header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 80px;">
    <!-- Breadcrumb Bar -->
    <nav style="padding: 0 0 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border-subtle); margin-bottom: 40px;" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/index.php" style="color: var(--text-body);">HOME</a>
        <span style="margin: 0 10px; color: var(--border-light);">/</span>
        <span style="color: var(--text-display);">HOW TO BUY & PROTOCOL</span>
    </nav>

    <!-- Headline Band -->
    <div style="max-width: 800px; margin-bottom: 50px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
            <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
            <span style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--primary);">
                ACQUISITION PROTOCOL
            </span>
        </div>
        <h1 style="font-size:clamp(30px, 3.8vw, 44px); font-weight:700; letter-spacing:-0.02em; text-transform:uppercase; color:var(--text-display); line-height:1.15; margin-bottom:14px;">
            ขั้นตอนการสั่งซื้อไอเทม
        </h1>
        <p style="color:var(--text-body); font-size:16px; font-weight:300; line-height:1.7;">
            ทำความเข้าใจขั้นตอนการจัดหาและรับไอเทมเข้าสู่ตัวละครของคุณอย่างถูกต้อง รวดเร็ว ปลอดภัย ผ่านแฟนเพจ Facebook ทางการ
        </p>
    </div>

    <!-- 4-Step Acquisition Protocol Grid -->
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 70px;">
        <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:32px 24px; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="font-size:32px; font-weight:700; color:var(--primary); line-height:1; margin-bottom:16px; font-family:var(--font-mono);">
                    01
                </div>
                <h3 style="font-size:16px; font-weight:700; text-transform:uppercase; color:var(--text-display); margin-bottom:10px;">
                    เลือกดูไอเทมในคลัง
                </h3>
                <p style="font-size:13px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    เลือกชมไอเทม อาวุธ สไนเปอร์ หรือชุดเซ็ต 6 ชิ้นที่ต้องการผ่านหน้า <strong>INVENTORY</strong> หรือ <strong>LOADOUT SETS</strong>
                </p>
            </div>
            <div style="margin-top:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted);">
                STEP 01 / SELECTION
            </div>
        </div>

        <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:32px 24px; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="font-size:32px; font-weight:700; color:var(--primary); line-height:1; margin-bottom:16px; font-family:var(--font-mono);">
                    02
                </div>
                <h3 style="font-size:16px; font-weight:700; text-transform:uppercase; color:var(--text-display); margin-bottom:10px;">
                    กดทักเพจ / คัดลอกรหัส
                </h3>
                <p style="font-size:13px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    กดปุ่ม <strong>"ทักสอบถาม / สั่งซื้อผ่าน FACEBOOK"</strong> หรือกดคัดลอกรหัสไอเทมที่ต้องการเพื่อเตรียมส่งให้แอดมิน
                </p>
            </div>
            <div style="margin-top:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted);">
                STEP 02 / FANPAGE CTA
            </div>
        </div>

        <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:32px 24px; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="font-size:32px; font-weight:700; color:var(--primary); line-height:1; margin-bottom:16px; font-family:var(--font-mono);">
                    03
                </div>
                <h3 style="font-size:16px; font-weight:700; text-transform:uppercase; color:var(--text-display); margin-bottom:10px;">
                    ยืนยันคำสั่งซื้อกับแอดมิน
                </h3>
                <p style="font-size:13px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    ส่งข้อความแจ้งชื่อไอเทมและชื่อตัวละครในเกมผ่านแชทเพจ แอดมินจะตรวจสอบสต็อกและส่งรายละเอียดการชำระเงิน
                </p>
            </div>
            <div style="margin-top:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted);">
                STEP 03 / CONFIRMATION
            </div>
        </div>

        <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:32px 24px; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div style="font-size:32px; font-weight:700; color:var(--primary); line-height:1; margin-bottom:16px; font-family:var(--font-mono);">
                    04
                </div>
                <h3 style="font-size:16px; font-weight:700; text-transform:uppercase; color:var(--text-display); margin-bottom:10px;">
                    รับไอเทมในเกมทันที
                </h3>
                <p style="font-size:13px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    เมื่อแจ้งสลิปเรียบร้อย แอดมินจะส่งมอบไอเทมตรงเข้าคลังตัวละครของคุณภายใน 5-15 นาที
                </p>
            </div>
            <div style="margin-top:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted);">
                STEP 04 / DELIVERY
            </div>
        </div>
    </div>

    <!-- Direct Fanpage Callout Band -->
    <div style="background:var(--surface-dark); color:#ffffff; padding:48px 40px; margin-bottom:70px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:24px;">
        <div style="max-width:620px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
                <span style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--primary);">
                    OFFICIAL FANPAGE CHANNEL
                </span>
            </div>
            <h2 style="font-size:26px; font-weight:700; text-transform:uppercase; margin-bottom:8px;">
                ติดต่อแอดมินแฟนเพจ DEKROYZZ ได้ตลอด 24 ชั่วโมง
            </h2>
            <p style="color:var(--text-secondary-dark); font-size:14px; font-weight:300; line-height:1.6;">
                ลิงก์ทางการ: https://www.facebook.com/dekroyzz — แอดมินตอบไว ให้คำปรึกษาการจัดเซ็ตไอเทม และพร้อมส่งของทันที
            </p>
        </div>
        <div>
            <a href="https://www.facebook.com/dekroyzz" target="_blank" rel="noopener noreferrer" class="btn-fanpage" style="padding:16px 28px; font-size:13px; letter-spacing:0.08em;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>เปิด Facebook Fanpage DEKROYZZ</span>
            </a>
        </div>
    </div>

    <!-- FAQ Section -->
    <section id="faq" style="max-width: 900px; margin: 0 auto; padding-top: 30px; border-top: 1px solid var(--border-light);">
        <div style="text-align: center; margin-bottom: 40px;">
            <span class="section-eyebrow">FREQUENTLY ASKED QUESTIONS</span>
            <h2 class="section-headline">คำถามที่พบบ่อย (FAQ)</h2>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px;">
            <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:24px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-display); margin-bottom:8px; text-transform:uppercase;">
                    Q: ส่งมอบไอเทมอย่างไร และใช้เวลานานแค่ไหน?
                </h3>
                <p style="font-size:14px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    A: การส่งมอบไอเทมทำผ่านระบบส่งตรงเข้าตัวละคร เพียงแจ้งชื่อตัวละครที่ถูกต้องให้กับแอดมินทางแฟนเพจ โดยเฉลี่ยใช้เวลาเพียง 5–15 นาทีหลังยืนยันยอดเรียบร้อย
                </p>
            </div>

            <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:24px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-display); margin-bottom:8px; text-transform:uppercase;">
                    Q: รูปภาพไอเทมและชื่อตรงกับของจริงในเกมหรือไม่?
                </h3>
                <p style="font-size:14px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    A: ตรงกัน 100% เนื่องจากรูปภาพทั้งหมดถูกแปลงโดยตรงจากไฟล์ไอคอนคุณภาพสูง พร้อมเชื่อมโยงฐานข้อมูล ทำให้คุณเห็นหน้าตาไอเทมจริงก่อนตัดสินใจซื้อ
                </p>
            </div>

            <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:24px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-display); margin-bottom:8px; text-transform:uppercase;">
                    Q: การจัดหมวดหมู่สินค้า 6 หมวดหลักมีอะไรบ้าง?
                </h3>
                <p style="font-size:14px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    A: สินค้าแบ่งตามฐานข้อมูลทางการอย่างถูกต้องแม่นยำ 6 หมวด ได้แก่ <strong>WEAPONS</strong> (อาวุธปืน/มีด/ของแต่ง), <strong>ARMOR</strong> (เกราะลำตัว), <strong>HEAD</strong> (หมวก/หน้ากาก), <strong>HERO</strong> (ตัวละคร), <strong>BACKPACK</strong> (กระเป๋าเป้) และ <strong>OTHERS</strong> (กล่องสุ่ม/เสบียง/อื่นๆ) ทุกชิ้นราคามาตรฐาน 20 บาท และมีโปรลดพิเศษ 15 บาท
                </p>
            </div>

            <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:24px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-display); margin-bottom:8px; text-transform:uppercase;">
                    Q: ช่องทางการชำระเงินรองรับอะไรบ้าง?
                </h3>
                <p style="font-size:14px; font-weight:300; color:var(--text-body); line-height:1.7;">
                    A: รองรับการโอนผ่านธนาคารทุกธนาคารในประเทศไทย, พร้อมเพย์ (PromptPay QR), และ TrueMoney Wallet สามารถขอ QR รับเงินได้ทันทีในแชทแฟนเพจ Facebook
                </p>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>
