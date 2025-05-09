<?php
<<<<<<< Updated upstream



try {
       $db = new PDO("mysql:host=localhost;dbname=test;charset=utf8mb4", "root", "root");
       $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
    } 
catch( PDOException $ex) {
           die("DB Connect Error : " . $ex->getMessage()) ;
}

function checkUser($email, $pass, &$user) {
    global $db ;

    $stmt = $db->prepare("select * from auth where email=?") ;
    $stmt->execute([$email]) ;
    $user = $stmt->fetch() ;
    return $user ? password_verify($pass, $user["password"]) : false ;
}

// Remember me
function getUserByToken($token) {
   global $db ;
   $stmt = $db->prepare("select * from auth where remember = ?") ;
   $stmt->execute([$token]) ;
   return $stmt->fetch() ;
}

function setTokenByEmail($email, $token) {
   global $db ;
   $stmt = $db->prepare("update auth set remember = ? where email = ?") ;
   $stmt->execute([$token, $email]) ;
}
=======
// Database configuration (customize accordingly)
$dsn = "mysql:host=localhost;dbname=expirySaver;charset=utf8mb4";
$user = "root";
$pass = "";

// Database connection
try {
    $db = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    print("<p>Connection Error: " . $ex->getMessage() . "</p>"); 
    exit; 
}
?>
>>>>>>> Stashed changes
