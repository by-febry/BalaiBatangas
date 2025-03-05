<?php
session_start();
include 'connection.php';

// Add this function at the top of the file, after the includes
function convertToPHP($usd) {
    $rate = 56; // Current USD to PHP rate (you can update this)
    return $usd * $rate;
}

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Unauthorized access');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'get_content':
            $page = $_POST['page'];
            switch($page) {
                case 'dashboard':
                    $timeframe = isset($_POST['timeframe']) ? $_POST['timeframe'] : 'month';
                    
                    // Set date range based on timeframe
                    switch($timeframe) {
                        case 'today':
                            $date_condition = "DATE(order_date) = CURDATE()";
                            break;
                        case 'week':
                            $date_condition = "YEARWEEK(order_date) = YEARWEEK(CURDATE())";
                            break;
                        case 'month':
                            $date_condition = "YEAR(order_date) = YEAR(CURDATE()) AND MONTH(order_date) = MONTH(CURDATE())";
                            break;
                        case 'year':
                            $date_condition = "YEAR(order_date) = YEAR(CURDATE())";
                            break;
                        default:
                            $date_condition = "1=1"; // Show all
                    }

                    // Get total users (not affected by timeframe)
                    $userQuery = "SELECT COUNT(*) as total_users FROM users";
                    $userResult = $conn->query($userQuery);
                    $totalUsers = $userResult->fetch_assoc()['total_users'];

                    // Get total festivals (not affected by timeframe)
                    $festivalQuery = "SELECT COUNT(*) as total_festivals FROM library";
                    $festivalResult = $conn->query($festivalQuery);
                    $totalFestivals = $festivalResult->fetch_assoc()['total_festivals'];

                    // Get total revenue (all time)
                    $revenueQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue FROM orders";
                    $revenueResult = $conn->query($revenueQuery);
                    $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'];
                    $totalRevenuePHP = convertToPHP($totalRevenue);

                    // Get total sales for selected timeframe
                    $salesQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_sales 
                                   FROM orders 
                                   WHERE $date_condition";
                    $salesResult = $conn->query($salesQuery);
                    $totalSales = $salesResult->fetch_assoc()['total_sales'];
                    $totalSalesPHP = convertToPHP($totalSales);

                    // Get total orders with timeframe
                    $ordersQuery = "SELECT COUNT(*) as total_orders 
                                    FROM orders 
                                    WHERE $date_condition";
                    $ordersResult = $conn->query($ordersQuery);
                    $totalOrders = $ordersResult->fetch_assoc()['total_orders'];

                    // Get recent orders with timeframe
                    $recentOrdersQuery = "SELECT o.*, u.username 
                                         FROM orders o 
                                         JOIN users u ON o.user_id = u.user_id 
                                         WHERE $date_condition
                                         ORDER BY o.order_date DESC 
                                         LIMIT 5";
                    $recentOrders = $conn->query($recentOrdersQuery);

                    // Get top selling products with timeframe
                    $topProductsQuery = "SELECT p.product_name, 
                                           COUNT(oi.product_id) as total_sold,
                                           SUM(oi.quantity * oi.rate) as revenue
                                        FROM order_item oi 
                                        JOIN product p ON oi.product_id = p.product_id 
                                        JOIN orders o ON oi.order_id = o.order_id
                                        WHERE $date_condition
                                        GROUP BY oi.product_id 
                                        ORDER BY total_sold DESC 
                                        LIMIT 5";
                    $topProducts = $conn->query($topProductsQuery);

                    echo '<div class="page-header">
                            <h2>Dashboard</h2>
                            <div class="date-filter">
                                <select onchange="filterDashboard(this.value)">
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month" selected>This Month</option>
                                    <option value="year">This Year</option>
                                </select>
                            </div>
                          </div>
                          
                          <div class="dashboard-stats">
                              <div class="stat-card">
                                  <div class="stat-icon"><i class="fas fa-coins"></i></div>
                                  <div class="stat-details">
                                      <h3>Total Revenue</h3>
                                      <p>₱ '.number_format($totalRevenuePHP, 2).'</p>
                                  </div>
                              </div>
                              
                              <div class="stat-card">
                                  <div class="stat-icon"><i class="fas fa-peso-sign"></i></div>
                                  <div class="stat-details">
                                      <h3>Period Sales</h3>
                                      <p>₱ '.number_format($totalSalesPHP, 2).'</p>
                                  </div>
                              </div>
                              
                              <div class="stat-card">
                                  <div class="stat-icon"><i class="fas fa-users"></i></div>
                                  <div class="stat-details">
                                      <h3>Total Users</h3>
                                      <p>'.$totalUsers.'</p>
                                  </div>
                              </div>
                              
                              <div class="stat-card">
                                  <div class="stat-icon"><i class="fas fa-calendar-days"></i></div>
                                  <div class="stat-details">
                                      <h3>Total Festivals</h3>
                                      <p>'.$totalFestivals.'</p>
                                  </div>
                              </div>
                          </div>

                          <div class="dashboard-grid">
                              <div class="dashboard-card">
                                  <h3><i class="fas fa-clock"></i> Recent Orders</h3>
                                  <div class="table-container">
                                      <table>
                                          <thead>
                                              <tr>
                                                  <th>Order ID</th>
                                                  <th>Customer</th>
                                                  <th>Amount</th>
                                                  <th>Date</th>
                                              </tr>
                                          </thead>
                                          <tbody>';
                                          while($order = $recentOrders->fetch_assoc()) {
                                              $amountPHP = convertToPHP($order['total_amount']);
                                              echo '<tr>
                                                      <td>#'.$order['order_id'].'</td>
                                                      <td>'.$order['username'].'</td>
                                                      <td>₱ '.number_format($amountPHP, 2).'</td>
                                                      <td>'.date('M d, Y', strtotime($order['order_date'])).'</td>
                                                  </tr>';
                                          }
                                          echo '</tbody>
                                      </table>
                                  </div>
                              </div>

                              <div class="dashboard-card">
                                  <h3><i class="fas fa-chart-bar"></i> Top Selling Products</h3>
                                  <div class="table-container">
                                      <table>
                                          <thead>
                                              <tr>
                                                  <th>Product</th>
                                                  <th>Units Sold</th>
                                                  <th>Revenue</th>
                                              </tr>
                                          </thead>
                                          <tbody>';
                                          while($product = $topProducts->fetch_assoc()) {
                                              $revenuePHP = convertToPHP($product['revenue']);
                                              echo '<tr>
                                                      <td>'.$product['product_name'].'</td>
                                                      <td>'.$product['total_sold'].'</td>
                                                      <td>₱ '.number_format($revenuePHP, 2).'</td>
                                                  </tr>';
                                          }
                                          echo '</tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>';
                    break;

                case 'users':
                    echo '<div class="page-header">
                            <h2>Manage Users</h2>
                            <div class="header-actions">
                                <div class="search-bar">
                                    <input type="text" id="searchUser" placeholder="Search users..." onkeyup="searchUsers()">
                                    <i class="fas fa-search"></i>
                                </div>
                                <button onclick="showAddUserForm()" class="btn">
                                    <i class="fas fa-plus"></i> Add New User
                                </button>
                            </div>
                          </div>
                          <div class="table-container" id="usersTable">';
                    
                    // Get users query
                    $query = "SELECT * FROM users ORDER BY user_id DESC";
                    $result = $conn->query($query);

                    echo '<table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';

                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>'.$row['user_id'].'</td>
                                <td>'.$row['username'].'</td>
                                <td>'.$row['email'].'</td>
                                <td>'.$row['role'].'</td>
                                <td>
                                    <button onclick="editUser('.$row['user_id'].')" class="btn btn-small">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteUser('.$row['user_id'].')" class="btn btn-small btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>';
                    }
                    echo '</tbody></table></div>';
                    break;

                case 'library':
                    echo '<div class="page-header">
                            <h2>Manage Library</h2>
                            <div class="header-actions">
                                <div class="search-bar">
                                    <input type="text" id="searchFestival" placeholder="Search festivals..." onkeyup="searchFestivals()">
                                    <i class="fas fa-search"></i>
                                </div>
                                <button onclick="showAddFestivalForm()" class="btn">
                                    <i class="fas fa-plus"></i> Add New Festival
                                </button>
                            </div>
                          </div>
                          <div class="table-container" id="festivalTable">';
                    
                    $query = "SELECT * FROM library ORDER BY festival_id DESC";
                    $result = $conn->query($query);

                    echo '<table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Date Celebrated</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';

                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>'.$row['festival_id'].'</td>
                                <td>'.$row['festival_name'].'</td>
                                <td>'.$row['location'].'</td>
                                <td>'.date('M d, Y', strtotime($row['date_celebrated'])).'</td>
                                <td><img src="img/'.$row['festival_image'].'" alt="'.$row['festival_name'].'" style="width: 50px; height: 50px; object-fit: cover;"></td>
                                <td class="actions">
                                    <button onclick="editFestival('.$row['festival_id'].')" class="btn btn-small">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteFestival('.$row['festival_id'].')" class="btn btn-small btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>';
                    }
                    echo '</tbody></table></div>';
                    break;

                case 'payments':
                    // Fetch pending payments
                    $query = "
                        SELECT 
                            o.order_id,
                            o.order_date,
                            o.total_amount,
                            u.username,
                            pp.image_path,
                            pp.status
                        FROM orders o
                        JOIN users u ON o.user_id = u.user_id
                        JOIN payment_proofs pp ON o.order_id = pp.order_id
                        WHERE pp.status = 'pending'
                        ORDER BY o.order_date DESC
                    ";
                    
                    $result = $conn->query($query);
                    
                    echo '
                    <div class="section-header">
                        <h2>Payment Verifications</h2>
                    </div>
                    <div class="payments-container">
                    ';
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
                            <div class="payment-card">
                                <div class="payment-header">
                                    <h3>Order #' . $row['order_id'] . '</h3>
                                    <span class="payment-date">' . date('M d, Y', strtotime($row['order_date'])) . '</span>
                                </div>
                                <div class="payment-details">
                                    <p><strong>Customer:</strong> ' . htmlspecialchars($row['username']) . '</p>
                                    <p><strong>Amount:</strong> ₱' . number_format($row['total_amount'], 2) . '</p>
                                    <div class="payment-proof">
                                        <img src="' . htmlspecialchars($row['image_path']) . '" alt="Payment Proof">
                                    </div>
                                    <button onclick="verifyPayment(' . $row['order_id'] . ')" class="btn-verify">
                                        Verify Payment
                                    </button>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p class="no-payments">No pending payments to verify</p>';
                    }
                    echo '</div>';
                    break;
            }
            break;

        case 'get_user':
            $userId = $_POST['userId'];
            $stmt = $conn->prepare("SELECT user_id, username, email, role FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            echo json_encode($user);
            break;

        case 'edit':
            $userId = $_POST['userId'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $username, $email, $role, $userId);
            $result = $stmt->execute();
            
            echo json_encode(array('success' => $result));
            break;

        case 'delete':
            $userId = $_POST['userId'];
            
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $result = $stmt->execute();
            
            echo json_encode(array('success' => $result));
            break;

        case 'add':
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = hash('sha256', $_POST['password']);
            $role = $_POST['role'];
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $role);
            $result = $stmt->execute();
            
            echo json_encode(array('success' => $result));
            break;

        case 'edit_festival':
            $festival_id = $_POST['festival_id'];
            $description = $_POST['description'];
            $location = $_POST['location'];
            $map_coordinates = $_POST['map_coordinates'];
            
            // Handle image upload if a new image is provided
            if (isset($_FILES['festival_image']) && $_FILES['festival_image']['error'] === 0) {
                $allowed = array('jpg', 'jpeg', 'png', 'webp');
                $filename = $_FILES['festival_image']['name'];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($file_ext, $allowed)) {
                    $new_filename = uniqid() . '.' . $file_ext;
                    $upload_path = 'assets/images/festivals/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['festival_image']['tmp_name'], $upload_path)) {
                        // Get old image to delete
                        $stmt = $conn->prepare("SELECT festival_image FROM library WHERE festival_id = ?");
                        $stmt->bind_param("i", $festival_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $old_image = $result->fetch_assoc()['festival_image'];
                        
                        // Delete old image if it exists
                        if ($old_image && file_exists('assets/images/festivals/' . $old_image)) {
                            unlink('assets/images/festivals/' . $old_image);
                        }
                        
                        // Update with new image
                        $stmt = $conn->prepare("UPDATE library SET description = ?, location = ?, map_coordinates = ?, festival_image = ? WHERE festival_id = ?");
                        $stmt->bind_param("ssssi", $description, $location, $map_coordinates, $new_filename, $festival_id);
                    } else {
                        echo json_encode(array('success' => false, 'message' => 'Failed to upload image'));
                        exit;
                    }
                } else {
                    echo json_encode(array('success' => false, 'message' => 'Invalid file type'));
                    exit;
                }
            } else {
                // Update without changing image
                $stmt = $conn->prepare("UPDATE library SET description = ?, location = ?, map_coordinates = ? WHERE festival_id = ?");
                $stmt->bind_param("sssi", $description, $location, $map_coordinates, $festival_id);
            }
            
            $result = $stmt->execute();
            echo json_encode(array('success' => $result));
            break;

        case 'delete_festival':
            $festival_id = $_POST['festival_id'];
            
            // First get the image filename
            $stmt = $conn->prepare("SELECT festival_image FROM library WHERE festival_id = ?");
            $stmt->bind_param("i", $festival_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $festival = $result->fetch_assoc();
            
            // Delete the image file if it exists
            if ($festival && $festival['festival_image'] && file_exists('img/' . $festival['festival_image'])) {
                unlink('img/' . $festival['festival_image']);
            }
            
            // Delete the database record
            $stmt = $conn->prepare("DELETE FROM library WHERE festival_id = ?");
            $stmt->bind_param("i", $festival_id);
            $result = $stmt->execute();
            
            echo json_encode(array('success' => $result));
            break;

        case 'add_festival':
            // Handle file upload
            $festival_name = $_POST['festival_name'];
            $description = $_POST['description'];
            $location = $_POST['location'];
            $map_coordinates = $_POST['map_coordinates'];
            $date_celebrated = $_POST['date_celebrated'];
            
            if(isset($_FILES['festival_image'])) {
                $allowed = array('jpg', 'jpeg', 'png', 'webp');
                $filename = $_FILES['festival_image']['name'];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if(in_array($file_ext, $allowed)) {
                    $new_filename = uniqid() . '.' . $file_ext;
                    $upload_path = 'img/' . $new_filename;
                    
                    if(move_uploaded_file($_FILES['festival_image']['tmp_name'], $upload_path)) {
                        $query = "INSERT INTO library (festival_name, description, location, map_coordinates, date_celebrated, festival_image) 
                                 VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("ssssss", $festival_name, $description, $location, $map_coordinates, $date_celebrated, $new_filename);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                        exit;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                    exit;
                }
            } else {
                $query = "INSERT INTO library (festival_name, description, location, map_coordinates, date_celebrated) 
                         VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $festival_name, $description, $location, $map_coordinates, $date_celebrated);
            }
            
            $success = $stmt->execute();
            echo json_encode(['success' => $success]);
            break;

        case 'edit_library':
            $festival_id = $_POST['festival_id'];
            $festival_name = $_POST['festival_name'];
            $description = $_POST['description'];
            $location = $_POST['location'];
            $map_coordinates = $_POST['map_coordinates'];
            $date_celebrated = $_POST['date_celebrated'];
            
            if (isset($_FILES['festival_image']) && $_FILES['festival_image']['error'] === 0) {
                $allowed = array('jpg', 'jpeg', 'png', 'webp');
                $filename = $_FILES['festival_image']['name'];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($file_ext, $allowed)) {
                    $new_filename = uniqid() . '.' . $file_ext;
                    $upload_path = 'img/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['festival_image']['tmp_name'], $upload_path)) {
                        // Get old image
                        $stmt = $conn->prepare("SELECT festival_image FROM library WHERE festival_id = ?");
                        $stmt->bind_param("i", $festival_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $old_image = $result->fetch_assoc()['festival_image'];
                        
                        // Delete old image
                        if ($old_image && file_exists('img/' . $old_image)) {
                            unlink('img/' . $old_image);
                        }
                        
                        // Update with new image
                        $stmt = $conn->prepare("UPDATE library SET festival_name = ?, description = ?, location = ?, map_coordinates = ?, date_celebrated = ?, festival_image = ? WHERE festival_id = ?");
                        $stmt->bind_param("ssssssi", $festival_name, $description, $location, $map_coordinates, $date_celebrated, $new_filename, $festival_id);
                    } else {
                        echo json_encode(array('success' => false, 'message' => 'Failed to upload image'));
                        exit;
                    }
                } else {
                    echo json_encode(array('success' => false, 'message' => 'Invalid file type'));
                    exit;
                }
            } else {
                // Update without changing image
                $stmt = $conn->prepare("UPDATE library SET festival_name = ?, description = ?, location = ?, map_coordinates = ?, date_celebrated = ? WHERE festival_id = ?");
                $stmt->bind_param("sssssi", $festival_name, $description, $location, $map_coordinates, $date_celebrated, $festival_id);
            }
            
            $result = $stmt->execute();
            echo json_encode(array('success' => $result));
            break;

        case 'add_user':
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];
            
            // Check if username or email already exists
            $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
                exit;
            }
            
            // Insert new user
            $insertQuery = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $username, $email, $password, $role);
            $success = $stmt->execute();
            
            echo json_encode(['success' => $success]);
            break;

        case 'get_festival':
            $festival_id = $_POST['festival_id'];
            $stmt = $conn->prepare("SELECT * FROM library WHERE festival_id = ?");
            $stmt->bind_param("i", $festival_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $festival = $result->fetch_assoc();
            echo json_encode($festival);
            break;

        case 'verify_payment':
            $orderId = $_POST['order_id'];
            
            $conn->begin_transaction();
            try {
                // Update payment_proofs status
                $updatePayment = $conn->prepare("
                    UPDATE payment_proofs 
                    SET status = 'verified' 
                    WHERE order_id = ?
                ");
                $updatePayment->bind_param("i", $orderId);
                $updatePayment->execute();

                // Update orders status
                $updateOrder = $conn->prepare("
                    UPDATE orders 
                    SET order_status = 1 
                    WHERE order_id = ?
                ");
                $updateOrder->bind_param("i", $orderId);
                $updateOrder->execute();

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Error verifying payment: ' . $e->getMessage()]);
            }
            break;
    }
    exit;
} 