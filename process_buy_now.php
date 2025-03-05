<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to make a purchase']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$product_id = (int)$data['product_id'];
$quantity = (int)$data['quantity'];

try {
    // Start transaction
    $conn->begin_transaction();

    // Check product availability and price
    $query = "SELECT quantity, rate FROM product WHERE product_id = ? AND active = 1 AND status = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        throw new Exception('Product not available');
    }

    if ($product['quantity'] < $quantity) {
        throw new Exception('Insufficient stock');
    }

    $total_amount = $quantity * $product['rate'];

    // Create order
    $order_query = "INSERT INTO orders (user_id, order_date, total_amount, status) 
                    VALUES (?, NOW(), ?, 'pending')";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("id", $user_id, $total_amount);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Add order items
    $items_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($items_query);
    $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['rate']);
    $stmt->execute();

    // Update product quantity
    $update_query = "UPDATE product SET quantity = quantity - ? WHERE product_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $quantity, $product_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order created successfully'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 