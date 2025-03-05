<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    
    $query = "SELECT r.*, u.username 
              FROM product_reviews r
              JOIN users u ON r.user_id = u.user_id
              WHERE r.product_id = ? AND r.status = 'approved'
              ORDER BY r.review_date DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($review = $result->fetch_assoc()) {
        $reviews[] = $review;
    }
    
    echo json_encode(['reviews' => $reviews]);
}
?> 