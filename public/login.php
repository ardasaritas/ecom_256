<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../app/includes/db.php" ;
require "../app/templates/header.php";
require "../app/templates/navbar.php";

//routes the user based on the role of the user
function routeUser($user){
    if (isset($user["role"])){
         if($user["role"] == "market"){
             header("Location: market_dashboard.php");
         } else if($user["role"] == "consumer"){
            header("Location: consumer_dashboard.php");
        }
    }
}



 // Remember-me part
 if (isset($_COOKIE["access_token"])) {
    $user = getUserByToken($_COOKIE["access_token"]) ;
    if ( $user ) {
        $_SESSION["user"] = $user ; // auto login
        routeUser($user);
        exit ; 
    }
 }
 // if the user has already logged in, don't show login form
//  if ( isset($_SESSION["user"])) {
//     routeUser($_SESSION["user"]);
//     exit ;
//   } 

 
// Process Login Form
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    extract($_POST);
    if (checkUser($email, $password, $user)) {
        // login as $user
        // var_dump($user);
        // remember me
        if (isset($remember)) {
            $token = bin2hex(random_bytes(32)); // random token - 64 hex-chars
            setcookie("access_token", $token, time() + 60 * 60 * 24 * 365); // for a year
            setTokenByEmail($email, $token); // mark the user<
        }

        // login
        $_SESSION["user"] = $user;
        var_dump(isset($_SESSION["user"]));

        routeUser($user);
        exit;
    } else {
        // User couldn't login for some reason
        $fail = true;
    }
}


?>
      
<!-- Login Form -->
<main class="d-flex flex-grow-1 justify-content-center align-items-center">
  <div class="card p-5 shadow width-500">
    <h2 class="text-center mb-5 fw-bold">Login to ExpirySaver</h2>
    <form method="post" action="login.php" autocomplete="off">
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" id="email" autocomplete="off" 
        required 
        oninvalid="this.setCustomValidity('Please enter your email address')"
        oninput="this.setCustomValidity('')">

      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" required
        oninvalid="this.setCustomValidity('Please enter your password')"
        oninput="this.setCustomValidity('')">

      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">Remember Me</label>
      </div>
      <?php if (isset($fail) && $fail): ?>
        <div class="text-danger mb-3">
            Login failed. Please try again.
        </div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</main>




<?php require "../app/templates/footer.php"; ?>