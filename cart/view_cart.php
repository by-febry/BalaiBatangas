<?php
session_start();
include '../connection.php';
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['name'] == $_POST['product_name']) {
            $cartItem['quantity'] = (int)$_POST['quantity']; // Update the quantity
            break; // Exit the loop once we've updated the quantity
        }
    }
}
// Handle item removal
if (isset($_GET['remove'])) {
    $product_name = $_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $cartItem) {
        if ($cartItem['name'] == $product_name) {
            unset($_SESSION['cart'][$key]); // Remove item from cart
            break; // Exit the loop once we've removed the item
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
}
// Handle checkout
if (isset($_POST['checkout'])) {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $user_id = $_SESSION['user_id'];
        // Insert each cart item into the orders table
        foreach ($_SESSION['cart'] as $item) {
            $product_name = $item['name'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            // Insert into orders table
            $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isid", $user_id, $product_name, $quantity, $price);
            $stmt->execute();
        }
        // Clear the cart after checkout
        unset($_SESSION['cart']);
        // Redirect to a confirmation page (you can create this page)
        header("Location: order_confirmation.php");
        exit();
    } else {
        echo "Your cart is empty!";
    }
}
// Add to Cart logic (update this section in your shop.php)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_name'], $_POST['price'])) {
    $user_id = $_SESSION['user_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    
    // Check if the product already exists in the cart
    $stmt = $conn->prepare("SELECT * FROM carts WHERE user_id = ? AND product_id = (SELECT product_id FROM products WHERE name = ?)");
    $stmt->bind_param("is", $user_id, $product_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // If the product is already in the cart, increase the quantity
        $cartItem = $result->fetch_assoc();
        $newQuantity = $cartItem['quantity'] + 1;
        $updateStmt = $conn->prepare("UPDATE carts SET quantity = ? WHERE cart_id = ?");
        $updateStmt->bind_param("ii", $newQuantity, $cartItem['cart_id']);
        $updateStmt->execute();
    } else {
        // If not, add the product to the cart
        $stmt = $conn->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, (SELECT product_id FROM products WHERE name = ?), ?)");
        $quantity = 1; // default quantity
        $stmt->bind_param("isi", $user_id, $product_name, $quantity);
        $stmt->execute();
    }
    
    header("Location: shop.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Explore Batangas</title>
    <link rel="stylesheet" href="../css/shops.css">
    <script src="script.js" defer></script>
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="logo">
            <img src="img/logo3.png" alt="Logo" />
        </div>
        <nav>
            <ul>
                <li><a href="../home.php">Home</a></li>
                <li><a href="#">Library</a></li>
                <li><a href="../shop.php">Shop</a></li>
                <li><a href="#">About</a></li>
                <li><a href="../cart/view_cart.php">Cart (<span id="cartCount"><?php echo count($_SESSION['cart'] ?? []); ?></span>)</a></li>
                <!-- User Profile Section -->
                <div class="user-menu">
                    <?php if (!empty($profile_picture)): ?>
                        <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture" class="user-icon">
                    <?php else: ?>
                        <div class="user-icon">
                            <?php echo strtoupper($username[0]); ?>
                        </div>
                    <?php endif; ?>
                    <div class="dropdown-content">
                        <p><?php echo htmlspecialchars($username); ?></p>
                        <a href="../userpanel.php">Dashboard</a>
                        <a href="../logout.php">Logout</a>
                    </div>
                </div>
            </ul>
        </nav>
    </header>
    <!-- Cart Overview -->
    <div class="cart-container">
        <h2>Your Cart</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0; 
                    foreach ($_SESSION['cart'] as $cartItem): 
                        $subtotal = $cartItem['price'] * $cartItem['quantity'];
                        $total += $subtotal; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cartItem['name']); ?></td>
                            <td>$<?php echo number_format($cartItem['price'], 2); ?></td>
                            <td>
                                <form method="POST" action="view_cart.php">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($cartItem['name']); ?>">
                                    <input type="number" name="quantity" value="<?php echo $cartItem['quantity']; ?>" min="1">
                                    <button type="submit" name="update" class="btn">Update</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td><a href="view_cart.php?remove=<?php echo urlencode($cartItem['name']); ?>" class="btn">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <form action="" method="POST">
                <button type="submit" name="checkout">Checkout</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty. Start shopping!</p>
        <?php endif; ?>
    </div>
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Explore Batangas. All Rights Reserved.</p>
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a> | 
            <a href="#">Contact Us</a>
        </div>
    </footer>
</body>
</html>