<?php

try {
       $db = new PDO("mysql:host=localhost;dbname=expirySaver;charset=utf8mb4", "root", "");
       $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
    } 
catch( PDOException $ex) {
           die("DB Connect Error : " . $ex->getMessage()) ;
}

function checkUser($email, $pass, &$user) {
    global $db ;
    try {
        $stmt = $db->prepare("select * from users where email=?") ;
        $stmt->execute([$email]) ;
        $user = $stmt->fetch() ;
    }catch (PDOException $e) {
        echo $e->getMessage();
    }
    return $user ? password_verify($pass, $user["password"]) : false ;
}

// Remember me
function getUserByToken($token) {
   global $db ;
   try {
    $stmt = $db->prepare("select * from users where remember = ?") ;
    $stmt->execute([$token]) ;
   } catch (PDOException $e){
        $e->getMessage();
   }
   
   return $stmt->fetch() ;
}

function setTokenByEmail($email, $token) {
   global $db ;
   $stmt = $db->prepare("update users set remember = ? where email = ?") ;
   $stmt->execute([$token, $email]) ;
}
