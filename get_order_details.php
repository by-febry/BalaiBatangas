<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$orderId = $_GET['order_id'];
$userId = $_SESSION['user_id'];

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, p.product_name, oi.quantity, oi.rate
    FROM orders o
    JOIN order_item oi ON o.order_id = oi.order_id
    JOIN product p ON oi.product_id = p.product_id
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$orderData = null;
$items = [];

while ($row = $result->fetch_assoc()) {
    if (!$orderData) {
        $orderData = [
            'order_id' => $row['order_id'],
            'order_date' => date('M d, Y', strtotime($row['order_date'])),
            'status' => $row['order_status'] == 1 ? 'Completed' : 'Pending',
            'total_amount' => number_format($row['total_amount'], 2),
            'items' => []
        ];
    }
    
    $items[] = [
        'name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'price' => number_format($row['rate'], 2)
    ];
}

$orderData['items'] = $items;

header('Content-Type: application/json');
echo json_encode($orderData);
