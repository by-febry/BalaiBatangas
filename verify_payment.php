<?php
session_start();
require_once 'connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if order_id is provided
if (!isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$orderId = (int)$_POST['order_id'];

try {
    $conn->begin_transaction();

    // Update payment_proofs status
    $updatePayment = $conn->prepare("
        UPDATE payment_proofs 
        SET status = 'verified' 
        WHERE order_id = ?
    ");
    $updatePayment->bind_param("i", $orderId);
    $updatePayment->execute();

    // Update orders status
    $updateOrder = $conn->prepare("
        UPDATE orders 
        SET order_status = 1 
        WHERE order_id = ?
    ");
    $updateOrder->bind_param("i", $orderId);
    $updateOrder->execute();

    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Payment verified successfully'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false, 
        'message' => 'Error verifying payment: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 