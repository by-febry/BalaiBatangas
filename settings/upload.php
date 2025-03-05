<?php
session_start();  // Start the session to access session data

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the session variable is not set, redirect to the login page
    header("Location: login.php");
    exit;
}

include 'connection.php'; // Include your database connection

if (isset($_POST['submit'])) {
    $userId = $_SESSION['user_id']; // Get the user's ID from the session
    $targetDir = "uploads/"; // Directory where the image will be saved
    $fileName = basename($_FILES["profile-picture"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allow certain file formats (e.g., jpg, png, jpeg, gif)
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
    if (in_array($fileType, $allowedTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $targetFilePath)) {
            // Update the user's profile_picture column with the file path
            $updateQuery = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('si', $targetFilePath, $userId);

            if ($stmt->execute()) {
                echo "Profile picture updated successfully!";
                // Redirect to the dashboard
                header("Location: dashboard.php");
            } else {
                echo "Error updating profile picture in the database.";
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}
?>
