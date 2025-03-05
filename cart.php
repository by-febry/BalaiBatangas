<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data for the navbar
$query = "SELECT profile_picture, username FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$profile_picture = $user['profile_picture'];
$username = $user['username'];

$php_rate = 56; // PHP to USD conversion rate

// Fetch cart items with product details
$query = "SELECT ci.*, p.product_name, p.rate, p.quantity as stock, 
          (p.rate * ci.quantity) as total_price 
          FROM cart_items ci 
          JOIN product p ON ci.product_id = p.product_id 
          WHERE ci.user_id = $user_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Explore Batangas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        html, body {
            height: 100%;
            background-color: rgb(244, 244, 244);
            color: rgb(51, 51, 51);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navigation Bar */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #8B0000;
            padding: 10px 20px;
            color: rgb(255, 255, 255);
            font-size: 25px;
        }

        /* Logo Section */
        header .logo img {
            max-height: 50px;
            margin-top: 10px;
        }

        /* Navigation Links */
        header nav ul {
            list-style: none;
            display: flex;
            align-items: center;
        }

        header nav ul li {
            margin-right: 30px;
        }

        header nav ul li a {
            text-decoration: none;
            color: rgb(255, 255, 255);
            font-weight: bold;
        }

        header nav ul li a:hover {
            color: rgb(0, 0, 0);
        }

        /* User Profile Styling */
        .user-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            color: #333;
            border: 2px solid #333;
        }

        .user-menu {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a, .dropdown-content p {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .user-menu:hover .dropdown-content {
            display: block;
        }

        /* Cart Icon Styling */
        .cart-icon {
            position: relative;
            margin-right: 20px;
        }

        .cart-icon a {
            font-size: 24px;
            color: white;
            text-decoration: none;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #2ecc71;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            min-width: 18px;
            text-align: center;
        }

        /* Footer Section */
        footer {
            background-color: #8B0000;
            color: rgb(255, 255, 255);
            padding: 20px 0;
            text-align: center;
            width: 100%;
            margin-top: auto;
        }

        .footer-content p {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgb(0, 0, 0);
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Your existing cart styles here */
        /* ... (keep your existing cart-specific styles) ... */

        /* Add these styles after your existing header/footer styles */

        /* Cart Container Styles */
        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-container h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* Cart Item Styles */
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            gap: 20px;
        }

        .cart-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-details {
            flex: 1;
        }

        .item-details h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
        }

        /* Price Styles */
        .price-details {
            margin: 10px 0;
        }

        .usd-price {
            font-size: 18px;
            font-weight: bold;
            color: #8B0000;
        }

        .php-price {
            font-size: 16px;
            color: #666;
            margin-left: 10px;
        }

        /* Quantity Controls */
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .quantity-controls button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .quantity-controls button:not(.remove-button) {
            background: #8B0000;
            color: white;
        }

        .quantity-controls button:hover:not(.remove-button) {
            background: #660000;
        }

        .quantity-controls span {
            min-width: 30px;
            text-align: center;
            font-weight: bold;
        }

        .remove-button {
            background: #ff4444;
            color: white;
            margin-left: 10px;
        }

        .remove-button:hover {
            background: #cc0000;
        }

        /* Checkout Section */
        .checkout-section {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            text-align: right;
        }

        .total-amount {
            margin-bottom: 20px;
        }

        .total-amount h2 {
            color: #333;
            margin-bottom: 5px;
        }

        /* Button Group */
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .continue-shopping {
            padding: 12px 24px;
            background: #8B0000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .continue-shopping:hover {
            background: #660000;
        }

        .checkout-button {
            padding: 12px 24px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .checkout-button:hover {
            background: #27ae60;
        }

        /* Empty Cart Message */
        .cart-container > p {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
            }

            .quantity-controls {
                justify-content: center;
            }

            .button-group {
                flex-direction: column;
            }

            .checkout-section {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="img/logo3.png" alt="Logo" />
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="library.php">Library</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="#">About</a></li>
                
                <!-- Cart Icon -->
                <li class="cart-icon">
                    <a href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </li>

                <!-- User Profile Section -->
                <div class="user-menu">
                    <?php if (!empty($profile_picture)): ?>
                        <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture" class="user-icon">
                    <?php else: ?>
                        <div class="user-icon">
                            <?php echo strtoupper($username[0]); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-content">
                        <p><?php echo $username; ?></p>
                        <a href="userpanel.php">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </ul>
        </nav>
    </header>

    <div class="cart-container">
        <h1>Your Shopping Cart</h1>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php 
            $total_usd = 0;
            while ($item = mysqli_fetch_assoc($result)): 
                $total_usd += $item['total_price'];
                $item_php_price = $item['rate'] * $php_rate;
                $item_total_php = $item_php_price * $item['quantity'];
            ?>
                <div class="cart-item">
                    <?php
                    // Determine image path based on product name
                    switch($item['product_name']) {
                        case 'Kapeng Barako':
                            $image_path = "assets/images/products/kapeng-barako.jpg";
                            break;
                        case 'Dried Fish':
                            $image_path = "assets/images/products/dried-fish.jpg";
                            break;
                        default:
                            $image_path = "assets/images/products/default.jpg";
                            break;
                    }
                    ?>
                    <img src="<?php echo $image_path; ?>" 
                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                         onerror="this.src='assets/images/products/default.jpg'">
                    
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                        <div class="price-details">
                            <span class="usd-price">$<?php echo number_format($item['rate'], 2); ?></span>
                            <span class="php-price">₱<?php echo number_format($item_php_price, 2); ?></span>
                        </div>
                        <div class="price-details">
                            <span class="usd-price">Total: $<?php echo number_format($item['total_price'], 2); ?></span>
                            <span class="php-price">₱<?php echo number_format($item_total_php, 2); ?></span>
                        </div>
                        
                        <div class="quantity-controls">
                            <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'decrease')">-</button>
                            <span><?php echo $item['quantity']; ?></span>
                            <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'increase')">+</button>
                            <button class="remove-button" onclick="removeItem(<?php echo $item['cart_id']; ?>)">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <div class="checkout-section">
                <div class="total-amount">
                    <h2 class="usd-price">Total: $<?php echo number_format($total_usd, 2); ?></h2>
                    <span class="php-price">₱<?php echo number_format($total_usd * $php_rate, 2); ?></span>
                </div>
                <div class="button-group">
                    <button class="continue-shopping" onclick="window.location.href='shop.php'">
                        Continue Shopping
                    </button>
                    <button class="checkout-button" onclick="window.location.href='checkout.php'">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        <?php else: ?>
            <p>Your cart is empty</p>
            <button class="checkout-button" onclick="window.location.href='shop.php'" style="background: #8B0000;">
                Continue Shopping
            </button>
        <?php endif; ?>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Explore Batangas. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a> |
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

    <script>
    function updateQuantity(cartId, action) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart_id=${cartId}&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function removeItem(cartId) {
        if (confirm('Are you sure you want to remove this item?')) {
            fetch('remove_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cart_id=${cartId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }
    </script>
</body>
</html> 