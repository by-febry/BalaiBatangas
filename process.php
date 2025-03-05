<?php
include 'connection.php';

// Function to add a user
function addUser($username, $email, $password, $role)
{
    global $conn;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $username, $email, $hashedPassword, $role);
    return $stmt->execute();
}

// Function to delete a user
function deleteUser($userId)
{
    global $conn;
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    return $stmt->execute();
}

// Function to edit a user
function editUser($userId, $username, $email, $role)
{
    global $conn;
    $query = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $username, $email, $role, $userId);
    return $stmt->execute();
}

// Handling form submissions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            addUser($_POST['username'], $_POST['email'], $_POST['password'], $_POST['role']);
            header("Location: adminpannel.php");
            break;
        case 'edit':
            editUser($_POST['userId'], $_POST['username'], $_POST['email'], $_POST['role']);
            header("Location: adminpannel.php");
            break;
        case 'delete':
            deleteUser($_POST['userId']);
            header("Location: adminpannel.php");
            break;
    }
}
