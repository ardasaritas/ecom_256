<?php

# Logic for dashboard of market later to be required in market_dashboard.php


//var_dump([$_SESSION["user"]]);
$max_filesize = ini_get("upload_max_filesize");
$max_postsize = ini_get("post_max_size");

$stmt = $db->prepare("SELECT * FROM products where user_id = ?") ;
$stmt->execute([$_SESSION["user"]["id"]]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Print image paths
echo "<!-- Debug image paths: -->";
foreach ($products as $product) {
    echo "<!-- Product ID: " . $product['id'] . ", Image path: " . $product['image_path'] . " -->\n";
}

function validatePrices($normal_price, $discounted_price) {
    if ($normal_price <= $discounted_price) {
        return "Normal price must be greater than discounted price";
    }
    return "";
}

function validateDate($date) {
    $expiry_date = new DateTime($date);
    $today = new DateTime();
    if ($expiry_date < $today) {
        return "Cannot use past dates";
    }
    return "";
}

function upload($filebox) {
    global $max_filesize;
    if (isset($_FILES[$filebox])) {
        $file = $_FILES[$filebox];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if ($file["error"] == UPLOAD_ERR_INI_SIZE) {
            $error = "{$file["name"]} : greater than $max_filesize";
        } else if ($file["error"] == UPLOAD_ERR_NO_FILE) {
            $error = "No file chosen";
        } else if (!in_array($ext, ["jpg", "png", "gif"])) {
            $error = "{$file["name"]} : Not an image file";
        } else {
            $filename = bin2hex(random_bytes(8)) . ".$ext";
            $upload_dir = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "uploads";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $target_path = $upload_dir . DIRECTORY_SEPARATOR . $filename;
            
            if (move_uploaded_file($file["tmp_name"], $target_path)) {
                // Store the path relative to the public directory
                return ["filename" => "uploads/" . $filename];
            }
            $error = "File upload failed - check directory permissions";
        }
    }
    return ["error" => $error];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    if (isset($_POST['normal_price']) && isset($_POST['discounted_price'])) {
        $price_error = validatePrices($_POST['normal_price'], $_POST['discounted_price']);
        if ($price_error) {
            $errors['price'] = $price_error;
        }
    }
 
    if (isset($_POST['expiration_date'])) {
        $date_error = validateDate($_POST['expiration_date']);
        if ($date_error) {
            $errors['date'] = $date_error;
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $action = isset($_POST['action']) ? $_POST['action'] : 'add';
    
    if ($action === 'edit') {
        require_once "edit_product.php";
    } else {
        require_once "add_product.php";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['product_id'])) {
    require_once "delete_product.php";
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['product_id'])) {
    require_once "edit_product.php";
}
?>