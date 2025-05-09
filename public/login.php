<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


 // Remember-me part
 if (isset($_COOKIE["access_token"])) {
    $user = getUserByToken($_COOKIE["access_token"]) ;
    if ( $user ) {
        $_SESSION["user"] = $user ; // auto login
        header("Location: main.php") ;
        exit ; 
    }
 }
 // if the user has already logged in, don't show login form
 if ( isset($_SESSION["user"])) {
     header("Location: main.php") ; // auto login
     exit ;
  } 

  require "db.php" ;
 
 
 // Process Login Form
 if ( !empty($_POST)) {
     extract($_POST) ;
     if ( checkUser($email, $pass, $user) ) {
         // now, you are authenticated, login as $user

        //var_dump($user);
         // remember me
         if ( isset($remember)) {
           $token = bin2hex(random_bytes(32)); // random token - 64 hex-chars
           setcookie("access_token", $token, time() + 60*60*24*365) ; // for a year
           setTokenByEmail($email, $token) ; // mark the user
         }

         // login
         $_SESSION["user"] = $user ; 
        // var_dump(isset($_SESSION["user"]));

         header("Location: main.php") ;
         exit ;
     }
     else { $fail = true  ; }

    }


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <!-- Login Form -->
    <form method = "post">
        <input type="text" placeholder = "Username" name = "username">
        <input type="password" placeholder = "Password" name = "password">
        <label for="remember-me">Remember Me</label>
        <input type="checkbox" name="remember-me" id="">


    </form>
    
</body>
</html>
