<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Handle file upload
    $upload_dir = 'uploads/payment_proofs/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['payment_proof'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, JPEG & PNG files are allowed.');
    }

    $new_filename = uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;

    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload file.');
    }

    // Create order
    $cart_items = json_decode($_POST['cart_items'], true);
    $total = $_POST['total'];
    $user_id = $_SESSION['user_id'];

    // Insert into orders table with pending status
    $order_query = "INSERT INTO orders (user_id, total_amount, order_date, order_status, payment_status) 
                    VALUES (?, ?, NOW(), 0, 0)";  // Both statuses set to 0 (pending)
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert payment proof with pending status
    $proof_query = "INSERT INTO payment_proofs (order_id, user_id, image_path, status) 
                    VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($proof_query);
    $stmt->bind_param("iis", $order_id, $user_id, $upload_path);
    $stmt->execute();

    // Clear cart
    $clear_cart = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($clear_cart);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 