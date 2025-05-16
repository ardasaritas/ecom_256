<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/db.php';

// Today's date
$today = date('Y-m-d');

// Get active products (not expired, in stock)
$stmt = $db->prepare("
    SELECT p.*, u.name AS seller_name 
    FROM products p 
    JOIN users u ON p.user_id = u.id
    WHERE p.expiration_date >= ? AND p.stock > 0
    ORDER BY p.discounted_price ASC
");
$stmt->execute([$today]);
$active_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get expired/out-of-stock products
$stmt = $db->prepare("
    SELECT p.*, u.name AS seller_name 
    FROM products p 
    JOIN users u ON p.user_id = u.id
    WHERE p.expiration_date < ? OR p.stock <= 0
    ORDER BY p.expiration_date DESC
");
$stmt->execute([$today]);
$inactive_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper: discount percentage
function calculateDiscountPercentage($normal_price, $discounted_price) {
    if ($normal_price <= 0) return 0;
    $discount = (($normal_price - $discounted_price) / $normal_price) * 100;
    return round($discount);
}
