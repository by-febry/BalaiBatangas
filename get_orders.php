<?php
session_start();
include 'connection.php';

// Search condition
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = '';
if ($search !== '') {
    $search_condition = " WHERE 
        p.product_name LIKE '%$search%' OR 
        o.client_name LIKE '%$search%' OR 
        o.client_contact LIKE '%$search%' OR 
        DATE_FORMAT(o.order_date, '%Y-%m-%d') LIKE '%$search%' OR
        DATE_FORMAT(o.order_date, '%b %d, %Y') LIKE '%$search%' OR
        DATE_FORMAT(o.order_date, '%M %d, %Y') LIKE '%$search%' OR
        DATE_FORMAT(o.order_date, '%m/%d/%Y') LIKE '%$search%'";
}

// Pagination setup
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total orders with search
$total_query = "SELECT COUNT(DISTINCT o.order_id) as total 
                FROM orders o 
                LEFT JOIN order_item oi ON o.order_id = oi.order_id 
                LEFT JOIN product p ON oi.product_id = p.product_id" 
                . $search_condition;
$total_result = $conn->query($total_query);
$total_orders = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $items_per_page);

// Get orders for current page with search
$orderQuery = "SELECT o.order_id, o.client_name, o.client_contact, o.grand_total, 
               o.order_date, o.payment_status, u.username as ordered_by,
               GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.product_name) SEPARATOR ', ') as order_items,
               GROUP_CONCAT(p.product_name SEPARATOR ', ') as products
               FROM orders o
               LEFT JOIN users u ON o.user_id = u.user_id
               LEFT JOIN order_item oi ON o.order_id = oi.order_id
               LEFT JOIN product p ON oi.product_id = p.product_id
               $search_condition
               GROUP BY o.order_id
               ORDER BY o.order_date DESC
               LIMIT $items_per_page OFFSET $offset";
$orderResult = $conn->query($orderQuery);
?>

<h1>Order Management</h1>
<div class="search-container">
    <input type="text" id="orderSearch" 
           placeholder="Search by product, client, contact, or date (e.g., Nov 14, 2024, 2024-03-21)" 
           value="<?php echo htmlspecialchars($search); ?>">
    <button onclick="searchOrders()" class="action-btn">Search</button>
    <button onclick="loadContent('orders')" class="action-btn" style="background-color: #4CAF50;">
        <i class="fas fa-sync-alt"></i> Refresh
    </button>
    <button onclick="deleteSelectedOrders()" class="action-btn delete-btn" style="float: right;">
        <i class="fas fa-trash"></i> Delete Selected
    </button>
</div>
<table>
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAllOrders" onclick="toggleAllOrders()"></th>
            <th>Client Name</th>
            <th>Contact</th>
            <th>Products</th>
            <th>Total Amount</th>
            <th>Order Date</th>
            <th>Payment Status</th>
            <th>Ordered By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="orderTable">
        <?php while ($orderRow = $orderResult->fetch_assoc()): ?>
        <tr class="clickable" onclick="toggleOrderDetails(this, '<?php echo htmlspecialchars($orderRow['order_items'], ENT_QUOTES); ?>')">
            <td onclick="event.stopPropagation()">
                <input type="checkbox" class="order-checkbox" value="<?php echo $orderRow['order_id']; ?>">
            </td>
            <td><?php echo $orderRow['client_name']; ?></td>
            <td><?php echo $orderRow['client_contact']; ?></td>
            <td><?php echo $orderRow['products']; ?></td>
            <td>₱<?php echo number_format($orderRow['grand_total'], 2); ?></td>
            <td><?php echo date('M d, Y', strtotime($orderRow['order_date'])); ?></td>
            <td><?php echo $orderRow['payment_status'] == 1 ? 'Paid' : 'Unpaid'; ?></td>
            <td><?php echo $orderRow['ordered_by']; ?></td>
            <td onclick="event.stopPropagation()">
                <button onclick="printOrder(<?php echo $orderRow['order_id']; ?>)" class="action-btn print-btn">
                    <i class="fas fa-print"></i>
                </button>
                <button onclick="deleteOrder(<?php echo $orderRow['order_id']; ?>)" class="action-btn delete-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td colspan="9" class="order-details" id="details-<?php echo $orderRow['order_id']; ?>">
                <strong>Order Items:</strong> <?php echo $orderRow['order_items']; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination">
    <?php if($page > 1): ?>
        <button onclick="loadContent('orders', <?php echo ($page-1); ?>)" class="page-btn">
            <i class="fas fa-chevron-left"></i> Previous
        </button>
    <?php endif; ?>

    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <button onclick="loadContent('orders', <?php echo $i; ?>)" 
                class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </button>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <button onclick="loadContent('orders', <?php echo ($page+1); ?>)" class="page-btn">
            Next <i class="fas fa-chevron-right"></i>
        </button>
    <?php endif; ?>
</div> 