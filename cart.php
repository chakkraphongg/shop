<?php
/**
 * DEKROYSHOP - Shopping Cart Page
 * BMW Corporate-Automotive Light Canvas Design Language
 * Stock Validation | Direct Facebook Fanpage Inquiry | Instant Checkout
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/cart.php';

// Handle Cart Updates / Deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_qty') {
        $pId = (int)($_POST['product_id'] ?? 0);
        $qty = max(0, (int)($_POST['quantity'] ?? 1));
        $res = update_cart_quantity($pId, $qty);
        set_flash($res['success'] ? 'info' : 'danger', $res['message']);
        redirect(APP_URL . '/cart.php');
    }

    if ($action === 'remove_item') {
        $pId = (int)($_POST['product_id'] ?? 0);
        remove_from_cart($pId);
        set_flash('info', 'นำสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
        redirect(APP_URL . '/cart.php');
    }

    if ($action === 'clear_all') {
        clear_cart();
        set_flash('info', 'ล้างตะกร้าสินค้าทั้งหมดแล้ว');
        redirect(APP_URL . '/cart.php');
    }
}

// Fetch validated cart details
$cartData = get_cart_details();
$items    = $cartData['items'];
$total    = $cartData['total'];
$count    = $cartData['count'];

$pageTitle = 'ตะกร้าสินค้าของคุณ (Cart)';
$extraCss = ['shop.css'];
require_once __DIR__ . '/includes/frontend-header.php';

// Build cart summary text for Facebook Fanpage
$cartSummaryLines = ["สวัสดีครับ ต้องการสั่งซื้อรายการไอเทมในตะกร้า DEKROYSHOP:"];
foreach ($items as $idx => $it) {
    $cartSummaryLines[] = ($idx + 1) . ". " . $it['name'] . " x " . $it['quantity'] . " ชิ้น (" . format_price($it['subtotal']) . " บาท)";
}
$cartSummaryLines[] = "ยอดรวมสุทธิ: " . format_price($total) . " บาท";
$cartSummaryText = implode("\n", $cartSummaryLines);
$fanpageUrl = "https://www.facebook.com/dekroyzz";
?>

<div class="container" style="padding-top: 40px; padding-bottom: 80px;">
    <!-- Breadcrumb Bar -->
    <nav style="padding: 0 0 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border-subtle); margin-bottom: 30px;" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/index.php" style="color: var(--text-body);">HOME</a>
        <span style="margin: 0 10px; color: var(--border-light);">/</span>
        <span style="color: var(--text-display);">SHOPPING CART</span>
    </nav>

    <div style="margin-bottom: 30px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
            <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
            <span style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--primary);">
                SELECTED INVENTORY
            </span>
        </div>
        <h1 style="font-size:clamp(28px, 3.2vw, 38px); font-weight:700; letter-spacing:-0.02em; text-transform:uppercase; color:var(--text-display);">
            ตะกร้าสินค้า (SHOPPING CART)
        </h1>
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-catalog" style="padding: 80px 20px;">
            <div class="empty-icon">🛒</div>
            <h2 style="font-size: 22px; font-weight:700; color: var(--text-display); margin-bottom: 10px; text-transform:uppercase;">
                ตะกร้าสินค้าของคุณยังว่างเปล่า
            </h2>
            <p style="color: var(--text-body); font-size: 14px; font-weight:300; margin-bottom: 28px;">
                สำรวจคลังแสงเพื่อเลือกไอเทม สไนเปอร์ และอุปกรณ์ที่คุณต้องการ
            </p>
            <a href="<?= APP_URL ?>/shop.php" class="btn-prime">
                ไปที่หน้าคลังสินค้า &rarr;
            </a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- Left: Items Table -->
            <div style="background:var(--surface-card); border:1px solid var(--border-light); overflow:hidden;">
                <div style="overflow-x:auto;">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">ITEM</th>
                                <th>ชื่อสินค้า / รายละเอียด</th>
                                <th style="text-align:right;">ราคาต่อหน่วย</th>
                                <th style="text-align:center; width:150px;">จำนวน</th>
                                <th style="text-align:right;">รวม (บาท)</th>
                                <th style="text-align:center; width:60px;">ลบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr class="cart-item-row">
                                    <td>
                                        <?php
                                        $img = !empty($item['image']) ? APP_URL . '/' . ltrim($item['image'], '/') : APP_URL . '/assets/images/no-image.svg';
                                        ?>
                                        <img src="<?= e($img) ?>" alt="<?= e($item['name']) ?>" class="cart-thumb" width="64" height="64" loading="lazy">
                                    </td>
                                    <td>
                                        <a href="<?= APP_URL ?>/product.php?id=<?= $item['id'] ?>" style="font-weight:700; color:var(--text-display); font-size:15px; display:block; text-transform:uppercase;">
                                            <?= e($item['name']) ?>
                                        </a>
                                        <?php if (!empty($item['item_code'])): ?>
                                            <span style="font-family:var(--font-mono); font-size:11px; color:var(--primary); font-weight:700;">CODE: <?= e($item['item_code']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:right; font-weight:400; color:var(--text-body);">
                                        <?= format_price($item['price']) ?> ฿
                                    </td>
                                    <td style="text-align:center;">
                                        <form method="POST" action="" style="display:inline-flex; align-items:center; gap:6px;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="update_qty">
                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= (int)$item['stock'] ?>"
                                                   style="width:55px; height:36px; background:#ffffff; border:1px solid var(--border-light); color:var(--text-display); text-align:center; font-weight:700;"
                                                   onchange="this.form.submit()">
                                        </form>
                                        <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">(คงเหลือ <?= (int)$item['stock'] ?>)</div>
                                    </td>
                                    <td style="text-align:right; font-weight:700; color:var(--text-display); font-size:16px;">
                                        <?= format_price($item['subtotal']) ?> ฿
                                    </td>
                                    <td style="text-align:center;">
                                        <form method="POST" action="" onsubmit="return confirm('นำสินค้านี้ออกจากตะกร้าหรือไม่?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="remove_item">
                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                            <button type="submit" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:16px;" title="ลบรายการนี้">
                                                ✕
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="padding:16px 20px; border-top:1px solid var(--border-light); display:flex; justify-content:space-between; align-items:center; background:#ffffff;">
                    <a href="<?= APP_URL ?>/shop.php" class="btn-ghost" style="font-size:11px; padding:8px 16px;">
                        &larr; เลือกดูสินค้าต่อ
                    </a>
                    <form method="POST" action="" onsubmit="return confirm('ต้องการล้างตะกร้าสินค้าทั้งหมดหรือไม่?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="clear_all">
                        <button type="submit" style="background:none; border:none; color:#c5221f; font-size:12px; cursor:pointer; text-decoration:underline;">
                            ล้างตะกร้าทั้งหมด
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Summary Card -->
            <div class="cart-summary-card">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                    <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
                    <span style="font-size:11px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--primary);">
                        ORDER SUMMARY
                    </span>
                </div>
                <h3 style="font-size:18px; font-weight:700; color:var(--text-display); text-transform:uppercase; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--border-light);">
                    สรุปคำสั่งซื้อ
                </h3>

                <div class="summary-row">
                    <span>จำนวนไอเทมทั้งหมด</span>
                    <strong style="color:var(--text-display); font-weight:700;"><?= $count ?> ชิ้น</strong>
                </div>

                <div class="summary-row">
                    <span>ยอดรวมสินค้า</span>
                    <span style="font-weight:700; color:var(--text-display);"><?= format_price($total) ?> บาท</span>
                </div>

                <div class="summary-row">
                    <span>ค่าธรรมเนียมส่งมอบ</span>
                    <span style="color:#0e8345; font-weight:700;">ฟรี (0.00 บาท)</span>
                </div>

                <div class="summary-row total">
                    <span>ยอดชำระสุทธิ</span>
                    <span><?= format_price($total) ?> ฿</span>
                </div>

                <!-- High Impact Facebook Fanpage Order Option -->
                <div style="margin-top:24px; padding-top:20px; border-top:1px solid var(--border-light); display:flex; flex-direction:column; gap:10px;">
                    <a href="<?= $fanpageUrl ?>" target="_blank" rel="noopener noreferrer" class="btn-fanpage" style="justify-content:center; padding:14px; font-size:13px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>สั่งซื้อตะกร้านี้ผ่าน FACEBOOK</span>
                    </a>

                    <button type="button" class="btn-ghost" style="padding:10px; font-size:11px;" onclick="copyCartSummary()">
                        📋 คัดลอกรายการในตะกร้าสำหรับส่งในแชท
                    </button>
                    <div id="cartCopyNotice" style="display:none; font-size:11px; font-weight:700; color:#0e8345; text-align:center; padding:6px; background:#e8f7ee; border:1px solid #b7e1cd;">
                        ✓ คัดลอกรายการตะกร้าแล้ว! นำไปส่งในแฟนเพจได้ทันที
                    </div>
                </div>

                <!-- Site Checkout Option -->
                <div style="margin-top:16px;">
                    <a href="<?= APP_URL ?>/checkout.php" class="btn-prime" style="width:100%; padding:14px; font-size:13px;">
                        ดำเนินการสั่งซื้อผ่านระบบเว็บ (CHECKOUT) &rarr;
                    </a>
                </div>

                <div style="margin-top:16px; font-size:11px; color:var(--text-muted); text-align:center; line-height:1.6;">
                    🔒 ระบบความปลอดภัย 100% | ตรวจสอบสต็อกอัตโนมัติก่อนทำรายการ
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function copyCartSummary() {
    const textToCopy = <?= json_encode($cartSummaryText, JSON_UNESCAPED_UNICODE) ?>;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            showCartNotice();
        }).catch(() => {
            fallbackCartCopy(textToCopy);
        });
    } else {
        fallbackCartCopy(textToCopy);
    }
}

function fallbackCartCopy(text) {
    const tempInput = document.createElement("textarea");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    try {
        document.execCommand("copy");
        showCartNotice();
    } catch (e) {
        alert("กรุณาคัดลอกข้อความนี้: " + text);
    }
    document.body.removeChild(tempInput);
}

function showCartNotice() {
    const notice = document.getElementById('cartCopyNotice');
    if (notice) {
        notice.style.display = 'block';
        setTimeout(() => {
            notice.style.display = 'none';
        }, 5000);
    }
}
</script>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>
