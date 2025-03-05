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
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Balai Batangas</title>
    <style>
        /* Reuse your existing header and footer styles */
        
        /* About Hero Section */
        .about-hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('img/batangas-landscape.jpg');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .about-hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        /* Mission & Vision Section */
        .mission-vision {
            padding: 60px 20px;
            background-color: #fff;
            text-align: center;
        }

        .mission-vision-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
        }

        .mission-vision-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background: #f9f9f9;
        }

        .mission-vision-card h3 {
            color: #8B0000;
            margin-bottom: 15px;
        }

        /* Team Section */
        .team-section {
            padding: 60px 20px;
            background-color: #f4f4f4;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
        }

        .team-member {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
        }

        /* History Section */
        .history-section {
            padding: 60px 20px;
            background: white;
        }

        .history-content {
            max-width: 1000px;
            margin: 0 auto;
            text-align: justify;
        }

        .timeline {
            margin: 40px 0;
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Contact Section */
        .contact-section {
            padding: 60px 20px;
            background-color: #8B0000;
            color: white;
            text-align: center;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
        }

        .contact-info {
            padding: 20px;
        }

        .contact-info i {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Reuse your existing header -->
    <?php include 'header.php'; ?>

    <main>
        <!-- About Hero Section -->
        <section class="about-hero">
            <div class="hero-content">
                <h1>About Balai Batangas</h1>
                <p>Connecting Culture, Commerce, and Community</p>
            </div>
        </section>

        <!-- Mission & Vision Section -->
        <section class="mission-vision">
            <div class="mission-vision-grid">
                <div class="mission-vision-card">
                    <h3>Our Mission</h3>
                    <p>To promote and preserve Batangas' cultural heritage by creating a digital marketplace that connects local artisans and producers with customers worldwide.</p>
                </div>
                <div class="mission-vision-card">
                    <h3>Our Vision</h3>
                    <p>To be the premier platform for discovering and experiencing Batangas' rich cultural offerings, fostering economic growth while preserving traditional practices.</p>
                </div>
            </div>
        </section>

        <!-- History Section -->
        <section class="history-section">
            <div class="history-content">
                <h2>Our Journey</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <h3>2024</h3>
                        <p>Launch of Balai Batangas platform, bringing local products and cultural experiences to a digital marketplace.</p>
                    </div>
                    <div class="timeline-item">
                        <h3>Heritage</h3>
                        <p>Batangas has been known for its rich cultural heritage, from traditional crafts to local delicacies. Our platform aims to preserve and promote these treasures.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="team-section">
            <h2>Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="img/team-member1.jpg" alt="Team Member 1">
                    <h3>John Doe</h3>
                    <p>Founder & CEO</p>
                </div>
                <div class="team-member">
                    <img src="img/team-member2.jpg" alt="Team Member 2">
                    <h3>Jane Smith</h3>
                    <p>Cultural Director</p>
                </div>
                <div class="team-member">
                    <img src="img/team-member3.jpg" alt="Team Member 3">
                    <h3>Mike Johnson</h3>
                    <p>Community Manager</p>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="contact-section">
            <h2>Get in Touch</h2>
            <div class="contact-grid">
                <div class="contact-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Location</h3>
                    <p>Batangas City, Philippines</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@balaibatangas.com</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-phone"></i>
                    <h3>Phone</h3>
                    <p>+63 123 456 7890</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Reuse your existing footer -->
    <?php include 'footer.php'; ?>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
</body>
</html> 