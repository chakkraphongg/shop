<?php
/**
 * DEKROYSHOP - Fortnite.GG Style Product Detail & 3D Inspection
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/cart.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($productId <= 0) {
    set_flash('danger', 'ไม่พบรหัสสินค้าที่ต้องการตรวจสอบ');
    redirect(APP_URL . '/index.php');
}

// Fetch Product Details
$stmt = db()->prepare("
    SELECT 
        p.id, p.category_id, p.item_code, p.name, p.slug, p.description,
        p.price, p.original_price, p.stock, p.status, p.image,
        c.name AS category_name, c.slug AS cat_slug
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.id = ? AND p.status = 'active'
    LIMIT 1
");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'ไม่พบสินค้านี้ หรือสินค้านี้อาจถูกปิดการขายแล้ว');
    redirect(APP_URL . '/index.php');
}

// Handle Add to Cart / Instant Checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = $_POST['action'] ?? 'add_to_cart';
    $qty    = max(1, (int)($_POST['quantity'] ?? 1));

    $result = add_to_cart($productId, $qty);
    set_flash($result['success'] ? 'success' : 'danger', $result['message']);

    if ($action === 'buy_now' && $result['success']) {
        redirect(APP_URL . '/checkout.php');
    } else {
        redirect(APP_URL . '/product.php?id=' . $productId);
    }
}

// Fetch Related Products in same category (up to 7 items)
$relatedProducts = [];
try {
    if (!empty($product['category_id'])) {
        $stmtRel = db()->prepare("
            SELECT p.id, p.name, p.slug, p.item_code, p.price, p.original_price, p.stock, p.image, c.name AS category_name, c.slug AS cat_slug
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
            ORDER BY p.price ASC, p.id DESC
            LIMIT 7
        ");
        $stmtRel->execute([$product['category_id'], $productId]);
        $relatedProducts = $stmtRel->fetchAll();
    }
} catch (Throwable $e) {
    log_system_error("Related products fetch error: " . $e->getMessage());
}

$pageTitle = $product['name'] . ' - Fortnite.GG Style Cosmetic Inspection';
$metaDescription = !empty($product['description']) ? mb_strimwidth(strip_tags($product['description']), 0, 150, '...') : 'สั่งซื้อ ' . $product['name'] . ' ที่ DEKROYSHOP';
$extraJs = ['fortnite-gg.js'];
require_once __DIR__ . '/includes/frontend-header.php';

$isSale = ((float)$product['price'] < (float)$product['original_price']);
$fanpageOrderText = $isSale
    ? "สวัสดีครับ สนใจสั่งซื้อไอเทม: " . $product['name'] . " (รหัส: " . ($product['item_code'] ?: 'ITM-' . $product['id']) . ") ราคาพิเศษ " . format_price($product['price']) . " บาท (ปกติ " . format_price($product['original_price']) . " บาท) จากเว็บ DEKROYSHOP"
    : "สวัสดีครับ สนใจสั่งซื้อไอเทม: " . $product['name'] . " (รหัส: " . ($product['item_code'] ?: 'ITM-' . $product['id']) . ") ราคาปกติ " . format_price($product['price']) . " บาท จากเว็บ DEKROYSHOP";
$fanpageUrl = "https://www.facebook.com/dekroyzz";
$rarityClass = 'rarity-' . ($product['cat_slug'] ?: 'weapons');
$img = !empty($product['image']) ? APP_URL . '/' . ltrim($product['image'], '/') : APP_URL . '/assets/images/no-image.svg';
$stageImg = ($product['cat_slug'] === 'hero') ? get_item_smart_preview_image($product['item_code'], $product['name'], $product['cat_slug'], $img) : $img;
?>

<div class="fn-container" style="padding-top: 24px; padding-bottom: 70px;">
    <!-- Breadcrumb Bar -->
    <nav style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--fn-text-muted); margin-bottom: 24px;" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/shop.php?category=<?= urlencode($product['cat_slug'] ?: 'hero') ?>" style="color: var(--fn-text-body);">COSMETICS</a>
        <span style="margin: 0 8px; color: var(--fn-border);">/</span>
        <?php if (!empty($product['category_name'])): ?>
            <a href="<?= APP_URL ?>/index.php?category=<?= urlencode($product['cat_slug']) ?>" style="color: var(--fn-text-body);"><?= e($product['category_name']) ?></a>
            <span style="margin: 0 8px; color: var(--fn-border);">/</span>
        <?php endif; ?>
        <span style="color: #ffffff;"><?= e($product['name']) ?></span>
    </nav>

    <!-- Inspection Main Layout -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 40px; background: var(--fn-surface); border: 1px solid var(--fn-border); border-radius: var(--fn-radius-lg); overflow: hidden; margin-bottom: 50px;">
        <!-- Left Stage: Rarity Gradient Background + Large 3D Visual -->
        <div class="fn-modal-stage <?= $rarityClass ?>" id="productPageStage" style="min-height: 480px; position:relative;">
            <div class="fn-stage-glow"></div>
            <div class="fn-stage-pedestal"></div>

            <?php if ($isSale): ?>
                <span class="fn-badge-sale" style="top:16px; left:16px; font-size:12px; padding:4px 10px; z-index:5;">🔥 SALE 15.-</span>
            <?php endif; ?>

            <div class="fn-3d-wrapper" id="productPage3dWrapper">
                <div class="fn-3d-sheen"></div>
                <img src="<?= e($stageImg) ?>" alt="<?= e($product['name']) ?>" class="fn-3d-model <?= $product['cat_slug'] === 'hero' ? 'hero-standing' : '' ?>" style="max-height: 440px; object-fit: contain;">
            </div>

            <!-- 3D Viewport Controls Pill -->
            <div class="fn-stage-controls" style="bottom: 16px; left: 16px;">
                <button type="button" class="fn-stage-btn btn-real-3d" onclick="openFullBody3DModal('<?= e(addslashes($product['item_code'] ?: '')) ?>', '<?= e(addslashes($product['name'])) ?>', <?= (int)$product['id'] ?>)" title="เปิดดูโมเดล 3D เต็มตัวแบบสมจริง (360° Real Mesh)">
                    <span class="icon">🎮</span> REAL 3D MODEL
                </button>
            </div>

            <div class="fn-stage-brand">
                <span class="fn-brand-circle" style="width:20px;height:20px;font-size:10px;line-height:20px;">GG</span>
                <span>DEKROY<span style="color:var(--fn-cyan);">SHOP</span> &bull; 3D SHOWCASE</span>
            </div>
        </div>

        <!-- Right: Metadata & Order Action Box -->
        <div style="padding: 40px; display: flex; flex-direction: column; justify-content: space-between; background: var(--fn-bg-secondary);">
            <div>
                <div style="font-size: 11px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: var(--fn-cyan); margin-bottom: 6px;">
                    <?= e($product['category_name'] ?? 'COSMETIC') ?>
                </div>

                <h1 style="font-size: clamp(26px, 3.2vw, 38px); font-weight: 900; text-transform: uppercase; color: #ffffff; line-height: 1.15; margin-bottom: 8px;">
                    <?= e($product['name']) ?>
                </h1>

                <?php if (!empty($product['item_code'])): ?>
                    <div style="font-family: monospace; font-size: 12px; color: var(--fn-yellow); margin-bottom: 20px;">
                        ITEM CODE: <strong><?= e($product['item_code']) ?></strong>
                    </div>
                <?php endif; ?>

                <!-- High-Contrast Striking Price Box (ปกติ 20 บาท / พิเศษ 15 บาท) -->
                <div class="fn-price-showcase-box" style="margin-bottom: 22px;">
                    <?php if ($isSale): ?>
                        <div class="fn-price-special-wrap">
                            <span class="fn-price-special-label">พิเศษ</span>
                            <span class="fn-price-special-amount">15</span>
                            <span class="fn-price-special-currency">บาท</span>
                        </div>
                        <div class="fn-price-regular-wrap">
                            <span class="fn-price-regular-text">ปกติ 20 บาท</span>
                        </div>
                        <span class="fn-price-discount-badge">🔥 ลด 25%</span>
                    <?php else: ?>
                        <div class="fn-price-special-wrap">
                            <span class="fn-price-special-label" style="color:#cbd5e1;">ปกติ</span>
                            <span class="fn-price-special-amount">20</span>
                            <span class="fn-price-special-currency">บาท</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div style="font-size: 13px; color: var(--fn-text-body); line-height: 1.7; margin-bottom: 24px; padding: 16px; background: var(--fn-surface); border: 1px solid var(--fn-border); border-radius: var(--fn-radius-md);">
                    <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #ffffff; margin-bottom: 4px;">
                        SPECIFICATIONS & DESCRIPTION
                    </div>
                    <p style="white-space: pre-wrap;"><?= !empty($product['description']) ? e($product['description']) : 'ไอเทมคุณภาพสูง ตรวจสอบโมเดลสมบูรณ์ 100%' ?></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <!-- Primary Action: Facebook Fanpage -->
                <a href="<?= $fanpageUrl ?>" target="_blank" rel="noopener noreferrer" class="fn-btn-shop-fortnite" style="padding: 15px; font-size: 14px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>ทักสอบถาม / สั่งซื้อผ่าน FACEBOOK FANPAGE</span>
                </a>
                <!-- Real 3D Full-Body Interactive Mesh Button -->
                <button type="button" class="fn-hud-btn" onclick="openFullBody3DModal('<?= e(addslashes($product['item_code'] ?: '')) ?>', '<?= e(addslashes($product['name'])) ?>', <?= (int)$product['id'] ?>)" style="width:100%; justify-content:center; padding:12px; background:linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color:#ffffff; font-weight:900; border:1px solid #38bdf8; border-radius:8px; cursor:pointer; font-size:13px; box-shadow:0 4px 15px rgba(2, 132, 199, 0.4);">
                    <span style="font-size:16px;">🎮</span> เปิดดูโมเดล 3D เต็มตัว 360° (REAL 3D WEBGL)
                </button>

                <!-- 1-Click Copy Slip -->
                <button type="button" class="fn-btn-copy-slip" style="padding: 12px; font-size: 12px;" onclick="copyItemInfo()">
                    📋 คัดลอกรหัส & ข้อมูลไอเทม (สำหรับนำไปวางในแชทแฟนเพจ)
                </button>
                <div id="copyNotice" style="display:none; font-size:11px; font-weight:800; color:#34d399; text-align:center; padding:8px; background:rgba(16,185,129,0.15); border-radius:6px;">
                    ✓ คัดลอกข้อความสำเร็จ! นำไปส่งในกล่องข้อความแฟนเพจ Facebook ได้เลย
                </div>

                <!-- Add to Cart / Instant Buy -->
                <?php if ($product['stock'] > 0): ?>
                    <form method="POST" action="" style="display:flex; gap:10px; margin-top:6px;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="action" value="add_to_cart" class="fn-pill-btn" style="flex:1; justify-content:center; padding:12px;">
                            + ใส่ตะกร้า
                        </button>
                        <button type="submit" name="action" value="buy_now" class="fn-pill-btn" style="flex:1; justify-content:center; padding:12px; background:var(--fn-yellow); color:#141518; font-weight:800;">
                            ⚡ ซื้อทันที (BUY NOW)
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Cosmetics Grid -->
    <?php if (!empty($relatedProducts)): ?>
        <div style="margin-top: 40px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 800; text-transform: uppercase; color: #ffffff;">
                    RELATED <?= e($product['category_name']) ?> COSMETICS
                </h3>
                <a href="<?= APP_URL ?>/index.php?category=<?= urlencode($product['cat_slug']) ?>" style="color: var(--fn-cyan); font-size: 12px; font-weight: 700;">
                    VIEW ALL <?= e($product['category_name']) ?> &rarr;
                </a>
            </div>

            <div class="fn-cosmetics-grid">
                <?php foreach ($relatedProducts as $rel): ?>
                    <?php
                    $rBaseImg = !empty($rel['image']) ? APP_URL . '/' . ltrim($rel['image'], '/') : APP_URL . '/assets/images/no-image.svg';
                    $rCardImg = ($rel['cat_slug'] === 'hero') ? get_item_card_preview_image($rel['item_code'], $rel['name'], $rel['cat_slug'], $rBaseImg) : $rBaseImg;
                    $rFullImg = ($rel['cat_slug'] === 'hero') ? get_item_smart_preview_image($rel['item_code'], $rel['name'], $rel['cat_slug'], $rBaseImg) : $rBaseImg;
                    $rSale = ((float)$rel['price'] < (float)$rel['original_price']);
                    $rRarity = 'rarity-' . ($rel['cat_slug'] ?: 'weapons');
                    ?>
                    <article class="fn-card <?= $rRarity ?>"
                             data-id="<?= (int)$rel['id'] ?>"
                             data-name="<?= e($rel['name']) ?>"
                             data-code="<?= e($rel['item_code']) ?>"
                             data-price="<?= (float)$rel['price'] ?>"
                             data-orig-price="<?= (float)$rel['original_price'] ?>"
                             data-image="<?= e($rCardImg) ?>"
                             data-full-image="<?= e($rFullImg) ?>"
                             data-category="<?= e($rel['category_name'] ?? 'COSMETIC') ?>"
                             data-cat-slug="<?= e($rel['cat_slug'] ?? 'weapons') ?>"
                             tabindex="0"
                             role="button"
                             onclick="openInspectModal(this)" aria-label="Inspect <?= e($rel['name']) ?>">

                        <?php if ($rSale): ?>
                            <span class="fn-badge-sale">🔥 SALE 15.-</span>
                        <?php endif; ?>

                        <button type="button" class="fn-card-action-btn" onclick="openInspectModal(this); event.stopPropagation();" aria-label="Quick inspect">&plus;</button>

                        <div class="fn-card-visual">
                            <img src="<?= e($rCardImg) ?>" alt="<?= e($rel['name']) ?>" width="200" height="200" class="fn-card-img <?= $rel['cat_slug'] === 'hero' ? 'hero-card-zoom' : '' ?>" loading="lazy">
                        </div>

                        <div class="fn-card-footer">
                            <div class="fn-card-title"><?= e($rel['name']) ?></div>
                            <div class="fn-card-price-row">
                                <?php if ($rSale): ?>
                                    <span class="fn-card-price special">พิเศษ 15 บาท</span>
                                    <span class="fn-card-old-price">ปกติ 20 บาท</span>
                                <?php else: ?>
                                    <span class="fn-card-price">ปกติ 20 บาท</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function copyItemInfo() {
    const textToCopy = <?= json_encode($fanpageOrderText, JSON_UNESCAPED_UNICODE) ?>;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            showNotice();
        }).catch(() => fallbackCopy(textToCopy));
    } else {
        fallbackCopy(textToCopy);
    }
}

function fallbackCopy(text) {
    const tempInput = document.createElement("textarea");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    try {
        document.execCommand("copy");
        showNotice();
    } catch (e) {
        alert("กรุณาคัดลอกข้อความนี้: " + text);
    }
    document.body.removeChild(tempInput);
}

function showNotice() {
    const notice = document.getElementById('copyNotice');
    if (notice) {
        notice.style.display = 'block';
        setTimeout(() => {
            notice.style.display = 'none';
        }, 5000);
    }
}

// 3D Parallax Tilt for Product Detail Page
const pStage = document.getElementById('productPageStage');
const p3d = document.getElementById('productPage3dWrapper');
if (pStage && p3d) {
    pStage.addEventListener('mousemove', (e) => {
        const rect = pStage.getBoundingClientRect();
        const xPercent = (e.clientX - rect.left) / rect.width - 0.5;
        const yPercent = (e.clientY - rect.top) / rect.height - 0.5;
        p3d.style.transform = `perspective(1000px) rotateX(${(-yPercent * 24).toFixed(2)}deg) rotateY(${(xPercent * 24).toFixed(2)}deg) scale3d(1.05, 1.05, 1.05)`;
    });
    pStage.addEventListener('mouseleave', () => {
        p3d.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
    });
}
</script>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>
