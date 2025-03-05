<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Batangas - Shop</title>
    <link rel="stylesheet" href="shop.css">
</head>
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
    }

    body {
        display: flex;
        flex-direction: column;
    }

    /* Wrapper for content */
    .wrapper {
        flex: 1;
        /* Allows the wrapper to grow and fill the available space */
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

    /* Logo Section in the Header */
    header .logo img {
        max-height: 80px;
        /* Adjust this value based on header height */
    }

    /* Navigation Links */
    header nav {
        margin-left: auto;
        /* Pushes the navigation links to the right */
    }

    header nav ul {
        list-style: none;
        /* Removes bullet points from list */
        display: flex;
        /* Displays list items in a row */
    }

    /* Individual List Items */
    header nav ul li {
        margin-right: 30px;
        /* Space between menu items */
    }

    /* Links in Navigation */
    header nav ul li a {
        text-decoration: none;
        /* Removes underline from links */
        color: rgb(255, 255, 255);
        /* White text color */
        font-weight: bold;
    }

    /* Hover Effect on Links */
    header nav ul li a:hover {
        color: rgb(0, 0, 0);
        /* Changes text color to black when hovered */
    }

    /* User Icon & Dropdown Placeholder */
    .login-circle {
        position: relative;
    }

    .user-icon {
        width: 90px;
        height: 75px;
        background-color: #fff;
        /* Remove this if you always want to show an image */
        background-size: cover;
        /* Ensure the image covers the entire circle */
        background-position: center;
        /* Center the image */
        background-repeat: no-repeat;
        /* Prevent the image from repeating */
        border-radius: 50%;
        /* Makes the placeholder a circle */
        cursor: pointer;
        /* Changes cursor to pointer (clickable) */
    }


    /* Container for each image */
    .image-item {
        width: 300px;
        background-color: rgb(255, 255, 255);
        /* Container box background */
        border-radius: 10px;
        margin: 20px;
        padding: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    /* Image Gallery Layout */
    .image-gallery {
        display: flex;
        justify-content: space-evenly;
        flex-wrap: wrap;
        margin-top: 50px;
    }

    .image-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .image-item h3 {
        margin-top: 10px;
        font-size: 18px;
        color: rgb(51, 51, 51);
        /* Title color */
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
        /* Slightly lighter red on hover */
    }

    /* Footer Section */
    footer {
        background-color: #8B0000;
        /* #C72C41 */
        color: rgb(255, 255, 255);
        /* #fff */
        padding: 20px 0;
        text-align: center;
        width: 100%;
        /* Stick the footer to the bottom */
        margin-top: auto;
    }

    .footer-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .footer-content p {
        margin-bottom: 10px;
    }

    .footer-links {
        margin-top: 10px;
    }

    .footer-links a {
        color: rgb(0, 0, 0);
        text-decoration: none;
        margin: 0 10px;
    }

    .footer-links a:hover {
        text-decoration: underline;
    }
</style>

<body>

    <!-- Wrapper for content -->
    <div class="wrapper">

        <header>
            <div class="logo">
                <img src="img/Screenshot 2024-10-13 151839.png" alt="Logo" />
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="#">About</a></li>
                    <!-- Image Gallery with individual containers -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

        </header>
        <!-- Image Gallery with individual containers -->
        <div class="image-gallery">
            <div class="image-item">
                <img src="img/bakahan.jpg" alt="Bakahan Festival">
                <h3>Bakahan Festival</h3>
            </div>
            <div class="image-item">
                <img src="img/tapusan.jpg" alt="Tapusan Festival">
                <h3>Tapusan Festival</h3>
            </div>
            <div class="image-item">
                <img src="img/lambayok.webp" alt="Lambayok Festival">
                <h3>Lambayok Festival</h3>
            </div>
        </div>

        <!-- View More Button -->
        <div class="view-more-container">
            <a href="viewmore.php" class="view-more-btn">View More</a>
        </div>


    </div> <!-- End of Wrapper -->

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

</body>

</html>
<script src="script.js"></script>

<main>
    <div class="image-gallery">
        <div class="image-item">
            <img src="img/bakahan.jpg" alt="Product 1">
            <h3>Product 1</h3>
        </div>
        <div class="image-item">
            <img src="img/lambayok.webp" alt="Product 2">
            <h3>Product 2</h3>
        </div>
        <div class="image-item">
            <img src="img/tapusan.jpg" alt="Product 3">
            <h3>Product 3</h3>
        </div>
        <!-- Add more products as needed -->
    </div>
    <div class="view-more-container">
        <button class="view-more-btn">View More</button>
    </div>
</main>