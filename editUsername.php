<?php
session_start(); // Start the session
include 'connection.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$userId = $_SESSION['user_id'];

// Handle the edit profile request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the action is to edit the profile
    if ($_POST['action'] == 'editProfile') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // Validate input
        if (empty($username) || empty($email)) {
            echo "Username and email cannot be empty.";
            exit();
        }

        // Prepare and execute the update statement
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $userId);

        if ($stmt->execute()) {
            // Update session variables if successful
            $_SESSION['username'] = $username; 
            echo "Profile updated successfully.";
        } else {
            echo "Error updating profile: " . $stmt->error;
        }
        
        $stmt->close();
    }
} else {
    echo "Invalid request.";
}

$conn->close(); // Close the database connection
?>
