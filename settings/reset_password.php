

<?php

require 'vendor/autoload.php';

include 'connection.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Validate token and check expiration
        $query = "SELECT email FROM password_resets WHERE token = ? AND expires > ?";
        $stmt = $conn->prepare($query);
        $current_time = date("U");
        $stmt->bind_param("si", $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Token is valid
            $row = $result->fetch_assoc();
            $email = $row['email'];

            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the users table
            $updateQuery = "UPDATE users SET password = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $hashed_password, $email);
            $updateStmt->execute();

            // Delete the token after use
            $deleteQuery = "DELETE FROM password_resets WHERE email = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("s", $email);
            $deleteStmt->execute();

            echo "Password has been reset successfully!";
        } else {
            echo "Invalid or expired token.";
        }
    } else {
        echo "Passwords do not match.";
    }
} else {
    // Display the reset password form
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        ?>
        <form action="reset_password.php" method="post">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>
            <button type="submit">Reset Password</button>
        </form>
        <?php
    } else {
        echo "Invalid request.";
    }
}
?>
