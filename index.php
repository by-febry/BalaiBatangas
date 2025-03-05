<?php
session_start(); // Start the session
// Include the database connection
include 'connection.php'; // Make sure the path is correct

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Batangas - Shop</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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

        /* Login Button */
        .btnLogin-popup {
            width: 110px;
            height: 30px;
            background: transparent;
            border: 2px solid #fff;
            outline: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: small;
            color: #fff;
            font-weight: 500;
            transition: .5s;
            margin-left: 10px;
        }

        .btnLogin-popup:hover {
            background: #fff;
            color: #162938;
        }

        /* Form Popup Background */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        /* Form Box */
        .form-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            position: relative;
        }

        .form-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Input Fields */
        .input-box {
            position: relative;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .input-box label {
            width: 100px;
            font-size: 14px;
            color: #333;
            margin-right: 10px;
        }

        .input-box input {
            width: calc(100% - 110px);
            padding: 10px;
            border: 2px solid #333;
            border-radius: 5px;
            outline: none;
            background: none;
        }

        /* Close Button */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background-color: #8B0000;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 16px;
            line-height: 30px;
            text-align: center;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .btn {
            width: 100%;
            background-color: #8B0000;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #333;
        }

        .login-register p {
            text-align: center;
        }

        .login-register p a {
            color: #8B0000;
            text-decoration: none;
        }

        .login-register p a:hover {
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
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        .search-container {
            width: 300px;
            text-align: center;
        }

        #searchInput {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #results {
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-height: 200px;
            overflow-y: auto;
        }

        .result-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-item:hover {
            background-color: #f0f0f0;
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
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Library</a></li>
                <li><a href="#">About</a></li>
                <button class="btnLogin-popup">Login</button>
            </ul>
        </nav>
    </header>

    <!-- Popup Form -->
    <div class="popup" id="popupForm">
        <div class="form-box login" id="loginForm">
            <button class="close-btn" id="closeBtn">&times;</button>
            <h2>Login</h2>
            <form method="POST" action="login.php"> <!-- Point to the login handler -->
                <div class="input-box">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="input-box">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="g-recaptcha" data-sitekey="6LeOP2AqAAAAAHQB2xmCuXSJzy1yqinSx01NeCxJ"></div>
                <div class="remember-forgot">
                    <label><input type="checkbox"> Remember me</label>
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
                <div class="login-register">
                    <p>Don't have an account? <a href="#" class="register-link">Register</a></p>
                </div>
            </form>
        </div>

        <div class="form-box register" id="registerForm" style="display: none;">
            <button class="close-btn" id="closeRegisterBtn">&times;</button>
            <h2>Register</h2>
            <form method="POST" action="register.php"> <!-- Point to the registration handler -->
                <div class="input-box">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="input-box">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="input-box">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" name="register" class="btn">Register</button>
                <div class="login-register">
                    <p>Already have an account? <a href="#" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>




    <!-- Your main content here -->
    <main>
       


        <div class="image-gallery">
            <div class="image-item">
                <img src="img/bakahan.jpg" alt="Product 1">
                <h3>Showcase 1</h3>
            </div>
            <div class="image-item">
                <img src="img/lambayok.webp" alt="Product 2">
                <h3>Showcase 2</h3>
            </div>
            <div class="image-item">
                <img src="img/tapusan.jpg" alt="Product 3">
                <h3>Showcase 3</h3>
            </div>
            <!-- Add more products as needed -->
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
    <script>
        const loginBtn = document.querySelector('.btnLogin-popup');
        const popup = document.getElementById('popupForm');
        const closeBtn = document.getElementById('closeBtn');
        const closeRegisterBtn = document.getElementById('closeRegisterBtn');
        const registerLink = document.querySelector('.register-link');
        const loginLink = document.querySelector('.login-link');

        loginBtn.addEventListener('click', () => {
            popup.style.display = 'flex';
            document.getElementById('loginForm').style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        closeRegisterBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        registerLink.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        });

        loginLink.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        });
    </script>

</body>

</html>

<?php
$conn->close(); // Close the database connection
?>