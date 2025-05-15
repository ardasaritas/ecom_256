<?php
require_once __DIR__ . '/../app/includes/db.php'; 
require_once __DIR__ . '/../app/templates/header.php';
require_once __DIR__ . '/../app/templates/navbar.php';




if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];

 
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);


    $updateQuery = "UPDATE users SET name = :name, email = :email, city = :city, district = :district WHERE id = :user_id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([
        'name' => $name,
        'email' => $email,
        'city' => $city,
        'district' => $district,
        'user_id' => $user_id
    ]);

 
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['user'] = $user;

    echo "<p class='alert alert-success'>Profile updated successfully!</p>";
}

?>

<div class="container py-5">
  <div class="row justify-content-center">
  
    <div class="col-md-8">
    
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Profile Information</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Name:<br></strong> <?= htmlspecialchars($user['name']) ?></p>
              <p><strong>Email:<br></strong> <?= htmlspecialchars($user['email']) ?></p>
              <p><strong>City:<br></strong> <?= htmlspecialchars($user['city']) ?></p>
              <p><strong>District:<br></strong> <?= htmlspecialchars($user['district']) ?></p>
              <p><strong>Role: <br></strong> <?= htmlspecialchars($user['role']) ?></p>
            </div>
            <div class="col-md-6 text-md-end">
      
              <a href="#editProfile" class="btn btn-secondary" data-bs-toggle="collapse">Edit Profile</a>
            </div>
          </div>
        </div>
      </div>

      <div class="collapse" id="editProfile">
        <div class="card shadow-sm">
          <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Edit Profile</h5>
          </div>
          <div class="card-body">
            <form action="profile.php" method="POST">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
              </div>
              <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
              </div>
              <div class="mb-3">
                <label for="district" class="form-label">District</label>
                <input type="text" class="form-control" id="district" name="district" value="<?= htmlspecialchars($user['district']) ?>" required>
              </div>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../app/templates/footer.php'; ?>
