<?php
session_start();
require_once 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'Not logged in'));
    exit;
}

$userId = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action) {
    // Handle profile update
    case 'updateProfile':
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validate inputs
        if (empty($username) || empty($email)) {
            echo json_encode(array('success' => false, 'message' => 'Username and email are required'));
            exit;
        }
        
        // Start transaction
        $conn->autocommit(FALSE);
        
        try {
            // Check if password change is requested
            if (!empty($currentPassword)) {
                // Verify current password
                $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                // Check if password is MD5 or SHA-256
                $currentPasswordMD5 = md5($currentPassword);
                
                if ($currentPasswordMD5 !== $user['password']) {
                    throw new Exception('Current password is incorrect');
                }
                
                // Validate new password
                if (empty($newPassword)) {
                    throw new Exception('New password is required');
                }
                
                if ($newPassword !== $confirmPassword) {
                    throw new Exception('New passwords do not match');
                }
                
                if (strlen($newPassword) < 6) {
                    throw new Exception('Password must be at least 6 characters long');
                }
                
                // Hash new password with MD5 to maintain consistency
                $hashedPassword = md5($newPassword);
                
                // Update user with new password
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE user_id = ?");
                $stmt->bind_param("sssi", $username, $email, $hashedPassword, $userId);
            } else {
                // Update user without changing password
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
                $stmt->bind_param("ssi", $username, $email, $userId);
            }
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update profile');
            }
            
            $conn->commit();
            echo json_encode(array('success' => true, 'message' => 'Profile updated successfully'));
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(array('success' => false, 'message' => $e->getMessage()));
        }
        break;

    // Handle cart item removal
    case 'removeFromCart':
        $cartId = $_POST['cart_id'] ?? 0;
        
        if (!$cartId) {
            echo json_encode(array('success' => false, 'message' => 'Invalid cart item'));
            exit;
        }
        
        // Delete cart item
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartId, $userId);
        
        if ($stmt->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Item removed from cart'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to remove item'));
        }
        break;

    // Handle cart quantity update
    case 'updateCartQuantity':
        $cartId = isset($_POST['cart_id']) ? $_POST['cart_id'] : 0;
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 0;
        
        if (!$cartId || $quantity < 1) {
            echo json_encode(array('success' => false, 'message' => 'Invalid quantity'));
            exit;
        }
        
        // Update cart quantity
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cartId, $userId);
        
        if ($stmt->execute()) {
            // Get updated cart total
            $stmt = $conn->prepare("
                SELECT SUM(c.quantity * p.rate) as total 
                FROM cart_items c 
                JOIN product p ON c.product_id = p.product_id 
                WHERE c.user_id = ?
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $total = $result->fetch_assoc()['total'] ?? 0;
            
            echo json_encode(array(
                'success' => true, 
                'message' => 'Quantity updated',
                'total' => number_format($total, 2)
            ));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to update quantity'));
        }
        break;

    // Handle cart checkout
    case 'checkout':
        // Start transaction
        $conn->autocommit(FALSE);
        
        try {
            // Get cart items
            $stmt = $conn->prepare("
                SELECT c.*, p.rate, p.quantity as stock 
                FROM cart_items c 
                JOIN product p ON c.product_id = p.product_id 
                WHERE c.user_id = ?
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $cartItems = $stmt->get_result();
            
            if ($cartItems->num_rows === 0) {
                throw new Exception('Cart is empty');
            }
            
            // Calculate total
            $total = 0;
            while ($item = $cartItems->fetch_assoc()) {
                $total += $item['quantity'] * $item['rate'];
            }
            
            // Create order
            $stmt = $conn->prepare("
                INSERT INTO orders (user_id, total_amount, order_date) 
                VALUES (?, ?, NOW())
            ");
            $stmt->bind_param("id", $userId, $total);
            $stmt->execute();
            $orderId = $conn->insert_id;
            
            // Move cart items to order items
            $stmt = $conn->prepare("
                INSERT INTO order_item (order_id, product_id, quantity, rate) 
                SELECT ?, product_id, quantity, ? 
                FROM cart_items 
                WHERE user_id = ?
            ");
            
            foreach ($cartItems as $item) {
                $stmt->bind_param("idi", $orderId, $item['rate'], $userId);
                $stmt->execute();
                
                // Update product stock
                $newStock = $item['stock'] - $item['quantity'];
                if ($newStock < 0) throw new Exception('Insufficient stock');
                
                $updateStock = $conn->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
                $updateStock->bind_param("ii", $newStock, $item['product_id']);
                $updateStock->execute();
            }
            
            // Clear cart
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            
            $conn->commit();
            echo json_encode(array('success' => true, 'message' => 'Order placed successfully'));
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(array('success' => false, 'message' => $e->getMessage()));
        }
        break;

    default:
        echo json_encode(array('success' => false, 'message' => 'Invalid action'));
        break;
}

$conn->close();
?>
