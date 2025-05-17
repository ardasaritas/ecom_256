<?php
// public/cart.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php";
require_once "../app/includes/functions.php";
require_once "../app/templates/header.php";
require_once "../app/templates/navbar.php";

if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $db->prepare("SELECT c.id as cart_id, c.quantity, p.*, u.name AS seller_name
                      FROM cart_items c
                      JOIN products p ON c.product_id = p.id
                      JOIN users u ON p.user_id = u.id
                      WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$total = 0;
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">🛒 Your Cart</h1>
        <a href="/consumer_dashboard.php" class="btn btn-outline-secondary">← Continue Shopping</a>
    </div>

    <?php if (count($items) > 0): ?>
        <div class="vstack gap-4">
            <?php foreach ($items as $item): 
                $item_total = $item['discounted_price'] * $item['quantity'];
                $total += $item_total;
            ?>
            <div class="card shadow-sm">
                <div class="row g-0 align-items-center">
                    <div class="col-md-2">
                        <img src="<?= htmlspecialchars($item['image_path']) ?: '/uploads/placeholder.jpg' ?>" class="img-fluid rounded-start w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($item['title']) ?>">
                    </div>
                    <div class="col-md-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($item['title']) ?></h5>
                                    <p class="text-muted small mb-2">by <?= htmlspecialchars($item['seller_name']) ?></p>
                                    <p class="mb-1">Unit Price: <strong><?= $item['discounted_price'] ?> TL</strong></p>
                                    <p class="mb-1">Subtotal: <strong><?= $item_total ?> TL</strong></p>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <div class="input-group mb-2" style="width: 120px;">
                                        <button class="btn btn-outline-secondary btn-sm update-qty" data-cart-id="<?= $item['cart_id'] ?>" data-change="-1">−</button>
                                        <input type="text" class="form-control text-center bg-white border-start-0 border-end-0" value="<?= $item['quantity'] ?>" readonly>
                                        <button class="btn btn-outline-secondary btn-sm update-qty" data-cart-id="<?= $item['cart_id'] ?>" data-change="1">+</button>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm remove-button" data-cart-id="<?= $item['cart_id'] ?>">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-5">
            <h4 class="mb-0">🧾 Total: <strong><?= $total ?> TL</strong></h4>
            <button class="btn btn-success" id="checkout-button">Checkout</button>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Your cart is empty. <a href="/consumer_dashboard.php" class="alert-link">Go shopping</a> 🛍️
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.update-qty').on('click', function() {
        const cartId = $(this).data('cart-id');
        const change = parseInt($(this).data('change'));
        const $input = $(this).siblings('input');
        let quantity = parseInt($input.val()) + change;

        if (quantity < 1) return;

        $.post('./ajax/update_cart.php', {
            action: 'update',
            cart_id: cartId,
            quantity: quantity
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.error);
            }
        }, 'json');
    });

    $('.remove-button').on('click', function() {
        let cartId = $(this).data('cart-id');

        $.post('./ajax/update_cart.php', {
            action: 'remove',
            cart_id: cartId
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.error);
            }
        }, 'json');
    });

    $('#checkout-button').on('click', function() {
        $.post('./ajax/update_cart.php', {
            action: 'checkout'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.error);
            }
        }, 'json');
    });
});
</script>

<?php require_once "../app/templates/footer.php"; ?>