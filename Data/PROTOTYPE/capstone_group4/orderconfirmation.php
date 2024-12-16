<?php 
include 'header.php'; 

if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

if (isset($_SESSION['quantity'])) {
    unset($_SESSION['quantity']);
}
?>

<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow-lg mx-auto orderconfirm" style="max-width: 700px;">
            <div class="card-header bg-success text-white text-center py-4">
                <h2 class="mb-0">Order Confirmed!</h2>
            </div>
            <div class="card-body text-center">
                <img src="Images/order_confirmed.png" alt="Order Confirmed" class="img-fluid mb-4" style="max-height: 300px;">

                <h4 class="mb-3">Thank you for your order!</h4>
                <p>Your order has been successfully placed and is being processed.</p>
                <p><strong>Order ID:</strong> #<?= uniqid('ORD-'); ?></p>

                <div class="mt-4">
                    <a href="products.php" class="btn btn-success btn-lg me-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
