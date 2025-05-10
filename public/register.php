<?php

?>

<?php


  if (!empty($_POST)) {
    extract($_POST) ; // $email, $pass, $user
    require "../includes/db.php" ;
    // Validate and Verify Form Data
       $hash = password_hash("123", PASSWORD_BCRYPT) ; 
    //    $email = "efeelde@gmail.com" ; 
    //    $role = "market" ; 
    //    $name = "aaaa" ;
    //    $city = "ankara" ; 
    //    $district = "bilkent" ; 
    //    $verified = 1 ;
    //    $code = "443" ;
       $sql = "insert into users (email, password, role, name, city, district, is_verified, verification_code)
       values (
           ?,
           ?,
           ?,
           ?,
           ?,
           ?,
           ?,
           ?
       ) " ;
       $stmt = $db->prepare($sql) ;
       $stmt->execute([$email, $hash, $role, $name ,$city , $district , $verified, $code ]) ;
       header("Location: verify_email.php") ;
       exit ; 
  
  
      }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
</head>
<body>
   
<form action="" method = "post">
    <button type="submit">Sign up</button>
</form>
   
</body>
</html>