<?php

require_once "../app/includes/db.php" ;
require "../app/templates/header.php";
require "../app/templates/navbar.php";

//check if user is coming from register.php - otherwise there won't be any cookie -
if($_COOKIE["email"] != ""){
    $user = getUserByEmail($_COOKIE["email"]);


    if($user["is_verified"] == 0){
        if(!empty($_POST)){
            extract($_POST);

            if(isset($otp)){
                if($otp == $user["verification_code"]){

                    setUserVerified($_COOKIE["email"]);

                    // destroy cookie
                    unset($_COOKIE["email"]);
                    setcookie("email", "", time() - 3600, "/"); 

                    //user is verified, route user to login.php
                    header("Location: login.php");
                } else{
                    $out = "Wrong code, enter again" ;
                }
            }
    }
    }
} else{
    header("Location: index.php");
}


?>
<main class="d-flex flex-grow-1 justify-content-center align-items-center">
  <div class="card shadow p-5 width-500">
    <h2 class="text-center mb-4">Verify Your Email</h2>
    <form method="POST" action="" autocomplete="off">
      <div class="mb-3">
        <label for="verification_code" class="form-label">Verification Code</label>
        <input type="text" class="form-control" inputmode="numeric" maxlength="6" name="otp" placeholder="Enter the code sent to your email" required>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Verify</button>
      </div>
    </form>
    <div class="mt-3 text-center">
      <p class="text-muted">Didn't receive a code? <a href="?">Resend</a></p>
    </div>
  </div>
</main>

<?php require "../app/templates/footer.php";?>