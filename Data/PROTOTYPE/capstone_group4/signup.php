<?php
session_start();

require 'db_connection.php';

include 'header.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';
$userExists = false; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmpassword'];
    $pcOptimum = isset($_POST['pcOptimum']) ? 1 : 0;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    } 
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } 
    elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } 
    else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $userExists = true;
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (email, password, pc_optimum) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute([$email, $hashedPassword, $pcOptimum])) {
                
                if ($pcOptimum) {
                    $mail = new PHPMailer(true);
                    
                    try {
                        $mail->isSMTP();                        
                        $mail->Host = 'smtp.gmail.com'; 
                        $mail->SMTPAuth = true;                            
                        $mail->Username = 'loblystpcoptimum@gmail.com';  // SMTP username (Senders Mail)
                        $mail->Password = 'jyywnzaxgtvfjkvq';         // SMTP password (Sendes Password)
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      
                        $mail->Port = 587;                                

                        $mail->setFrom('loblystpcoptimum@gmail.com', 'Loblyst PC Optimum');
                        $mail->addAddress($email);    
                        
                        $mail->isHTML(true);                              
                        $mail->Subject = 'Setup Your PC Optimum Account';
                        $mail->Body    = 'Thank you for signing up for Loblyst and opting into the PC Optimum membership!<br><br>Click the link below to complete your PC Optimum account setup:<br><a href="https://accounts.pcid.ca/create-account">PC Optimum Account Setup</a>';
                        
                        $mail->send();
                    } catch (Exception $e) {
                        $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                }

                header("Location: signin.php");
                exit();
            } else {
                $error = "Error: Could not create your account. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | Loblyst</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div class="container" style="min-height: 75vh;">
    <div class="form-container text-center">
      <img src="Images/icon.png" alt="Loblyst Logo" class="img-fluid mb-4" style="width: 150px;">
      <h3 class="mb-3">Create your Loblyst account</h3>
      <p>Already have one? <a href="signin.php">Sign in here</a></p>
      
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
      <?php endif; ?>

      <?php if ($userExists): ?>
        <script type="text/javascript">
          alert("This email is already registered. Please use a different email.");
          window.location.href='signin.php';
        </script>
      <?php endif; ?>

      <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
        <div class="text-center mb-4">
            <img src="Images/create.png" alt="Shop Logo" class="img-fluid">
        </div>
        <div class="form-group mb-3">
          <input type="email" class="form-control" name="email" placeholder="Email ID" required>
        </div>
        <div class="form-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
        <div class="form-group mb-3">
          <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password" required>
        </div>
        <div class="form-check mb-4 text-start">
          <input type="checkbox" class="form-check-input" id="pcOptimum" name="pcOptimum">
          <label class="form-check-label" for="pcOptimum">PC Optimum Membership?</label>
        </div>
        
        <button type="submit" class="btn btn-join w-100">Join Loblyst!</button>
        
        <p class="mt-4">By submitting this form, you agree to Loblyst’s <a href="#">Terms of Service</a>.</p>
      </form>
    </div>
  </div>
  
<?php
include 'footer.php';
?>
</body>
</html>
