<?php
  require_once "../app/includes/db.php" ;
  require_once "../app/includes/csrf.php" ;
  require "../app/templates/header.php";
  require "../app/templates/navbar.php";
  require_once "../app/includes/email.php";
  require_once "../app/vendor/autoload.php";
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


  
  if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    echo "entered post";
    extract($_POST) ; // $role, $email, $name, $password, $city, $district (UPDATED BEWARE)
    var_dump($_POST);
    
    // Validate and Verify Form Data
    $hashedPass = password_hash($password, PASSWORD_BCRYPT) ; 
    $verified = 0 ; // when user enters OTP this field will be 1
   
    $code = random_int(100000, 999999);
    $sql = "insert into users (email, password, role, name, city, district, is_verified, verification_code) 
                                 values (?, ?, ?, ?, ?, ?, ?, ?)" ;
    $stmt = $db->prepare($sql) ;
    $stmt->execute([$email, $hashedPass, $role, $name , $city , $district , $verified, $code ]) ;

    // after 15 minutes the cookie below will expire (this is for email verification)
    setcookie("email", $email, time() + (15 * 60), "/"); 

    //send mail to user
    Mail::send($email, "6 digit OTP", $code) ;



    header("Location: verify_email.php") ; // user will enter the OTP in verify_email.php 
    exit ;
  }
?>

<main class="d-flex flex-grow-1 justify-content-center align-items-center">
  <div class="card p-5 shadow width-500">
    <h2 class="text-center mb-5 fw-bold">Register for ExpirySaver</h2>

    <form method="post" action="register.php" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <!-- Role Toggle -->
      <div class="mb-5 text-center">
        <div class="d-flex btn-group" role="group">
          <input type="radio" class="btn-check" name="role" id="consumer" value="consumer" autocomplete="off" checked>
          <label class="btn btn-outline-primary flex-fill" for="consumer">Consumer</label>

          <input type="radio" class="btn-check" name="role" id="market" value="market" autocomplete="off">
          <label class="btn btn-outline-primary flex-fill" for="market">Market</label>
        </div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" id="email" required>
      </div>

      
      <div class="mb-3">
        <label for="name" class="form-label" id="nameLabel">Full Name</label>
        <input type="text" name="name" class="form-control" id="name" required>
      </div>

      
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" required>
      </div>

      
      <div class="mb-3">
        <label for="city" class="form-label">City</label>
        <input type="text" name="city" class="form-control" id="city" required>
      </div>

      <div class="mb-3">
        <label for="district" class="form-label">District</label>
        <input type="text" name="district" class="form-control" id="district" required>
      </div>

      <?php if (isset($fail) && $fail): ?>
        <div class="text-danger mb-3">
            Login failed. Please try again.
        </div>
      <?php endif; ?>

      <button type="submit" class="btn btn-success w-100">Register</button>
    </form>
  </div>
</main>

<!-- JS to update label dynamically -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const consumerRadio = document.getElementById("consumer");
    const marketRadio = document.getElementById("market");
    const nameLabel = document.getElementById("nameLabel");

    function updateLabel() {
      nameLabel.textContent = consumerRadio.checked ? "Full Name" : "Market Name";
    }

    consumerRadio.addEventListener("change", updateLabel);
    marketRadio.addEventListener("change", updateLabel);

    updateLabel(); // initial run
  });
</script>


<?php require "../app/templates/footer.php"; ?>