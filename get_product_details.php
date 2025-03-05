<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    
    // Get product details
    $query = "SELECT p.*, b.brand_name, c.categories_name,
              (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.product_id AND status = 'approved') as review_count,
              (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.product_id AND status = 'approved') as avg_rating
              FROM product p 
              JOIN brands b ON p.brand_id = b.brand_id 
              JOIN categories c ON p.categories_id = c.categories_id 
              WHERE p.product_id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($product = $result->fetch_assoc()) {
        // Get all approved reviews for this product
        $reviews_query = "SELECT 
                            r.*,
                            u.username,
                            u.profile_picture,
                            COUNT(ri.image_id) as image_count
                         FROM product_reviews r
                         JOIN users u ON r.user_id = u.user_id
                         LEFT JOIN review_images ri ON r.review_id = ri.review_id
                         WHERE r.product_id = ? AND r.status = 'approved'
                         GROUP BY r.review_id
                         ORDER BY r.review_date DESC";
        
        $stmt = $conn->prepare($reviews_query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $reviews_result = $stmt->get_result();
        $reviews = [];
        
        while ($review = $reviews_result->fetch_assoc()) {
            // Format the date
            $review_date = new DateTime($review['review_date']);
            $formatted_date = $review_date->format('F j, Y');
            
            $reviews[] = [
                'username' => $review['username'],
                'profile_picture' => $review['profile_picture'],
                'rating' => $review['rating'],
                'review_text' => $review['review_text'],
                'review_date' => $formatted_date,
                'image_count' => $review['image_count'],
                'admin_response' => $review['admin_response']
            ];
        }
        
        // Get all active feedback for this product
        $feedback_query = "SELECT 
                            f.*,
                            u.username,
                            u.profile_picture,
                            COUNT(DISTINCT fr.user_id) as helpful_count
                         FROM product_feedback f
                         JOIN users u ON f.user_id = u.user_id
                         LEFT JOIN feedback_reactions fr ON f.feedback_id = fr.feedback_id
                         WHERE f.product_id = ? AND f.status = 'active'
                         GROUP BY f.feedback_id
                         ORDER BY helpful_count DESC, f.feedback_date DESC";
        
        $stmt = $conn->prepare($feedback_query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $feedback_result = $stmt->get_result();
        $feedback = [];
        
        while ($fb = $feedback_result->fetch_assoc()) {
            // Format the date
            $feedback_date = new DateTime($fb['feedback_date']);
            $formatted_date = $feedback_date->format('F j, Y');
            
            $feedback[] = [
                'username' => $fb['username'],
                'profile_picture' => $fb['profile_picture'],
                'feedback_text' => $fb['feedback_text'],
                'feedback_date' => $formatted_date,
                'helpful_count' => $fb['helpful_count']
            ];
        }
        
        // Calculate average rating and total reviews
        $avg_rating = $product['avg_rating'] ? number_format($product['avg_rating'], 1) : 0;
        $total_reviews = $product['review_count'];
        
        echo json_encode([
            'success' => true,
            'product_name' => $product['product_name'],
            'brand_name' => $product['brand_name'],
            'categories_name' => $product['categories_name'],
            'description' => $product['description'] ?: 'No description available',
            'rate' => $product['rate'],
            'quantity' => $product['quantity'],
            'image_path' => $product['product_image'],
            'avg_rating' => $avg_rating,
            'total_reviews' => $total_reviews,
            'reviews' => $reviews,
            'feedback' => $feedback
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
}
?> 