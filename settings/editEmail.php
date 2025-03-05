<?php
session_start();
include '../connection.php';
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newEmail = $_POST['email'];
        // Update email in the database
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $newEmail, $userId);
        if ($stmt->execute()) {
            echo "Email updated successfully.";
            header("Refresh:1; url=../userpanel.php"); // Redirect after 5 seconds
        } else {
            echo "Error updating email: " . $stmt->error;
        }
    }
} else {
    header("Location: login.php");
    exit();
}
?>