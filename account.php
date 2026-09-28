<?php
/**
 * DEKROYSHOP - Customer Account Dashboard & Order History
 * BMW Corporate-Automotive Light Canvas Design Language
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart.php';

// Enforce Customer Authentication
if (!is_logged_in()) {
    set_flash('warning', 'กรุณาเข้าสู่ระบบเพื่อดูข้อมูลบัญชีและประวัติคำสั่งซื้อ');
    redirect(APP_URL . '/auth/login.php?return=' . urlencode('/account.php'));
}

$user = get_current_user_data();
if (!$user) {
    logout_user();
    redirect(APP_URL . '/auth/login.php');
}

$userId = (int)$user['id'];

// Fetch User's Orders with their Order Items (grouped cleanly)
$orders = [];
try {
    $stmt = db()->prepare("
        SELECT id, order_number, total_amount, status, customer_note, admin_note, created_at
        FROM orders
        WHERE user_id = ?
        ORDER BY id DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    // Fetch items for each order
    $itemStmt = db()->prepare("
        SELECT oi.*, p.image 
        FROM order_items oi
        LEFT JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id = ?
    ");

    foreach ($orders as &$ord) {
        $itemStmt->execute([$ord['id']]);
        $ord['items'] = $itemStmt->fetchAll();
    }
    unset($ord);

} catch (Throwable $e) {
    log_system_error("Account orders fetch error: " . $e->getMessage());
}

$pageTitle = 'บัญชีของฉัน & ประวัติการสั่งซื้อ';
$extraCss = ['shop.css'];
require_once __DIR__ . '/includes/frontend-header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 80px;">
    <!-- Breadcrumb Bar -->
    <nav style="padding: 0 0 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border-subtle); margin-bottom: 30px;" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/index.php" style="color: var(--text-body);">HOME</a>
        <span style="margin: 0 10px; color: var(--border-light);">/</span>
        <span style="color: var(--text-display);">CUSTOMER PROFILE</span>
    </nav>

    <div style="margin-bottom: 30px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
            <div class="m-stripe"><span class="m-c1"></span><span class="m-c2"></span><span class="m-c3"></span></div>
            <span style="font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--primary);">
                AUTHORIZED PROFILE
            </span>
        </div>
        <h1 style="font-size:clamp(28px, 3.2vw, 38px); font-weight:700; letter-spacing:-0.02em; text-transform:uppercase; color:var(--text-display);">
            บัญชีของฉัน (MY ACCOUNT)
        </h1>
    </div>

    <div class="account-layout">
        <!-- 1. Left Profile Summary Card -->
        <aside>
            <div class="profile-card">
                <div class="profile-avatar">
                    <?= strtoupper(substr($user['display_name'] ?: $user['username'], 0, 1)) ?>
                </div>
                <h2 style="font-size:18px; font-weight:700; text-transform:uppercase; color:var(--text-display); margin-bottom:4px;">
                    <?= e($user['display_name'] ?: $user['username']) ?>
                </h2>
                <div style="font-size:12px; font-weight:700; color:var(--primary); font-family:var(--font-mono); margin-bottom:16px;">
                    @<?= e($user['username']) ?>
                </div>

                <div style="text-align:left; padding-top:16px; border-top:1px solid var(--border-light); font-size:13px; line-height:2.2;">
                    <div><strong style="color:var(--text-display);">อีเมล:</strong> <?= e($user['email']) ?></div>
                    <div><strong style="color:var(--text-display);">สถานะ:</strong> <span style="color:#0e8345; font-weight:700;"><?= strtoupper(e($user['status'])) ?></span></div>
                    <div><strong style="color:var(--text-display);">วันที่สมัคร:</strong> <?= format_datetime($user['created_at'], 'd/m/Y') ?></div>
                </div>

                <div style="margin-top:24px; display:flex; flex-direction:column; gap:10px;">
                    <a href="<?= APP_URL ?>/shop.php" class="btn-prime" style="font-size:12px; padding:12px;">
                        เลือกดูไอเทมต่อ &rarr;
                    </a>
                    <a href="https://www.facebook.com/dekroyzz" target="_blank" rel="noopener noreferrer" class="btn-fanpage" style="font-size:12px; padding:12px; justify-content:center;">
                        ทักเพจ DEKROYZZ
                    </a>
                    <a href="<?= APP_URL ?>/auth/logout.php" class="btn-ghost" style="font-size:12px; padding:10px; color:#c5221f; border-color:#c5221f;">
                        ออกจากระบบ
                    </a>
                </div>
            </div>
        </aside>

        <!-- 2. Right: Order History -->
        <main>
            <div style="background:var(--surface-card); border:1px solid var(--border-light); overflow:hidden;">
                <div style="padding:20px 24px; border-bottom:2px solid var(--text-display); display:flex; justify-content:space-between; align-items:center; background:#ffffff;">
                    <h3 style="font-size:15px; font-weight:700; color:var(--text-display); text-transform:uppercase; letter-spacing:0.06em;">
                        📦 ประวัติคำสั่งซื้อทั้งหมด (<?= count($orders) ?>)
                    </h3>
                </div>

                <?php if (empty($orders)): ?>
                    <div style="padding:60px 20px; text-align:center;">
                        <div style="font-size:40px; margin-bottom:12px;">📭</div>
                        <h4 style="font-size:16px; font-weight:700; color:var(--text-display); margin-bottom:6px; text-transform:uppercase;">
                            คุณยังไม่มีประวัติการสั่งซื้อ
                        </h4>
                        <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">เมื่อคุณสั่งซื้อไอเทม ประวัติการสั่งซื้อและสถานะจะแสดงที่นี่</p>
                        <a href="<?= APP_URL ?>/shop.php" class="btn-prime" style="font-size:12px; padding:10px 20px;">
                            ไปที่คลังสินค้า
                        </a>
                    </div>
                <?php else: ?>
                    <div style="padding:24px;">
                        <?php foreach ($orders as $ord): ?>
                            <div style="background:#ffffff; border:1px solid var(--border-light); margin-bottom:20px; overflow:hidden;">
                                <!-- Order Header Bar -->
                                <div style="padding:16px 20px; background:#fafafa; border-bottom:1px solid var(--border-light); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                                    <div>
                                        <span style="font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em;">ORDER NUMBER</span>
                                        <div style="font-family:var(--font-mono); font-weight:700; color:var(--primary); font-size:15px;">
                                            <?= e($ord['order_number']) ?>
                                        </div>
                                    </div>

                                    <div>
                                        <span style="font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em;">DATE</span>
                                        <div style="font-size:13px; color:var(--text-display);"><?= format_datetime($ord['created_at']) ?></div>
                                    </div>

                                    <div>
                                        <span style="font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em;">STATUS</span>
                                        <div>
                                            <?php
                                            $pillStyle = match ($ord['status']) {
                                                'completed'  => 'color:#0e8345; background:#e8f7ee;',
                                                'cancelled'  => 'color:#c5221f; background:#fce8e6;',
                                                default      => 'color:var(--primary); background:rgba(28, 105, 212, 0.1);'
                                            };
                                            ?>
                                            <span style="display:inline-block; padding:3px 10px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; <?= $pillStyle ?>">
                                                <?= strtoupper(e($ord['status'])) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <span style="font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em;">TOTAL AMOUNT</span>
                                        <div style="font-size:17px; font-weight:700; color:var(--text-display);">
                                            <?= format_price($ord['total_amount']) ?> ฿
                                        </div>
                                    </div>

                                    <div>
                                        <a href="https://www.facebook.com/dekroyzz" target="_blank" rel="noopener noreferrer" class="btn-fanpage" style="font-size:11px; padding:6px 12px;" title="ทักแจ้งแอดมินทางแฟนเพจ">
                                            ทักเพจแจ้งเลขออเดอร์
                                        </a>
                                    </div>
                                </div>

                                <!-- Items List Inside Order -->
                                <div style="padding:16px 20px;">
                                    <div style="display:flex; flex-direction:column; gap:10px;">
                                        <?php foreach ($ord['items'] as $it): ?>
                                            <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px; padding-bottom:8px; border-bottom:1px solid var(--border-subtle);">
                                                <div style="display:flex; align-items:center; gap:10px;">
                                                    <strong style="color:var(--text-display); text-transform:uppercase;"><?= e($it['product_name']) ?></strong>
                                                    <span style="color:var(--text-muted);">&times; <?= (int)$it['quantity'] ?></span>
                                                </div>
                                                <div style="font-weight:700; color:var(--text-display);">
                                                    <?= format_price($it['subtotal']) ?> ฿
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (!empty($ord['customer_note'])): ?>
                                        <div style="margin-top:14px; padding-top:10px; border-top:1px dashed var(--border-light); font-size:12px; color:var(--text-body);">
                                            <strong>บันทึก/ชื่อในเกม:</strong> <?= e($ord['customer_note']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($ord['admin_note'])): ?>
                                        <div style="margin-top:8px; padding:8px 12px; background:rgba(28, 105, 212, 0.08); font-size:12px; color:var(--primary); font-weight:700;">
                                            <strong>ข้อความจากแอดมิน:</strong> <?= e($ord['admin_note']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>
