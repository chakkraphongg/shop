<?php
/**
 * DEKROYSHOP - Gaming Cosmetic Store & Arsenal
 * Pure PHP 8.4 + MySQL | 5-Columns Grid | Dark Blue Aesthetic | Real 3D Models
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/cart.php';

// Handle Add to Cart POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    require_csrf();
    $productId = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));

    $res = add_to_cart($productId, $qty);
    set_flash($res['success'] ? 'success' : 'danger', $res['message']);
    redirect($_SERVER['REQUEST_URI'] ?: APP_URL . '/index.php');
}

// Filters & Parameters
$categorySlug = trim($_GET['category'] ?? '');
if ($categorySlug === '') {
    $categorySlug = 'hero'; // Default directly to HERO (no ALL mode)
}

$priceFilter  = trim($_GET['price'] ?? '');
$search       = trim($_GET['search'] ?? '');
$sort         = trim($_GET['sort'] ?? 'popular');
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 50; // 5 columns x 10 rows

$pdo = db();

// Fetch all 7 categories
$categories = $pdo->query("
    SELECT c.id, c.name, c.slug, c.description, COUNT(p.id) AS total_items
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
    WHERE c.status = 'active'
    GROUP BY c.id
    ORDER BY c.sort_order ASC
")->fetchAll();

// Category Slug to ID mapping
$catMap = [];
$activeCategoryName = 'HERO';
$activeCategoryDesc = 'ตัวละครพิเศษ สกินฮีโร่ และผู้รอดชีวิต';
$activeCategoryId = 4;

foreach ($categories as $cat) {
    $catMap[$cat['slug']] = $cat;
    if ($categorySlug === $cat['slug']) {
        $activeCategoryName = $cat['name'];
        $activeCategoryDesc = $cat['description'] ?? '';
        $activeCategoryId = (int)$cat['id'];
    }
}

// Build Query Conditions
$where = ["p.status = 'active'"];
$params = [];

if ($activeCategoryId > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $activeCategoryId;
}

if ($priceFilter === '15') {
    $where[] = "p.price = 15.00";
} elseif ($priceFilter === '20') {
    $where[] = "p.price = 20.00";
}

if ($search !== '') {
    $where[] = "(p.name LIKE ? OR p.item_code LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$whereSql = implode(' AND ', $where);

// Sorting
$orderBy = match ($sort) {
    'price_asc'  => 'p.price ASC, p.id DESC',
    'price_desc' => 'p.price DESC, p.id DESC',
    'name_asc'   => 'p.name ASC',
    'newest'     => 'p.id DESC',
    default      => 'p.price ASC, p.id DESC' // prioritize 15.- sale items first
};

// Count total matching items
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE {$whereSql}");
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalProducts / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

// Fetch Products for Current Page
$prodStmt = $pdo->prepare("
    SELECT 
        p.id, p.name, p.slug, p.item_code, p.price, p.original_price, p.stock, p.image, p.description,
        c.name AS category_name, c.slug AS cat_slug
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE {$whereSql}
    ORDER BY {$orderBy}
    LIMIT {$perPage} OFFSET {$offset}
");
$prodStmt->execute($params);
$products = $prodStmt->fetchAll();

// Category Icon Mapping
$catIcons = [
    'hero'     => '🦸',
    'set'      => '🎁',
    'weapons'  => '🔫',
    'armor'    => '🛡️',
    'head'     => '🪖',
    'backpack' => '🎒',
    'others'   => '📦',
];

$pageTitle = 'DEKROYSHOP';
require_once __DIR__ . '/includes/frontend-header.php';
?>

<div class="fn-container">
    <!-- 1. Hero Title Banner -->
    <div class="fn-catalog-hero">
        <h1 class="fn-hero-main-title">
            DEKROYSHOP
        </h1>
        <div class="fn-hero-subtitle">
            <?= e($activeCategoryName) ?> &bull; SHOWING <?= number_format($totalProducts) ?> COSMETICS &bull; ทุกอย่าง 20.- &bull; ลดราคา 15.-
        </div>
    </div>

    <!-- 2. Horizontal Category Icons Bar (No ALL Mode - 7 Distinct Categories) -->
    <div class="fn-category-bar-wrapper">
        <div class="fn-category-bar">
            <?php foreach ($categories as $cat): ?>
                <?php
                $isActive = ($categorySlug === $cat['slug']);
                $icon = $catIcons[$cat['slug']] ?? '🎮';
                ?>
                <a href="?category=<?= urlencode($cat['slug']) ?><?= $priceFilter !== '' ? '&price=' . urlencode($priceFilter) : '' ?>" 
                   class="fn-cat-btn <?= $isActive ? 'active' : '' ?>"
                   title="<?= e($cat['description'] ?? $cat['name']) ?>">
                    <span class="fn-cat-icon"><?= $icon ?></span>
                    <span class="fn-cat-label"><?= e($cat['name']) ?> (<?= number_format((int)$cat['total_items']) ?>)</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3. Search & Filter Bar -->
    <div class="fn-filter-bar">
        <!-- Search Input -->
        <form method="GET" action="<?= APP_URL ?>/index.php" class="fn-search-wrap">
            <input type="hidden" name="category" value="<?= e($categorySlug) ?>">
            <?php if ($priceFilter !== ''): ?>
                <input type="hidden" name="price" value="<?= e($priceFilter) ?>">
            <?php endif; ?>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--fn-text-muted);">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="SEARCH ITEM NAME OR CODE...">
        </form>

        <!-- Quick Price & Rarity Filter Pills -->
        <div class="fn-filter-pills">
            <?php
            $baseParams = $_GET;
            unset($baseParams['price'], $baseParams['page']);
            ?>
            <a href="?<?= http_build_query($baseParams) ?>" class="fn-pill-btn <?= $priceFilter === '' ? 'active' : '' ?>">
                ALL PRICES
            </a>
            <a href="?<?= http_build_query(array_merge($baseParams, ['price' => '15'])) ?>" class="fn-pill-btn fn-pill-sale <?= $priceFilter === '15' ? 'active' : '' ?>">
                🔥 SALE 15.- (ลดราคา)
            </a>
            <a href="?<?= http_build_query(array_merge($baseParams, ['price' => '20'])) ?>" class="fn-pill-btn <?= $priceFilter === '20' ? 'active' : '' ?>">
                20.- (ราคาปกติ)
            </a>

            <!-- Sort Select -->
            <div class="fn-select-wrap">
                <select onchange="window.location.href='?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>&sort=' + this.value" aria-label="Sort items">
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>SORT: RECOMMENDED</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>PRICE: 15.- &rarr; 20.-</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>PRICE: 20.- &rarr; 15.-</option>
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>NEWEST FIRST</option>
                    <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>NAME (A-Z)</option>
                </select>
            </div>

            <?php if ($priceFilter !== '' || $search !== ''): ?>
                <a href="?category=<?= urlencode($categorySlug) ?>" class="fn-pill-btn" style="background:rgba(239,68,68,0.15);border-color:rgba(239,68,68,0.3);color:#f87171;" title="ล้างตัวกรอง">
                    ✕ CLEAR FILTERS
                </a>
            <?php endif; ?>

            <!-- Fanpage CTA Pill -->
            <a href="https://www.facebook.com/dekroyzz" target="_blank" rel="noopener noreferrer" class="fn-pill-btn" style="background:rgba(24,119,242,0.18);border-color:rgba(24,119,242,0.4);color:#60a5fa;" title="ทักสอบถามแอดมินแฟนเพจ">
                💬 แฟนเพจ DEKROYZZ
            </a>
        </div>
    </div>

    <!-- 4. 5-Columns Responsive Gaming Grid -->
    <?php if (empty($products)): ?>
        <div style="text-align:center; padding:90px 20px; background:var(--fn-surface); border:1px solid var(--fn-border); border-radius:var(--fn-radius-lg); margin-bottom:50px;">
            <div style="font-size:48px; margin-bottom:12px;">🔍</div>
            <h3 style="font-size:22px; font-weight:800; color:#fff; text-transform:uppercase; margin-bottom:8px;">
                NO COSMETICS FOUND
            </h3>
            <p style="color:var(--fn-text-body); font-size:14px; margin-bottom:20px;">
                ไม่พบไอเทมตามเงื่อนไขการค้นหา ลองเปลี่ยนคำค้นหา หรือสอบถามทีมงานผ่าน Facebook Fanpage
            </p>
            <a href="?category=<?= urlencode($categorySlug) ?>" class="fn-pill-btn" style="background:var(--fn-yellow); color:#141518; padding:10px 24px; font-weight:800;">
                RESET CATEGORY
            </a>
        </div>
    <?php else: ?>
        <div class="fn-cosmetics-grid">
            <?php foreach ($products as $item): ?>
                <?php
                $baseImg = !empty($item['image']) ? APP_URL . '/' . ltrim($item['image'], '/') : APP_URL . '/assets/images/no-image.svg';
                $cardImg = ($item['cat_slug'] === 'hero') ? get_item_card_preview_image($item['item_code'], $item['name'], $item['cat_slug'], $baseImg) : $baseImg;
                $fullImg = ($item['cat_slug'] === 'hero') ? get_item_smart_preview_image($item['item_code'], $item['name'], $item['cat_slug'], $baseImg) : $baseImg;
                $isSale = ((float)$item['price'] < (float)$item['original_price']);
                $rarityClass = 'rarity-' . ($item['cat_slug'] ?: 'weapons');
                $subtag = get_item_subtag($item['name'], $item['item_code'], $item['cat_slug'] ?? 'hero', (int)$item['id']);
                ?>
                <article class="fn-card <?= $rarityClass ?>"
                         data-id="<?= (int)$item['id'] ?>"
                         data-name="<?= e($item['name']) ?>"
                         data-code="<?= e($item['item_code']) ?>"
                         data-price="<?= (float)$item['price'] ?>"
                         data-orig-price="<?= (float)$item['original_price'] ?>"
                         data-image="<?= e($cardImg) ?>"
                         data-full-image="<?= e($fullImg) ?>"
                         data-category="<?= e($item['category_name'] ?? 'COSMETIC') ?>"
                         data-cat-slug="<?= e($item['cat_slug'] ?? 'hero') ?>"
                         tabindex="0"
                         role="button"
                         onclick="openFullBody3DModal('<?= e($item['item_code'] ?: $item['name']) ?>', '<?= e($item['name']) ?>', <?= (int)$item['id'] ?>)"
                         aria-label="3D Model <?= e($item['name']) ?>">

                    <!-- Visual Top Section (Large Character on Navy Blue Gradient) -->
                    <div class="fn-card-visual">
                        <!-- Top-Left Floating Badges -->
                        <div class="fn-card-top-badges">
                            <span class="fn-badge-stock"><span class="stock-dot">●</span> พร้อมส่ง</span>
                            <?php if ($isSale): ?>
                                <span class="fn-badge-sale">🔥 SALE 15.-</span>
                            <?php endif; ?>
                        </div>

                        <!-- 3D Rotate Button Bottom-Right -->
                        <button type="button" class="fn-badge-3d" onclick="openFullBody3DModal('<?= e($item['item_code'] ?: $item['name']) ?>', '<?= e($item['name']) ?>', <?= (int)$item['id'] ?>); event.stopPropagation();" title="หมุนโมเดล 3D แบบเต็มตัว">
                            <span class="icon">🔄</span> หมุน 3D
                        </button>

                        <!-- Large Character / Item Visual -->
                        <img src="<?= e($cardImg) ?>" alt="<?= e($item['name']) ?>" width="260" height="260" class="fn-card-img <?= $item['cat_slug'] === 'hero' ? 'hero-card-zoom' : '' ?>" loading="lazy">
                    </div>

                    <!-- Info Bottom Section -->
                    <div class="fn-card-body">
                        <!-- Sub-tag & ID Row -->
                        <div class="fn-card-meta-row">
                            <span class="fn-card-subtag"><?= e($subtag) ?></span>
                            <span class="fn-card-item-id">#<?= str_pad((string)$item['id'], 6, '0', STR_PAD_LEFT) ?></span>
                        </div>

                        <!-- Item Title -->
                        <h3 class="fn-card-name" title="<?= e($item['name']) ?>"><?= e($item['name']) ?></h3>

                        <!-- Price Row -->
                        <div class="fn-card-pricing">
                            <?php if ($isSale): ?>
                                <span class="fn-price-current sale">฿15</span>
                                <span class="fn-price-original">฿20</span>
                            <?php else: ?>
                                <span class="fn-price-current">฿20</span>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons Grid -->
                        <div class="fn-card-actions-grid">
                            <button type="button" class="fn-btn-card-inspect" onclick="openFullBody3DModal('<?= e($item['item_code'] ?: $item['name']) ?>', '<?= e($item['name']) ?>', <?= (int)$item['id'] ?>); event.stopPropagation();">
                                รายละเอียด
                            </button>
                            <a href="https://www.facebook.com/dekroyzz" target="_blank" rel="noopener noreferrer" class="fn-btn-card-order" onclick="event.stopPropagation();" title="ทักสอบถาม/สั่งซื้อผ่าน Facebook">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                <span>สั่งซื้อเพจ</span>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- 5. Dark Pill Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="fn-pagination">
                <?php $qp = $_GET; ?>
                <?php if ($page > 1): ?>
                    <?php $qp['page'] = $page - 1; ?>
                    <a href="?<?= http_build_query($qp) ?>" class="fn-page-link" aria-label="Previous page">&laquo;</a>
                <?php endif; ?>

                <?php for ($p = max(1, $page - 3); $p <= min($totalPages, $page + 3); $p++): ?>
                    <?php $qp['page'] = $p; ?>
                    <a href="?<?= http_build_query($qp) ?>" class="fn-page-link <?= $p === $page ? 'active' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <?php $qp['page'] = $page + 1; ?>
                    <a href="?<?= http_build_query($qp) ?>" class="fn-page-link" aria-label="Next page">&raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>
