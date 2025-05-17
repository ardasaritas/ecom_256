<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">ExpirySaver</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav ms-auto">  
      <ul class="navbar-nav ms-auto">
        <li class="nav-item "><a class="nav-link text-center px-3 width-100" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="about.php">About</a></li>
        <?php if(isset($_SESSION["user"]) || isset($_COOKIE["access-token"])): ?>
          <?php if($_SESSION["user"]["role"] == "consumer"): ?>
            <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="consumer_dashboard.php">Dashboard</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="market_dashboard.php">Dashboard</a></li>
            <?php endif?>
            <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="profile.php"><?= $_SESSION["user"]["name"]?></a></li>  
            <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="cart.php">Shopping Cart</a></li>  
            <li class="nav-item"><a class="nav-link" href="/cart.php">🛒 <span id="cart-count"><?= $_SESSION['cart_count'] ?? 0 ?></span></a></li>

            <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link text-center px-3 width-100" href="register.php">Register</a></li>
        <?php endif ?>
        
      </ul>
      </ul>
    </div>
  </div>
</nav>
