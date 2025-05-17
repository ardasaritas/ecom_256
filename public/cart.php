<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php";
require "../app/templates/header.php";
require "../app/templates/navbar.php";

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    $_SESSION['error'] = "You must be logged in to view your cart";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Retrieve cart items with product details
try {
    $stmt = $db->prepare("
        SELECT c.id as cart_id, c.quantity, c.added_at,
               p.id as product_id, p.title, p.normal_price, p.discounted_price, p.stock, p.expiration_date, p.image_path,
               u.name as seller_name, u.city, u.district
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        JOIN users u ON p.user_id = u.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle the case where the join fails
    error_log("Cart retrieval error: " . $e->getMessage());
    $cart_items = [];
}

// Calculate totals
$cart_total = 0;
$cart_savings = 0;
$total_items = 0;

foreach ($cart_items as &$item) {
    // Add a flag for expired products
    $expiry_date = new DateTime($item['expiration_date']);
    $today = new DateTime();
    $item['is_expired'] = $expiry_date < $today;
    
    // Add a flag for out-of-stock products
    $item['is_out_of_stock'] = $item['stock'] <= 0;
    
    // Calculate item total
    $item['item_total'] = $item['quantity'] * $item['discounted_price'];
    $item['item_savings'] = $item['quantity'] * ($item['normal_price'] - $item['discounted_price']);
    
    // Only add to totals if product is valid
    if (!$item['is_expired'] && !$item['is_out_of_stock']) {
        $cart_total += $item['item_total'];
        $cart_savings += $item['item_savings'];
        $total_items += $item['quantity'];
    }
    
    // Calculate discount percentage
    if ($item['normal_price'] > $item['discounted_price']) {
        $item['discount_percentage'] = round(100 - ($item['discounted_price'] / $item['normal_price'] * 100));
    } else {
        $item['discount_percentage'] = 0;
    }
}

// Process cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        // Update cart item quantity
        if ($_POST['action'] === 'update' && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
            $cart_id = (int)$_POST['cart_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity <= 0) {
                // Remove item if quantity is 0 or negative
                $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
                $stmt->execute([$cart_id, $user_id]);
                $_SESSION['success'] = "Item removed from cart";
            } else {
                // Check product stock before updating
                try {
                    $stmt = $db->prepare("
                        SELECT p.stock, c.product_id 
                        FROM cart_items c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.id = ? AND c.user_id = ?
                    ");
                    $stmt->execute([$cart_id, $user_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($product && $quantity <= $product['stock']) {
                        $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
                        $stmt->execute([$quantity, $cart_id, $user_id]);
                        $_SESSION['success'] = "Cart updated successfully";
                    } else {
                        $_SESSION['error'] = "Cannot update quantity: exceeds available stock";
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Could not update cart: " . $e->getMessage();
                }
            }
            
            header("Location: cart.php");
            exit();
        }
        
        // Remove item from cart
        if ($_POST['action'] === 'remove' && isset($_POST['cart_id'])) {
            $cart_id = (int)$_POST['cart_id'];
            
            try {
                $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
                $stmt->execute([$cart_id, $user_id]);
                $_SESSION['success'] = "Item removed from cart";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Could not remove item: " . $e->getMessage();
            }
            
            header("Location: cart.php");
            exit();
        }
        
        // Clear entire cart
        if ($_POST['action'] === 'clear') {
            try {
                $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $_SESSION['success'] = "Cart cleared successfully";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Could not clear cart: " . $e->getMessage();
            }
            
            header("Location: cart.php");
            exit();
        }
        
        // Checkout
        if ($_POST['action'] === 'checkout') {
            try {
                // Begin transaction
                $db->beginTransaction();
                
                // Get all valid cart items for this user
                $stmt = $db->prepare("
                    SELECT c.id, c.product_id, c.quantity, p.stock
                    FROM cart_items c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.user_id = ? AND p.expiration_date >= CURDATE() AND p.stock > 0
                ");
                $stmt->execute([$user_id]);
                $checkout_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($checkout_items)) {
                    $_SESSION['warning'] = "No valid items to checkout";
                    $db->rollBack();
                    header("Location: cart.php");
                    exit();
                }
                
                $purchased_count = 0;
                
                // Process products for the checkout
                foreach ($checkout_items as $item) {
                    // Verify current stock level (it might have changed)
                    $stock_check = $db->prepare("SELECT stock FROM products WHERE id = ?");
                    $stock_check->execute([$item['product_id']]);
                    $current_stock = $stock_check->fetchColumn();
                    
                    // Calculate actual quantity to purchase (limit to available stock)
                    $purchase_quantity = min($item['quantity'], $current_stock);
                    
                    if ($purchase_quantity <= 0) {
                        continue; // Skip if no stock available
                    }
                    
                    // Delete the product if we're buying all of it, otherwise update stock
                    if ($purchase_quantity >= $current_stock) {
                        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
                        $stmt->execute([$item['product_id']]);
                    } else {
                        $new_stock = $current_stock - $purchase_quantity;
                        $stmt = $db->prepare("UPDATE products SET stock = ? WHERE id = ?");
                        $stmt->execute([$new_stock, $item['product_id']]);
                    }
                    
                    $purchased_count += $purchase_quantity;
                }
                
                // Clear cart items for this user
                $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
                $stmt->execute([$user_id]);
                
                // Commit transaction
                $db->commit();
                
                // Update cart count
                $_SESSION['cart_count'] = 0;
                $_SESSION['success'] = "Checkout successful! Your purchase of {$purchased_count} items has been completed.";
                
            } catch (PDOException $e) {
                // Rollback transaction on error
                $db->rollBack();
                $_SESSION['error'] = "Checkout failed: " . $e->getMessage();
                error_log("Checkout error: " . $e->getMessage());
            }
            
            header("Location: cart.php");
            exit();
        }
    }
}

// Count items in cart for navbar (sum quantities, not just count rows)
try {
    $stmt = $db->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetchColumn() ?: 0; // Use 0 if null is returned
    $_SESSION['cart_count'] = $cart_count;
} catch (PDOException $e) {
    $_SESSION['cart_count'] = 0;
    error_log("Cart count error: " . $e->getMessage());
}
?>

<div class="container py-5">
    <h1 class="mb-4">Shopping Cart</h1>
    
    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php } ?>
    
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php } ?>
    
    <?php if (isset($_SESSION['warning'])) { ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($_SESSION['warning']) ?>
            <?php unset($_SESSION['warning']); ?>
        </div>
    <?php } ?>
    
    <?php if (isset($_SESSION['info'])) { ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($_SESSION['info']) ?>
            <?php unset($_SESSION['info']); ?>
        </div>
    <?php } ?>
    
    <?php if (empty($cart_items)) { ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="consumer_dashboard.php" class="alert-link">Continue shopping</a>.
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Cart Items (<?= count($cart_items) ?>)</h5>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?');">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Clear Cart</button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cart_items as $item) { ?>
                                <li class="list-group-item p-3 <?= ($item['is_expired'] || $item['is_out_of_stock']) ? 'bg-light' : '' ?>">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <?php if (!empty($item['image_path'])) { ?>
                                                <img src="<?= htmlspecialchars($item['image_path']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['title']) ?>" onerror="this.onerror=null; this.src='uploads/placeholder.jpg';">
                                            <?php } else { ?>
                                                <div class="bg-light h-100 d-flex align-items-center justify-content-center rounded">
                                                    <span class="text-muted">No Image</span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-5">
                                            <h5 class="mb-1"><?= htmlspecialchars($item['title']) ?></h5>
                                            <p class="text-muted mb-1">
                                                Seller: <?= htmlspecialchars($item['seller_name']) ?> 
                                                <span class="mx-1">|</span> 
                                                <?= htmlspecialchars($item['city']) ?>, <?= htmlspecialchars($item['district']) ?>
                                            </p>
                                            <p class="mb-1">
                                                <strong><?= $item['discounted_price'] ?> TL</strong>
                                                <?php if ($item['discount_percentage'] > 0) { ?>
                                                    <span class="text-muted text-decoration-line-through ms-2"><?= $item['normal_price'] ?> TL</span>
                                                    <span class="badge bg-danger ms-1">-<?= $item['discount_percentage'] ?>%</span>
                                                <?php } ?>
                                            </p>
                                            
                                            <?php if ($item['is_expired']) { ?>
                                                <div class="badge bg-danger">Expired</div>
                                            <?php } elseif ($item['is_out_of_stock']) { ?>
                                                <div class="badge bg-danger">Out of Stock</div>
                                            <?php } elseif ($item['stock'] < $item['quantity']) { ?>
                                                <div class="badge bg-warning text-dark">Only <?= $item['stock'] ?> available</div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-3">
                                            <form method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="decrease">-</button>
                                                    <input type="number" name="quantity" class="form-control text-center quantity-input" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" <?= ($item['is_expired'] || $item['is_out_of_stock']) ? 'disabled' : '' ?>>
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="increase">+</button>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-outline-primary ms-2" <?= ($item['is_expired'] || $item['is_out_of_stock']) ? 'disabled' : '' ?>>
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <div class="fw-bold mb-2"><?= number_format($item['item_total'], 2) ?> TL</div>
                                            <form method="POST" onsubmit="return confirm('Remove this item?');">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="consumer_dashboard.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items (<?= $total_items ?>):</span>
                            <span><?= number_format($cart_total + $cart_savings, 2) ?> TL</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <span>-<?= number_format($cart_savings, 2) ?> TL</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3 fw-bold">
                            <span>Total:</span>
                            <span><?= number_format($cart_total, 2) ?> TL</span>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="checkout">
                            <button type="submit" class="btn btn-primary w-100" <?= ($cart_total <= 0) ? 'disabled' : '' ?>>
                                Proceed to Checkout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script>
$(document).ready(function() {
    // Handle quantity buttons
    $('.quantity-btn').click(function() {
        const input = $(this).closest('.input-group').find('.quantity-input');
        const currentVal = parseInt(input.val()) || 1; // Default to 1 if NaN
        const max = parseInt(input.attr('max')) || 1; // Default to 1 if NaN
        
        if ($(this).data('action') === 'increase') {
            if (currentVal < max) {
                input.val(currentVal + 1);
            }
        } else {
            if (currentVal > 1) {
                input.val(currentVal - 1);
            }
        }
    });
    
    // Manual submit for quantity change
    $('.quantity-btn').click(function() {
        setTimeout(function() {
            $(this).closest('form').submit();
        }.bind(this), 300); // Short delay to allow input change
    });
});
</script>

<?php require "../app/templates/footer.php"; ?>