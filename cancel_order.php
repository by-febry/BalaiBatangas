<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'];
$userId = $_SESSION['user_id'];

// Verify order belongs to user and is pending
$query = $conn->prepare("
    UPDATE order_tracking 
    SET status = 'cancelled' 
    WHERE order_id = ? 
    AND order_id IN (SELECT order_id FROM orders WHERE user_id = ?)
    AND status = 'pending'
");

$query->bind_param("ii", $orderId, $userId);
$success = $query->execute();

echo json_encode(array('success' => $success));
