<?php
// ajax/update_cart.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../../app/includes/db.php';


if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$action = $_POST['action'] ?? '';

if ($action === 'update') {
    $cart_id = (int)($_POST['cart_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);

    if ($cart_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    $stmt = $db->prepare("SELECT p.stock FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'error' => 'Item not found']);
        exit;
    }

    if ($quantity > $product['stock']) {
        echo json_encode(['success' => false, 'error' => 'Quantity exceeds available stock']);
        exit;
    }

    $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$quantity, $cart_id, $user_id]);

    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'remove') {
    $cart_id = (int)($_POST['cart_id'] ?? 0);

    if ($cart_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid cart ID']);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);

     // Get updated cart count
     $stmt = $db->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
     $stmt->execute([$user_id]);
     $cart_count = $stmt->fetchColumn();

     // Store updated cart count in session for future page loads
     $_SESSION['cart_count'] = $cart_count;

    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'checkout') {
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
     // Get updated cart count
     $stmt = $db->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
     $stmt->execute([$user_id]);
     $cart_count = $stmt->fetchColumn();

     // Store updated cart count in session for future page loads
     $_SESSION['cart_count'] = $cart_count;

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;