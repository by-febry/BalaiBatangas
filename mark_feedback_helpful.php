<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedback_id = (int)$_POST['feedback_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if user already marked this feedback as helpful
    $check_query = "SELECT * FROM feedback_reactions 
                   WHERE feedback_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $feedback_id, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        // Add reaction and update helpful count
        $conn->begin_transaction();
        
        try {
            // Insert reaction
            $insert_query = "INSERT INTO feedback_reactions (feedback_id, user_id, is_helpful) 
                           VALUES (?, ?, 1)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ii", $feedback_id, $user_id);
            $stmt->execute();
            
            // Update helpful count
            $update_query = "UPDATE product_feedback 
                           SET helpful_count = helpful_count + 1 
                           WHERE feedback_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            
            $conn->commit();
            
            // Get product_id for refresh
            $query = "SELECT product_id FROM product_feedback WHERE feedback_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $feedback_id);
            $stmt->execute();
            $product_id = $stmt->get_result()->fetch_assoc()['product_id'];
            
            echo json_encode(['success' => true, 'product_id' => $product_id]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error processing request']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Already marked as helpful']);
    }
}
?> 