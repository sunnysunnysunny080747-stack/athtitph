<?php
require __DIR__ . '/config/conn_pdo.php';

$sampleProducts = [
    ['productID' => 'PRD-1048', 'productName' => 'เมล็ดกาแฟคั่วกลาง', 'category' => 'เครื่องดื่ม', 'price' => 320, 'stocQty' => 24, 'unit' => 'ถุง'],
    ['productID' => 'PRD-1047', 'productName' => 'แก้วเซรามิกมินิมอล', 'category' => 'ของใช้', 'price' => 185, 'stocQty' => 12, 'unit' => 'ใบ'],
    ['productID' => 'PRD-1046', 'productName' => 'ชาเขียวมัทฉะพรีเมียม', 'category' => 'เครื่องดื่ม', 'price' => 490, 'stocQty' => 4, 'unit' => 'กระปุก'],
    ['productID' => 'PRD-1045', 'productName' => 'สมุดบันทึกปกผ้า', 'category' => 'เครื่องเขียน', 'price' => 129, 'stocQty' => 38, 'unit' => 'เล่ม'],
    ['productID' => 'PRD-1044', 'productName' => 'น้ำผึ้งดอกลำไย', 'category' => 'ของกิน', 'price' => 260, 'stocQty' => 7, 'unit' => 'ขวด'],
];

$products = $sampleProducts;
$stats = [
    'products' => 248,
    'categories' => 18,
    'low_stock' => 7,
    'inventory_value' => 384250,
];
$usingSampleData = true;

// ใช้ข้อมูลจริงเมื่อมีสินค้า และบันทึกข้อผิดพลาดก่อนเปลี่ยนไปใช้ข้อมูลตัวอย่าง
if ($pdo instanceof PDO) {
    try {
        $summary = $pdo->query(
            'SELECT COUNT(*) AS product_count,
                    COUNT(DISTINCT category) AS category_count,
                    SUM(CASE WHEN stocQty <= 5 THEN 1 ELSE 0 END) AS low_stock_count,
                    COALESCE(SUM(price * stocQty), 0) AS inventory_value
             FROM tblproduct'
        )->fetch();
        $databaseProducts = $pdo->query(
            'SELECT productID, productName, category, price, stocQty, unit
             FROM tblproduct
             ORDER BY productID DESC
             LIMIT 6'
        )->fetchAll();

        if ($databaseProducts !== []) {
            $products = $databaseProducts;
            $stats = [
                'products' => (int) $summary['product_count'],
                'categories' => (int) $summary['category_count'],
                'low_stock' => (int) $summary['low_stock_count'],
                'inventory_value' => (float) $summary['inventory_value'],
            ];
            $usingSampleData = false;
        }
    } catch (PDOException $exception) {
        error_log('Dashboard data query error: ' . $exception->getMessage());
    }
}

