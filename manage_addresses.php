<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address_line1 = $_POST['address_line1'];
    $address_line2 = $_POST['address_line2'] ?? null;
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $phone = $_POST['phone'];
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    // If setting as default, unset other default addresses
    if ($is_default) {
        $update_stmt = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();
    }

    // Insert new address
    $stmt = $conn->prepare("INSERT INTO user_addresses (user_id, address_line1, address_line2, city, state, postal_code, phone, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssi", $user_id, $address_line1, $address_line2, $city, $state, $postal_code, $phone, $is_default);
    
    if ($stmt->execute()) {
        header("Location: shop.php");
        exit();
    }
}

// Fetch existing addresses
$addresses_query = "SELECT * FROM user_addresses WHERE user_id = ?";
$stmt = $conn->prepare($addresses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$addresses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Addresses - Explore Batangas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: rgb(244, 244, 244);
            color: rgb(51, 51, 51);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            color: #8B0000;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .address-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .address-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .address-form h2 {
            color: #8B0000;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #8B0000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: border-color 0.3s ease;
            font-size: 14px;
        }

        .form-group input:focus {
            border-color: #8B0000;
            outline: none;
            box-shadow: 0 0 5px rgba(139, 0, 0, 0.2);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .submit-btn {
            background-color: #8B0000;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #660000;
        }

        .addresses-list {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .addresses-list h2 {
            color: #8B0000;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #8B0000;
        }

        .address-card {
            background: #f8f8f8;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
            border-left: 4px solid #8B0000;
            transition: transform 0.2s ease;
        }

        .address-card:hover {
            transform: translateX(5px);
        }

        .address-card p {
            margin: 5px 0;
            color: #444;
        }

        .default-badge {
            display: inline-block;
            background: #8B0000;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 10px;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #8B0000;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background: #660000;
        }

        @media (max-width: 768px) {
            .address-layout {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="shop.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Shop
        </a>
        
        <h1 class="page-title">Manage Shipping Addresses</h1>
        
        <div class="address-layout">
            <div class="address-form">
                <h2><i class="fas fa-plus-circle"></i> Add New Address</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="address_line1">Address Line 1</label>
                        <input type="text" id="address_line1" name="address_line1" required 
                               placeholder="Street address or P.O. Box">
                    </div>

                    <div class="form-group">
                        <label for="address_line2">Address Line 2 (Optional)</label>
                        <input type="text" id="address_line2" name="address_line2" 
                               placeholder="Apartment, suite, unit, building, floor, etc.">
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required 
                               placeholder="City name">
                    </div>

                    <div class="form-group">
                        <label for="state">State/Province</label>
                        <input type="text" id="state" name="state" required 
                               placeholder="State or province">
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" required 
                               placeholder="Postal code">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required 
                               placeholder="Contact number">
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="is_default" name="is_default">
                        <label for="is_default">Set as default shipping address</label>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Save Address
                    </button>
                </form>
            </div>

            <div class="addresses-list">
                <h2><i class="fas fa-map-marker-alt"></i> Your Addresses</h2>
                <?php if ($addresses->num_rows > 0): ?>
                    <?php while ($address = $addresses->fetch_assoc()): ?>
                        <div class="address-card">
                            <p><strong><?php echo htmlspecialchars($address['address_line1']); ?></strong></p>
                            <?php if ($address['address_line2']): ?>
                                <p><?php echo htmlspecialchars($address['address_line2']); ?></p>
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['state']); ?></p>
                            <p><?php echo htmlspecialchars($address['postal_code']); ?></p>
                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($address['phone']); ?></p>
                            <?php if ($address['is_default']): ?>
                                <span class="default-badge">
                                    <i class="fas fa-check"></i> Default Address
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No addresses saved yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 