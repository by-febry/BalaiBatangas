<?php
// Start the session at the top before any output
session_start();

// Include the database connection file
include 'connection.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user data from the database
    $query = "SELECT profile_picture, username FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    $profile_picture = $user['profile_picture'];
    $username = $user['username']; // Make sure $username is assigned properly here
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
            /* #f4f4f4 */
            color: rgb(51, 51, 51);
            /* #333 */
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
            /* Dark Red background */
            padding: 10px 20px;
            color: rgb(255, 255, 255);
            /* White text color */
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
            /* Change text color to black when hovered */
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
            /* Ensure it stays at the bottom */
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
            /* Allows main content to expand and push footer to the bottom */
        }

        h1 {
            font-size: 28px;
            margin-bottom: 40px;
        }

        .gallery {
            display: flex;
            justify-content: center;
            gap: 40px;
            /* Increase spacing between items */
        }

        .item {
            position: relative;
            /* Set position relative for absolute positioning of info */
            overflow: hidden;
            /* Hide overflow for zoom effect */
            width: 300px;
            /* Set fixed width */
            height: 300px;
            /* Set fixed height */
        }

        .item img {
            width: 100%;
            /* Make image responsive */
            height: auto;
            /* Maintain aspect ratio */
            transition: transform 0.3s ease;
            /* Transition for zoom effect */
        }

        .item:hover img {
            transform: scale(2);
            /* Zoom in on hover */
        }

        .info {
            position: absolute;
            /* Position info absolutely */
            bottom: 0;
            /* Align to bottom */
            left: 0;
            /* Align to left */
            right: 0;
            /* Align to right */
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            color: white;
            /* White text */
            padding: 10px;
            /* Padding around text */
            opacity: 0;
            /* Initially hide the info */
            transition: opacity 0.3s ease;
            /* Transition for opacity */
        }

        .item:hover .info {
            opacity: 1;
            /* Show info on hover */
        }




        /* Cultural Heritage Layout */
        .cultural-heritage-layout {
            display: flex;
            flex-direction: column;
            /* Stack gallery items vertically */
            gap: 20px;
            /* Space between gallery items */
        }

        /* Gallery Item */
        .gallery-item {
            display: flex;
            /* Enable flexbox for horizontal layout */
            align-items: center;
            /* Align items vertically centered */
            background-color: #f9f9f9;
            /* Light background for contrast */
            border: 1px solid #ccc;
            /* Light border for definition */
            border-radius: 5px;
            /* Slight rounding of corners */
            padding: 15px;
            /* Padding around the item */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Soft shadow for depth */
        }

        /* Gallery Content: Flexbox for image and text */
        .gallery-content {
            display: flex;
            width: 100%;
            /* Ensure full width */
            height: auto;
            max-height: 300px;

        }

        /* Image Styling */
        .gallery-item img {
            width: 100%;
            max-width: 300px;
            /* Set a max-width for images */
            height: 100%;
            border-radius: 5px;
            /* Optional: round the corners of the image */
            margin-right: 15px;
            /* Space between image and text */
        }

        /* Gallery Text Styling */
        .gallery-text {
            flex: 1;
            /* Allow text to take remaining space */
        }

        /* Style for the label */
        .gallery-label {
            display: block;
            /* Make label a block element */
            font-size: 18px;
            /* Set font size for the label */
            font-weight: bold;
            /* Make the label bold */
            color: #333;
            /* Color of the label */
            margin-bottom: 5px;
            /* Space between label and paragraph */
        }

        /* Paragraph Styling */
        /* Default styles for the gallery text */
        .gallery-text p {
            font-size: 0.8rem;
            /* Base font size for small screens */
        }

        /* Responsive styles for larger screens */
        @media (min-width: 576px) {

            /* Increase font size for small devices (576px and up) */
            .gallery-text p {
                font-size: 1rem;
                /* Increase font size for better readability */
            }

            .gallery-label {
                font-size: 1.1rem;
                /* Slightly larger label */
            }

            /* Adjust the layout for larger screens */
            .gallery-item {
                flex-direction: row;
                /* Ensure horizontal layout */
            }

            /* Adjust image size for better proportion on larger screens */
            .gallery-item img {
                max-width: 250px;
                /* Increase max-width for larger screens */
            }
        }

        @media (min-width: 768px) {

            /* Increase font size for medium devices (768px and up) */
            .gallery-text p {
                font-size: 1.1rem;
                /* Larger font size */
            }

            .gallery-label {
                font-size: 1.2rem;
                /* Larger label font size */
            }

            /* Further adjust image size for larger screens */
            .gallery-item img {
                max-width: 300px;
                /* Adjust max-width for larger screens */
            }
        }

        @media (min-width: 992px) {

            /* Increase font size for large devices (992px and up) */
            .gallery-text p {
                font-size: 1.2rem;
                /* Largest font size */
            }

            .gallery-label {
                font-size: 1.3rem;
                /* Largest label font size */
            }
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
            /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            /* Black with opacity */
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

        /* Modal open animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
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

                <!-- User Profile Section -->
                <div class="user-menu">
                    <?php if (!empty($profile_picture)): ?>
                        <!-- If profile picture exists, show it -->
                        <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture" class="user-icon">
                    <?php else: ?>
                        <!-- If no profile picture, show default white circle -->
                        <div class="user-icon">
                            <?php
                            $initials = strtoupper($username[0]); // Display first letter of the username
                            echo $initials;
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-content">
                        <p><?php echo $username; ?></p> <!-- Display username inside the dropdown -->
                        <a href="userpanel.php">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </ul>
        </nav>
    </header>
    <!-- Main Content for Cultural Heritage -->
    <main>
        <h2>*Cultural Heritage Part*</h2>
        <p>Festival, arts and crafts, and other cultural aspects related to Batangas.</p>
        <div class="cultural-heritage-layout">
            <!-- Gallery Item 1 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/lambayok.webp" alt="Gallery Item 1" />
                    <div class="gallery-text">
                        <label class="gallery-label">Lambayok Festival</label>
                        <p>The Lambayok Festival is an annual celebration in San Juan, Batangas, Philippines, that highlights the town’s three main sources of livelihood: "Lambanog" (local coconut wine), "Palayok" (clay pots), and "Karagatan" (the sea). The festival showcases the town's rich cultural heritage through parades, street dances, and various contests. It's held every December and serves as a way to promote local products and tourism.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal1')">Location</a>
                    </div>
                </div>
            </div>



            <!-- Gallery Item 2 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/bakahan.jpg" alt="Gallery Item 2" />
                    <div class="gallery-text">
                        <label class="gallery-label">Bakahan Festival</label>
                        <p>The Bakahan Festival is an annual event in San Juan, Batangas, Philippines, celebrating the town's cattle industry. Typically held in April, the festival features livestock parades, cattle shows, street dancing, and various competitions. It aims to promote agricultural practices, raise awareness about livestock farming, and highlight the community's cultural heritage. The festival fosters a sense of pride among locals and attracts tourists to the region.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal2')">Location</a>
                    </div>
                </div>
            </div>



            <!-- Gallery Item 3 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/tapusan.jpg" alt="Gallery Item 3" />
                    <div class="gallery-text">
                        <label class="gallery-label">Tapusan Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal3')">Location</a>
                    </div>
                </div>
            </div>

            <!-- Gallery Item 4 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/Sublian.png" alt="Gallery Item 3" />
                    <div class="gallery-text">
                        <label class="gallery-label">Sublian Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal4')">Location</a>
                    </div>
                </div>
            </div>

            <!-- Gallery Item 5 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/parada ng lechon.png" alt="Gallery Item 5" />
                    <div class="gallery-text">
                        <label class="gallery-label">Parada ng Lechon Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal5')">Location</a>
                    </div>
                </div>
            </div>


            <!-- Gallery Item 6 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/El pasubat.png" alt="Gallery Item 3" />
                    <div class="gallery-text">
                        <label class="gallery-label">El Pasubat Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal6')">Location</a>
                    </div>
                </div>
            </div>



            <!-- Gallery Item 7 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/Balsa.png" alt="Gallery Item 3" />
                    <div class="gallery-text">
                        <label class="gallery-label">Balsa Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal7')">Location</a>
                    </div>
                </div>
            </div>

            <!-- Gallery Item 8 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/Tinapay.png" alt="Gallery Item 3" />
                    <div class="gallery-text">
                        <label class="gallery-label">Tinapay Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal8')">Location</a>
                    </div>
                </div>
            </div>


            <!-- Gallery Item 9 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/Anihan.png" alt="Gallery Item 3" />
                    <div class="gallery-text">
                        <label class="gallery-label">Anihan Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal9')">Location</a>
                    </div>
                </div>
            </div>

            <!-- Gallery Item 10 -->
            <div class="gallery-item">
                <div class="gallery-content">
                    <img src="img/kawayan.png" alt="Gallery Item 3" />
                    <div class="gallery-text">
                        <label class="gallery-label">Kawayan Festival</label>
                        <p>The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January. It honors the tradition of "tapusan," which means "to end" in Filipino, marking the conclusion of the Christmas season and the feast of the Santo Niño (Child Jesus). The festival features colorful street parades, cultural performances, and various competitions, showcasing the rich heritage and traditions of the local community. It aims to foster unity among residents and highlight the significance of faith and gratitude in their lives.</p>
                        <a href="#" class="location-btn" onclick="openMap('mapModal10')">Location</a>
                    </div>
                </div>
            </div>
            <!-- Map Modal 1 -->
            <div id="mapModal1" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeMap('mapModal1')">&times;</span>
                    <iframe id="map-iframe" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d124011.1570953101!2d121.3178980694302!3d13.757835605666763!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd3b0f51ae56a7%3A0x3503f98f2afe74ee!2sSan%20Juan%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730638608627!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            <div id="mapModal2" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeMap('mapModal2')">&times;</span>
                    <iframe id="map-iframe"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d61973.11421356095!2d121.17294910763891!3d13.87982613176864!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd1508d7e45093%3A0xffd7ab9aefab93ab!2sPadre%20Garcia%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730645573513!5m2!1sen!2sph"
                        width="600" height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            <div id="mapModal3" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeMap('mapModal3')">&times;</span>
                    <iframe id="map-iframe"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30987.808968272115!2d120.9979003013883!3d13.870455384559003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd0c1e6a4aef21%3A0xd572d65f900bb6ec!2sAlitagtag%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730646623700!5m2!1sen!2sph"
                        width="600"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>

                </div>
            </div>
            <div id="mapModal4" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeMap('mapModal4')">&times;</span>
                    <iframe id="map-iframe"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d248097.07668355267!2d120.93977907160428!3d13.687118678509401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd1b1d4c1cd013%3A0xe4592a820e411177!2sBatangas!5e0!3m2!1sen!2sph!4v1730646887301!5m2!1sen!2sph"
                        width="600"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <div id="mapModal5" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeMap('mapModal5')">&times;</span>

                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d61954.15373830944!2d120.68866516552214!3d13.95058966976329!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bda321fa23c4af%3A0x15255e1209796544!2sBalayan%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730648892917!5m2!1sen!2sph"
                        width="600"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>



            <div id="mapModal6" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeMap('mapModal6')">&times;</span>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d61970.33001393953!2d120.89698658833612!3d13.89023934794681!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd0a1efabef203%3A0x4ef524d256f82617!2sTaal%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730645524610!5m2!1sen!2sph"
                        width="600"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
        <div id="mapModal7" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeMap('mapModal7')">&times;</span>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123885.52554903114!2d120.56275800456045!3d13.992933897040006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd965879bed27b%3A0x5621337013ad1e0e!2sLian%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730645542639!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
        <div id="mapModal8" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeMap('mapModal8')">&times;</span>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d61975.61862621026!2d120.97734278822529!3d13.870452802473523!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd0d0a3043483d%3A0x64d8e191e873e404!2sCuenca%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730645637737!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

            </div>
        </div>
        <div id="mapModal9" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeMap('mapModal9')">&times;</span>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62013.95129148797!2d120.87576503742214!3d13.726201085524549!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd2203014b54d7%3A0x7716e3f4d7c9cf0c!2sLobo%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730645674895!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

            </div>
        </div>
        <div id="mapModal10" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeMap('mapModal10')">&times;</span>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d61932.582408295406!2d120.700605989127!3d14.030671915866533!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd990e0e3dad5f%3A0xb68839bd43778451!2sTuy%2C%20Batangas!5e0!3m2!1sen!2sph!4v1730645709487!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

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
    </script>
</body>

</html>