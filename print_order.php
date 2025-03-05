<?php
session_start();
include 'connection.php';

if (!isset($_GET['order_id'])) {
    die('Order ID not provided');
}

$order_id = $_GET['order_id'];

// Fetch order details
$query = "SELECT o.*, u.username as ordered_by,
          GROUP_CONCAT(
            CONCAT(oi.quantity, ' x ', 
            CONVERT(p.product_name USING utf8), ' (₱', 
            oi.rate, ')')
            COLLATE utf8_general_ci 
            SEPARATOR '\n'
          ) as order_items
          FROM orders o
          LEFT JOIN users u ON o.user_id = u.user_id
          LEFT JOIN order_item oi ON o.order_id = oi.order_id
          LEFT JOIN product p ON oi.product_id = p.product_id
          WHERE o.order_id = ?
          GROUP BY o.order_id";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die('Order not found');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt #<?php echo $order_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 14px;
        }
        .receipt {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-info {
            margin-bottom: 20px;
        }
        .items {
            margin-bottom: 20px;
            white-space: pre-line;
        }
        .total {
            text-align: right;
            font-weight: bold;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>Order Receipt</h2>
            <p>Order #<?php echo $order_id; ?></p>
        </div>
        
        <div class="order-info">
            <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Client:</strong> <?php echo $order['client_name']; ?></p>
            <p><strong>Contact:</strong> <?php echo $order['client_contact']; ?></p>
            <p><strong>Processed by:</strong> <?php echo $order['ordered_by']; ?></p>
        </div>

        <div class="items">
            <strong>Order Items:</strong><br>
            <?php echo $order['order_items']; ?>
        </div>

        <div class="total">
            <p>Total Amount: ₱<?php echo number_format($order['grand_total'], 2); ?></p>
            <p>Status: <?php echo $order['payment_status'] == 1 ? 'Paid' : 'Unpaid'; ?></p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>

    <script>
        // Automatically open print dialog when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html> 