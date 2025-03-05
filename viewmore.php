<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Batangas - Art Gallery</title>
    <style>
        /* Reset body margin and padding */
        body {
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
        }

        /* Navigation Bar */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #8B0000; /* Dark Red background */
            padding: 10px 20px;
            color: rgb(255, 255, 255); /* White text color */
            font-size: 25px;
            margin: 0; /* Remove margin around header */
        }

        /* Logo Section in the Header */
        header .logo img {
            max-height: 80px; /* Adjust this value based on header height */
        }

        /* Navigation Links */
        header nav {
            margin-left: auto; /* Pushes the navigation links to the right */
        }

        header nav ul {
            list-style: none; /* Removes bullet points from list */
            display: flex; /* Displays list items in a row */
        }

        /* Individual List Items */
        header nav ul li {
            margin-right: 30px; /* Space between menu items */
        }

        /* Links in Navigation */
        header nav ul li a {
            text-decoration: none; /* Removes underline from links */
            color: rgb(255, 255, 255); /* White text color */
            font-weight: bold;
        }

        /* Hover Effect on Links */
        header nav ul li a:hover {
            color: rgb(0, 0, 0); /* Changes text color to black when hovered */
        }

        /* User Icon & Dropdown Placeholder */
        .login-circle {
            position: relative;
        }

        .user-icon {
            width: 90px;
            height: 75px;
            background-color: #fff; /* White circle as placeholder for user icon */
            border-radius: 50%; /* Makes the placeholder a circle */
            cursor: pointer; /* Changes cursor to pointer (clickable) */
        }

        /* Main Content for Cultural Heritage */
        main {
            padding: 20px; /* Optional: Add padding to main content */
        }

        /* Footer Section */
footer {
    background-color: #8B0000; /* #C72C41 */
    color: rgb(255, 255, 255); /* #fff */
    padding: 20px 0;
    text-align: center;
    width: 100%;
    /* Stick the footer to the bottom */
    margin-top: auto;
}

.footer-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.footer-content p {
    margin-bottom: 10px;
}

.footer-links {
    margin-top: 10px;
}

.footer-links a {
    color: rgb(0, 0, 0); 
    text-decoration: none;
    margin: 0 10px;
}

.footer-links a:hover {
    text-decoration: underline;
}

.view-more {
    display: block;
    margin-top: 20px;
    color: blue;
    text-decoration: none;
}

/* Main Section */
main {
    text-align: center;
    padding: 40px;
    flex: 1; /* Allows main content to expand and push footer to the bottom */
}

h1 {
    font-size: 28px;
    margin-bottom: 40px;
}

.gallery {
    display: flex;
    justify-content: center;
    gap: 40px; /* Increase spacing between items */
}

.item {
    position: relative; /* Set position relative for absolute positioning of info */
    overflow: hidden; /* Hide overflow for zoom effect */
    width: 300px; /* Set fixed width */
    height: 300px; /* Set fixed height */
}

.item img {
    width: 100%; /* Make image responsive */
    height: auto; /* Maintain aspect ratio */
    transition: transform 0.3s ease; /* Transition for zoom effect */
}

.item:hover img {
    transform: scale(2); /* Zoom in on hover */
}

.info {
    position: absolute; /* Position info absolutely */
    bottom: 0; /* Align to bottom */
    left: 0; /* Align to left */
    right: 0; /* Align to right */
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
    color: white; /* White text */
    padding: 10px; /* Padding around text */
    opacity: 0; /* Initially hide the info */
    transition: opacity 0.3s ease; /* Transition for opacity */
}

.item:hover .info {
    opacity: 1; /* Show info on hover */
}


.view-more {
    display: block;
    margin-top: 20px;
    color: blue;
    text-decoration: none;
}

.view-more:hover {
    text-decoration: underline;
}

/* Cultural Heritage Layout */
.cultural-heritage-layout {
    display: flex;
    flex-direction: column; /* Stack gallery items vertically */
    gap: 20px; /* Space between gallery items */
}

/* Gallery Item */
.gallery-item {
    display: flex; /* Enable flexbox for horizontal layout */
    align-items: center; /* Align items vertically centered */
    background-color: #f9f9f9; /* Light background for contrast */
    border: 1px solid #ccc; /* Light border for definition */
    border-radius: 5px; /* Slight rounding of corners */
    padding: 15px; /* Padding around the item */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
}

/* Gallery Content: Flexbox for image and text */
.gallery-content {
    display: flex;
    width: 100%; /* Ensure full width */
    height: auto;
    max-height: 300px;

}

/* Image Styling */
.gallery-item img {
    width: 100%;    
    max-width: 300px; /* Set a max-width for images */
    height: 100%;
    border-radius: 5px; /* Optional: round the corners of the image */
    margin-right: 15px; /* Space between image and text */
}

/* Gallery Text Styling */
.gallery-text {
    flex: 1; /* Allow text to take remaining space */
}

/* Style for the label */
.gallery-label {
    display: block; /* Make label a block element */
    font-size: 18px; /* Set font size for the label */
    font-weight: bold; /* Make the label bold */
    color: #333; /* Color of the label */
    margin-bottom: 5px; /* Space between label and paragraph */
}

/* Paragraph Styling */
/* Default styles for the gallery text */
.gallery-text p {
    font-size: 0.8rem; /* Base font size for small screens */
}

/* Responsive styles for larger screens */
@media (min-width: 576px) {
    /* Increase font size for small devices (576px and up) */
    .gallery-text p {
        font-size: 1rem; /* Increase font size for better readability */
    }

    .gallery-label {
        font-size: 1.1rem; /* Slightly larger label */
    }
    
    /* Adjust the layout for larger screens */
    .gallery-item {
        flex-direction: row; /* Ensure horizontal layout */
    }
    
    /* Adjust image size for better proportion on larger screens */
    .gallery-item img {
        max-width: 250px; /* Increase max-width for larger screens */
    }
}

@media (min-width: 768px) {
    /* Increase font size for medium devices (768px and up) */
    .gallery-text p {
        font-size: 1.1rem; /* Larger font size */
    }

    .gallery-label {
        font-size: 1.2rem; /* Larger label font size */
    }
    
    /* Further adjust image size for larger screens */      
    .gallery-item img {
        max-width: 300px; /* Adjust max-width for larger screens */
    }
}

@media (min-width: 992px) {
    /* Increase font size for large devices (992px and up) */
    .gallery-text p {
        font-size: 1.2rem; /* Largest font size */
    }

    .gallery-label {
        font-size: 1.3rem; /* Largest label font size */
    }
}
/* Products Button */
.products-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 16px;
    background-color: #8B0000; /* Dark red to match header */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.products-btn:hover {
    background-color: #A52A2A; /* Slightly lighter red for hover */
}
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="logo">
        <img src="img/logo3.png" alt="Logo" />
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="#">About</a></li>
                <li><a href="login.html">Login</a></li>
            </ul>
        </nav>
        <div class="login-circle">
            <!-- Trigger area for dropdown -->
            <div class="user-icon"></div>
        </div>
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
                        <a href="#" class="products-btn">Products</a>
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
                        <a href="#" class="products-btn">Products</a>
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
                        <a href="#" class="products-btn">Products</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
   <!-- Footer -->
   <footer>
        <div class="footer-content">
            <p>&copy; 2024 Explore Batangas. All Rights Reserved.</p>
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a> | 
            <a href="#">Contact Us</a>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