$userName = 'ผู้ดูแลระบบ';
$userInitial = function_exists('mb_substr')
    ? mb_substr($userName, 0, 1, 'UTF-8')
    : (preg_match('/^./us', $userName, $initialMatch) ? $initialMatch[0] : '?');
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$today = new DateTimeImmutable('now', new DateTimeZone('Asia/Bangkok'));
$thaiDate = $today->format('j') . ' ' . $thaiMonths[(int) $today->format('n') - 1] . ' พ.ศ. ' . ((int) $today->format('Y') + 543);
$chartValues = [42, 64, 51, 82, 59, 73, 94];
$chartLabels = ['จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.', 'อา.'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f2a43">
    <title>ภาพรวมระบบ | Bech16</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="top-accent" aria-hidden="true"></div>
    <div class="page-progress" id="pageProgress" aria-hidden="true"></div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <aside class="sidebar" id="sidebar" aria-label="แถบเมนูหลัก">
        <div class="sidebar-brand">
            <a class="brand-lockup" href="index.php" aria-label="Bech16 หน้าภาพรวม">
                <span class="brand-mark"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i></span>
                <span class="brand-copy"><strong>Bech16</strong><small>จัดการธุรกิจของคุณ</small></span>
            </a>
            <button class="icon-button sidebar-collapse" id="collapseSidebar" type="button" aria-label="ย่อแถบเมนู" aria-pressed="false">
                <i class="bi bi-layout-sidebar-inset" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="sidebar-nav" aria-label="เมนูระบบ">
            <p class="nav-group-title">เมนูหลัก</p>
            <span class="active-indicator" id="activeIndicator" aria-hidden="true"></span>
            <a class="nav-link-item is-active" href="index.php" aria-label="ภาพรวม" aria-current="page" data-tooltip="ภาพรวม">
                <i class="bi bi-house-door" aria-hidden="true"></i><span>ภาพรวม</span>
            </a>
            <button class="nav-link-item accordion-trigger" type="button" aria-label="คลังสินค้า" aria-expanded="false" aria-controls="inventorySubmenu" data-tooltip="คลังสินค้า">
                <i class="bi bi-box-seam" aria-hidden="true"></i><span>คลังสินค้า</span><i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </button>
            <div class="submenu" id="inventorySubmenu" aria-hidden="true">
                <a href="testdb.php" class="submenu-link" tabindex="-1" data-tooltip="รายการสินค้า">รายการสินค้า</a>
                <a href="testdb.php" class="submenu-link" tabindex="-1" data-tooltip="หมวดหมู่สินค้า">หมวดหมู่สินค้า</a>
                <a href="testdb.php" class="submenu-link" tabindex="-1" data-tooltip="ตรวจนับสต็อก">ตรวจนับสต็อก</a>
            </div>
            <button class="nav-link-item accordion-trigger" type="button" aria-label="การขาย" aria-expanded="false" aria-controls="salesSubmenu" data-tooltip="การขาย">
                <i class="bi bi-receipt" aria-hidden="true"></i><span>การขาย</span><i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
            </button>
            <div class="submenu" id="salesSubmenu" aria-hidden="true">
                <a href="#" class="submenu-link" tabindex="-1" data-tooltip="รายการขาย">รายการขาย</a>
                <a href="#" class="submenu-link" tabindex="-1" data-tooltip="รายงานยอดขาย">รายงานยอดขาย</a>
            </div>
            <a class="nav-link-item" href="#" aria-label="ลูกค้า" data-tooltip="ลูกค้า">
                <i class="bi bi-people" aria-hidden="true"></i><span>ลูกค้า</span>
            </a>

            <p class="nav-group-title nav-group-spaced">จัดการระบบ</p>
            <a class="nav-link-item" href="#" aria-label="รายงาน" data-tooltip="รายงาน">
                <i class="bi bi-bar-chart-line" aria-hidden="true"></i><span>รายงาน</span>
            </a>
            <a class="nav-link-item" href="#" aria-label="ตั้งค่า" data-tooltip="ตั้งค่า">
                <i class="bi bi-gear" aria-hidden="true"></i><span>ตั้งค่า</span>
            </a>
        </nav>

        <section class="sidebar-user" aria-label="บัญชีผู้ใช้">
            <span class="avatar avatar-sidebar" aria-hidden="true"><?= $escape($userInitial) ?></span>
            <span class="user-copy"><strong><?= $escape($userName) ?></strong><small>ผู้ดูแลระบบ</small></span>
            <button class="icon-button user-more" type="button" aria-label="ตัวเลือกบัญชี" data-tooltip="ตัวเลือกบัญชี"><i class="bi bi-three-dots" aria-hidden="true"></i></button>
        </section>
    </aside>

    <div class="app-shell" id="appShell">
        <header class="topbar">
            <div class="topbar-left">
                <button class="icon-button mobile-menu-button" id="openSidebar" type="button" aria-label="เปิดเมนู" aria-expanded="false" aria-controls="sidebar">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <button class="icon-button desktop-menu-button" id="collapseSidebarTop" type="button" aria-label="ย่อหรือขยายแถบเมนู" aria-pressed="false">
                    <i class="bi bi-layout-sidebar" aria-hidden="true"></i>
                </button>
                <div class="topbar-heading"><span class="eyebrow">พื้นที่ทำงาน</span><h1>ภาพรวม</h1></div>
            </div>
            <div class="topbar-actions">
                <label class="search-box">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <span class="sr-only">ค้นหาในระบบ</span>
                    <input type="search" placeholder="ค้นหา..." aria-label="ค้นหาในระบบ">
                    <kbd>/</kbd>
                </label>
                <button class="icon-button notification-button" type="button" aria-label="การแจ้งเตือน มีรายการใหม่">
                    <i class="bi bi-bell" aria-hidden="true"></i><span class="notification-dot" aria-hidden="true"></span>
                </button>
                <div class="profile-menu">
                    <button class="profile-trigger" id="profileTrigger" type="button" aria-expanded="false" aria-haspopup="true" aria-controls="profileDropdown">
                        <span class="avatar"><?= $escape($userInitial) ?></span>
                        <span class="profile-name"><?= $escape($userName) ?></span>
                        <i class="bi bi-chevron-down profile-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="profile-dropdown" id="profileDropdown" aria-hidden="true">
                        <span class="dropdown-caption"><?= $escape($userName) ?></span>
                        <a href="#" class="dropdown-item" tabindex="-1"><i class="bi bi-person" aria-hidden="true"></i>โปรไฟล์ของฉัน</a>
                        <a href="#" class="dropdown-item" tabindex="-1"><i class="bi bi-sliders" aria-hidden="true"></i>ตั้งค่าบัญชี</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="main-content" id="mainContent">
            <section class="welcome-row reveal">
                <div>
                    <p class="welcome-kicker"><span class="live-dot" aria-hidden="true"></span>ภาพรวมธุรกิจของคุณ</p>
                    <h2>สวัสดี, <?= $escape($userName) ?> <span aria-hidden="true">👋</span></h2>
                    <p class="welcome-subtitle">นี่คือสิ่งที่เกิดขึ้นกับธุรกิจของคุณในวันนี้</p>
                </div>
                <div class="date-pill"><i class="bi bi-calendar3" aria-hidden="true"></i><time datetime="<?= $today->format('Y-m-d') ?>"><?= $escape($thaiDate) ?></time></div>
            </section>

            <section class="stats-grid" aria-label="สถิติภาพรวม">
                <article class="stat-card reveal" style="--reveal-order: 1">
                    <div class="stat-top"><span class="stat-icon icon-blue"><i class="bi bi-box-seam" aria-hidden="true"></i></span><span class="stat-trend trend-up"><i class="bi bi-arrow-up-right" aria-hidden="true"></i> 8.2%</span></div>
                    <p class="stat-label">สินค้าทั้งหมด</p><p class="stat-value" data-count="<?= $escape($stats['products']) ?>">0</p><p class="stat-note">รายการในระบบ</p>
                </article>
                <article class="stat-card reveal" style="--reveal-order: 2">
                    <div class="stat-top"><span class="stat-icon icon-teal"><i class="bi bi-collection" aria-hidden="true"></i></span><span class="stat-trend trend-up"><i class="bi bi-arrow-up-right" aria-hidden="true"></i> 3.1%</span></div>
                    <p class="stat-label">หมวดหมู่</p><p class="stat-value" data-count="<?= $escape($stats['categories']) ?>">0</p><p class="stat-note">หมวดหมู่สินค้า</p>
                </article>
                <article class="stat-card reveal" style="--reveal-order: 3">
                    <div class="stat-top"><span class="stat-icon icon-amber"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span><span class="stat-trend trend-warn">ต้องตรวจสอบ</span></div>
                    <p class="stat-label">สินค้าใกล้หมด</p><p class="stat-value" data-count="<?= $escape($stats['low_stock']) ?>">0</p><p class="stat-note">รายการที่เหลือน้อย</p>
                </article>
                <article class="stat-card reveal" style="--reveal-order: 4">
                    <div class="stat-top"><span class="stat-icon icon-slate"><i class="bi bi-currency-baht" aria-hidden="true"></i></span><span class="stat-trend trend-neutral"><i class="bi bi-clock" aria-hidden="true"></i> มูลค่าคงคลัง</span></div>
                    <p class="stat-label">มูลค่าสินค้าคงคลัง</p><p class="stat-value stat-currency" data-count="<?= $escape((int) $stats['inventory_value']) ?>" data-currency="true">0</p><p class="stat-note">บาท</p>
                </article>
            </section>

            <section class="content-grid">
                <article class="panel chart-panel reveal" style="--reveal-order: 5">
                    <div class="panel-heading">
                        <div><p class="panel-kicker">สรุปกิจกรรม</p><h3>ความเคลื่อนไหวประจำสัปดาห์</h3></div>
                        <button class="select-button" type="button" aria-label="ช่วงเวลา สัปดาห์นี้">สัปดาห์นี้ <i class="bi bi-chevron-down" aria-hidden="true"></i></button>
                    </div>
                    <div class="chart-summary"><strong>1,284</strong><span>รายการที่จัดการ</span><span class="chart-growth"><i class="bi bi-arrow-up-right" aria-hidden="true"></i> 12.8%</span></div>
                    <div class="bar-chart" role="img" aria-label="กราฟกิจกรรมรายวัน: วันจันทร์ 42, วันอังคาร 64, วันพุธ 51, วันพฤหัสบดี 82, วันศุกร์ 59, วันเสาร์ 73, วันอาทิตย์ 94">
                        <?php foreach ($chartValues as $index => $value): ?>
                            <div class="bar-column"><div class="bar-track"><span class="chart-bar" style="--bar-height: <?= $value ?>%; --bar-order: <?= $index ?>"></span></div><span class="bar-label"><?= $escape($chartLabels[$index]) ?></span></div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="panel insight-panel reveal" style="--reveal-order: 6">
                    <div class="panel-heading">
                        <div><p class="panel-kicker">สถานะสต็อก</p><h3>ภาพรวมคลังสินค้า</h3></div>
                        <span class="status-orb" aria-hidden="true"><i class="bi bi-boxes"></i></span>
                    </div>
                    <div class="stock-highlight"><span class="stock-number"><?= $escape(number_format((int) $stats['products'])) ?></span><span>สินค้าพร้อมจัดการ<br>ในระบบของคุณ</span></div>
                    <div class="stock-meter" aria-label="สัดส่วนสต็อกปกติ 78 เปอร์เซ็นต์"><span></span></div>
                    <div class="stock-legend"><span><i class="legend-dot dot-normal"></i>สต็อกปกติ <strong>78%</strong></span><span><i class="legend-dot dot-low"></i>ใกล้หมด <strong><?= $escape((int) $stats['low_stock']) ?> รายการ</strong></span></div>
                    <a class="text-link" href="testdb.php">ดูรายการสินค้า <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>
            </section>

            <section class="panel table-panel reveal" style="--reveal-order: 7" aria-labelledby="latestHeading">
                <div class="panel-heading table-heading">
                    <div><p class="panel-kicker">อัปเดตล่าสุด</p><h3 id="latestHeading">รายการสินค้า</h3></div>
                    <a class="outline-button" href="testdb.php">ดูทั้งหมด <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
                <?php if ($usingSampleData): ?><p class="data-note"><i class="bi bi-info-circle" aria-hidden="true"></i>กำลังแสดงข้อมูลตัวอย่าง<?= $pdo instanceof PDO ? ' เนื่องจากยังไม่มีข้อมูลสินค้า' : ' เนื่องจากยังไม่ได้เชื่อมต่อฐานข้อมูล' ?></p><?php endif; ?>
                <div class="table-wrap">
                    <table class="table dashboard-table">
                        <thead><tr><th scope="col">สินค้า</th><th scope="col">รหัสสินค้า</th><th scope="col">หมวดหมู่</th><th scope="col" class="text-right">ราคา</th><th scope="col">สถานะสต็อก</th></tr></thead>
                        <tbody id="productTableBody">
                            <?php foreach ($products as $product): ?>
                                <?php $isLowStock = (int) $product['stocQty'] <= 5; ?>
                                <tr>
                                    <td><span class="product-cell"><span class="product-thumb" aria-hidden="true"><i class="bi bi-box"></i></span><strong><?= $escape($product['productName']) ?></strong></span></td>
                                    <td class="muted-cell"><?= $escape($product['productID']) ?></td>
                                    <td><?= $escape($product['category']) ?></td>
                                    <td class="text-right price-cell">฿<?= $escape(number_format((float) $product['price'], 2)) ?></td>
                                    <td><span class="stock-badge <?= $isLowStock ? 'badge-low' : 'badge-good' ?>"><span></span><?= $isLowStock ? 'ใกล้หมด · ' . $escape($product['stocQty']) . ' ' . $escape($product['unit']) : 'มีสินค้า · ' . $escape($product['stocQty']) . ' ' . $escape($product['unit']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <footer class="page-footer"><span>© <?= $today->format('Y') ?> Bech16 · ระบบจัดการข้อมูล</span><span><i class="bi bi-shield-check" aria-hidden="true"></i> ข้อมูลของคุณปลอดภัย</span></footer>
        </main>
    </div>
    <script src="js/main.js" defer></script>
</body>
</html>
