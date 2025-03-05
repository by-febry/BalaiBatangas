<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Function to create notification
function createNotification($conn, $user_id, $title, $message, $type = 'order') {
    $stmt = $conn->prepare("INSERT INTO user_notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $message, $type);
    return $stmt->execute();
}

// Update order status and create notification
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    // Update order tracking
    $stmt = $conn->prepare("INSERT INTO order_tracking (order_id, status, status_message) VALUES (?, ?, ?)");
    $message = "Your order #$order_id has been $status";
    $stmt->bind_param("iss", $order_id, $status, $message);
    
    if ($stmt->execute()) {
        // Create notification for user
        $title = "Order Status Update";
        createNotification($conn, $user_id, $title, $message);
        
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating status']);
    }
} 