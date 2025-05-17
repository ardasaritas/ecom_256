<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php";
require_once "../app/includes/functions.php";  // Fonksiyonlar burada tanımlı
require "../app/templates/header.php";
require "../app/templates/navbar.php";

$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 4;
$offset = ($page - 1) * $per_page;

list($active_products, $total_pages) = searchProducts($db, $_SESSION['user']['city'], $_SESSION['user']['district'], $search_keyword, $offset, $per_page);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Available Products</h1>
        <form class="d-flex" method="GET" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search_keyword) ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </div>

    <div class="row g-4">
        <?php if (count($active_products) > 0): ?>
            <?php foreach ($active_products as $product): ?>
                <?php 
                $discount_percentage = 0;
                if ($product['normal_price'] > $product['discounted_price']) {
                    $discount_percentage = round(100 - ($product['discounted_price'] / $product['normal_price'] * 100));
                }
                ?>
                <div class="col-md-3">
                    <div class="card h-100">
                        <?php if (!empty($product['image_path'])): ?>
                            <div class="ratio ratio-4x3">
                                <img src="<?= htmlspecialchars($product['image_path']) ?>" class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($product['title']) ?>" onerror="this.onerror=null; this.src='/uploads/placeholder.jpg';">
                            </div>
                        <?php else: ?>
                            <div class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center">
                                <span class="text-muted">No Image</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                            <p class="card-text mb-1">
                                <strong><?= $product['discounted_price'] ?> TL</strong>
                                <?php if ($discount_percentage > 0): ?>
                                    <span class="text-muted text-decoration-line-through ms-2"><?= $product['normal_price'] ?> TL</span>
                                    <span class="badge bg-danger ms-2">-<?= $discount_percentage ?>%</span>
                                <?php endif; ?>
                            </p>
                            <p class="text-muted mb-1">Stock: <?= $product['stock'] ?></p>
                            <p class="text-muted mb-1">Seller: <?= htmlspecialchars($product['seller_name']) ?></p>
                            <p class="text-muted mb-1">Location: <?= htmlspecialchars($product['city']) ?> / <?= htmlspecialchars($product['district']) ?></p>
                            <p class="text-muted mb-1">Valid until: <?= $product['expiration_date'] ?></p>
                        </div>
                        <div class="card-footer d-flex gap-2">
                            <form method="POST" action="/ajax/purchase.php" class="flex-fill">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">No products available at the moment.</div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages >= 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search_keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require "../app/templates/footer.php" ?>
