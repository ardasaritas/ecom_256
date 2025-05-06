<?php
// Database configuration (customize accordingly)
define("DSN", "mysql:host=localhost;dbname=expirySaver;charset=utf8mb4");
define("USER", "root");
define("PASS", "");

// Database connection
try {
    $db = new PDO(DSN, USER, PASS);
} catch (PDOException $e) {
    print("<p>Connection Error: " . $ex->getMessage() . "</p>"); 
    exit; 
}
?>
