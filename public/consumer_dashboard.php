<?php
# Consumer Dashboard Interface 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php";
require "../app/templates/header.php";
require "../app/templates/navbar.php";

require "../app/controllers/consumer/dashboard.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market - Browse Products</title>
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Available Products</h1>
        
        <div class="products-grid">
            <?php if (count($active_products) > 0): ?>
                <?php foreach ($active_products as $product): ?>
                    <?php $discount_percentage = calculateDiscountPercentage($product['normal_price'], $product['discounted_price']); ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?= $product['image_path'] ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                            <?php if ($discount_percentage > 0): ?>
                                <div class="discount-badge">-<?= $discount_percentage ?>%</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h3><?= htmlspecialchars($product['title']) ?></h3>
                            <p class="price">
                                <span class="discounted"><?= $product['discounted_price'] ?> TL</span>
                                <?php if ($discount_percentage > 0): ?>
                                    <span class="original"><?= $product['normal_price'] ?> TL</span>
                                <?php endif; ?>
                            </p>
                            <p class="stock">Stock: <?= $product['stock'] ?></p>
                            <p class="seller">Seller: <?= htmlspecialchars($product['seller_name']) ?></p>
                            <p class="expiry">Valid until: <?= $product['expiration_date'] ?></p>
                            <div class="product-actions">
                                <button class="btn-add-to-cart" data-product-id="<?= $product['id'] ?>">Add to Cart</button>
                                <button class="btn-buy-now" data-product-id="<?= $product['id'] ?>">Buy Now</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">No products available at the moment.</p>
            <?php endif; ?>
        </div>

        <?php if (isset($inactive_products) && count($inactive_products) > 0): ?>
            <h2>Unavailable Products</h2>
            <div class="products-grid inactive">
                <?php foreach ($inactive_products as $product): ?>
                    <?php 
                        $expiry_date = new DateTime($product['expiration_date']);
                        $today = new DateTime();
                        $is_expired = $expiry_date < $today;
                        $is_out_of_stock = $product['stock'] <= 0;
                    ?>
                    <div class="product-card inactive <?= $is_expired ? 'expired' : '' ?> <?= $is_out_of_stock ? 'out-of-stock' : '' ?>">
                        <div class="product-image">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?= $product['image_path'] ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                            <?php if ($is_expired): ?>
                                <div class="status-badge expired">Expired</div>
                            <?php elseif ($is_out_of_stock): ?>
                                <div class="status-badge out-of-stock">Out of Stock</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h3><?= htmlspecialchars($product['title']) ?></h3>
                            <p class="price">
                                <span class="discounted"><?= $product['discounted_price'] ?> TL</span>
                                <span class="original"><?= $product['normal_price'] ?> TL</span>
                            </p>
                            <p class="stock">Stock: <?= $product['stock'] ?></p>
                            <p class="seller">Seller: <?= htmlspecialchars($product['seller_name']) ?></p>
                            <p class="expiry">Valid until: <?= $product['expiration_date'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            // Add to cart functionality (placeholder)
            $('.btn-add-to-cart').click(function() {
                const productId = $(this).data('product-id');
                alert('Product ' + productId + ' added to cart!');
                // Implement actual cart functionality here
            });
            
            // Buy now functionality (placeholder)
            $('.btn-buy-now').click(function() {
                const productId = $(this).data('product-id');
                alert('Proceeding to checkout for product ' + productId);
                // Implement actual checkout functionality here
            });
        });
    </script>

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h1, h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-card.inactive {
            opacity: 0.7;
        }
        
        .product-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .no-image {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
            color: #999;
        }
        
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .status-badge.expired {
            background-color: #e74c3c;
        }
        
        .status-badge.out-of-stock {
            background-color: #7f8c8d;
        }
        
        .product-details {
            padding: 15px;
        }
        
        .product-details h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .price {
            margin-bottom: 10px;
        }
        
        .price .discounted {
            font-weight: bold;
            font-size: 18px;
            color: #2c3e50;
        }
        
        .price .original {
            text-decoration: line-through;
            color: #95a5a6;
            margin-left: 8px;
            font-size: 14px;
        }
        
        .stock, .seller, .expiry {
            margin: 5px 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .product-actions button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        
        .btn-add-to-cart {
            background-color: #3498db;
            color: white;
        }
        
        .btn-add-to-cart:hover {
            background-color: #2980b9;
        }
        
        .btn-buy-now {
            background-color: #2ecc71;
            color: white;
        }
        
        .btn-buy-now:hover {
            background-color: #27ae60;
        }
        
        .inactive .product-actions {
            display: none;
        }
        
        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-size: 18px;
        }
    </style>
</body>
</html>

<?php require "../app/templates/footer.php" ?>