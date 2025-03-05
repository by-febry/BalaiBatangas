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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Festival in Batangas</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        html,
        body {
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

        /* Circular Profile Picture or Default Circle */
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

        /* Dropdown menu styling */
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

        .dropdown-content a,
        p {
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

        /* Username styling under the circle */
        .user-name {
            text-align: center;
            font-size: 12px;
            color: #333;
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

        /* Main Section */
        main {
            text-align: center;
            padding: 40px;
            flex: 1;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 40px;
        }

        /* Cultural Heritage Layout */
        .cultural-heritage-layout {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Gallery Item */
        .gallery-item {
            display: flex;
            align-items: center;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* Gallery Content */
        .gallery-content {
            display: flex;
            width: 100%;
            height: auto;
            max-height: 300px;
        }

        /* Image Styling */
        .gallery-item img {
            width: 100%;
            max-width: 300px;
            height: 100%;
            border-radius: 5px;
            margin-right: 15px;
        }

        /* Gallery Text Styling */
        .gallery-text {
            flex: 1;
        }

        .gallery-label {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        /* Button styling */
        .location-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .location-btn:hover {
            background-color: #0056b3;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            position: relative;
            background-color: white;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
        }

        /* Pagination styling */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 10px;
        }

        .page-link {
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .page-link:hover {
            background-color: #ddd;
        }

        .page-link.active {
            background-color: #8B0000;
            color: white;
            border-color: #8B0000;
        }

        /* Responsive styles */
        @media (min-width: 576px) {
            .gallery-text p {
                font-size: 1rem;
            }
            .gallery-label {
                font-size: 1.1rem;
            }
            .gallery-item img {
                max-width: 250px;
            }
        }

        @media (min-width: 768px) {
            .gallery-text p {
                font-size: 1.1rem;
            }
            .gallery-label {
                font-size: 1.2rem;
            }
            .gallery-item img {
                max-width: 300px;
            }
        }

        @media (min-width: 992px) {
            .gallery-text p {
                font-size: 1.2rem;
            }
            .gallery-label {
                font-size: 1.3rem;
            }
        }

        /* Search Bar Styling */
        .search-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .search-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-form input[type="text"] {
            padding: 10px 15px;
            border: 2px solid #8B0000;
            border-radius: 5px;
            width: 100%;
            max-width: 400px;
            font-size: 16px;
        }

        .search-btn {
            padding: 10px 20px;
            background-color: #8B0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .search-btn:hover {
            background-color: #660000;
        }

        .clear-search {
            padding: 10px 20px;
            background-color: #666;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .clear-search:hover {
            background-color: #444;
        }

        .search-results {
            margin: 20px 0;
            font-style: italic;
            color: #666;
            text-align: center;
        }

        /* Responsive Search Bar */
        @media (max-width: 576px) {
            .search-form {
                flex-direction: column;
                padding: 0 15px;
            }

            .search-form input[type="text"],
            .search-btn,
            .clear-search {
                width: 100%;
            }
        }

        /* Typing Indicator */
        .typing-indicator {
            background-color: #f0f0f0;
            padding: 15px;
            display: flex;
            align-items: center;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background: #8B0000;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            animation: bounce 1.3s linear infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.15s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes bounce {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-4px);
            }
        }

        /* Improve message styling */
        .message {
            margin: 8px 0;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 85%;
            word-wrap: break-word;
            line-height: 1.4;
        }

        .message.user {
            background: #8B0000;
            color: white;
            margin-left: auto;
        }

        .message.bot {
            background: #f0f0f0;
            margin-right: auto;
        }

        /* Professional Chat Widget Styles */
        .chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 380px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .chat-header {
            background: linear-gradient(135deg, #8B0000, #660000);
            color: white;
            padding: 20px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .chat-header span {
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chat-header span::before {
            content: '';
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #4CAF50;
            border-radius: 50%;
            margin-right: 5px;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.3);
        }

        .minimize-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .minimize-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .chat-body {
            height: 500px;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .message {
            margin: 8px 0;
            padding: 12px 16px;
            border-radius: 15px;
            max-width: 85%;
            font-size: 0.95rem;
            line-height: 1.4;
            position: relative;
            transition: all 0.3s ease;
        }

        .message.user {
            background: linear-gradient(135deg, #8B0000, #660000);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .message.bot {
            background: white;
            color: #333;
            margin-right: auto;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .typing-indicator {
            background: white;
            padding: 15px 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
            width: fit-content;
            margin: 10px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #8B0000;
            border-radius: 50%;
            display: inline-block;
            opacity: 0.4;
            animation: typing 1.4s infinite;
        }

        @keyframes typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .typing-indicator span:nth-child(1) { animation-delay: 0s; }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

        .chat-input {
            display: flex;
            padding: 20px;
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            gap: 10px;
        }

        .chat-input input {
            flex-grow: 1;
            padding: 12px 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 25px;
            outline: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .chat-input input:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.1);
            background: white;
        }

        .chat-input button {
            padding: 12px 20px;
            background: linear-gradient(135deg, #8B0000, #660000);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .chat-input button:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(139, 0, 0, 0.2);
        }

        .chat-input button:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .chat-widget {
                width: 100%;
                height: 100%;
                bottom: 0;
                right: 0;
                border-radius: 0;
                position: fixed;
            }

            .chat-body {
                height: calc(100vh - 140px);
            }

            .chat-input {
                padding: 15px;
                position: sticky;
                bottom: 0;
            }
        }
    </style>
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

                <div class="user-menu">
                    <?php if (!empty($profile_picture)): ?>
                        <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture" class="user-icon">
                    <?php else: ?>
                        <div class="user-icon">
                            <?php echo strtoupper($username[0]); ?>
                        </div>
                    <?php endif; ?>

                    <div class="dropdown-content">
                        <p><?php echo $username; ?></p>
                        <a href="userpanel.php">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Festivals in Batangas</h1>
        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Search festivals..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search-btn">Search</button>
                <?php if(isset($_GET['search'])): ?>
                    <a href="library.php" class="clear-search">Clear Search</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="cultural-heritage-layout">
            <?php
            // Define items per page
            $items_per_page = 3; // You can adjust this number
            
            // Get search term if it exists
            $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

            // Modify the count query to include search
            $count_query = "SELECT COUNT(*) as total FROM library WHERE status = 1";
            if ($search) {
                $count_query .= " AND (festival_name LIKE '%$search%' 
                                      OR description LIKE '%$search%' 
                                      OR location LIKE '%$search%'
                                      OR date_celebrated LIKE '%$search%')";
            }
            $count_result = mysqli_query($conn, $count_query);
            $row = mysqli_fetch_assoc($count_result);
            $total_items = $row['total'];
            $total_pages = ceil($total_items / $items_per_page);

            // Get current page
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $current_page = max(1, min($current_page, $total_pages));
            $offset = ($current_page - 1) * $items_per_page;

            // Modify the main query to include search
            $query = "SELECT * FROM library WHERE status = 1";
            if ($search) {
                $query .= " AND (festival_name LIKE '%$search%' 
                                 OR description LIKE '%$search%' 
                                 OR location LIKE '%$search%'
                                 OR date_celebrated LIKE '%$search%')";
            }
            $query .= " ORDER BY festival_id LIMIT $offset, $items_per_page";
            $result = mysqli_query($conn, $query);

            // Add this after the query to show search results count
            if ($search) {
                echo "<p class='search-results'>Found " . $total_items . " result(s) for '" . htmlspecialchars($search) . "'</p>";
            }

            while ($festival = mysqli_fetch_assoc($result)) {
                ?>
                <div class="gallery-item">
                    <div class="gallery-content">
                        <img src="img/<?php echo $festival['festival_image']; ?>" alt="<?php echo $festival['festival_name']; ?>" />
                        <div class="gallery-text">
                            <label class="gallery-label"><?php echo $festival['festival_name']; ?></label>
                            <p><?php echo $festival['description']; ?></p>
                            <p><strong>Location:</strong> <?php echo $festival['location']; ?></p>
                            <p><strong>Date Celebrated:</strong> <?php echo $festival['date_celebrated']; ?></p>
                            <a href="#" class="location-btn" onclick="openMap('mapModal<?php echo $festival['festival_id']; ?>')">View Location</a>
                        </div>
                    </div>
                </div>

                <!-- Modal for this festival -->
                <div id="mapModal<?php echo $festival['festival_id']; ?>" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeMap('mapModal<?php echo $festival['festival_id']; ?>')">&times;</span>
                        <h2><?php echo $festival['festival_name']; ?> - Location</h2>
                        <p>Coordinates: <?php echo $festival['map_coordinates']; ?></p>
                        <iframe 
                            src="https://maps.google.com/maps?q=<?php echo urlencode($festival['location']); ?>&output=embed"
                            width="100%" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            <?php } ?>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo ($current_page - 1); ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                       class="page-link">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                       class="page-link <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo ($current_page + 1); ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                       class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

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
        function openMap(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeMap(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = "none";
                }
            }
        };

        let isChatOpen = true;
        let isWaitingForResponse = false;

        function toggleChat() {
            const chatBody = document.querySelector('.chat-body');
            const minimizeBtn = document.querySelector('.minimize-btn');
            
            if (isChatOpen) {
                chatBody.style.display = 'none';
                minimizeBtn.textContent = '+';
            } else {
                chatBody.style.display = 'flex';
                minimizeBtn.textContent = '−';
            }
            isChatOpen = !isChatOpen;
        }

        async function sendMessage() {
            const input = document.getElementById('userInput');
            const message = input.value.trim();
            
            if (message && !isWaitingForResponse) {
                // Add user message
                addMessage(message, 'user');
                input.value = '';
                
                // Show typing indicator
                isWaitingForResponse = true;
                const typingIndicator = addTypingIndicator();
                
                try {
                    // Send message to backend
                    const response = await fetch('chat_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ message: message })
                    });
                    
                    const data = await response.json();
                    
                    // Remove typing indicator
                    typingIndicator.remove();
                    
                    if (data.status === 'success') {
                        addMessage(data.message, 'bot');
                    } else {
                        addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    typingIndicator.remove();
                    addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                }
                
                isWaitingForResponse = false;
            }
        }

        function addMessage(text, sender) {
            const messagesDiv = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            messageDiv.textContent = text;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function addTypingIndicator() {
            const messagesDiv = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot typing-indicator';
            typingDiv.innerHTML = '<span></span><span></span><span></span>';
            messagesDiv.appendChild(typingDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            return typingDiv;
        }

        // Add event listener for Enter key in input
        document.getElementById('userInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>

    <!-- Chat Widget -->
    <div class="chat-widget" id="chatWidget">
        <div class="chat-header" onclick="toggleChat()">
            <span>CHIP Assistant</span>
            <button class="minimize-btn">−</button>
        </div>
        <div class="chat-body">
            <div class="chat-messages" id="chatMessages">
                <div class="message bot">
                    👋 Hi! I'm CHIP (Cultural Heritage Information Provider). I'm your personal guide to Batangas festivals and cultural heritage. How can I assist you today?
                </div>
            </div>
            <div class="chat-input">
                <input type="text" id="userInput" placeholder="Ask CHIP about Batangas festivals...">
                <button onclick="sendMessage()">
                    Send
                </button>
            </div>
        </div>
    </div>
</body>
</html>