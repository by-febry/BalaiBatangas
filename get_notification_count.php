<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$userId = $_SESSION['user_id'];

$query = "SELECT COUNT(*) as count 
          FROM user_notifications 
          WHERE user_id = ? AND is_read = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc()['count'];

header('Content-Type: application/json');
echo json_encode(['count' => (int)$count]);
?> 