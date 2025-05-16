<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php";
require "../app/templates/header.php";
require "../app/templates/navbar.php";
require "../app/controllers/consumer/dashboard.php";
?>

<div class="container py-5">
    <h1 class="text-center mb-5">Available Products</h1>

    <div class="row g-4">
        <?php if (count($active_products) > 0): ?>
            <?php foreach ($active_products as $product): ?>
                <?php $discount_percentage = calculateDiscountPercentage($product['normal_price'], $product['discounted_price']); ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if (!empty($product['image_path'])): ?>
                            <div class="ratio ratio-4x3">
                            <img src="<?= $product['image_path'] ?>"
                            class="w-100 h-100 object-fit-cover"
                            alt="<?= htmlspecialchars($product['title']) ?>"
                            onerror="this.onerror=null; this.src='/uploads/placeholder.jpg';">
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

    <?php if (isset($inactive_products) && count($inactive_products) > 0): ?>
        <h2 class="text-center my-5">Unavailable Products</h2>
        <div class="row g-4">
            <?php foreach ($inactive_products as $product): ?>
                <?php 
                    $expiry_date = new DateTime($product['expiration_date']);
                    $today = new DateTime();
                    $is_expired = $expiry_date < $today;
                    $is_out_of_stock = $product['stock'] <= 0;
                ?>
                <div class="col-md-4">
                    <div class="card h-100 border-secondary-subtle">
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= $product['image_path'] ?>" class="card-img-top fixed-product-image" alt="<?= htmlspecialchars($product['title']) ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                <span class="text-muted">No Image</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                            <p class="card-text mb-1">
                                <strong><?= $product['discounted_price'] ?> TL</strong>
                                <span class="text-muted text-decoration-line-through ms-2"><?= $product['normal_price'] ?> TL</span>
                            </p>
                            <p class="text-muted mb-1">Stock: <?= $product['stock'] ?></p>
                            <p class="text-muted mb-1">Seller: <?= htmlspecialchars($product['seller_name']) ?></p>
                            <p class="text-muted mb-1">Valid until: <?= $product['expiration_date'] ?></p>
                            <?php if ($is_expired): ?>
                                <div class="badge bg-danger mt-2">Expired</div>
                            <?php elseif ($is_out_of_stock): ?>
                                <div class="badge bg-secondary mt-2">Out of Stock</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require "../app/templates/footer.php" ?>