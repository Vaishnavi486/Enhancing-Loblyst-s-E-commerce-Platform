<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db_connection.php';

$isPcOptimumMember = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT pc_optimum FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $isPcOptimumMember = (bool)$result['pc_optimum'];
    }
}

$totalCartItems = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $itemId) {
        $quantity = isset($_SESSION['qty'][$itemId]) ? $_SESSION['qty'][$itemId] : 1;
        $totalCartItems += $quantity;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="css/styles.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <header class="bg-light border-bottom py-2 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
       <div>
        <a href="index.php" class="text-decoration-none">
            <img src="Images/icon.png" alt="Loblyst Icon" class="img-fluid" style="width: 200px; height: auto;">
        </a>
        <?php if (isset($_SESSION['user_id']) && !$isPcOptimumMember): ?>
        <a href="join_pc_optimum.php" class="btn btn-outline-secondary text-decoration-none ms-3">
            Join PC Optimum
        </a>
        <?php endif; ?>

      </div>
      
      <nav>
        <ul class="nav">
          <li class="nav-item">
            <a href="index.php" class="nav-link text-dark">
              <i class="fas fa-home me-1"></i> Home
            </a>
          </li>
          <li class="nav-item">
            <a href="products.php" class="nav-link text-dark">
              <i class="fas fa-th-large me-1"></i> Products
            </a>
          </li>

            <li class="nav-item">
              <a href="cart.php" class="nav-link text-dark">
                  <i class="fas fa-shopping-cart me-1"></i> Cart
                  <?php if ($totalCartItems > 0): ?>
                      <span class="badge bg-danger text-white"><?= $totalCartItems; ?></span>
                  <?php endif; ?>
              </a>
          </li>

          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a href="logout.php" class="nav-link text-dark">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a href="signin.php" class="nav-link text-dark">
                <i class="fas fa-sign-in-alt me-1"></i> Sign In
              </a>
            </li>
            <li class="nav-item">
              <a href="signup.php" class="nav-link text-dark">
                <i class="fas fa-user-plus me-1"></i> Sign Up
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>
