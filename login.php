<?php
// Include the database connection
include 'connection.php';

// Your secret key from Google reCAPTCHA
$secretKey = "secret key";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CAPTCHA response
    $captcha = $_POST['g-recaptcha-response'];

    if (!$captcha) {
        echo "Please check the reCAPTCHA box.";
    }
    // Send the response to Google's verification server
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseKeys = json_decode($response, true);

    // Check if reCAPTCHA is successful
    if (intval($responseKeys["success"]) !== 1) {
        echo "CAPTCHA verification failed. Please try again.";
    } else {
        // CAPTCHA was successful, proceed with login
        $email = $_POST['email'];
        $password = md5($_POST['password']); // Use MD5 to match your database

        // Check if the email and password match
        $query = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Start a session and store user info
            session_start();
            $_SESSION['user_id'] = $user['user_id']; // Changed to match your database column name
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on the role
            if ($user['role'] == 'admin') {
                header("Location: adminpannel.php"); // Redirect to admin dashboard
            } else {
                header("Location: home.php"); // Redirect to user dashboard
            }
            exit();
        } else {
            echo "Invalid email or password. Please try again.";
        }
        $stmt->close();
    }
}
