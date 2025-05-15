<?php
require_once "../app/includes/db.php";

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
        $stmt = $db->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product && $product['image_path']) {
            $image_path = $_SERVER['DOCUMENT_ROOT'] . $product['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
  
}

header("Location: /market_dashboard.php"); // change to header("Location: ../public/market_dashboard.php"); if web root is expirySaver instead of expirySaver/public
exit();
?>