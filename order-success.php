<?php
/**
 * DEKROYSHOP - Order Success & Fanpage Delivery Handshake Page
 * BMW Corporate-Automotive Light Canvas Design Language
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/cart.php';

$orderNumber = trim($_GET['order'] ?? '');
if (empty($orderNumber)) {
    redirect(APP_URL . '/shop.php');
}

// Fetch order
$stmt = db()->prepare("
    SELECT id, order_number, total_amount, status, customer_note, created_at
    FROM orders
    WHERE order_number = ?
    LIMIT 1
");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('danger', 'ไม่พบข้อมูลคำสั่งซื้อที่ระบุ');
    redirect(APP_URL . '/shop.php');
}

// Fetch order items
$itemStmt = db()->prepare("
    SELECT oi.*, p.image, p.item_code
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");
$itemStmt->execute([$order['id']]);
$orderItems = $itemStmt->fetchAll();

$pageTitle = 'คำสั่งซื้อสำเร็จ #' . $order['order_number'];
$metaDescription = 'คำสั่งซื้อสำเร็จ แจ้งรับไอเทมผ่านทาง Facebook Fanpage DEKROYZZ';
$extraCss = ['shop.css'];
require_once __DIR__ . '/includes/frontend-header.php';

$fanpageNotifyText = "สวัสดีครับ แจ้งเลขออเดอร์ DEKROYSHOP: " . $order['order_number'] . " ยอดชำระ: " . format_price($order['total_amount']) . " บาท (" . ($order['customer_note'] ?: 'ไม่ได้ระบุชื่อตัวละคร') . ")";
$fanpageUrl = "https://www.facebook.com/dekroyzz";
?>

<div class="container" style="padding-top: 50px; padding-bottom: 90px; max-width: 900px;">
    <!-- Success Banner -->
    <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:40px; text-align:center; margin-bottom:30px;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:64px; height:64px; background:#e8f7ee; color:#0e8345; font-size:32px; margin-bottom:16px;">
            ✓
        </div>

        <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-bottom:8px;">
            <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
            <span style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--primary);">
                TRANSACTION CONFIRMED
            </span>
        </div>

        <h1 style="font-size:clamp(26px, 3.5vw, 36px); font-weight:700; text-transform:uppercase; color:var(--text-display); margin-bottom:8px;">
            บันทึกคำสั่งซื้อเรียบร้อยแล้ว
        </h1>
        <p style="color:var(--text-body); font-size:15px; font-weight:300;">
            ระบบได้บันทึกและล็อกสต็อกไอเทมของคุณเรียบร้อยแล้ว กรุณาดำเนินการแจ้งรับไอเทมผ่านแฟนเพจ Facebook
        </p>

        <!-- Order Number Badge -->
        <div style="margin-top:24px; padding:16px 24px; background:#ffffff; border:1px solid var(--border-light); display:inline-block;">
            <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); display:block; margin-bottom:4px;">
                YOUR ORDER NUMBER
            </span>
            <span style="font-family:var(--font-mono); font-size:22px; font-weight:700; color:var(--primary); letter-spacing:0.04em;">
                <?= e($order['order_number']) ?>
            </span>
        </div>
    </div>

    <!-- PRIMARY ACTION: Fanpage Handshake -->
    <div style="background:var(--surface-dark); color:#ffffff; padding:36px; margin-bottom:36px; display:flex; flex-direction:column; gap:20px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
            <span style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--primary);">
                STEP 2: FANPAGE HANDSHAKE (ขั้นตอนสำคัญ)
            </span>
        </div>

        <h2 style="font-size:22px; font-weight:700; text-transform:uppercase;">
            กดส่งเลขออเดอร์ให้แอดมินแฟนเพจเพื่อรับไอเทมเข้าเกม
        </h2>

        <p style="color:var(--text-secondary-dark); font-size:14px; font-weight:300; line-height:1.7;">
            แอดมินแฟนเพจ <strong>DEKROYZZ</strong> พร้อมตรวจสอบและส่งมอบไอเทมตรงเข้าคลังตัวละครของคุณทันทีหลังจากได้รับข้อความ
        </p>

        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:8px;">
            <a href="<?= $fanpageUrl ?>" target="_blank" rel="noopener noreferrer" class="btn-fanpage" style="flex:1; min-width:240px; justify-content:center; padding:16px; font-size:13px; letter-spacing:0.06em;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>ทักแจ้งเลขออเดอร์ผ่านแฟนเพจ FACEBOOK</span>
            </a>

            <button type="button" class="btn-ghost" style="padding:14px 20px; font-size:12px; color:#ffffff; border-color:#ffffff;" onclick="copyOrderText()">
                📋 คัดลอกข้อความสำหรับส่งให้แอดมิน
            </button>
        </div>

        <div id="orderCopyNotice" style="display:none; font-size:12px; font-weight:700; color:#0e8345; text-align:center; padding:8px; background:#e8f7ee; border:1px solid #b7e1cd;">
            ✓ คัดลอกข้อความสำเร็จ! นำไปวางในกล่องข้อความแฟนเพจ Facebook ได้เลย
        </div>
    </div>

    <!-- Order Items Breakdown -->
    <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:32px; margin-bottom:30px;">
        <h3 style="font-size:15px; font-weight:700; text-transform:uppercase; color:var(--text-display); margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--border-light);">
            รายการไอเทมในคำสั่งซื้อ (<?= count($orderItems) ?> รายการ)
        </h3>

        <div style="display:flex; flex-direction:column; gap:16px;">
            <?php foreach ($orderItems as $oi): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; border-bottom:1px solid var(--border-subtle);">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <?php
                        $oImg = !empty($oi['image']) ? APP_URL . '/' . ltrim($oi['image'], '/') : APP_URL . '/assets/images/no-image.svg';
                        ?>
                        <img src="<?= e($oImg) ?>" alt="<?= e($oi['product_name']) ?>" class="cart-thumb" width="48" height="48">
                        <div>
                            <strong style="color:var(--text-display); font-size:14px; text-transform:uppercase;"><?= e($oi['product_name']) ?></strong>
                            <div style="font-size:12px; color:var(--text-muted);">
                                จำนวน: <strong style="color:var(--text-display);"><?= $oi['quantity'] ?></strong> &times; <?= format_price($oi['price']) ?> ฿
                            </div>
                        </div>
                    </div>
                    <div style="font-weight:700; color:var(--text-display); font-size:15px;">
                        <?= format_price($oi['subtotal']) ?> ฿
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:20px; padding-top:16px; border-top:2px solid var(--text-display); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:14px; font-weight:700; text-transform:uppercase; color:var(--text-display);">
                ยอดรวมทั้งสิ้น
            </span>
            <span style="font-size:22px; font-weight:700; color:var(--primary);">
                <?= format_price($order['total_amount']) ?> ฿
            </span>
        </div>

        <?php if (!empty($order['customer_note'])): ?>
            <div style="margin-top:20px; padding:12px 16px; background:#ffffff; border:1px solid var(--border-light); font-size:12px; color:var(--text-body);">
                <strong>ข้อมูลการส่งมอบ:</strong> <?= e($order['customer_note']) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Navigation Buttons -->
    <div style="display:flex; justify-content:center; gap:16px;">
        <a href="<?= APP_URL ?>/shop.php" class="btn-prime" style="font-size:12px; padding:12px 24px;">
            &larr; กลับไปเลือกดูไอเทมต่อ (BROWSE ALL COSMETICS)
        </a>
    </div>
</div>

<script>
function copyOrderText() {
    const textToCopy = <?= json_encode($fanpageNotifyText, JSON_UNESCAPED_UNICODE) ?>;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            showOrderNotice();
        }).catch(() => {
            fallbackOrderCopy(textToCopy);
        });
    } else {
        fallbackOrderCopy(textToCopy);
    }
}

function fallbackOrderCopy(text) {
    const tempInput = document.createElement("textarea");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    try {
        document.execCommand("copy");
        showOrderNotice();
    } catch (e) {
        alert("กรุณาคัดลอกข้อความนี้: " + text);
    }
    document.body.removeChild(tempInput);
}

function showOrderNotice() {
    const notice = document.getElementById('orderCopyNotice');
    if (notice) {
        notice.style.display = 'block';
        setTimeout(() => {
            notice.style.display = 'none';
        }, 5000);
    }
}
</script>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>
