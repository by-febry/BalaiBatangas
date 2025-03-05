<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = (int)$_POST['cart_id'];
    $user_id = $_SESSION['user_id'];

    $delete_query = "DELETE FROM cart_items 
                     WHERE cart_id = $cart_id AND user_id = $user_id";
    
    if (mysqli_query($conn, $delete_query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }
} 