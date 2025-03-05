<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id']) && isset($_POST['action'])) {
    $cart_id = (int)$_POST['cart_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    // Verify cart item belongs to user
    $cart_query = "SELECT ci.*, p.quantity as stock 
                   FROM cart_items ci 
                   JOIN product p ON ci.product_id = p.product_id 
                   WHERE ci.cart_id = $cart_id AND ci.user_id = $user_id";
    $cart_result = mysqli_query($conn, $cart_query);
    
    if ($cart_item = mysqli_fetch_assoc($cart_result)) {
        $new_quantity = $cart_item['quantity'];
        
        if ($action === 'increase') {
            if ($new_quantity + 1 <= $cart_item['stock']) {
                $new_quantity++;
            } else {
                echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                exit;
            }
        } else if ($action === 'decrease' && $new_quantity > 1) {
            $new_quantity--;
        }

        $update_query = "UPDATE cart_items 
                        SET quantity = $new_quantity, 
                            updated_at = CURRENT_TIMESTAMP 
                        WHERE cart_id = $cart_id";
        
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    }
} 