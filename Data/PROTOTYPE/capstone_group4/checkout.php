<?php 

include 'header.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cartItems = [];
} else {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($_SESSION['cart']);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$final_total = 0;
$total_value = 0;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cart.php");
    exit();
} else {
    $total = $_POST['checkouttotal'];
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        preg_match('/\d+(\.\d{1,2})?/', $total, $matches);

        if (isset($matches[0])) {
            $total_value = floatval($matches[0]);
        } else {
            $total_value = 0; 
        }

        $formatted_total = $total_value;
        $final_total = $total_value;

        $promo_code_error = "";
        if (isset($_POST['promoCode']) && $_POST['promoCode'] === "NEW25" && !isset($_SESSION['promo'])) {  
            $discount = 0.25; 
            $final_total = $total_value - ($total_value * $discount);
            $_SESSION['promo'] = true;  
        } elseif (isset($_POST['promoCode']) && $_POST['promoCode'] !== "NEW25") {
            $promo_code_error = "Invalid Promo Code";
        }
        $formatted_total = number_format($final_total, 2);
    }

    $name_pattern = '/^[a-zA-Z\s]+$/';
    $street_pattern = '/^[a-zA-Z0-9\s]+$/';
    $zip_pattern = '/^[A-Za-z]\d[A-Za-z] \d[A-Za-z]\d$/';
    $card_pattern = '/^\d{16}$/';
    $cvv_pattern = '/^\d{3}$/';

    $name_error  = $city_error = $prov_error = $street_error = $zip_error = $card_error = $cvv_error = $expiry_error  = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['fullName'])) {
            $name = $_POST['fullName'];
            if (!preg_match($name_pattern, $name)) {
                $name_error = "Name must contain only letters and spaces";
            }
        }

        if (isset($_POST['city'])) {
            $city = $_POST['city'];
            if (!preg_match($name_pattern, $city)) {
                $city_error = "City contains only letter and spaces";
            }
        }

        if (isset($_POST['province'])) {
            $prov = $_POST['province'];
            if (!preg_match($name_pattern, $prov)) {
                $prov_error = "Province contains only letter and spaces";
            }
        }

        if (isset($_POST['street'])) {
            $street = $_POST['street'];
            if (!preg_match($street_pattern, $street)) {
                $street_error = "Street contains only letter, number and spaces";
            }
        }

        if (isset($_POST['zip'])) {
            $zip = $_POST['zip'];
            if (!preg_match($zip_pattern, $zip)) {
                $zip_error = "Postal code must be in A1A 1A1 format";
            }
        }

        if (isset($_POST['cardNumber'])) {
            $card = $_POST['cardNumber'];
            if (!preg_match($card_pattern, $card)) {
                $card_error = "Card number must contain 16 digits";
            }
        }

        if (isset($_POST['expiryDate'])) {
            $expiry_date = $_POST['expiryDate'];
            if (empty($expiry_date)) {
                $expiry_error = "Please select expiry date";
            }
        }

        if (isset($_POST['cvv'])) {
            $cvv = $_POST['cvv'];
            if (!preg_match($cvv_pattern, $cvv)) {
                $cvv_error = "CVV must contain 3 digits";
            }
        }
    }

    if (isset($_POST['submit']) && empty($name_error) && empty($city_error) && empty($prov_error) && empty($street_error) && empty($zip_error) && empty($card_error) && empty($cvv_error)){
        header("Location: orderconfirmation.php");
        exit();
    }
}

?>

<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Checkout</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white pt-3">
                        <h5>Your Cart</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($cartItems as $item): ?>
                            <li class="list-group-item d-flex align-items-center">
                                <img src="Images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px; height: 50px;" class="me-3">
                                <div>
                                    <h6 class="m-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small>Quantity: <?= $_SESSION['qty'][$item['id']] ?? 1; ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-footer text-center">
                        <strong><?php echo "Total($)";?></strong>
                    </div>
                    <div class="card-footer text-center">
                        <strong><?php echo $formatted_total;?></strong>
                    </div>
                </div>

                <div class="card-footer mt-3">
                    <?php if (!isset($_SESSION['promo'])) { ?>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="checkouttotal" value="<?php echo $total; ?>" readonly>
                            <input type="text" class="form-control me-2" name="promoCode" placeholder="Enter Promo Code" style="width: 100%;">
                            <button type="submit" class="btn btn-dark w-100">Apply</button>
                        </form>

                        <?php if (!empty($promo_code_error)) { ?>
                            <div class="mt-3 text-danger"><?php echo $promo_code_error; ?></div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="mt-3 text-success">Promo code already applied!</div>
                    <?php } ?>
                </div>

            </div>

            <div class="col-md-8">
                <div class="card mb-5">
                    <div class="card-header bg-dark text-white text-center pt-3">
                        <h5>Checkout Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
                            <div class="mb-3">
                             <input type="hidden" name="checkouttotal" value="$<?php echo $formatted_total; ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullName" name="fullName">
                                <?php if (!empty($name_error)) { ?>
                                    <div class="text-danger"><?php echo $name_error; ?></div>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label for="street" class="form-label">Street</label>
                                <input type="text" class="form-control" id="street" name="street"></input>
                                <?php if (!empty($street_error)) { ?>
                                    <div class="text-danger"><?php echo $street_error; ?></div>
                                <?php } ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city">
                                    <?php if (!empty($city_error)) { ?>
                                        <div class="text-danger"><?php echo $city_error; ?></div>
                                    <?php } ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="province" class="form-label">Province</label>
                                    <input type="text" class="form-control" id="province" name="province">
                                    <?php if (!empty($prov_error)) { ?>
                                        <div class="text-danger"><?php echo $prov_error; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="zip" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="zip" name="zip">
                                <?php if (!empty($zip_error)) { ?>
                                    <div class="text-danger"><?php echo $zip_error; ?></div>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="cardNumber" name="cardNumber">
                                <?php if (!empty($card_error)) { ?>
                                    <div class="text-danger"><?php echo $card_error; ?></div>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label for="expiryDate" class="form-label">Expiry Date</label>
                                <input type="month" class="form-control" id="expiryDate" name="expiryDate">
                                <?php if (!empty($expiry_error)) { ?>
                                    <div class="text-danger"><?php echo $expiry_error; ?></div>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="password" class="form-control" id="cvv" name="cvv">
                                <?php if (!empty($cvv_error)) { ?>
                                    <div class="text-danger"><?php echo $cvv_error; ?></div>
                                <?php } ?>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-dark w-25 p-3" name="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
