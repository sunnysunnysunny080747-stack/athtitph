<?php require __DIR__ . '/config/conn_pdo.php';

$products = [];
$errorMessage = '';

if (isset($conn) && $conn instanceof PDO) {
    try {
        $products = $conn
            ->query('SELECT productID, productName, category, price, stocQty, unit FROM tblproduct ORDER BY productID')
            ->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Product listing error: ' . $exception->getMessage());
        $errorMessage = 'ไม่สามารถดึงข้อมูลสินค้าได้ กรุณาตรวจสอบตาราง tblproduct';
    }
} else {
    $errorMessage = 'เชื่อมต่อฐานข้อมูลไม่สำเร็จ';
}

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
<script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
    <main class="container py-5">
        <div class="mb-4">
            <h1 class="h3 mb-2">รายการสินค้า</h1>
            <p class="text-muted mb-0">ข้อมูลจากตาราง <code>tblproduct</code></p>
        </div>

        <?php if ($errorMessage !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?= $escape($errorMessage) ?>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    สินค้าทั้งหมด <?= count($products) ?> รายการ
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="theme-warning">
                                                        <tr>
                                <th scope="col">รหัสสินค้า</th>
                                <th scope="col">ชื่อสินค้า</th>
                                <th scope="col">หมวดหมู่</th>
                                <th scope="col" class="text-end">ราคา</th>
                                <th scope="col" class="text-end">จำนวนคงเหลือ</th>
                                <th scope="col">หน่วย</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products === []): ?>
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-muted">ยังไม่มีสินค้าในตาราง</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= $escape($product['productID']) ?></td>
                                        <td><?= $escape($product['productName']) ?></td>
                                        <td><?= $escape($product['category']) ?></td>
                                        <td class="text-end"><?= number_format((float) $product['price'], 2) ?></td>
                                        <td class="text-end"><?= $escape($product['stocQty']) ?></td>
                                        <td><?= $escape($product['unit']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>