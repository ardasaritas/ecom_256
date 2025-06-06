<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php";
require_once "../app/includes/csrf.php";
require "../app/templates/header.php";
require "../app/templates/navbar.php";

generate_csrf_token();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
      // For AJAX:
      echo json_encode(['success' => false, 'error' => 'CSRF token mismatch']);
      exit;
      // Or for normal form:
      // die('CSRF token mismatch');
  }
  // ... continue with valid logic
}
require "../app/controllers/market/dashboard.php";
?>

<div class="container py-5">
    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['errors'] as $type => $message): ?>
                <div><?= htmlspecialchars($message) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Your Market Products</h5>
            <a href="#" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Product</a>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Stock</th>
                        <th>Normal Price</th>
                        <th>Discounted Price</th>
                        <th>Expiration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php 
                            $expiry_date = new DateTime($product['expiration_date']);
                            $today = new DateTime();
                            $is_expired = $expiry_date < $today;
                        ?>
                        <tr class="<?= $is_expired ? 'table-danger' : '' ?>">
                            <td>
                                <?php if (!empty($product['image_path'])): ?>
                                    <img src="<?= $product['image_path'] ?>" class="img-thumbnail" style="width: 50px; height: 50px;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product['title']) ?></td>
                            <td><?= $product['stock'] ?></td>
                            <td><?= $product['normal_price'] ?> TL</td>
                            <td><?= $product['discounted_price'] ?> TL</td>
                            <td><?= $product['expiration_date'] ?></td>
                            <td>
                                <a href="#" class="btn btn-outline-primary btn-sm edit-btn" data-product='<?= json_encode($product) ?>'>Edit</a>
                                <a href="market_dashboard.php?action=delete&product_id=<?= $product['id'] ?>" class="btn btn-outline-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_SESSION['form_data']['title'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Product Image</label>
            <input type="file" name="product_image" class="form-control" accept="image/*" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Stock Quantity</label>
            <input type="number" name="stock" class="form-control" required min="0" value="<?= htmlspecialchars($_SESSION['form_data']['stock'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Normal Price (TL)</label>
            <input type="number" name="normal_price" class="form-control" required min="0" step="any" value="<?= htmlspecialchars($_SESSION['form_data']['normal_price'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Discounted Price (TL)</label>
            <input type="number" name="discounted_price" class="form-control" required min="0" step="any" value="<?= htmlspecialchars($_SESSION['form_data']['discounted_price'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Expiration Date</label>
            <input type="date" name="expiration_date" class="form-control" required value="<?= htmlspecialchars($_SESSION['form_data']['expiration_date'] ?? '') ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" action="market_dashboard.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_product_id" name="product_id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" id="edit_title" name="title" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Product Image</label>
            <input type="file" id="edit_product_image" name="product_image" class="form-control" accept="image/*">
          </div>
          <div class="col-md-4">
            <label class="form-label">Stock Quantity</label>
            <input type="number" id="edit_stock" name="stock" class="form-control" required min="0">
          </div>
          <div class="col-md-4">
            <label class="form-label">Normal Price (TL)</label>
            <input type="number" id="edit_normal_price" name="normal_price" class="form-control" required min="0" step="any">
          </div>
          <div class="col-md-4">
            <label class="form-label">Discounted Price (TL)</label>
            <input type="number" id="edit_discounted_price" name="discounted_price" class="form-control" required min="0" step="any">
          </div>
          <div class="col-md-6">
            <label class="form-label">Expiration Date</label>
            <input type="date" id="edit_expiration_date" name="expiration_date" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const product = JSON.parse(this.dataset.product);
                document.getElementById('edit_product_id').value = product.id;
                document.getElementById('edit_title').value = product.title;
                document.getElementById('edit_stock').value = product.stock;
                document.getElementById('edit_normal_price').value = product.normal_price;
                document.getElementById('edit_discounted_price').value = product.discounted_price;
                document.getElementById('edit_expiration_date').value = product.expiration_date;

                const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                editModal.show();
            });
        });

        <?php if (isset($_SESSION['errors'])): ?>
            const targetModal = document.getElementById(
                <?= isset($_SESSION['form_data']['action']) && $_SESSION['form_data']['action'] === 'edit' 
                    ? "'editProductModal'" 
                    : "'addProductModal'" ?>
            );
            new bootstrap.Modal(targetModal).show();
        <?php endif; ?>
    });
</script>

<?php
unset($_SESSION['form_data']);
unset($_SESSION['errors']);
require "../app/templates/footer.php";
?>