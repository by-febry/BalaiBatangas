<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Get user basic info
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Get user stats
    $statsStmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT order_id) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_spent
        FROM orders 
        WHERE user_id = ?
    ");
    $statsStmt->bind_param("i", $userId);
    $statsStmt->execute();
    $stats = $statsStmt->get_result()->fetch_assoc();

    $response = [
        'username' => $user['username'],
        'email' => $user['email'],
        'total_orders' => $stats['total_orders'],
        'total_spent' => number_format($stats['total_spent'], 2)
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 