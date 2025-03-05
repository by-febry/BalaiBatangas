<?php
session_start();
include '../connection.php';
$message = ''; // Variable to hold the success or error message
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        // Verify old password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (password_verify($oldPassword, $user['password'])) {
            // Hash new password and update it
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $hashedPassword, $userId);
            if ($updateStmt->execute()) {
                // Successful password update
                $message = "Password updated successfully.";
            } else {
                $message = "Error updating password: " . $updateStmt->error;
            }
        } else {
            $message = "Old password is incorrect.";
            header("Refresh:1; url=../userpanel.php"); // Redirect after 5 seconds
        }
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <div>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
            <?php if (strpos($message, 'successfully') !== false): ?>
                <a href="../userpanel.php"><button>Go Back to Dashboard</button></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>