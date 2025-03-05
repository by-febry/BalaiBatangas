<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Updated query to exclude orders with verified payment status
$pendingOrdersQuery = "
    SELECT 
        o.order_id, 
        o.order_date, 
        o.total_amount,
        GROUP_CONCAT(p.product_name SEPARATOR ', ') as products,
        pp.status as payment_status,
        pp.image_path
    FROM orders o 
    LEFT JOIN order_item oi ON o.order_id = oi.order_id 
    LEFT JOIN product p ON oi.product_id = p.product_id
    LEFT JOIN payment_proofs pp ON o.order_id = pp.order_id
    WHERE o.user_id = ? 
    AND (pp.status IS NULL OR pp.status != 'verified')  /* Only show unverified payments */
    GROUP BY o.order_id
    ORDER BY o.order_date DESC";

$stmt = $conn->prepare($pendingOrdersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$pendingOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get other dashboard data...

$response = [
    'pendingOrders' => $pendingOrders,
    // ... other dashboard data ...
];

header('Content-Type: application/json');
echo json_encode($response);
?> 