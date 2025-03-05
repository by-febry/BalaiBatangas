<?php
// Start the session at the top before any output
session_start();

// Include the database connection file
include 'connection.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user data from the database
    $query = "SELECT profile_picture, username FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    $profile_picture = $user['profile_picture'];
    $username = $user['username'];
} else {
    // If the user is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Initialize search and category filters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Modify the product query for "All Categories" view
if ($category_filter === 0) {
    $query = "SELECT p.*, b.brand_name, c.categories_name 
              FROM product p 
              JOIN brands b ON p.brand_id = b.brand_id 
              JOIN categories c ON p.categories_id = c.categories_id 
              WHERE p.active = 1 AND p.status = 1";
    
    if ($search) {
        $query .= " AND (p.product_name LIKE '%$search%' OR b.brand_name LIKE '%$search%')";
    }
    
    $query .= " ORDER BY c.categories_name, p.product_name";
} else {
    // Existing filtered query remains the same
    $query = "SELECT p.*, b.brand_name, c.categories_name 
              FROM product p 
              JOIN brands b ON p.brand_id = b.brand_id 
              JOIN categories c ON p.categories_id = c.categories_id 
              WHERE p.active = 1 AND p.status = 1 AND p.categories_id = $category_filter";
    
    if ($search) {
        $query .= " AND (p.product_name LIKE '%$search%' OR b.brand_name LIKE '%$search%')";
    }
    
    $query .= " ORDER BY p.product_name";
}

$products = mysqli_query($conn, $query);

// Fetch all categories for the sidebar
$categories_query = "SELECT * FROM categories ORDER BY categories_name";
$categories_result = mysqli_query($conn, $categories_query);

// Fetch user's addresses
$addresses_query = "SELECT * FROM user_addresses WHERE user_id = ? AND is_default = 1";
$stmt = $conn->prepare($addresses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$default_address = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Batangas - Shop</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        html, body {
            height: 100%;
            background-color: rgb(244, 244, 244);
            color: rgb(51, 51, 51);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navigation Bar */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #8B0000;
            padding: 10px 20px;
            color: rgb(255, 255, 255);
            font-size: 25px;
        }

        /* Logo Section */
        header .logo img {
            max-height: 50px;
            margin-top: 10px;
        }

        /* Navigation Links */
        header nav ul {
            list-style: none;
            display: flex;
            align-items: center;
        }

        header nav ul li {
            margin-right: 30px;
        }

        header nav ul li a {
            text-decoration: none;
            color: rgb(255, 255, 255);
            font-weight: bold;
        }

        header nav ul li a:hover {
            color: rgb(0, 0, 0);
        }

        /* User Profile Styling */
        .user-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            color: #333;
            border: 2px solid #333;
        }

        .user-menu {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a, .dropdown-content p {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .user-menu:hover .dropdown-content {
            display: block;
        }

        /* Updated Shop Specific Styles */
        .shop-container {
            padding: 10px;
            flex: 1;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(4, auto);
            gap: 15px;
            padding: 10px;
        }

        .product-card {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
        }

        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: contain;
            background: #fff;
            padding: 10px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .product-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-info h3 {
            margin: 0 0 10px 0;
            font-size: 1.2em;
        }

        .brand, .category {
            color: #666;
            margin: 5px 0;
            font-size: 0.9em;
        }

        .price {
            font-size: 1.2em;
            font-weight: bold;
            color: #8B0000;
            margin: 10px 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .original-price {
            font-size: 0.9em;
            color: #666;
            text-decoration: none;
        }

        .peso-price {
            color: #8B0000;
        }

        .add-to-cart {
            width: 100%;
            padding: 10px;
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .add-to-cart:hover {
            background-color: #660000;
        }

        .out-of-stock {
            width: 100%;
            padding: 10px;
            background-color: #ccc;
            color: #666;
            border: none;
            border-radius: 4px;
            cursor: not-allowed;
        }

        /* Footer Section */
        footer {
            background-color: #8B0000;
            color: rgb(255, 255, 255);
            padding: 20px 0;
            text-align: center;
            width: 100%;
            margin-top: auto;
        }

        .footer-content p {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgb(0, 0, 0);
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Add this to your existing style section */
        .shop-layout {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .sidebar {
            width: 250px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            margin-bottom: 15px;
            color: #8B0000;
        }

        .category-list {
            list-style: none;
        }

        .category-list li {
            margin-bottom: 10px;
        }

        .category-list a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .category-list a:hover,
        .category-list a.active {
            background-color: #f0f0f0;
            color: #8B0000;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .search-container button {
            width: 100%;
            padding: 10px;
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #660000;
        }

        .main-content {
            flex: 1;
        }

        /* Add these new styles */
        .category-divider {
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #8B0000;
        }

        .category-divider h2 {
            color: #8B0000;
            font-size: 1.5em;
            margin: 0;
        }

        .products-grid {
            margin-bottom: 30px;
        }

        /* Modify the existing products-grid style */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 10px 0;
        }

        /* Add to your existing styles */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .buy-now, .add-to-cart {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .buy-now {
            background-color: #2ecc71;
            color: white;
        }

        .buy-now:hover {
            background-color: #27ae60;
        }

        .add-to-cart {
            background-color: #8B0000;
            color: white;
        }

        .add-to-cart:hover {
            background-color: #660000;
        }

        .out-of-stock {
            width: 100%;
            padding: 10px;
            background-color: #ccc;
            color: #666;
            border: none;
            border-radius: 4px;
            cursor: not-allowed;
        }

        /* Update product-info to accommodate the new button layout */
        .product-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-info h3 {
            margin: 0 0 10px 0;
            font-size: 1.2em;
        }

        /* Add to your existing styles */
        .cart-icon {
            position: relative;
            margin-right: 20px;
        }

        .cart-icon a {
            font-size: 24px;
            color: white;
            text-decoration: none;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #2ecc71;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            min-width: 18px;
            text-align: center;
        }

        .checkout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }

        .checkout-btn:hover {
            background-color: #27ae60;
        }

        .cart-total {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .cart-total p {
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* Add to your existing style section */
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .shipping-address {
            background: #f8f8f8;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .change-address, .add-address-btn {
            display: inline-block;
            padding: 5px 10px;
            background: #8B0000;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }

        .change-address:hover, .add-address-btn:hover {
            background: #660000;
        }

        /* Modal Styles */
        .product-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            border-radius: 8px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            position: relative;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .modal-body {
            padding: 20px;
        }

        .product-details {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-info {
            flex: 1;
        }

        .product-info p {
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        #modalImage {
            max-width: 300px;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
        }

        .modal-content {
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-body {
            padding: 20px;
        }

        /* Ensure proper spacing without the buttons */
        .product-info {
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .modal-tabs {
            margin-top: 20px;
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 10px 20px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-weight: bold;
            color: #666;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            color: #8B0000;
        }

        .tab-button.active {
            color: #8B0000;
            border-bottom: 2px solid #8B0000;
        }

        .tab-panel {
            display: none;
            padding: 20px 0;
        }

        .tab-panel.active {
            display: block;
        }

        /* Make sure the description tab is visible by default */
        #description.tab-panel {
            display: block;
        }

        /* Style the reviews and feedback content */
        .review-item, .feedback-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }

        .review-item:last-child, .feedback-item:last-child {
            border-bottom: none;
        }

        .review-header, .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .username {
            font-weight: bold;
            color: #333;
        }

        .review-meta, .feedback-date {
            color: #666;
            font-size: 0.9em;
        }

        .rating {
            color: #ffd700;
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        .review-content, .feedback-content {
            margin: 15px 0;
            line-height: 1.6;
        }

        .admin-response {
            margin-top: 15px;
            padding: 15px;
            background: #f5f5f5;
            border-left: 3px solid #8B0000;
        }

        .helpful-count {
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .helpful-count i {
            color: #8B0000;
            margin-right: 5px;
        }

        .review-item.pending {
            opacity: 0.7;
            background-color: #f9f9f9;
            border-left: 3px solid #ffd700;
            padding-left: 15px;
        }

        .no-reviews, .no-feedback {
            text-align: center;
            color: #666;
            padding: 20px;
            font-style: italic;
        }

        .rating-select {
            margin-bottom: 15px;
        }

        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 5px;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 25px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffd700;
        }

        /* Add to your existing CSS */
        .price-container {
            margin: 15px 0;
            font-size: 1.2em;
        }

        .usd-price {
            color: #666;
            font-size: 0.9em;
            text-decoration: line-through;
            margin-bottom: 5px;
        }

        .php-price {
            color: #8B0000;
            font-weight: bold;
            font-size: 1.2em;
        }

        /* Add to your existing CSS */
        .review-form, .feedback-form {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .review-form h3, .feedback-form h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .rating-select {
            margin-bottom: 15px;
        }

        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 5px;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 25px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffd700;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }

        .submit-btn {
            background: #8B0000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #660000;
        }

        /* Reviews and Feedback Styles */
        .reviews-summary {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .average-rating {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .big-rating {
            font-size: 2em;
            font-weight: bold;
            color: #8B0000;
        }

        .out-of {
            font-size: 1.2em;
            color: #666;
        }

        .total-reviews {
            font-size: 1.2em;
            color: #666;
        }

        .reviews-list {
            margin-bottom: 20px;
        }

        .review-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            margin-bottom: 10px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .username {
            font-weight: bold;
        }

        .review-meta {
            display: flex;
            align-items: center;
        }

        .rating {
            font-size: 1.2em;
            font-weight: bold;
            color: #8B0000;
            margin-right: 10px;
        }

        .date {
            font-size: 0.9em;
            color: #666;
        }

        .review-content {
            margin-bottom: 10px;
        }

        .admin-response {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
        }

        .no-reviews {
            text-align: center;
            color: #666;
        }

        .feedback-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            margin-bottom: 10px;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .feedback-author {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .feedback-date {
            font-size: 0.9em;
            color: #666;
        }

        .feedback-content {
            margin-bottom: 10px;
        }

        .helpful-count {
            color: #666;
            font-size: 0.9em;
        }

        .no-feedback {
            text-align: center;
            color: #666;
        }

        /* Reviews and Feedback Styles */
        .reviews-summary {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .big-rating {
            font-size: 48px;
            color: #8B0000;
            font-weight: bold;
        }

        .out-of {
            font-size: 24px;
            color: #666;
        }

        .total-reviews {
            color: #666;
            margin-top: 10px;
        }

        .review-item, .feedback-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .username {
            font-weight: bold;
            color: #333;
        }

        .review-meta, .feedback-date {
            color: #666;
            font-size: 0.9em;
        }

        .rating {
            color: #ffd700;
            font-size: 1.2em;
        }

        .review-content, .feedback-content {
            margin: 15px 0;
            line-height: 1.6;
        }

        .admin-response {
            margin-top: 15px;
            padding: 15px;
            background: #f5f5f5;
            border-left: 3px solid #8B0000;
        }

        .helpful-count {
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .helpful-count i {
            color: #8B0000;
            margin-right: 5px;
        }

        .no-reviews, .no-feedback {
            text-align: center;
            color: #666;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .review-item.pending {
            opacity: 0.7;
            background-color: #f9f9f9;
            border-left: 3px solid #ffd700;
        }

        .review-item.pending::after {
            content: '(Pending Admin Approval)';
            display: block;
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }

        /* Modal Close Button Styles */
        .modal-close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #666;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            color: #8B0000;
            background-color: rgba(0, 0, 0, 0.1);
        }

        /* Make sure modal header has proper positioning */
        .modal-header {
            position: relative;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        /* Bottom Navigation and Logout Styles */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            z-index: 1000;
        }

        .logout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: right;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: #8B0000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #660000;
            transform: translateY(-2px);
        }

        .logout-btn i {
            font-size: 1.2em;
        }

        /* Adjust main content to prevent overlap with bottom nav */
        body {
            padding-bottom: 70px; /* Adjust this value based on your bottom nav height */
        }

        /* Make sure modal appears above bottom nav */
        .product-modal {
            z-index: 1001;
        }

        .navigation {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #8B0000;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="img/logo3.png" alt="Logo" />
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="library.php">Library</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="#">About</a></li>
                
                <!-- Add Cart Icon -->
                <li class="cart-icon">
                    <a href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </li>

                <!-- User Profile Section -->
                <div class="user-menu">
                    <?php if (!empty($profile_picture)): ?>
                        <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture" class="user-icon">
                    <?php else: ?>
                        <div class="user-icon">
                            <?php echo strtoupper($username[0]); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-content">
                        <p><?php echo $username; ?></p>
                        <a href="userpanel.php">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </ul>
        </nav>
    </header>

    <main class="shop-container">
        <div class="shop-layout">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="search-container">
                    <form action="" method="GET">
                        <input type="text" name="search" placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <h2>Categories</h2>
                <ul class="category-list">
                    <li>
                        <a href="?<?php echo $search ? 'search=' . urlencode($search) : ''; ?>" 
                           <?php echo $category_filter === 0 ? 'class="active"' : ''; ?>>
                            All Categories
                        </a>
                    </li>
                    <?php while($category = mysqli_fetch_assoc($categories_result)) { ?>
                        <li>
                            <a href="?category=<?php echo $category['categories_id']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                               <?php echo $category_filter === (int)$category['categories_id'] ? 'class="active"' : ''; ?>>
                                <?php echo htmlspecialchars($category['categories_name']); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <h1>Our Products</h1>
                <?php if (mysqli_num_rows($products) > 0) { ?>
                    <?php 
                    $current_category = '';
                    while($product = mysqli_fetch_assoc($products)) {
                        // If we're showing all categories and we've reached a new category
                        if ($category_filter === 0 && $current_category !== $product['categories_name']) {
                            if ($current_category !== '') {
                                echo '</div>'; // Close previous products-grid
                            }
                            $current_category = $product['categories_name'];
                            ?>
                            <div class="category-divider">
                                <h2><?php echo htmlspecialchars($current_category); ?></h2>
                            </div>
                            <div class="products-grid">
                        <?php
                        }
                        // If we're filtering by category and this is the first product
                        else if ($category_filter !== 0 && $current_category === '') {
                            $current_category = $product['categories_name'];
                            ?>
                            <div class="products-grid">
                        <?php
                        }
                        ?>
                        <div class="product-card" onclick="openProductModal(<?php echo $product['product_id']; ?>)">
                            <?php
                            switch($product['product_name']) {
                                case 'Kapeng Barako':
                                    $image_path = "assets/images/products/kapeng-barako.jpg";
                                    break;
                                case 'Dried Fish':
                                    $image_path = "assets/images/products/dried-fish.jpg";
                                    break;
                                case 'Lambanog':
                                    $image_path = "assets/images/products/lambanog.jpg";
                                    break;
                                default:
                                    $image_path = "assets/images/products/default.jpg";
                                    break;
                            }
                            ?>
                            <img src="<?php echo $image_path; ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 onerror="this.src='assets/images/products/default.jpg'">
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="brand"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                                <p class="category"><?php echo htmlspecialchars($product['categories_name']); ?></p>
                                <?php
                                $usd_price = $product['rate'];
                                $php_price = $usd_price * 56;
                                ?>
                                <p class="price">
                                    <span class="original-price">$<?php echo number_format($usd_price, 2); ?></span>
                                    <span class="peso-price">₱<?php echo number_format($php_price, 2); ?></span>
                                </p>
                                <p class="stock">Stock: <?php echo $product['quantity']; ?></p>
                                <div class="button-group">
                                    <?php if($product['quantity'] > 0) { ?>
                                        <button class="buy-now" onclick="buyNow(<?php echo $product['product_id']; ?>)">
                                            Buy Now
                                        </button>
                                        <button class="add-to-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                            Add to Cart
                                        </button>
                                    <?php } else { ?>
                                        <button class="out-of-stock" disabled>Out of Stock</button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div> <!-- Close the last products-grid -->
                <?php } else { ?>
                    <p>No products found.</p>
                <?php } ?>
            </div>
        </div>
    </main>

    <?php
    $user_id = $_SESSION['user_id'];
    $cart_total_query = "SELECT SUM(c.quantity * p.rate) as total 
                         FROM cart_items c 
                         JOIN product p ON c.product_id = p.product_id 
                         WHERE c.user_id = ?";
    $stmt = $conn->prepare($cart_total_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_total = $result->fetch_assoc()['total'] ?? 0;
    ?>

    <?php if ($cart_total > 0): ?>
    <div class="cart-total">
        <p>Cart Total: ₱<?php echo number_format($cart_total * 56, 2); ?></p>
        
        <?php if ($default_address): ?>
            <div class="shipping-address">
                <h4>Shipping to:</h4>
                <p><?php echo htmlspecialchars($default_address['address_line1']); ?></p>
                <p><?php echo htmlspecialchars($default_address['city']) . ', ' . htmlspecialchars($default_address['state']); ?></p>
                <p><?php echo htmlspecialchars($default_address['postal_code']); ?></p>
                <a href="manage_addresses.php" class="change-address">Change Address</a>
            </div>
        <?php else: ?>
            <a href="manage_addresses.php" class="add-address-btn">Add Shipping Address</a>
        <?php endif; ?>
        
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
    </div>
    <?php endif; ?>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Explore Batangas. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a> |
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

    <script>
    function updateCartDisplay() {
        // Update cart count
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                // Update all cart count elements
                document.querySelectorAll('.cart-count').forEach(element => {
                    element.textContent = data.count;
                });
                
                // Update cart total
                return fetch('get_cart_total.php');
            })
            .then(response => response.json())
            .then(data => {
                const cartTotalDiv = document.querySelector('.cart-total');
                if (data.total > 0) {
                    if (!cartTotalDiv) {
                        const newCartTotal = document.createElement('div');
                        newCartTotal.className = 'cart-total';
                        newCartTotal.innerHTML = `
                            <p>Cart Total: ${data.formatted_total}</p>
                            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                        `;
                        document.body.appendChild(newCartTotal);
                    } else {
                        cartTotalDiv.querySelector('p').textContent = `Cart Total: ${data.formatted_total}`;
                    }
                } else if (cartTotalDiv) {
                    cartTotalDiv.remove();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function addToCart(productId) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                updateCartDisplay();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding to cart');
        });
    }

    function buyNow(productId) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'checkout.php';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request');
        });
    }

    // Update cart display every 5 seconds
    setInterval(updateCartDisplay, 5000);

    // Initial update when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateCartDisplay();
    });

    function checkNotifications() {
        fetch('get_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        if (!notification.is_read) {
                            showNotification(notification);
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function showNotification(notification) {
        // Create notification element
        const notifElement = document.createElement('div');
        notifElement.className = 'notification-toast';
        notifElement.innerHTML = `
            <h4>${notification.title}</h4>
            <p>${notification.message}</p>
        `;

        // Add to document
        document.body.appendChild(notifElement);

        // Remove after 5 seconds
        setTimeout(() => {
            notifElement.remove();
        }, 5000);

        // Mark as read
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notification_id=${notification.notification_id}`
        });
    }

    // Check for notifications every 30 seconds
    setInterval(checkNotifications, 30000);

    // Initial check when page loads
    document.addEventListener('DOMContentLoaded', checkNotifications);

    function openProductModal(productId) {
        currentProductId = productId; // Make sure this is set
        console.log('Opening modal for product:', currentProductId); // Debug line
        const modal = document.getElementById('productModal');
        modal.style.display = 'block';
        
        fetch(`get_product_details.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update basic product info
                    document.getElementById('modalTitle').textContent = data.product_name;
                    document.getElementById('modalImage').src = data.image_path;
                    document.getElementById('modalBrand').textContent = `Brand: ${data.brand_name}`;
                    document.getElementById('modalCategory').textContent = `Category: ${data.categories_name}`;
                    
                    // Calculate and display PHP price
                    const usdPrice = parseFloat(data.rate);
                    const phpPrice = usdPrice * 56;
                    document.getElementById('modalPrice').innerHTML = `₱${phpPrice.toFixed(2)}`;
                    
                    document.getElementById('modalStock').textContent = `Stock: ${data.quantity}`;
                    document.getElementById('modalDescription').textContent = data.description;
                    
                    // Display reviews and feedback
                    displayReviewsAndFeedback(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading product details');
            });
    }

    function displayReviewsAndFeedback(data) {
        // Display Reviews
        const reviewsList = document.getElementById('reviewsList');
        if (data.reviews && data.reviews.length > 0) {
            const reviewsHTML = data.reviews.map(review => `
                <div class="review-item">
                    <div class="review-header">
                        <div class="user-info">
                            <span class="username">${review.username}</span>
                        </div>
                        <div class="review-meta">
                            <div class="rating">
                                ${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}
                            </div>
                            <div class="date">${review.review_date}</div>
                        </div>
                    </div>
                    <div class="review-content">
                        <p>${review.review_text}</p>
                    </div>
                    ${review.admin_response ? `
                        <div class="admin-response">
                            <strong>Admin Response:</strong>
                            <p>${review.admin_response}</p>
                        </div>
                    ` : ''}
                </div>
            `).join('');
            
            reviewsList.innerHTML = reviewsHTML;
        } else {
            reviewsList.innerHTML = '<p class="no-reviews">No reviews yet. Be the first to review this product!</p>';
        }

        // Display Feedback
        const feedbackList = document.getElementById('feedbackList');
        if (data.feedback && data.feedback.length > 0) {
            const feedbackHTML = data.feedback.map(fb => `
                <div class="feedback-item">
                    <div class="feedback-header">
                        <div class="user-info">
                            <span class="username">${fb.username}</span>
                        </div>
                        <div class="feedback-date">${fb.feedback_date}</div>
                    </div>
                    <div class="feedback-content">
                        <p>${fb.feedback_text}</p>
                    </div>
                    <div class="helpful-count">
                        <i class="fas fa-thumbs-up"></i>
                        ${fb.helpful_count} ${fb.helpful_count === 1 ? 'person found' : 'people found'} this helpful
                    </div>
                </div>
            `).join('');
            
            feedbackList.innerHTML = feedbackHTML;
        } else {
            feedbackList.innerHTML = '<p class="no-feedback">No feedback yet. Be the first to share your thoughts!</p>';
        }
    }

    function submitReview(event) {
        event.preventDefault();
        
        if (!currentProductId) {
            alert('Error: Product ID not found');
            return;
        }
        
        const form = event.target;
        const rating = form.querySelector('input[name="rating"]:checked');
        const reviewText = form.querySelector('textarea[name="review_text"]');
        
        if (!rating) {
            alert('Please select a rating');
            return;
        }
        
        if (!reviewText.value.trim()) {
            alert('Please write a review');
            return;
        }
        
        const formData = new FormData();
        formData.append('product_id', currentProductId);
        formData.append('rating', rating.value);
        formData.append('review_text', reviewText.value.trim());
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
        
        fetch('submit_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const reviewsList = document.getElementById('reviewsList');
                const newReviewHTML = `
                    <div class="review-item pending" ${data.review.review_id ? `data-review-id="${data.review.review_id}"` : ''}>
                        <div class="review-header">
                            <div class="user-info">
                                <span class="username">${data.review.username}</span>
                            </div>
                            <div class="review-meta">
                                <div class="rating">
                                    ${'★'.repeat(data.review.rating)}${'☆'.repeat(5-data.review.rating)}
                                </div>
                                <div class="date">Just now (Pending Approval)</div>
                            </div>
                        </div>
                        <div class="review-content">
                            <p>${data.review.review_text}</p>
                        </div>
                    </div>
                `;
                
                if (data.review.is_update) {
                    // Try to find and update existing review
                    const existingReview = reviewsList.querySelector(`[data-review-id="${data.review.review_id}"]`);
                    if (existingReview) {
                        existingReview.outerHTML = newReviewHTML;
                    } else {
                        reviewsList.insertAdjacentHTML('afterbegin', newReviewHTML);
                    }
                } else {
                    if (reviewsList.querySelector('.no-reviews')) {
                        reviewsList.innerHTML = newReviewHTML;
                    } else {
                        reviewsList.insertAdjacentHTML('afterbegin', newReviewHTML);
                    }
                }
                
                // Reset form
                form.reset();
                alert('Review submitted successfully! It will be visible after approval.');
            } else {
                alert(data.message || 'Error submitting review');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting review. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        });
    }

    function submitFeedback(event) {
        event.preventDefault();
        
        if (!currentProductId) {
            console.error('No product ID found');
            alert('Error: Product ID not found');
            return;
        }
        
        const formData = new FormData(event.target);
        formData.append('product_id', currentProductId);
        
        console.log('Submitting feedback for product:', currentProductId); // Debug line
        
        fetch('submit_feedback.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Feedback submission response:', data); // Debug line
            if (data.success) {
                alert(data.message);
                event.target.reset();
                
                // Add the new feedback to the display
                const feedbackList = document.getElementById('feedbackList');
                const newFeedbackHTML = `
                    <div class="feedback-item">
                        <div class="feedback-header">
                            <div class="user-info">
                                <span class="username">${data.feedback.username}</span>
                            </div>
                            <div class="feedback-date">Just now</div>
                        </div>
                        <div class="feedback-content">
                            <p>${data.feedback.feedback_text}</p>
                        </div>
                        <div class="helpful-count">
                            <i class="fas fa-thumbs-up"></i>
                            0 people found this helpful
                        </div>
                    </div>
                `;
                
                if (feedbackList.querySelector('.no-feedback')) {
                    feedbackList.innerHTML = newFeedbackHTML;
                } else {
                    feedbackList.insertAdjacentHTML('afterbegin', newFeedbackHTML);
                }
            } else {
                alert(data.message || 'Error submitting feedback');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting feedback');
        });
    }

    // Add this function at the top level of your JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Initial tab setup
        showTab('description');
        
        // Add click handlers to all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                showTab(tabName);
            });
        });
    });

    function showTab(tabName) {
        // Hide all tab panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.style.display = 'none';
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // Show the selected tab panel
        const selectedPanel = document.getElementById(tabName);
        if (selectedPanel) {
            selectedPanel.style.display = 'block';
        }
        
        // Add active class to the clicked button
        const selectedButton = document.querySelector(`.tab-button[data-tab="${tabName}"]`);
        if (selectedButton) {
            selectedButton.classList.add('active');
        }
    }

    // Add these functions for modal handling
    function closeModal() {
        const modal = document.getElementById('productModal');
        modal.style.display = 'none';
        
        // Reset forms when closing
        document.getElementById('reviewForm')?.reset();
        document.getElementById('feedbackForm')?.reset();
        
        // Clear the current product ID
        currentProductId = null;
    }

    // Close modal when clicking the X button or outside the modal
    document.addEventListener('DOMContentLoaded', function() {
        // Close button handler
        const closeButton = document.querySelector('.modal-close');
        if (closeButton) {
            closeButton.addEventListener('click', closeModal);
        }
        
        // Click outside modal handler
        const modal = document.getElementById('productModal');
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        
        // Optional: Add escape key handler
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    });
    </script>

    <!-- Product Modal -->
    <div id="productModal" class="product-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle"></h2>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="product-details">
                    <img id="modalImage" src="" alt="">
                    <div class="product-info">
                        <p id="modalBrand"></p>
                        <p id="modalCategory"></p>
                        <p id="modalPrice"></p>
                        <p id="modalStock"></p>
                    </div>
                </div>
                <div class="modal-tabs">
                    <div class="tab-buttons">
                        <button class="tab-button active" data-tab="description">Description</button>
                        <button class="tab-button" data-tab="reviews">Reviews</button>
                        <button class="tab-button" data-tab="feedback">Feedback</button>
                    </div>
                    <div class="tab-content">
                        <div id="description" class="tab-panel active">
                            <p id="modalDescription"></p>
                        </div>
                        <div id="reviews" class="tab-panel">
                            <div class="review-form">
                                <h3>Write a Review</h3>
                                <form id="reviewForm" onsubmit="submitReview(event)">
                                    <div class="rating-select">
                                        <span>Your Rating:</span>
                                        <div class="star-rating">
                                            <input type="radio" id="star5" name="rating" value="5" required>
                                            <label for="star5">★</label>
                                            <input type="radio" id="star4" name="rating" value="4">
                                            <label for="star4">★</label>
                                            <input type="radio" id="star3" name="rating" value="3">
                                            <label for="star3">★</label>
                                            <input type="radio" id="star2" name="rating" value="2">
                                            <label for="star2">★</label>
                                            <input type="radio" id="star1" name="rating" value="1">
                                            <label for="star1">★</label>
                                        </div>
                                    </div>
                                    <textarea name="review_text" placeholder="Write your review here..." required></textarea>
                                    <button type="submit" class="submit-btn">Submit Review</button>
                                </form>
                            </div>
                            <div id="reviewsList"></div>
                        </div>
                        <div id="feedback" class="tab-panel">
                            <div class="feedback-form">
                                <h3>Share Your Feedback</h3>
                                <form id="feedbackForm" onsubmit="submitFeedback(event)">
                                    <textarea name="feedback_text" placeholder="Write your feedback here..." required></textarea>
                                    <button type="submit" class="submit-btn">Submit Feedback</button>
                                </form>
                            </div>
                            <div id="feedbackList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this at the bottom of your page, before the closing </body> tag -->
   
</body>
</html>