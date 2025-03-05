<?php
session_start();
include '../connection.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newUsername = $_POST['username'];
        $fileUploaded = false; // Flag to track if a file was uploaded

        // Handle file upload for profile picture
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileSize = $_FILES['profile_picture']['size'];
            $fileType = $_FILES['profile_picture']['type'];

            // Specify the path to upload the file
            $uploadFileDir = 'uploads/profile_pictures/';
            $destPath = $uploadFileDir . basename($fileName);

            // Move the uploaded file
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $fileUploaded = true; // Set flag to true if the file upload is successful
            } else {
                echo "Error moving the uploaded file.";
                exit();
            }
        }

        // Prepare the SQL statement
        if ($fileUploaded) {
            // Update username and profile picture in the database
            $stmt = $conn->prepare("UPDATE users SET username = ?, profile_picture = ? WHERE id = ?");
            $stmt->bind_param("ssi", $newUsername, $destPath, $userId);
        } else {
            // Update username only if no new file uploaded
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->bind_param("si", $newUsername, $userId);
        }

        // Execute the statement
        if ($stmt->execute()) {
            echo "Profile updated successfully.";
            header("Refresh:5; url=../userpanel.php"); // Redirect after 1 second
            exit();
        } else {
            echo "Error updating profile: " . $stmt->error;
        }
    }
} else {
    header("Location: login.php");
    exit();
}
?>
