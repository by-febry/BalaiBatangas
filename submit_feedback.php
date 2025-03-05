<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to submit feedback']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $feedback_text = trim($_POST['feedback_text']);
    
    if (empty($feedback_text)) {
        echo json_encode(['success' => false, 'message' => 'Feedback text is required']);
        exit;
    }
    
    // Insert the feedback
    $query = "INSERT INTO product_feedback (product_id, user_id, feedback_text, status) 
              VALUES (?, ?, ?, 'active')";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $product_id, $user_id, $feedback_text);
    
    if ($stmt->execute()) {
        // Immediately fetch the new feedback to return to the client
        $feedback_id = $conn->insert_id;
        $select_query = "SELECT pf.*, u.username 
                        FROM product_feedback pf
                        JOIN users u ON pf.user_id = u.user_id
                        WHERE pf.feedback_id = ?";
        
        $stmt = $conn->prepare($select_query);
        $stmt->bind_param("i", $feedback_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $feedback = $result->fetch_assoc();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error submitting feedback']);
    }
}
?> 