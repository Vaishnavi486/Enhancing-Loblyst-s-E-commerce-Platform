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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {

        $resetLink = "https://accounts.pcid.ca/forgot-password";

        $mail = new PHPMailer(true);
        
        try {

            $mail->isSMTP();                                       
            $mail->Host = 'smtp.gmail.com';  
            $mail->SMTPAuth = true;                                   
            $mail->Username = 'loblystpcoptimum@gmail.com';  // Server username(Mail Sender's Email)
            $mail->Password = 'jyywnzaxgtvfjkvq';         // Server password(Mail Sender's password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
            $mail->Port = 587;     

            $mail->setFrom('loblystpcoptimum@gmail.com', 'Loblyst PC Optimum');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = 'We received a request to reset your password. Click the link below to reset your password:<br><br><a href="' . $resetLink . '">' . $resetLink . '</a><br><br>If you did not request a password reset, please ignore this email.';

            $mail->send();

            $success = "A reset link has been sent to your email.";

        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Email not found in our database.";
    }
}
?>

<div class="container" style="min-height: 75vh;">
  <div class="form-container text-center">
    <h3 class="mb-4">Forgot Password</h3>
    
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>

    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
      <div class="text-center mb-4">
        <img src="Images/forgot.png" alt="Shop Logo" class="img-fluid">
      </div>
      <div class="form-group mb-3">
        <input type="email" class="form-control" name="email" placeholder="Email ID" required>
      </div>
      <button type="submit" class="btn btn-signin w-100 mb-3">Send Reset Link to Mail</button>
      <a href="signin.php" class="d-block">Click here to Sign In</a>
    </form>
  </div>
</div>
  
<?php
include 'footer.php';
?>
</body>
</html>
