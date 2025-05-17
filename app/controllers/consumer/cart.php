<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/db.php';

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
            // Implementation for checkout would go here
            
            // For now, just redirect back with a message
            $_SESSION['info'] = "Checkout functionality is not yet implemented";
            header("Location: cart.php");
            exit();
        }
    }
}

// Count items in cart for navbar
try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetchColumn();
    $_SESSION['cart_count'] = $cart_count;
} catch (PDOException $e) {
    $_SESSION['cart_count'] = 0;
    error_log("Cart count error: " . $e->getMessage());
}