<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Handle profile picture upload
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
            $_SESSION['profile_picture'] = $targetFilePath; // Store path in session
            $message = "Profile picture updated successfully!";
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    } else {
        $message = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- Navigation Header -->
    <header>
        <div class="logo">LOGO</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="logout.php">Logout</a></li>
           
            </ul>
        </nav>
        <div class="login-circle">
            <div class="user-icon">
                <img src="<?php echo isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.png'; ?>" alt="Profile" style="width: 50px; height: 50px; border-radius: 50%;">
            </div>
        </div>
    </header>

    <!-- Dashboard Section -->
    <main class="dashboard-container">
        <!-- Profile Section -->
        <div class="profile">
            <div class="profile-picture">
                <img src="<?php echo isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.png'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
            </div>
            <p class="username"><?php echo $_SESSION['username']; ?></p>
            <!-- Form to upload profile picture -->
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <label for="profile_picture">Upload new profile picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <button type="submit">Update Profile Picture</button>
            </form>

            <!-- Display message -->
            <p style="color:green;"><?php echo $message; ?></p>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <h2>User Dashboard</h2>

            <!-- Dashboard links for additional functionality -->
            <div class="dashboard-links">
                <a href="account_settings.php">Account Settings</a>
                <a href="order_history.php">Order History</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="support.php">Support</a>
            </div>

            <!-- Statistics or activity -->
            <div class="user-stats">
                <h3>Your Stats</h3>
                <p>Total Orders: 15</p>
                <p>Total Wishlist Items: 7</p>
                <p>Total Spent: $350</p>
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

    <style>
    .recent-orders {
        background: white;
        padding: 20px;