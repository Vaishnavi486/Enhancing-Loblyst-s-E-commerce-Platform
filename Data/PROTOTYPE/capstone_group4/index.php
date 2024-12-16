<?php
    session_start();
    require('db_connection.php');

    include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage | Loblyst</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .cover {
            position: relative;
            background: url('Images/christmas2.jpg') no-repeat center center/cover;
            height: 80vh;
            color: white;
        }

        .cover-content {
            position: absolute;
            top: 50%;
            left: 70%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .cover h1 {
            font-size: 4rem;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
        }

        .cover p {
            font-size: 1.5rem;
            font-weight: 300;
            margin-top: 10px;
            text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.6);
        }

        .products {
            padding: 4rem 0;
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
        }

        .values {
            background: #f9f9f9;
            padding: 4rem 2rem;
            text-align: center;
        }

        .values h2 {
            margin-bottom: 2rem;
            font-size: 2.5rem;
            color: #333;
        }

        .value-item {
            margin-top: 1rem;
        }

        .special-offers {
            background: #f1f8ff;
            padding: 4rem 2rem;
            text-align: center;
        }

        .special-offers img {
            max-width: 100%;
            height: auto;
        }

        .special-offers h3 {
            margin-top: 1rem;
            color: #ff4c4c;
        }

        .special-offers p {
            margin-top: 0.5rem;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="cover">
        <div class="cover-content">
            <h1>Welcome to Loblyst</h1>
            <p>Make your Christmas magical with our fresh, quality and festive goodies.</p>
            <a href="products.php" class="custom-btn">Shop Now</a>

        </div>
    </div>

    <div id="products" class="products container text-center">
        <h2 class="mb-5">Our Products</h2>
        <div class="row justify-content-center">
            <?php

            $query = "SELECT * FROM products ORDER BY RAND() LIMIT 2";
            $products = $conn->query($query);

            if ($products && $products->rowCount() > 0) {
                while ($row = $products->fetch(PDO::FETCH_ASSOC)){ 
                    ?>
                    <div class="col-md-5 mb-4">
                        <div class="card product-card">
                            <img src="Images/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <a href="products.php" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-muted'>No featured products available.</p>";
            }
            ?>
        </div>
    </div>



    <div class="values text-center my-5">
    <h2 class="mb-5">What We Offer</h2>
    <div class="container">
        <div class="row">
            <div class="col-md-4 value-item">
                <div class="icon mb-3">
                    <i class="fa-solid fa-gift" style="font-size: 4rem; color: #007bff;"></i> 
                </div>
                <h5>Unique Gifts</h5>
                <p>Curated products to make your loved ones feel special.</p>
            </div>
            <div class="col-md-4 value-item">
                <div class="icon mb-3">
                    <i class="fa-solid fa-truck-fast" style="font-size: 4rem; color: #28a745;"></i>
                </div>
                <h5>Fast Delivery</h5>
                <p>Quick and reliable shipping to get your items on time.</p>
            </div>
            <div class="col-md-4 value-item">
                <div class="icon mb-3">
                    <i class="fa-solid fa-face-smile" style="font-size: 4rem; color: #ffc107;"></i> 
                </div>
                <h5>Customer Satisfaction</h5>
                <p>Your happiness is our top priority.</p>
            </div>
        </div>
    </div>
</div>

<div class="stores my-5 py-5 text-center bg-light">
    <h2 class="mb-4">Major Stores Under Loblaws</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-2">
                <a href="https://www.realcanadiansuperstore.ca" target="_blank">
                    <img src="Images/realcanadiansuperstore.png" class="store-logo img-fluid" alt="Real Canadian Superstore">
                </a>
            </div>
            <div class="col-2">
                <a href="https://www.nofrills.ca" target="_blank">
                    <img src="Images/nofrills.png" class="store-logo img-fluid" alt="No Frills">
                </a>
            </div>
            <div class="col-2">
                <a href="https://www.shoppersdrugmart.ca/" target="_blank">
                    <img src="Images/shoppersdrugmart.png" class="store-logo img-fluid" alt="Shoppers Drug Mart">
                </a>
            </div>
            <div class="col-2">
                <a href="https://www.zehrs.ca" target="_blank">
                    <img src="Images/zehrs.png" class="store-logo img-fluid" alt="Zehrs Markets">
                </a>
            </div>
            <div class="col-2">
                <a href="https://www.provigo.ca" target="_blank">
                    <img src="Images/provigo.png" class="store-logo img-fluid" alt="Provigo">
                </a>
            </div>
            <div class="col-2">
                <a href="https://www.fortinos.ca" target="_blank">
                    <img src="Images/fortinos.png" class="store-logo img-fluid" alt="Fortinos">
                </a>
            </div>
            <div class="col-2 mt-4">
                <a href="https://www.wholesaleclub.ca" target="_blank">
                    <img src="Images/wholesale.png" class="store-logo img-fluid" alt="Wholesale Club">
                </a>
            </div>
            <div class="col-2 mt-4">
                <a href="https://www.yourindependentgrocer.ca" target="_blank">
                    <img src="Images/independent.png" class="store-logo img-fluid" alt="Independent City Market">
                </a>
            </div>
        </div>
    </div>
</div>



<?php
            include 'footer.php';

?>