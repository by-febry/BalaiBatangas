<?php
// Include your database connection
include 'connection.php';
session_start();  // Start the session to access session data

// Initialize the profile picture variable
$profilePicture = 'default_profile_picture.jpg'; // Set a default image for guests

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Fetch user details, including profile picture
    $userId = $_SESSION['user_id'];
    $query = "SELECT profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($profilePicture);
    $stmt->fetch();
    $stmt->close();
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="css/products.css">
</head>

<body>
    <!-- Navigation Bar -->
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

    <!-- Main Content -->
    <main>
        <div class="product-container">
            <div class="product-image">
                <img src="img/Dried Fish.jpg" alt="Product Image" id="main-product-image">
            </div>
            <div class="product-details">
                <h1>Lambanog</h1>
                <p>San Juan Lambanog is a traditional Philippine distilled spirit made from coconut. With San Juan Lambanog, we have improved production by fermenting on-site which guarantees a finer and cleaner spirit that preserves the complexity of a true Lambanog.</p>
                <p class="availability">Availability: <span id="availability">In Stock</span></p>
                <div class="buying-section">
                    <label for="quantity">Quantity: </label>
                    <input type="number" id="quantity" placeholder="Enter quantity" min="1" />
                    <button class="buy-button">Buy Now</button>
                </div>
            </div>
        </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Explore Batangas. All Rights Reserved.</p>
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a> |
            <a href="#">Terms of Service</a> |
            <a href="#">Contact Us</a>
        </div>
    </footer>

    <script src="script.js"></script>
</body>

</html>