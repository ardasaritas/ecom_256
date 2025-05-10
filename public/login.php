<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../includes/db.php" ;


//routes the user based on the role of the user
function routeUser($user){
    if($user["role"] == "market"){
        header("Location: ../market/dashboard.php");
    } else if($user["role"] == "consumer"){
        header("Location: ../consumer/dashboard.php");
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
 if ( isset($_SESSION["user"])) {
    routeUser($_SESSION["user"]);
     exit ;
  } 

 
// Process Login Form
if (!empty($_POST)) {
    extract($_POST);
    if (checkUser($email, $password, $user)) {
        // login as $user

        // var_dump($user);
        // remember me
        if (isset($remember)) {
            $token = bin2hex(random_bytes(32)); // random token - 64 hex-chars
            setcookie("access_token", $token, time() + 60 * 60 * 24 * 365); // for a year
            setTokenByEmail($email, $token); // mark the user
        }

        // login
        $_SESSION["user"] = $user;
        // var_dump(isset($_SESSION["user"]));

        routeUser($user);
        exit;
    } else {
        $fail = true;
    }
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
        <input type="text" placeholder = "Username" name = "email">
        <input type="password" placeholder = "Password" name = "password">
        <label for="remember-me">Remember Me</label>
        <input type="checkbox" name="remember-me" id="">
        <button type="submit">Log in</button>


    </form>
    
</body>
</html>
