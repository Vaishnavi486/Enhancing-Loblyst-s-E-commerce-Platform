<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cartItems = [];
} else {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($_SESSION['cart']);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$total = 0;

$userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
if ($userId) {
    $stmt = $conn->prepare("SELECT pc_optimum FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isPcOptimumMember = $user && $user['pc_optimum'] == 1;
} else {
    $isPcOptimumMember = false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['update_product_id'];
    $quantity = (int)$_POST['quantity'];


    if (!isset($_SESSION['qty']) || empty($_SESSION['qty'])) {
        $_SESSION['qty'] = [];
    }
    if ($quantity > 0 && $quantity < 11) {
        $_SESSION['qty'][$productId] = $quantity;
    }
}

?>

<?php include 'header.php'; ?>

<body>
    <div class="container mt-4 cartbdy">
        <h2 class="text-center mb-4">Your Cart</h2>
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info text-center">Your cart is empty.</div>
        <?php else: ?>
            <table class="table-bordered text-center table tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_product_id'])) {
                    $productIdToRemove = $_POST['remove_product_id'];

                    if (($key = array_search($productIdToRemove, $_SESSION['cart'])) !== false) {
                        unset($_SESSION['cart'][$key]);
                        unset($_SESSION['qty'][$productIdToRemove]);
                        $_SESSION['cart'] = array_values($_SESSION['cart']); 
                    }
                }
                ?>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><img src="Images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 100px;"></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td id="price_<?= $item['id']?>">
                                <?php
                                if ($isPcOptimumMember && !empty($item['member_price'])) {
                                    echo '$' . number_format($item['member_price'], 2);
                                    $price = $item['member_price'];
                                } else {
                                    echo '$' . number_format($item['non_member_price'], 2);
                                    $price = $item['non_member_price'];
                                }
                                ?>
                            </td>
                            <td>
                                <div class="input-group cartqty text-center">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updQty('<?= $item['id']; ?>', 'dec')">-</button>
                                    <input type="text" class="form-control m-0 cart-ip" value="<?= $_SESSION['qty'][$item['id']] ?? 1; ?>" name="qty_<?= $item['id']; ?>" id="qty_<?= $item['id']; ?>" disabled>
                                    <button class="btn btn-outline-secondary" type="button" onclick="updQty('<?= $item['id']; ?>', 'inc')">+</button>
                                </div>
                            </td>
                            <td id="subtotal_<?= $item['id']?>">
                                <?php 
                                $subtotal = $price * ($_SESSION['qty'][$item['id']] ?? 1);
                                echo '$' . number_format($subtotal, 2);
                                $total += $subtotal;
                                ?>
                            </td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="remove_product_id" value="<?= $item['id']; ?>">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart('<?= $item['id']; ?>')">Remove</button>

                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <form method="post" action="checkout.php">
                            <td colspan="6" class="text-center">
                                <input type="text" name="checkouttotal" class="form-control m-0 bg-primary text-white p-3 w-25 m-auto text-center rounded totalbox" id="cartTotal" value="Total: $<?php echo number_format($total, 2); ?>" readonly>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <input type="submit" class="btn btn-success p-3 w-25 m-auto text-center rounded mt-5" value="Proceed to Checkout" />
                                <?php else: ?>
                                    <a href="signincart.php" class="btn btn-dark p-3 w-25 m-auto text-center rounded mt-2">Sign In To Checkout</a>
                            <?php endif; ?>
                            </td>
                        </form>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function removeFromCart(productId) {
            var formData = new FormData();
            formData.append('remove_product_id', productId);

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                window.location.href = 'cart.php';
            })
            .catch(error => console.error('Error removing product:', error));
        }

    function updQty(productId, action) {
        var qtyInput = document.getElementById('qty_' + productId);
        var cartprice = document.getElementById('price_' + productId);
        var currentQty = parseInt(qtyInput.value);

        if (action === 'inc'&& currentQty <10) {
            currentQty += 1;
        } else if (action === 'dec' && currentQty > 1) {
            currentQty -= 1;
        }

        qtyInput.value = currentQty;
        let priceString = cartprice.innerText.replace('$', ''); 
        let price = parseFloat(priceString);
        var pdtprice = currentQty * price;
        document.querySelector('#subtotal_' + productId).innerText = '$' + pdtprice.toFixed(2);
        updateQuantityInCart(productId, currentQty);
    }

    function updateQuantityInCart(productId, newQty) {

        var formData = new FormData();
        formData.append('update_product_id', productId);
        formData.append('quantity', newQty);

        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {

        })
        .catch(error => console.error('Error updating quantity:', error));
        var cartTotal = 0;
        var totalElements = document.querySelectorAll('[id^=subtotal_]');
            totalElements.forEach(function(element) {
                cartTotal += parseFloat(element.innerText.replace('$', ''));
            });
            document.getElementById('cartTotal').value = 'Total: $' + cartTotal.toFixed(2);
        window.location.href='cart.php';
    }
    </script>
</body>

<?php include 'footer.php'; ?>
