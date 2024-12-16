<?php
session_start();

require 'db_connection.php';

$isPcOptimumMember = isset($_SESSION['user']) && $_SESSION['user']['pc_optimum'] == 1;

$sql = "SELECT * FROM products";
$products = $conn->query($sql);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = htmlspecialchars($_POST['product_id']);

    if (!in_array($productId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $productId;
    }

}
?>

<?php include 'header.php'; ?>



<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Our Products</h2>
        <div class="row">
            <?php if ($products->rowCount() > 0): ?>
                <?php while ($product = $products->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card pdcard">
                            <?php if (!empty($product['member_price'])): ?>
                                <span class="mx-3 my-2 text-danger memberspan">Member saves more!</span>
                            <?php endif; ?>
                                <img src="Images/<?= htmlspecialchars($product['image']) ?>" class="card-img-top pdimg" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title pdtitle"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text pddesc"><?= htmlspecialchars($product['description']) ?></p>
                                    <p class="card-text pdprice">
                                        <?php if ($isPcOptimumMember): ?>
                                            <?php if (!empty($product['member_price'])): ?>
                                                <span class="text-danger pdprice">Member Price: $<?= number_format($product['member_price'], 2) ?></span>
                                            <?php else: ?>
                                                Price: $<?= number_format($product['non_member_price'], 2) ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if (empty($product['member_price'])): ?>
                                                Price: $<?= number_format($product['non_member_price'], 2) ?><br>
                                            <?php elseif (!empty($product['member_price'])): ?>
                                                <span class="text-danger pdprice">Member Price: $<?= number_format($product['member_price'], 2) ?></span><br>
                                                Non-Member Price: $<?= number_format($product['non_member_price'], 2); ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </p>
                            </div>
                            <?php if ($product['stock'] == 0): ?>
                                        <a href="https://www.realcanadiansuperstore.ca" target="_blank" class="btn btn-danger w-100 pdaddcart">Out of Stock! Check on Real Canadian Superstore</a>
                                    <?php else: ?>
                                        <form method="POST" class="text-center">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>" id="productid-<?= htmlspecialchars($product['id']) ?>">
                                            <button 
                                                class="btn btn-dark mx-auto my-3 align-items-center pdaddcart" 
                                                id="<?= htmlspecialchars($product['id']) ?>" 
                                                <?= in_array($product['id'], $_SESSION['cart']) ? 'disabled' : '' ?>>
                                                <?= in_array($product['id'], $_SESSION['cart']) ? 'Added to Cart' : 'Add to Cart' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">No products available.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
<?php include 'footer.php'; 

?>
