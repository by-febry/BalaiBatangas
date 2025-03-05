<?php
session_start();
include '../connection.php';
if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}
$user_id = $_SESSION['user_id'];
$query = "SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_count);
$stmt->fetch();
echo $cart_count ? $cart_count : 0;
?>