<?php
session_start();

require 'db_connection.php';

include 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            header("Location: index.php"); 
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="container" style="min-height: 75vh;">
  <div class="form-container text-center">
    <h3 class="mb-4">Sign in</h3>
    
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <div class="text-center mb-4">
        <img src="Images/shop.png" alt="Shop Logo" class="img-fluid">
    </div>
      <div class="form-group mb-3">
        <input type="email" class="form-control" name="email" placeholder="Email ID" required>
      </div>
      <div class="form-group mb-3">
        <input type="password" class="form-control" name="password" placeholder="Password" required>
      </div>
      <button type="submit" class="btn btn-signin w-100 mb-3">Sign in</button>
      <a href="forgotpassword.php" class="d-block mb-2">Forgot password?</a>
      <a href="signup.php" class="d-block">Create New Account</a>
    </form>
  </div>
</div>
  
<?php
include 'footer.php';
?>
</body>
</html>
