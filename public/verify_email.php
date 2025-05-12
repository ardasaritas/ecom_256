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

<body>
    <form action="" method = "post" >
        <p>Enter the 6-Digit Code</p>
        <input type="text" inputmode="numeric" maxlength="6" placeholder="Enter OTP" name = "otp">
        <button type="submit">Verify Email</button>
    </form>
</body>

<?php require "../app/templates/footer.php";?>