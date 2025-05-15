<?php
require_once "../app/includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'add')) {
    $errors = [];
  
    $normal_price = $_POST['normal_price'];
    $discounted_price = $_POST['discounted_price'];
    if ($normal_price <= $discounted_price) {
        $errors['price'] = "Normal price must be greater than discounted price";
    }
    
    $expiry_date = new DateTime($_POST['expiration_date']);
    $today = new DateTime();
    if ($expiry_date < $today) {
        $errors['date'] = "Cannot use past dates";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: ../public/market_dashboard.php");
        exit();
    }
   
    if (isset($_FILES['product_image'])) {
        $upload_result = upload('product_image');
        if (!isset($upload_result['error'])) {
            try {
                $stmt = $db->prepare("INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_SESSION["user"]["id"],
                    $_POST['title'],
                    $_POST['stock'],
                    $_POST['normal_price'],
                    $_POST['discounted_price'],
                    $_POST['expiration_date'],
                    $upload_result['filename']
                ]);
                header("Location: ../public/market_dashboard.php");
                exit();
            } catch (PDOException $e) {
                $_SESSION['errors']['db'] = "Database error: " . $e->getMessage();
                $_SESSION['form_data'] = $_POST;
                header("Location: ../public/market_dashboard.php");
                exit();
            }
        } else {
            $_SESSION['errors']['upload'] = $upload_result['error'];
            $_SESSION['form_data'] = $_POST;
            header("Location: ../public/market_dashboard.php");
            exit();
        }
    }
}
?>
