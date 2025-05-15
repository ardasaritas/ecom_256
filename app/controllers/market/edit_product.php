<?php
require_once "../app/includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {

    $errors = [];

    // Sanitize title
    $_POST['title'] = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    
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
        header("Location: /market_dashboard.php"); // change to header("Location: ../public/market_dashboard.php"); if web root is expirySaver instead of expirySaver/public
        exit();
    }

    $sql = "UPDATE products SET title = ?, stock = ?, normal_price = ?, discounted_price = ?, expiration_date = ?";
    $input = [
        $_POST['title'],
        $_POST['stock'],
        $_POST['normal_price'],
        $_POST['discounted_price'],
        $_POST['expiration_date']
    ];

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = upload('product_image');
        if (!isset($upload_result['error'])) {
            $sql .= ", image_path = ?";
            $input[] = $upload_result['filename'];
        } else {
            $_SESSION['errors']['upload'] = $upload_result['error'];
            $_SESSION['form_data'] = $_POST;
            header("Location: /market_dashboard.php"); // change to header("Location: ../public/market_dashboard.php"); if web root is expirySaver instead of expirySaver/public
            exit();
        }
    }

    $sql .= " WHERE id = ?";
    $input[] = $_POST['product_id'];
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($input);
        header("Location: /market_dashboard.php"); // change to header("Location: ../public/market_dashboard.php"); if web root is expirySaver instead of expirySaver/public
        exit();
    } catch (PDOException $e) {
        $_SESSION['errors']['db'] = "Database error: " . $e->getMessage();
        $_SESSION['form_data'] = $_POST;
        header("Location: /market_dashboard.php"); // change to header("Location: ../public/market_dashboard.php"); if web root is expirySaver instead of expirySaver/public
        exit();
    }
}

if (isset($_GET['product_id'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$_GET['product_id']]);
        $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$edit_product) {
            $_SESSION['error'] = "Product not found";
            header("Location: /market_dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: /market_dashboard.php"); // change to header("Location: ../public/market_dashboard.php"); if web root is expirySaver instead of expirySaver/public
        exit();
    }
}
?>
