<?php
# Market Dashboard Interface 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php";
require "../app/templates/header.php";
require "../app/templates/navbar.php";

require "../app/controllers/market/dashboard.php";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market</title>
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['errors'])): ?>
        <div class="errors">
            <?php foreach ($_SESSION['errors'] as $type => $message): ?>
                <div class="error"><?= htmlspecialchars($message) ?></div>
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>

    <table>
        <tr>
            <td>Image</td>
            <td>Title</td>
            <td>Stock</td>
            <td>Normal Price</td>
            <td>Discounted Price</td>
            <td>Expiration Date</td>
            <td>Actions</td>
        </tr>
        <?php foreach ($products as $product): ?>
            <?php 
                $expiry_date = new DateTime($product['expiration_date']);
                $today = new DateTime();
                $is_expired = $expiry_date < $today;
            ?>
            <tr class="<?= $is_expired ? 'expired' : '' ?>">
                <td>
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="<?= $product['image_path'] ?>" alt="Product Image">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?= $product['title'] ?></td>
                <td><?= $product['stock'] ?></td>
                <td><?= $product['normal_price'] ?> TL</td>
                <td><?= $product['discounted_price'] ?> TL</td>
                <td class="<?= $is_expired ? 'expired' : '' ?>"><?= $product['expiration_date'] ?></td>
                <td>
                    <a href="#" class="edit-btn" data-product='<?= json_encode($product) ?>'>Edit</a>
                    <a href="market_dashboard.php?action=delete&product_id=<?= $product['id'] ?>" class="delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="7">
            <a href="#" id="openAddBtn" class="add-btn">+ Add Product</a>
            </td>
        </tr>
    </table>

    <div id="addProductPopup" class="popup">
        <div class="popup-content">
            <span class="close" id="closeAddBtn">&times;</span>
            <h2>Add New Product</h2>
            
            <form method="POST" enctype="multipart/form-data" action="market_dashboard.php">
                <div class="form-line">
                    <label>Title:</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= isset($_SESSION['form_data']['title']) && (!isset($_SESSION['form_data']['action']) || $_SESSION['form_data']['action'] !== 'edit') ? htmlspecialchars($_SESSION['form_data']['title']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Product Image:</label>
                    <input type="file" id="product_image" name="product_image" accept="image/*" required>
                </div>
                
                <div class="form-line">
                    <label>Stock Quantity:</label>
                    <input type="number" id="stock" name="stock" required min="0" 
                           value="<?= isset($_SESSION['form_data']['stock']) && (!isset($_SESSION['form_data']['action']) || $_SESSION['form_data']['action'] !== 'edit') ? htmlspecialchars($_SESSION['form_data']['stock']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Normal Price (TL):</label>
                    <input type="number" id="normal_price" name="normal_price" required min="0"
                           value="<?= isset($_SESSION['form_data']['normal_price']) && (!isset($_SESSION['form_data']['action']) || $_SESSION['form_data']['action'] !== 'edit') ? htmlspecialchars($_SESSION['form_data']['normal_price']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Discounted Price (TL):</label>
                    <input type="number" id="discounted_price" name="discounted_price" required min="0"
                           value="<?= isset($_SESSION['form_data']['discounted_price']) && (!isset($_SESSION['form_data']['action']) || $_SESSION['form_data']['action'] !== 'edit') ? htmlspecialchars($_SESSION['form_data']['discounted_price']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Expiration Date:</label>
                    <input type="date" id="expiration_date" name="expiration_date" required 
                           value="<?= isset($_SESSION['form_data']['expiration_date']) && (!isset($_SESSION['form_data']['action']) || $_SESSION['form_data']['action'] !== 'edit') ? htmlspecialchars($_SESSION['form_data']['expiration_date']) : '' ?>">
                </div>
                
                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>

    <!-- popup -->
    <div id="editProductPopup" class="popup">
        <div class="popup-content">
            <span class="close" id="closeEditBtn">&times;</span>
            <h2>Edit Product</h2>
            <?php 
            $is_edit_form = isset($_SESSION['form_data']['action']) && $_SESSION['form_data']['action'] === 'edit';
            if (isset($_SESSION['errors']) && $is_edit_form): 
            ?>
                <div class="errors">
                    <?php foreach ($_SESSION['errors'] as $type => $message): ?>
                        <div class="error"><?= htmlspecialchars($message) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" action="market_dashboard.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_product_id" name="product_id" 
                       value="<?= isset($_SESSION['form_data']['product_id']) ? htmlspecialchars($_SESSION['form_data']['product_id']) : '' ?>">
                
                <div class="form-line">
                    <label>Title:</label>
                    <input type="text" id="edit_title" name="title" required 
                           value="<?= $is_edit_form && isset($_SESSION['form_data']['title']) ? htmlspecialchars($_SESSION['form_data']['title']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Product Image:</label>
                    <input type="file" id="edit_product_image" name="product_image" accept="image/*">
                </div>
                
                <div class="form-line">
                    <label>Stock Quantity:</label>
                    <input type="number" id="edit_stock" name="stock" required min="0"
                           value="<?= $is_edit_form && isset($_SESSION['form_data']['stock']) ? htmlspecialchars($_SESSION['form_data']['stock']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Normal Price (TL):</label>
                    <input type="number" id="edit_normal_price" name="normal_price" required min="0" 
                           value="<?= $is_edit_form && isset($_SESSION['form_data']['normal_price']) ? htmlspecialchars($_SESSION['form_data']['normal_price']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Discounted Price (TL):</label>
                    <input type="number" id="edit_discounted_price" name="discounted_price" required min="0"
                           value="<?= $is_edit_form && isset($_SESSION['form_data']['discounted_price']) ? htmlspecialchars($_SESSION['form_data']['discounted_price']) : '' ?>">
                </div>
                
                <div class="form-line">
                    <label>Expiration Date:</label>
                    <input type="date" id="edit_expiration_date" name="expiration_date" required
                           value="<?= $is_edit_form && isset($_SESSION['form_data']['expiration_date']) ? htmlspecialchars($_SESSION['form_data']['expiration_date']) : '' ?>">
                </div>
                
                <button type="submit">Update Product</button>
            </form>
        </div>
    </div>

<script>
    $(document).ready(function() {
       
        $('#openAddBtn').click(function () {
            $('#addProductPopup').show();
        });

       
        $('#closeAddBtn').click(function () {
            $('#addProductPopup').hide();
        });

        $('.edit-btn').click(function () {
            const product = $(this).data('product');
            $('#edit_product_id').val(product.id);
            $('#edit_title').val(product.title);
            $('#edit_stock').val(product.stock);
            $('#edit_normal_price').val(product.normal_price);
            $('#edit_discounted_price').val(product.discounted_price);
            $('#edit_expiration_date').val(product.expiration_date);
            $('#editProductPopup').show();
        });

        $('#closeEditBtn').click(function () {
            $('#editProductPopup').hide();
        });

        <?php if (isset($_SESSION['errors'])): ?>
            <?php if (isset($_SESSION['form_data']['action']) && $_SESSION['form_data']['action'] === 'edit'): ?>
                $('#editProductPopup').show();
            <?php else: ?>
                $('#addProductPopup').show();
            <?php endif; ?>
        <?php endif; ?>
    });
</script>

<style>
    .errors {
        background-color: #ffe6e6;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
    }

    .error {
        color: red;
        margin: 5px 0;
    }
    
    table {   
        margin: 50px auto;
        border-radius: 10px;
        text-align: center;
    }

    img {
        width: 50px;
        height: 50px;
        object-fit: cover; 
    }
    
    .expired {
        color: red;
    }
   
    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
    }
    
    .popup-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border-radius: 5px;
        width: 70%;
        max-width: 500px;
        position: relative;
    }
    
    .close {
        position: absolute;
        right: 10px;
        top: 5px;
        font-size: 24px;
        cursor: pointer;
    }

    .form-line {
        margin: 10px 0;
    }
</style>

</body>
</html>

<?php 
  unset($_SESSION['form_data']); 
  unset($_SESSION['errors']);
  require "../app/templates/footer.php" 
  ?>