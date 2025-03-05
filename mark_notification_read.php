<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$notificationId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
$userId = $_SESSION['user_id'];

$query = "UPDATE user_notifications 
          SET is_read = 1 
          WHERE notification_id = ? AND user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $notificationId, $userId);

$success = $stmt->execute();

// Get updated count
$countQuery = "SELECT COUNT(*) as count 
               FROM user_notifications 
               WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($countQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$count = $stmt->get_result()->fetch_assoc()['count'];

$response = [
    'success' => $success,
    'count' => (int)$count,
    'message' => $success ? 'Notification marked as read' : 'Error marking notification as read'
];

header('Content-Type: application/json');
echo json_encode($response);