<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['user_id'];
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

        // Validate inputs
        if ($product_id <= 0) {
            throw new Exception('Invalid product ID');
        }
        if ($rating < 1 || $rating > 5) {
            throw new Exception('Rating must be between 1 and 5');
        }
        if (empty($review_text)) {
            throw new Exception('Review text is required');
        }

        // Check if user has already reviewed this product
        $check_query = "SELECT review_id FROM product_reviews 
                       WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing review
            $review = $result->fetch_assoc();
            $update_query = "UPDATE product_reviews 
                           SET rating = ?, 
                               review_text = ?, 
                               review_date = CURRENT_TIMESTAMP,
                               status = 'pending'
                           WHERE review_id = ?";
            
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("isi", $rating, $review_text, $review['review_id']);
            
            if ($stmt->execute()) {
                // Fetch the username for the response
                $user_query = "SELECT username FROM users WHERE user_id = ?";
                $stmt = $conn->prepare($user_query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user_result = $stmt->get_result();
                $user = $user_result->fetch_assoc();

                echo json_encode([
                    'success' => true,
                    'message' => 'Your review has been updated and is pending approval',
                    'review' => [
                        'review_id' => $review['review_id'],
                        'username' => $user['username'],
                        'rating' => $rating,
                        'review_text' => $review_text,
                        'status' => 'pending',
                        'is_update' => true
                    ]
                ]);
            } else {
                throw new Exception('Error updating review');
            }
        } else {
            // Insert new review
            $insert_query = "INSERT INTO product_reviews 
                           (product_id, user_id, rating, review_text, status) 
                           VALUES (?, ?, ?, ?, 'pending')";
            
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiis", $product_id, $user_id, $rating, $review_text);
            
            if ($stmt->execute()) {
                $review_id = $conn->insert_id;
                
                // Fetch the username for the response
                $user_query = "SELECT username FROM users WHERE user_id = ?";
                $stmt = $conn->prepare($user_query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user_result = $stmt->get_result();
                $user = $user_result->fetch_assoc();

                echo json_encode([
                    'success' => true,
                    'message' => 'Review submitted successfully and pending approval',
                    'review' => [
                        'review_id' => $review_id,
                        'username' => $user['username'],
                        'rating' => $rating,
                        'review_text' => $review_text,
                        'status' => 'pending',
                        'is_update' => false
                    ]
                ]);
            } else {
                throw new Exception('Error submitting review');
            }
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 