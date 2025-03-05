<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die(json_encode(array('success' => false, 'message' => 'Unauthorized access')));
}

if (isset($_GET['id'])) {
    $festival_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM library WHERE festival_id = ?");
    $stmt->bind_param("i", $festival_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($festival = $result->fetch_assoc()) {
        echo json_encode($festival);
    } else {
        echo json_encode(array('success' => false, 'message' => 'Festival not found'));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'No festival ID provided'));
}
?> 