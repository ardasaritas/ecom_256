<?php
  require_once "../app/includes/db.php" ;
  require "../app/templates/header.php";
  require "../app/templates/navbar.php";

  if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    echo "entered post";
    extract($_POST) ; // $role, $email, $name, $password, $city, $district (UPDATED BEWARE)
    var_dump($_POST);
    
    // Validate and Verify Form Data
    $hashedPass = password_hash($password, PASSWORD_BCRYPT) ; 
    $verified = 1 ; // default 0
    $code = random_int(100000, 999999);
       $sql = "insert into users (email, password, role, name, city, district, is_verified, verification_code) 
                                 values (?, ?, ?, ?, ?, ?, ?, ?)" ;
       $stmt = $db->prepare($sql) ;
       $stmt->execute([$email, $hashedPass, $role, $name , $city , $district , $verified, $code ]) ;
       header("Location: verify_email.php") ; // can we use require email.php here instead since backend logic should prob not be visible to web server? 
       exit ;
      }
?>

<main class="d-flex flex-grow-1 justify-content-center align-items-center py-5">
  <div class="card p-4 shadow width-500">
    <h2 class="text-center mb-4 fw-bold">Register for ExpirySaver</h2>

    <form method="post" action="register.php" autocomplete="off">

      <!-- Role Toggle -->
      <div class="mb-4 text-center">
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


<?php require "../templates/footer.php"; ?>