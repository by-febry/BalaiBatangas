<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'Please login first'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    
    // Check if product exists and has stock
    $check_product = "SELECT quantity FROM product WHERE product_id = ? AND active = 1";
    $stmt = $conn->prepare($check_product);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    $product = $product_result->fetch_assoc();
    
    if (!$product || $product['quantity'] <= 0) {
        echo json_encode(array('success' => false, 'message' => 'Product is out of stock'));
        exit();
    }
    
    // Check if item already exists in cart
    $check_cart = "SELECT cart_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_cart);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    
    if ($cart_result->num_rows > 0) {
        // Update existing cart item
        $cart_item = $cart_result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + 1;
        
        $update_query = "UPDATE cart_items SET quantity = ? WHERE cart_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $new_quantity, $cart_item['cart_id']);
        $success = $stmt->execute();
    } else {
        // Insert new cart item
        $insert_query = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $user_id, $product_id);
        $success = $stmt->execute();
    }
    
    if ($success) {
        // Get updated cart count
        $count_query = "SELECT COALESCE(SUM(quantity), 0) as total FROM cart_items WHERE user_id = ?";
        $stmt = $conn->prepare($count_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['total'];
        
        echo json_encode(array(
            'success' => true, 
            'message' => 'Item added to cart successfully',
            'count' => (int)$count
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to add item to cart'));
    }
}
?> 