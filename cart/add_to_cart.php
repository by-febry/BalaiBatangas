<?php
session_start();

// Check if product data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_name'], $_POST['price'], $_POST['quantity'])) {
    $product = [
        'name' => $_POST['product_name'],
        'price' => $_POST['price'],
        'quantity' => $_POST['quantity']
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the product is already in the cart
    $product_names = array_column($_SESSION['cart'], 'name');
    if (in_array($product['name'], $product_names)) {
        // If the product is already in the cart, increase the quantity
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['name'] == $product['name']) {
                $cartItem['quantity'] += $product['quantity']; // Increase quantity
            }
        }
    } else {
        // If not, add the product to the cart
        $_SESSION['cart'][] = $product;
    }
    
    echo count($_SESSION['cart']); // Send back the count of items in the cart
}
?>