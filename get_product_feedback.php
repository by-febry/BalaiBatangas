<?php
session_start();
require_once 'connection.php';

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    
    $query = "SELECT f.*, u.username 
              FROM product_feedback f
              JOIN users u ON f.user_id = u.user_id
              WHERE f.product_id = ? AND f.status = 'active'
              ORDER BY f.helpful_count DESC, f.feedback_date DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $feedback = [];
    while ($item = $result->fetch_assoc()) {
        $feedback[] = $item;
    }
    
    echo json_encode(['feedback' => $feedback]);
}
?> 