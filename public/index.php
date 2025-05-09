<?php 
  require_once "../includes/db.php";
  require "../templates/header.php";
  require "../templates/navbar.php"; 
?>

<!-- Hero Section -->
<div class="bg-light py-5 text-center">
  <div class="container">
    <h1 class="display-4 fw-bold">Welcome to ExpirySaver</h1>
    <p class="lead">Helping markets reduce waste and consumers save money on quality food nearing expiration.</p>
    <a href="login.php" class="btn btn-primary btn-lg me-2">Login</a>
    <a href="register.php" class="btn btn-outline-secondary btn-lg">Register</a>
  </div>
</div>

<!-- How It Works -->
<div class="container my-5">
  <div class="row text-center">
    <div class="col-md-4">
      <div class="card h-100 shadow-sm card-hover">
        <div class="card-body">
          <h5 class="card-title">&#128230; Add Products</h5>
          <p class="card-text">Markets upload products close to expiration with discounted prices.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mt-4 mt-md-0">
      <div class="card h-100 shadow-sm card-hover">
        <div class="card-body">
          <h5 class="card-title">&#128269; Browse & Search</h5>
          <p class="card-text">Consumers filter by location, view deals, and add items to their cart.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mt-4 mt-md-0">
      <div class="card h-100 shadow-sm card-hover">
        <div class="card-body">
          <h5 class="card-title">&#128722; Purchase</h5>
          <p class="card-text">Products are removed from inventory as consumers purchase them.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- About This Project -->
<div class="my-5">
  <div class = "container text-center">
    <h2 class="h4 fw-bold ">Why ExpirySaver?</h2>
    <p class="text-muted">
    The waste of expired products in markets poses a serious problem in terms of the effective use of resources. ExpirySaver is an information system designed to address this issue by enabling markets to sell products nearing their expiration date at discounted prices, reducing waste while benefiting consumers.
    </p>
  </div>
  
</div>

<!-- Tech Stack -->
<div class="my-5">
  <div class="container text-center">
    <h2 class="h4 fw-bold mb-4">Tech Stack</h2>
    <div class="row justify-content-center">
      <div class="col-md-2 col-6 mb-3">
        <div class="bg-light border rounded py-3">
        <span class="card-title">PHP</span>
        </div>
      </div>
      <div class="col-md-2 col-6 mb-3">
        <div class="bg-light border rounded py-3">
        <span class="card-title">MySQL</span>
        </div>
      </div>
      <div class="col-md-2 col-6 mb-3">
        <div class="bg-light border rounded py-3">
          <span class="card-title">Bootstrap 5</span>
        </div>
      </div>
      <div class="col-md-2 col-6 mb-3">
        <div class="bg-light border rounded py-3">
        <span class="card-title">HTML/CSS</span>
        </div>
      </div>
      <div class="col-md-2 col-6 mb-3">
        <div class="bg-light border rounded py-3">
        <span class="card-title">jQuery & AJAX</span>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="bg-primary text-white text-center py-4 mt-auto">
  <h5 class="mb-0">Ready to explore?
    <a href="register.php" class="btn btn-light btn-sm ms-2">Get Started</a>
  </h5>
</div>
<?php require_once "../templates/footer.php"; ?>
