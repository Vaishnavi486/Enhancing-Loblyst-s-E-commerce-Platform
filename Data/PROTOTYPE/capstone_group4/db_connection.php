<?php

$host = 'localhost';
$db = 'capstone_group4';
$user = 'root'; // MySQL username
$pass = '';     // MySQL password

try {
    $conn = new PDO("mysql:host=$host", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS $db");

    $conn->exec("USE $db");

    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        pc_optimum BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS products (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        non_member_price DECIMAL(10,2) NOT NULL,
        member_price DECIMAL(10,2) DEFAULT NULL,
        image VARCHAR(255) NOT NULL,
        stock INT(11) NOT NULL,
        PRIMARY KEY (id)
    ) ");

    $stmt = $conn->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $conn->exec("INSERT INTO products (name, description, non_member_price, member_price, image, stock) VALUES
            ('Loblaws Gala Apples, 4lb bag', 'Gala apples are crisp, sweet, and a little spicy with pink-orange stripes over a yellow background. They are juicy, fragrant, and best enjoyed raw. You can also caramelize Gala apples along with onions to use as a topping for pork chops', 8.00, 5.99, 'applesbag3lb.png', 20),
            ('No Name Original Bread, 675g', 'Perfect for busy mornings, Original Bread is sliced and ready to eat. Always have this kitchen staple in your fridge for when you want avocado toast or your favourite stacked sandwiches.', 1.99, NULL, 'nonamebread.png', 20),
            ('Ferrero COLLECTION Chocolates, 15ct', 'A divinely decadent collection of Ferreros finest confections. The 15-count FERRERO COLLECTION chocolate gift box is sure to captivate chocolate enthusiasts.', 10.99, 6.99, 'collectionchocolate.png', 50),
            ('Vaseline Aloe Vera Body Lotion, 600ml', 'Vaseline Intensive Care Aloe Vera Hydration Body Lotion for dry skin is a dermatologist-tested body lotion designed to refresh and soothe dehydrated skin.', 6.99, 4.99, 'vaseline.png', 34),
            ('Pepsi Soda, 6x710ml', 'The bold, refreshing, robust cola. Perfect for parties, meals, and celebrations big and small. 6 count of 710mL / 24oz bottles - Great value of your favourite drink.', 4.99, NULL, 'pepsi.png', 0),
            ('Strawberries 1LB','Strawberries vary in colour, shape, and size but their flavour is distinctively sweet. They are topped with a hull of green leaves and are speckled with seeds.',6.00, NULL,'strawberry.png',10)
        ");
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>
