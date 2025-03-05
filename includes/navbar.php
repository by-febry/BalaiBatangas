<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

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

            <!-- Cart Icon -->
            <li class="cart-icon">
                <a href="#" id="cartButton">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
            </li>

            <!-- User Profile Section -->
            <div class="user-menu">
                <?php if (!empty($profile_picture)): ?>
                    <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture" class="user-icon">
                <?php else: ?>
                    <div class="user-icon">
                        <?php echo strtoupper($username[0]); ?>
                    </div>
                <?php endif; ?>

                <!-- Dropdown Menu -->
                <div class="dropdown-content">
                    <p><?php echo $username; ?></p>
                    <a href="userpanel.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </ul>
    </nav>
</header>

<link rel="stylesheet" href="css/navbar.css"> 