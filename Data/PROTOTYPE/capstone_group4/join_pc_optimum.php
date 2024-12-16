<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

require 'db_connection.php';

$userId = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("UPDATE users SET pc_optimum = 1 WHERE id = ?");
    if ($stmt->execute([$userId])) {
        echo "<script>alert('Thank You For Joining the PC Optimum Membership.');</script>";
        echo "<script>window.location.href='index.php'</script>";
    } else {
        throw new Exception("Failed to update PC Optimum status.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
