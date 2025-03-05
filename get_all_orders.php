<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];

$query = "
    SELECT o.order_id, o.order_date, o.total_amount, o.order_status,
           GROUP_CONCAT(p.product_name SEPARATOR ', ') as products
    FROM orders o 
    LEFT JOIN order_item oi ON o.order_id = oi.order_id 
    LEFT JOIN product p ON oi.product_id = p.product_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'order_id' => $row['order_id'],
        'order_date' => date('M d, Y', strtotime($row['order_date'])),
        'total_amount' => number_format($row['total_amount'], 2),
        'status' => $row['order_status'] == 0 ? 'Pending' : 'Completed',
        'products' => $row['products']
    ];
}

header('Content-Type: application/json');
echo json_encode($orders); 