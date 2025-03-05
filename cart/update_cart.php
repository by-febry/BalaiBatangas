<?php
session_start();
include 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT c.quantity, p.name, p.price FROM carts c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
// Display cart items
foreach ($cartItems as $item) {
    echo $item['name'] . " - Quantity: " . $item['quantity'] . " - Price: $" . number_format($item['price'], 2) . "<br>";
}
?>