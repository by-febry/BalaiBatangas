<?php
session_start(); // Start the session
include 'connection.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = hash('sha256', $_POST['password']); // Hash the password using SHA-256

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['success_message'] = "Registration successful!";
        // Redirect to index.php
        header("Location: index.php");
        exit(); // Terminate the script after the redirect
    } else {
        // Set error message in session
        $_SESSION['error_message'] = "Registration failed: " . $stmt->error;
        // Redirect back to the registration page (or wherever you want)
        header("Location: register.php");
        exit();
    }
}

$conn->close(); // Close the database connection
