<?php

# Logic for consumer dashboard to display products

// Get all available products that aren't expired
$today = date('Y-m-d');
$stmt = $db->prepare("SELECT p.*, u.name as seller_name FROM products p 
                     JOIN users u ON p.user_id = u.id
                     WHERE p.expiration_date >= ? AND p.stock > 0
                     ORDER BY p.discounted_price ASC");
$stmt->execute([$today]);
$active_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get products that are expired or out of stock (optional, you can remove if not needed)
$stmt = $db->prepare("SELECT p.*, u.name as seller_name FROM products p 
                     JOIN users u ON p.user_id = u.id
                     WHERE p.expiration_date < ? OR p.stock <= 0
                     ORDER BY p.expiration_date DESC");
$stmt->execute([$today]);
$inactive_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate discount percentage for each product
function calculateDiscountPercentage($normal_price, $discounted_price) {
    if ($normal_price <= 0) return 0;
    $discount = (($normal_price - $discounted_price) / $normal_price) * 100;
    return round($discount);
}

?>