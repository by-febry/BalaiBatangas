<?php
// Start the session at the top before any output
session_start();

// Include the database connection file
include 'connection.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user data from the database
    $query = "SELECT profile_picture, username FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    $profile_picture = $user['profile_picture'];
    $username = $user['username']; // Make sure $username is assigned properly here
} else {
    // If the user is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Define an array of default product images from reliable sources
$product_images = [
    'Kapeng Barako' => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=800',  // Coffee
    'Dried Fish' => 'https://images.unsplash.com/photo-1574591620322-f14404a9ff65?w=800',      // Dried Fish
    'Lambanog' => 'https://images.unsplash.com/photo-1569529465841-dfecdab7503b?w=800',        // Local Drink
    'Balisong' => 'https://images.unsplash.com/photo-1593001874117-c99c800e3eb7?w=800',        // Knife
    'Batangas Lomi' => 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?w=800',   // Noodles
    'Batangas Bulalo' => 'https://images.unsplash.com/photo-1583835746434-cf1534674b41?w=800', // Soup
    'Tapang Taal' => 'https://images.unsplash.com/photo-1602414393797-455945716c67?w=800',     // Dried Meat
    'Panutsa' => 'https://images.unsplash.com/photo-1587132137056-bfbf0166836e?w=800',         // Sweet Snack
    'Suman' => 'https://images.unsplash.com/photo-1606790948592-7d8751b8f275?w=800',           // Rice Cake
    // Backup food/product images
    'backup1' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800',
    'backup2' => 'https://images.unsplash.com/photo-1516684732162-798a0062be99?w=800',
    'backup3' => 'https://images.unsplash.com/photo-1559181567-c3190ca9959b?w=800',
    // Default image for products without specific images
    'default' => 'https://images.unsplash.com/photo-1553531384-cc64ac80f931?w=800'
];

// Alternative image sources (if any of the above don't work)
$alternative_images = [
    'Kapeng Barako' => 'https://source.unsplash.com/800x600/?coffee',
    'Dried Fish' => 'https://source.unsplash.com/800x600/?dried,fish',
    'Lambanog' => 'https://source.unsplash.com/800x600/?drink,bottle',
    'default' => 'https://source.unsplash.com/800x600/?product'
];

// Function to check if image exists
function imageExists($url) {
    $headers = get_headers($url);
    return stripos($headers[0], "200 OK") ? true : false;
}

// Get image URL with fallback
function getProductImage($product_name, $product_images, $alternative_images) {
    // Try primary image
    if (isset($product_images[$product_name])) {
        $image_url = $product_images[$product_name];
        if (imageExists($image_url)) {
            return $image_url;
        }
    }
    
    // Try alternative image
    if (isset($alternative_images[$product_name])) {
        return $alternative_images[$product_name];
    }
    
    // Return default image
    return $product_images['default'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Batangas - Shop</title>

    <!-- CSS styles -->
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        html,
        body {
            height: 100%;
            background-color: rgb(244, 244, 244);
            /* #f4f4f4 */
            color: rgb(51, 51, 51);
            /* #333 */
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
            /* Dark Red background */
            padding: 10px 20px;
            color: rgb(255, 255, 255);
            /* White text color */
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
            /* Change text color to black when hovered */
        }

        /* Circular Profile Picture or Default Circle */
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

        /* Dropdown menu styling */
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

        .dropdown-content a,
        p {
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

        /* Username styling under the circle */
        .user-name {
            text-align: center;
            font-size: 12px;
            color: #333;
        }


        /* Footer Section */
        footer {
            background-color: #8B0000;
            color: rgb(255, 255, 255);
            padding: 20px 0;
            text-align: center;
            width: 100%;
            margin-top: auto;
            /* Ensure it stays at the bottom */
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

        /* Image Gallery Layout */
        .image-gallery {
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap;
            margin-top: 50px;
        }

        .image-item {
            width: 300px;
            background-color: rgb(255, 255, 255);
            border-radius: 10px;
            margin: 20px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .image-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #fff;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .image-item img:hover {
            transform: scale(1.05);
        }

        /* Add loading animation */
        @keyframes imageLoading {
            0% { opacity: 0.6; }
            50% { opacity: 0.8; }
            100% { opacity: 0.6; }
        }

        .image-item img[src=""] {
            animation: imageLoading 1.5s infinite;
            background-color: #f0f0f0;
        }

        .image-item h3 {
            margin-top: 10px;
            font-size: 18px;
            color: rgb(51, 51, 51);
        }

        /* Button Styling */
        .view-more-container {
            text-align: center;
            margin-top: 100px;
        }

        .view-more-btn {
            padding: 10px 20px;
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .view-more-btn:hover {
            background-color: #a62b2b;
        }

        /* Hero Section */
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/batangas-hero.jpg');
            background-size: cover;
            background-position: center;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }

        .hero-content h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .cta-button {
            padding: 15px 30px;
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #a62b2b;
        }

        /* Featured Categories */
        .categories-section {
            padding: 50px 20px;
            text-align: center;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .category-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        /* Featured Products Section */
        .featured-products {
            background-color: #f9f9f9;
            padding: 50px 20px;
            text-align: center;
        }

        /* About Section */
        .about-section {
            display: flex;
            align-items: center;
            padding: 50px 20px;
            background: white;
        }

        .about-content {
            flex: 1;
            padding: 0 50px;
        }

        .about-image {
            flex: 1;
            text-align: center;
        }

        .about-image img {
            max-width: 100%;
            border-radius: 10px;
        }

        /* Newsletter Section */
        .newsletter-section {
            background-color: #8B0000;
            color: white;
            padding: 50px 20px;
            text-align: center;
        }

        .newsletter-form {
            max-width: 500px;
            margin: 20px auto;
        }

        .newsletter-form input {
            padding: 10px;
            width: 60%;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
        }

        .newsletter-form button {
            padding: 10px 20px;
            background: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Featured Products Styling */
        .image-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .image-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .image-item h3 {
            margin-top: 10px;
            font-size: 18px;
            color: rgb(51, 51, 51);
        }

        .brand,
        .category {
            font-size: 12px;
            color: #333;
        }

        .price {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .original-price {
            text-decoration: line-through;
        }

        .peso-price {
            margin-left: 10px;
        }

        .button-group {
            margin-top: 10px;
        }

        .buy-now,
        .add-to-cart,
        .out-of-stock {
            padding: 10px 20px;
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-right: 10px;
        }

        .buy-now:hover,
        .add-to-cart:hover {
            background-color: #a62b2b;
        }

        .out-of-stock {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background-color: #f5f5f5;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f5f5f5;
            border-radius: 8px;
        }

        .product-image.loaded::before {
            display: none;
        }

        /* Loading animation */
        @keyframes shimmer {
            0% {
                background-position: -468px 0;
            }
            100% {
                background-position: 468px 0;
            }
        }

        .product-image:not(.loaded) {
            background: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-size: 800px 104px;
            animation: shimmer 1.5s infinite linear;
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
                <li><a href="about.php ">About</a></li>

                <!-- User Profile Section -->
                <div class="user-menu">
                    <?php if (!empty($profile_picture)): ?>
                        <!-- If profile picture exists, show it -->
                        <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture" class="user-icon">
                    <?php else: ?>
                        <!-- If no profile picture, show default white circle -->
                        <div class="user-icon">
                            <?php
                            $initials = strtoupper($username[0]); // Display first letter of the username
                            echo $initials;
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-content">
                        <p><?php echo $username; ?></p> <!-- Display username inside the dropdown -->
                        <a href="userpanel.php">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1>Discover Batangas</h1>
                <p>Explore local products, festivals, and cultural heritage</p>
                <a href="shop.php"><button class="cta-button">Start Shopping</button></a>
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="categories-section">
            <h2>Browse Categories</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <img src="img/food-icon.png" alt="Local Food">
                    <h3>Local Delicacies</h3>
                </div>
                <div class="category-card">
                    <img src="img/crafts-icon.png" alt="Crafts">
                    <h3>Traditional Crafts</h3>
                </div>
                <div class="category-card">
                    <img src="img/festivals-icon.png" alt="Festivals">
                    <h3>Festival Items</h3>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="image-gallery">
                <?php
                // Fetch featured products from database
                $query = "SELECT p.*, b.brand_name, c.categories_name 
                          FROM product p 
                          JOIN brands b ON p.brand_id = b.brand_id 
                          JOIN categories c ON p.categories_id = c.categories_id 
                          WHERE p.active = 1 AND p.status = 1 
                          LIMIT 3";
                $result = mysqli_query($conn, $query);
                
                while($product = mysqli_fetch_assoc($result)) {
                    // Get image URL with fallback
                    $image_path = getProductImage($product['product_name'], $product_images, $alternative_images);
                    ?>
                    <div class="image-item">
                        <img src="<?php echo $image_path; ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             onerror="this.src='<?php echo $alternative_images['default']; ?>'"
                             loading="lazy"
                             class="product-image">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="brand"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                        <p class="category"><?php echo htmlspecialchars($product['categories_name']); ?></p>
                        <?php
                        $usd_price = $product['rate'];
                        $php_price = $usd_price * 56;
                        ?>
                        <p class="price">
                            <span class="original-price">$<?php echo number_format($usd_price, 2); ?></span>
                            <span class="peso-price">₱<?php echo number_format($php_price, 2); ?></span>
                        </p>
                        <div class="button-group">
                            <?php if($product['quantity'] > 0) { ?>
                                <button class="buy-now" onclick="buyNow(<?php echo $product['product_id']; ?>)">
                                    Buy Now
                                </button>
                                <button class="add-to-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                    Add to Cart
                                </button>
                            <?php } else { ?>
                                <button class="out-of-stock" disabled>Out of Stock</button>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section">
            <div class="about-content">
                <h2>About Explore Batangas</h2>
                <p>Discover the rich cultural heritage and unique products of Batangas. Our platform connects you directly with local artisans and producers, helping preserve traditions while supporting the local community.</p>
            </div>
            <div class="about-image">
                <img src="img/about-batangas.jpg" alt="Batangas Culture">
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="newsletter-section">
            <h2>Stay Updated</h2>
            <p>Subscribe to our newsletter for the latest products and cultural events</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Enter your email">
                <button type="submit">Subscribe</button>
            </form>
        </section>
    </main>

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
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.product-image');
            
            images.forEach(img => {
                img.onload = function() {
                    this.classList.add('loaded');
                }
                
                img.onerror = function() {
                    // Try loading alternative image
                    if (!this.src.includes('default')) {
                        this.src = '<?php echo $alternative_images['default']; ?>';
                    }
                }
            });
        });
    </script>

</body>

</html>