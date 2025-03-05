<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #a52a2a;
            --text-color: #333;
            --bg-color: #f5f5f5;
            --card-bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            padding: 20px 0;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar a:hover {
            background-color: var(--secondary-color);
            padding-left: 30px;
        }

        /* Content Styles */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        /* Dashboard Stats */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background-color: rgba(139, 0, 0, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 24px;
            color: var(--primary-color);
        }

        .stat-details h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-details p {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0;
        }

        /* Add animation for numbers */
        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-details p {
            animation: countUp 0.5s ease-out forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .stat-details p {
                font-size: 20px;
            }
        }

        /* Table Styles */
        .table-container {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8f8f8;
        }

        /* Button Styles */
        .btn {
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn i {
            font-size: 16px;
        }

        /* Form Styles */
        form {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        form div {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        form input, form select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Page Headers */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        .page-header h2 {
            color: var(--primary-color);
            font-size: 24px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar a span {
                display: none;
            }

            .content {
                margin-left: 70px;
            }

            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }

        .loading:after {
            content: " ";
            width: 40px;
            height: 40px;
            border: 5px solid var(--primary-color);
            border-radius: 50%;
            border-top: 5px solid #fff;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Search Bar and Header Actions */
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-bar {
            position: relative;
            width: 300px;
        }

        .search-bar input {
            width: 100%;
            padding: 8px 35px 8px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
        }

        .search-bar i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 20px auto;
            padding: 25px;
            width: 90%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .close-modal {
            position: sticky;
            top: 0;
            float: right;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            margin-bottom: 10px;
            background: white;
            padding: 5px;
            z-index: 1;
        }

        .modal-content::-webkit-scrollbar {
            width: 8px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        /* Form Styles */
        textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-height: 100px;
            font-family: inherit;
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }

        /* File Input Styles */
        input[type="file"] {
            padding: 10px 0;
        }

        input[type="file"]::file-selector-button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            margin-right: 10px;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: var(--secondary-color);
        }

        /* Date Input Styles */
        input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .small {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        #imagePreview {
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }

        .date-filter {
            position: relative;
        }

        .date-filter select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
            cursor: pointer;
            appearance: none;
            padding-right: 30px;
        }

        .date-filter::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .date-filter select:hover {
            border-color: var(--primary-color);
        }

        .date-filter select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.1);
        }

        .stat-details p {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.5em;
            font-weight: bold;
            color: var(--primary-color);
        }

        .peso-sign {
            font-family: 'Arial Unicode MS', Arial;
            font-weight: normal;
        }

        /* Payment Verification Styles */
        .payments-container {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .payment-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .payment-proof img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin: 10px 0;
        }

        .btn-verify {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: background 0.3s ease;
        }

        .btn-verify:hover {
            background: #218838;
        }

        .no-payments {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
            <p>Welcome, <?php echo $_SESSION['username']; ?></p>
        </div>
        <a href="#" onclick="loadContent('dashboard')">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="#" onclick="loadContent('payments')">
            <i class="fas fa-money-bill"></i>
            <span>Payment Verifications</span>
        </a>
        <a href="#" onclick="loadContent('users')">
            <i class="fas fa-users"></i>
            <span>Manage Users</span>
        </a>
        <a href="#" onclick="loadContent('library')">
            <i class="fas fa-book"></i>
            <span>Manage Library</span>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>

    <div class="content" id="mainContent">
        <div class="loading"></div>
    </div>

    <script>
        function loadContent(page) {
            document.getElementById('mainContent').innerHTML = '<div class="loading"></div>';
            
            // If loading dashboard, include default timeframe
            const extraParams = page === 'dashboard' ? '&timeframe=month' : '';
            
            fetch('admin_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_content&page=${page}${extraParams}`
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;
                if (page === 'dashboard') {
                    initializeTooltips();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('mainContent').innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        Failed to load content. Please try again.
                    </div>
                `;
            });
        }

        function editUser(userId) {
            const formData = new FormData();
            formData.append('action', 'get_user');
            formData.append('userId', userId);

            fetch('admin_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(user => {
                const form = `
                    <form id="editUserForm">
                        <input type="hidden" name="userId" value="${user.user_id}">
                        <div>
                            <label>Username:</label>
                            <input type="text" name="username" value="${user.username}" required>
                        </div>
                        <div>
                            <label>Email:</label>
                            <input type="email" name="email" value="${user.email}" required>
                        </div>
                        <div>
                            <label>Role:</label>
                            <select name="role">
                                <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn">Save Changes</button>
                    </form>
                `;
                document.getElementById('mainContent').innerHTML = form;

                document.getElementById('editUserForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    formData.append('action', 'edit');

                    fetch('admin_actions.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            alert('User updated successfully');
                            loadContent('users');
                        } else {
                            alert('Error updating user');
                        }
                    });
                });
            });
        }

        function deleteUser(userId) {
            if(confirm('Are you sure you want to delete this user?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('userId', userId);

                fetch('admin_actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert('User deleted successfully');
                        loadContent('users');
                    } else {
                        alert('Error deleting user');
                    }
                });
            }
        }

        function searchUsers() {
            const input = document.getElementById('searchUser');
            const filter = input.value.toUpperCase();
            const table = document.querySelector('#usersTable table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { // Start from 1 to skip header
                const td = tr[i].getElementsByTagName('td');
                let txtValue = '';
                
                // Combine username and email for search
                txtValue = td[1].textContent + ' ' + td[2].textContent;
                
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }

        function showAddUserForm() {
            const form = `
                <div class="modal" id="addUserModal" style="display: block;">
                    <div class="modal-content">
                        <span class="close-modal" onclick="closeModal('addUserModal')">&times;</span>
                        <h2>Add New User</h2>
                        <form id="addUserForm" onsubmit="addUser(event)">
                            <div>
                                <label>Username:</label>
                                <input type="text" name="username" required>
                            </div>
                            <div>
                                <label>Email:</label>
                                <input type="email" name="email" required>
                            </div>
                            <div>
                                <label>Password:</label>
                                <input type="password" name="password" required>
                            </div>
                            <div>
                                <label>Role:</label>
                                <select name="role">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn">Add User</button>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', form);
        }

        function closeModal(modalId) {
            document.getElementById(modalId).remove();
            document.body.style.overflow = '';
        }

        function addUser(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            formData.append('action', 'add_user');

            fetch('admin_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User added successfully');
                    closeModal('addUserModal');
                    loadContent('users');
                } else {
                    alert('Error: ' + (data.message || 'Failed to add user'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the user');
            });
        }

        function searchFestivals() {
            const input = document.getElementById('searchFestival');
            const filter = input.value.toUpperCase();
            const table = document.querySelector('#festivalTable table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let txtValue = '';
                
                // Search in name and location
                txtValue = td[1].textContent + ' ' + td[2].textContent;
                
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }

        function showAddFestivalForm() {
            const form = `
                <div class="modal" id="addFestivalModal" style="display: block;">
                    <div class="modal-content">
                        <span class="close-modal" onclick="closeModal('addFestivalModal')">&times;</span>
                        <h2>Add New Festival</h2>
                        <form id="addFestivalForm" onsubmit="addFestival(event)">
                            <div>
                                <label>Festival Name:</label>
                                <input type="text" name="festival_name" required>
                            </div>
                            <div>
                                <label>Description:</label>
                                <textarea name="description" required></textarea>
                            </div>
                            <div>
                                <label>Location:</label>
                                <input type="text" name="location" required>
                            </div>
                            <div>
                                <label>Map Coordinates:</label>
                                <input type="text" name="map_coordinates" placeholder="e.g., 14.5995,120.9842">
                            </div>
                            <div>
                                <label>Date Celebrated:</label>
                                <input type="date" name="date_celebrated" required>
                            </div>
                            <div>
                                <label>Festival Image:</label>
                                <input type="file" name="festival_image" accept="image/*">
                            </div>
                            <button type="submit" class="btn">Add Festival</button>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', form);
        }

        function addFestival(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            formData.append('action', 'add_festival');

            fetch('admin_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Festival added successfully');
                    closeModal('addFestivalModal');
                    loadContent('library');
                } else {
                    alert('Error: ' + (data.message || 'Failed to add festival'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the festival');
            });
        }

        // Load dashboard by default
        document.addEventListener('DOMContentLoaded', () => loadContent('dashboard'));

        // Function to get festival details
        function editFestival(festivalId) {
            fetch('admin_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_festival&festival_id=${festivalId}`
            })
            .then(response => response.json())
            .then(festival => {
                showEditFestivalForm(festival);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching festival details');
            });
        }

        // Function to show edit form
        function showEditFestivalForm(festival) {
            const form = `
                <div class="modal" id="editFestivalModal" style="display: block;">
                    <div class="modal-content">
                        <span class="close-modal" onclick="closeModal('editFestivalModal')">&times;</span>
                        <h2>Edit Festival</h2>
                        <form id="editFestivalForm" onsubmit="updateFestival(event, ${festival.festival_id})">
                            <div>
                                <label>Festival Name:</label>
                                <input type="text" name="festival_name" value="${festival.festival_name}" required>
                            </div>
                            <div>
                                <label>Description:</label>
                                <textarea name="description" required>${festival.description}</textarea>
                            </div>
                            <div>
                                <label>Location:</label>
                                <input type="text" name="location" value="${festival.location}" required>
                            </div>
                            <div>
                                <label>Map Coordinates:</label>
                                <input type="text" name="map_coordinates" value="${festival.map_coordinates}" placeholder="e.g., 14.5995,120.9842">
                            </div>
                            <div>
                                <label>Date Celebrated:</label>
                                <input type="date" name="date_celebrated" value="${festival.date_celebrated}" required>
                            </div>
                            <div>
                                <label>Festival Image:</label>
                                <input type="file" name="festival_image" accept="image/*" onchange="previewImage(this)">
                                <img id="imagePreview" src="img/${festival.festival_image}" style="max-width:200px; margin-top:10px;">
                                <p class="small">Leave empty to keep current image</p>
                            </div>
                            <button type="submit" class="btn">Update Festival</button>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', form);
            document.body.style.overflow = 'hidden';
        }

        // Function to update festival
        function updateFestival(event, festivalId) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            formData.append('action', 'edit_library');
            formData.append('festival_id', festivalId);

            const submitButton = form.querySelector('button[type="submit"]');
            setLoadingState(submitButton, true);

            fetch('admin_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Festival updated successfully');
                    closeModal('editFestivalModal');
                    loadContent('library');
                } else {
                    alert('Error: ' + (data.message || 'Failed to update festival'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the festival');
            })
            .finally(() => {
                setLoadingState(submitButton, false);
            });
        }

        // Function to delete festival
        function deleteFestival(festivalId) {
            if (!confirm('Are you sure you want to delete this festival? This action cannot be undone.')) {
                return;
            }

            fetch('admin_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_festival&festival_id=${festivalId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Festival deleted successfully');
                    loadContent('library');
                } else {
                    alert('Error deleting festival');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the festival');
            });
        }

        // Helper function for loading states
        function setLoadingState(button, isLoading) {
            if (isLoading) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            } else {
                button.disabled = false;
                button.innerHTML = 'Update Festival';
            }
        }

        // Image preview function
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function filterDashboard(timeframe) {
            fetch('admin_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_content&page=dashboard&timeframe=${timeframe}`
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;
                // Reselect the timeframe
                const select = document.querySelector('.date-filter select');
                if (select) {
                    select.value = timeframe;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update dashboard');
            });
        }

        // Add tooltips to revenue and sales amounts
        function initializeTooltips() {
            const amounts = document.querySelectorAll('.stat-details p');
            amounts.forEach(amount => {
                if (amount.textContent.includes('₱')) {
                    const phpAmount = parseFloat(amount.textContent.replace(/[₱,]/g, ''));
                    const usdAmount = (phpAmount / 56).toFixed(2); // Using the same conversion rate
                    amount.setAttribute('title', `$ ${usdAmount}`);
                }
            });
        }

        function verifyPayment(orderId) {
            if (confirm('Are you sure you want to verify this payment?')) {
                fetch('verify_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `order_id=${orderId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment verified successfully');
                        loadContent('payments'); // Reload the payments page
                    } else {
                        alert('Error: ' + (data.message || 'Failed to verify payment'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while verifying the payment');
                });
            }
        }
    </script>
</body>
</html>
