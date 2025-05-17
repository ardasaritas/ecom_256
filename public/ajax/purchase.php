<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../app/includes/db.php";

// Get page and search parameters for redirection
$page = $_POST['page'] ?? 1;
$search = $_POST['search'] ?? '';
$redirectURL = "consumer_dashboard.php";

// Add search parameter to the URL if it exists
if (!empty($search)) {
    $redirectURL .= "?search=" . urlencode($search);
    if ($page > 1) {
        $redirectURL .= "&page=" . $page;
    }
} elseif ($page > 1) {
    $redirectURL .= "?page=" . $page;
}

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    $_SESSION['error'] = "You must be logged in to add items to your cart";
    header("Location: ../$redirectURL");
    exit();
}

// Check if product_id is provided
if (!isset($_POST['product_id'])) {
    $_SESSION['error'] = "Invalid request: missing product ID";
    header("Location: ../$redirectURL");
    exit();
}

$product_id = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; // Default to 1 if not specified
$user_id = $_SESSION['user']['id'];

try {
    // Check if product exists and has enough stock
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND stock >= ? AND expiration_date >= CURDATE()");
    $stmt->execute([$product_id, $quantity]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        $_SESSION['error'] = "Product not available or insufficient stock";
        header("Location: ../$redirectURL");
        exit();
    }
    
    // Check if product is already in cart_items
    $stmt = $db->prepare("SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cart_item) {
        // Update existing cart item
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        // Check if the new quantity exceeds available stock
        if ($new_quantity > $product['stock']) {
            $new_quantity = $product['stock'];
            $_SESSION['warning'] = "We've adjusted the quantity to match available stock";
        }
        
        $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $cart_item['id']]);
    } else {
        // Add new cart item
        $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    
    // Count cart items for navbar (sum of quantities)
    $stmt = $db->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $_SESSION['cart_count'] = $stmt->fetchColumn() ?: 0; // Use 0 if null is returned
    
    $_SESSION['success'] = "Item added to cart successfully";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

// Redirect back to the consumer dashboard
header("Location: ../$redirectURL");
exit();