<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

$query = "UPDATE user_notifications 
          SET is_read = 1 
          WHERE user_id = ? AND is_read = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);

$success = $stmt->execute();

$response = [
    'success' => $success,
    'count' => 0,
    'message' => $success ? 'All notifications marked as read' : 'Error marking notifications as read'
];

header('Content-Type: application/json');
echo json_encode($response);
?> 