<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_query = "SELECT c.*, p.product_name, p.rate FROM cart_items c 
               JOIN product p ON c.product_id = p.product_id 
               WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];
$total = 0;

while ($item = $result->fetch_assoc()) {
    $item['subtotal'] = $item['quantity'] * ($item['rate'] * 56); // Convert to PHP
    $total += $item['subtotal'];
    $cart_items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Explore Batangas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .checkout-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .cart-summary, .payment-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 300px;
        }

        h3 {
            color: #444;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #8B0000;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #8B0000;
        }

        .item-details h4 {
            color: #333;
            margin-bottom: 8px;
        }

        .item-details p {
            color: #666;
            margin: 5px 0;
            font-size: 0.9em;
        }

        .item-total {
            font-weight: bold;
            color: #8B0000;
            font-size: 1.1em;
        }

        .total-amount {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #8B0000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-amount h3 {
            color: #8B0000;
            border-bottom: none;
        }

        .payment-instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .payment-info {
            margin: 15px 0;
            padding: 15px;
            background: #fff;
            border-left: 4px solid #8B0000;
            border-radius: 4px;
        }

        .payment-info p {
            margin: 10px 0;
            color: #555;
        }

        .qr-section {
            text-align: center;
            margin: 20px 0;
        }

        .qr-code {
            max-width: 200px;
            margin: 15px auto;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .qr-code img {
            width: 100%;
            height: auto;
            display: block;
        }

        .payment-methods {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
        }

        .payment-method {
            text-align: center;
        }

        .payment-method img {
            height: 40px;
            width: auto;
        }

        .note {
            color: #666;
            font-style: italic;
            margin: 15px 0;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .confirm-btn, .cancel-btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .confirm-btn {
            background-color: #8B0000;
            color: white;
            border: none;
        }

        .confirm-btn:hover {
            background-color: #a01010;
            transform: translateY(-2px);
        }

        .confirm-btn:active {
            transform: translateY(0);
        }

        .cancel-btn {
            background-color: #fff;
            color: #8B0000;
            border: 2px solid #8B0000;
        }

        .cancel-btn:hover {
            background-color: #ffeeee;
        }

        .button-group button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .checkout-container {
                flex-direction: column;
            }

            .cart-summary, .payment-section {
                width: 100%;
            }
        }

        .upload-section {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .upload-btn {
            border: 2px dashed #8B0000;
            color: #8B0000;
            background-color: white;
            padding: 15px 20px;
            border-radius: 8px;
            font-size: 1em;
            width: 100%;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-btn:hover {
            background-color: #ffeeee;
        }

        .upload-btn-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        #preview-image {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 8px;
            display: none;
        }

        .preview-container {
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>
        
        <div class="checkout-container">
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="item-details">
                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Price: ₱<?php echo number_format($item['rate'] * 56, 2); ?></p>
                    </div>
                    <div class="item-total">
                        ₱<?php echo number_format($item['subtotal'], 2); ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="total-amount">
                    <h3>Total Amount</h3>
                    <h3>₱<?php echo number_format($total, 2); ?></h3>
                </div>
            </div>

            <div class="payment-section">
                <h3>Payment Details</h3>
                <div class="payment-instructions">
                    <p>Please send your payment using GCash:</p>
                    
                    <div class="qr-section">
                        <div class="qr-code">
                            <img src="assets/images/payments/QRcode.jpg" alt="GCash QR Code">
                        </div>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <img src="assets/images/payments/gcash-logo.png" alt="GCash">
                            </div>
                        </div>
                    </div>

                    <div class="payment-info">
                        <p><strong>GCash Number:</strong> 09270533556</p>
                        <p><strong>Account Name:</strong> PAUL NIKKO MISSION</p>
                    </div>
                    
                    <p class="note">After sending payment, click the button below to confirm your order.</p>
                </div>
                
                <div class="upload-section">
                    <h4>Upload Payment Proof</h4>
                    <p class="note">Please upload a screenshot of your payment</p>
                    
                    <div class="upload-btn-wrapper">
                        <button class="upload-btn" id="upload-btn-text">Click or drag to upload payment proof</button>
                        <input type="file" id="payment-proof" accept="image/*" onchange="previewImage(this)"/>
                    </div>
                    
                    <div class="preview-container">
                        <img id="preview-image" src="#" alt="Preview"/>
                    </div>
                </div>
                
                <div class="button-group">
                    <button onclick="cancelOrder()" class="cancel-btn" id="cancelBtn">Cancel Order</button>
                    <button onclick="submitOrder()" class="confirm-btn" id="submitBtn">Submit Order</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('preview-image');
        const uploadBtn = document.getElementById('upload-btn-text');
        const submitBtn = document.getElementById('submitBtn');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                uploadBtn.textContent = 'Change payment proof';
                submitBtn.disabled = false; // Enable submit button when image is uploaded
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function cancelOrder() {
        const cancelBtn = document.getElementById('cancelBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (confirm('Are you sure you want to cancel your order?')) {
            cancelBtn.disabled = true;
            submitBtn.disabled = true;
            window.location.href = 'shop.php';
        }
    }

    function submitOrder() {
        const fileInput = document.getElementById('payment-proof');
        const submitBtn = document.getElementById('submitBtn');
        const cancelBtn = document.getElementById('cancelBtn');

        if (!fileInput.files || !fileInput.files[0]) {
            alert('Please upload your payment proof first!');
            return;
        }

        if (confirm('Are you sure you want to submit your order?')) {
            // Disable buttons during submission
            submitBtn.disabled = true;
            cancelBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            const formData = new FormData();
            formData.append('payment_proof', fileInput.files[0]);
            formData.append('cart_items', JSON.stringify(<?php echo json_encode($cart_items); ?>));
            formData.append('total', <?php echo $total; ?>);
            formData.append('order_status', '0'); // 0 means pending

            // Send to process_order.php
            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order submitted successfully! Your order is now pending for approval.');
                    window.location.href = 'userpanel.php';
                } else {
                    alert('Error: ' + data.message);
                    // Re-enable buttons on error
                    submitBtn.disabled = false;
                    cancelBtn.disabled = false;
                    submitBtn.textContent = 'Submit Order';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting order. Please try again.');
                // Re-enable buttons on error
                submitBtn.disabled = false;
                cancelBtn.disabled = false;
                submitBtn.textContent = 'Submit Order';
            });
        }
    }

    // Disable submit button initially until proof is uploaded
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('submitBtn').disabled = true;
    });
    </script>
</body>
</html> 