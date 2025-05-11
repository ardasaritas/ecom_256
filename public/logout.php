<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}



require_once "../app/includes/db.php" ;


 // delete remember me part
 setTokenByEmail($_SESSION["user"]["email"], null) ;
 setcookie("access_token", "", 1) ; 

 // delete session file
 session_destroy() ;
 // delete session cookie
 setcookie("PHPSESSID", "", 1 , "/") ; // delete memory cookie 

 // redirect to landing page.
 header("Location: index.php") ;
?>

