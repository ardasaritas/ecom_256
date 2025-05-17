<?php 

$page = $_POST['page'] ?? 1;
$search = $_POST['search'] ?? '';
$redirectURL = "/consumer_dashboard.php?search=$search&page=$page";



header("Location: $redirectURL");
exit;
?>