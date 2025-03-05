<?php
include 'connection.php';
session_start();

// Initialize variables for current user info
$currentUsername = '';
$currentEmail = '';
$profilePicture = 'default_profile_picture.jpg';

// Check if the user is logged in and fetch user details
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Fetch username, email, and profile picture
    $query = "SELECT username, email, profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($currentUsername, $currentEmail, $profilePicture);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission for updating info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    // Check if old password is correct
    $passwordQuery = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($passwordQuery);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify old password
    if (password_verify($oldPassword, $storedPassword)) {
        // Update username, email, and password if new password is provided
        $updateQuery = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
        $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sssi', $username, $email, $newPasswordHashed, $userId);
        $stmt->execute();
        $stmt->close();

        echo "Account information updated successfully.";
    } else {
        echo "Incorrect old password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Batangas - Account Settings</title>
    <link rel="stylesheet" href="css/account.css">
</head>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="login-circle">
                <?php if ($profilePicture): ?>
                    <img src="<?php echo $profilePicture; ?>" alt="User Icon" class="user-icon" />
                <?php else: ?>
                    <div class="user-icon"></div>
                <?php endif; ?>
                <div class="dropdown-menu">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="account.php">Account Information</a>
                    <a href="logout.php">Sign Out</a>
                </div>
            </div>
        </header>

       

        <!-- Account Settings Form -->
        <div class="account-container">
    <h2>Update Account Information</h2>
    <form action="account.php" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($currentUsername); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentEmail); ?>" required>
        </div>
        <div class="form-group">
            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password"  pattern="(?=.*\d)[A-Za-z\d]{8,}" title="Password must be at least 8 characters long and contain at least one number.">
            <small>Password must be at least 8 characters long and include at least one number.</small>
        </div>
        <button type="submit">Update Information</button>
    </form>
</div>
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
