<?php
/**
 * DEKROYSHOP - Secure Transactional Checkout Page
 * BMW Corporate-Automotive Light Canvas Design Language
 * Server-Enforced Stock, Pricing & Order Item Creation
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/cart.php';
require_once __DIR__ . '/includes/auth.php';

$cartData = get_cart_details();
$items    = $cartData['items'];
$total    = $cartData['total'];

if (empty($items)) {
    set_flash('warning', 'ไม่มีสินค้าในตะกร้าสำหรับดำเนินการสั่งซื้อ');
    redirect(APP_URL . '/shop.php');
}

$currentUser = get_current_user_data();
$currentUserId = get_current_user_id();

// Handle Place Order POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $customerNote = trim($_POST['customer_note'] ?? '');
    $inGameName   = trim($_POST['in_game_name'] ?? '');
    $contactInfo  = trim($_POST['contact_info'] ?? '');

    // Combine notes
    $fullNote = "ชื่อในเกม/ตัวละคร: " . ($inGameName ?: 'ไม่ได้ระบุ');
    if ($contactInfo !== '') {
        $fullNote .= " | ติดต่อ: " . $contactInfo;
    }
    if ($customerNote !== '') {
        $fullNote .= " | ข้อความ: " . $customerNote;
    }

    $pdo = db();
    try {
        $pdo->beginTransaction();

        $verifiedItems = [];
        $computedTotal = 0.00;

        // Atomic row-level lock on each product
        $checkStmt = $pdo->prepare("
            SELECT id, name, price, stock, status 
            FROM products 
            WHERE id = ? 
            FOR UPDATE
        ");

        foreach ($_SESSION['cart'] as $pId => $qty) {
            $checkStmt->execute([$pId]);
            $prod = $checkStmt->fetch();

            if (!$prod || $prod['status'] !== 'active') {
                throw new Exception("สินค้าบางรายการถูกระงับหรือไม่พร้อมจำหน่ายแล้ว");
            }

            $currentStock = (int)$prod['stock'];
            if ($currentStock < $qty) {
                throw new Exception("สินค้า {$prod['name']} มีสต็อกไม่เพียงพอ (เหลือ {$currentStock} ชิ้น)");
            }

            // Real server-side price calculation
            $unitPrice = (float)$prod['price'];
            $lineSubtotal = round($unitPrice * $qty, 2);
            $computedTotal += $lineSubtotal;

            $verifiedItems[] = [
                'product_id'   => (int)$prod['id'],
                'product_name' => (string)$prod['name'],
                'price'        => $unitPrice,
                'quantity'     => (int)$qty,
                'subtotal'     => $lineSubtotal,
                'remaining'    => $currentStock - $qty
            ];
        }

        // Generate Unique Order Number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Insert Order Record
        $orderInsert = $pdo->prepare("
            INSERT INTO orders (user_id, order_number, total_amount, status, customer_note, created_at)
            VALUES (?, ?, ?, 'pending', ?, NOW())
        ");
        $orderInsert->execute([$currentUserId, $orderNumber, $computedTotal, $fullNote]);
        $orderId = (int)$pdo->lastInsertId();

        // Insert Order Items and Update Inventory
        $itemInsert = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stockUpdate = $pdo->prepare("
            UPDATE products 
            SET stock = ?, status = CASE WHEN ? <= 0 THEN 'out_of_stock' ELSE status END
            WHERE id = ?
        ");

        foreach ($verifiedItems as $item) {
            $itemInsert->execute([
                $orderId,
                $item['product_id'],
                $item['product_name'],
                $item['price'],
                $item['quantity'],
                $item['subtotal']
            ]);

            $stockUpdate->execute([$item['remaining'], $item['remaining'], $item['product_id']]);
        }

        $pdo->commit();

        // Clear user's session cart
        clear_cart();

        log_activity($currentUserId, 'CHECKOUT_ORDER', 'order', $orderId, "สร้างคำสั่งซื้อ {$orderNumber} มูลค่า {$computedTotal} ฿");

        // Redirect to Order Success Confirmation Screen
        redirect(APP_URL . '/order-success.php?order=' . urlencode($orderNumber));

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_system_error("Checkout error: " . $e->getMessage());
        set_flash('danger', 'เกิดข้อผิดพลาดในการทำรายการ: ' . $e->getMessage());
        redirect(APP_URL . '/checkout.php');
    }
}

$pageTitle = 'ยืนยันและชำระเงินคำสั่งซื้อ (Checkout)';
$extraCss = ['shop.css'];
require_once __DIR__ . '/includes/frontend-header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 80px;">
    <!-- Breadcrumb Bar -->
    <nav style="padding: 0 0 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border-subtle); margin-bottom: 30px;" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/index.php" style="color: var(--text-body);">HOME</a>
        <span style="margin: 0 10px; color: var(--border-light);">/</span>
        <a href="<?= APP_URL ?>/cart.php" style="color: var(--text-body);">CART</a>
        <span style="margin: 0 10px; color: var(--border-light);">/</span>
        <span style="color: var(--text-display);">CHECKOUT</span>
    </nav>

    <div style="margin-bottom: 30px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
            <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
            <span style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--primary);">
                FINAL TRANSACTION PROTOCOL
            </span>
        </div>
        <h1 style="font-size:clamp(28px, 3.2vw, 38px); font-weight:700; letter-spacing:-0.02em; text-transform:uppercase; color:var(--text-display);">
            ยืนยันคำสั่งซื้อ (CHECKOUT)
        </h1>
    </div>

    <form method="POST" action="" class="cart-layout">
        <?= csrf_field() ?>

        <!-- Left: Customer Information & Delivery Details -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:32px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-display); text-transform:uppercase; margin-bottom:20px; letter-spacing:0.04em;">
                    1. ข้อมูลผู้สั่งซื้อ & การส่งมอบไอเทมในเกม
                </h3>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:16px;">
                    <div>
                        <label class="filter-label" for="inGameName">ชื่อตัวละครในเกม (Character In-Game Name) *</label>
                        <input type="text" id="inGameName" name="in_game_name" class="filter-input" required 
                               placeholder="เช่น Sniper_King, Pro_Player">
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">* ใช้สำหรับการส่งมอบไอเทมโดยตรงเข้าตัวละคร</div>
                    </div>

                    <div>
                        <label class="filter-label" for="contactInfo">ช่องทางติดต่อ (Facebook / Discord / เบอร์)</label>
                        <input type="text" id="contactInfo" name="contact_info" class="filter-input" 
                               value="<?= e($currentUser['email'] ?? '') ?>" placeholder="Facebook ชื่อผู้ติดต่อ หรือ เบอร์โทร">
                    </div>
                </div>

                <div>
                    <label class="filter-label" for="customerNote">หมายเหตุเพิ่มเติม (ถ้ามี)</label>
                    <textarea id="customerNote" name="customer_note" class="filter-input" rows="3" 
                              placeholder="ระบุเซิร์ฟเวอร์ที่เล่น หรือช่วงเวลาที่สะดวกรับไอเทม"></textarea>
                </div>
            </div>

            <!-- Items Overview Box -->
            <div style="background:var(--surface-card); border:1px solid var(--border-light); padding:32px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-display); text-transform:uppercase; margin-bottom:20px; letter-spacing:0.04em;">
                    2. ตรวจสอบรายการไอเทมในคำสั่งซื้อ (<?= count($items) ?> รายการ)
                </h3>

                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($items as $item): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid var(--border-light);">
                            <div style="display:flex; align-items:center; gap:14px;">
                                <?php
                                $img = !empty($item['image']) ? APP_URL . '/' . ltrim($item['image'], '/') : APP_URL . '/assets/images/no-image.svg';
                                ?>
                                <img src="<?= e($img) ?>" alt="<?= e($item['name']) ?>" class="cart-thumb" width="48" height="48" style="width:48px; height:48px;">
                                <div>
                                    <strong style="color:var(--text-display); font-size:14px; text-transform:uppercase;"><?= e($item['name']) ?></strong>
                                    <div style="font-size:12px; color:var(--text-muted);">
                                        จำนวน: <strong style="color:var(--text-display);"><?= $item['quantity'] ?></strong> &times; <?= format_price($item['price']) ?> ฿
                                    </div>
                                </div>
                            </div>
                            <div style="font-weight:700; color:var(--text-display); font-size:15px;">
                                <?= format_price($item['subtotal']) ?> ฿
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right: Sticky Order Summary & Submit -->
        <div class="cart-summary-card">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
                <span style="font-size:11px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--primary);">
                    PAYMENT CHECKOUT
                </span>
            </div>
            <h3 style="font-size:18px; font-weight:700; color:var(--text-display); text-transform:uppercase; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--border-light);">
                สรุปยอดชำระ
            </h3>

            <div class="summary-row">
                <span>ยอดรวมสินค้า</span>
                <span style="font-weight:700; color:var(--text-display);"><?= format_price($total) ?> ฿</span>
            </div>

            <div class="summary-row">
                <span>ค่าธรรมเนียมจัดส่ง</span>
                <span style="color:#0e8345; font-weight:700;">ฟรี (0.00 ฿)</span>
            </div>

            <div class="summary-row total">
                <span>ยอดสุทธิทั้งหมด</span>
                <span><?= format_price($total) ?> ฿</span>
            </div>

            <div style="margin-top:24px;">
                <button type="submit" class="btn-prime" style="width:100%; padding:16px; font-size:14px; letter-spacing:0.06em;">
                    ⚡ PLACE ORDER NOW
                </button>
            </div>

            <div style="margin-top:18px; font-size:11px; color:var(--text-muted); text-align:center; line-height:1.7;">
                เมื่อกดยืนยัน ระบบจะทำธุรกรรมตัดสต็อกอัตโนมัติ และสร้างเลขออเดอร์เพื่อให้คุณติดต่อรับไอเทมทางแฟนเพจ Facebook ได้ทันที
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>
