<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/db.php';

// Fetch all market products (expired + active)
$stmt = $db->prepare("
    SELECT * FROM products
    ORDER BY expiration_date ASC
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
