<?php
require_once 'navbar.php';

if (isset($_SESSION['user_id'])) {
    // Log out the user
    session_unset(); // Clear session variables
    session_destroy(); // Destroy the session

    // Redirect to the index page to prevent further access
    header("Location: index.php");
    exit();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Home Page</title>
    <link rel="stylesheet" href="styles.css"> 
</head>

<body>
    <div class="wrapper1">
        <!-- Main Content -->
        <div class="main-content1">
            <!-- Hero Section -->
            <div class="hero1">
                <h1>Welcome to EV Manager</h1>
                <p>
                    Join our mission to create a cleaner and greener future by eliminating environmentally harmful vehicles and providing grants to support the purchase of low CO2 emission vehicles.
                </p>
            </div>

            <!-- Content Section -->
            <div class="content1">
                <div class="content-box1">
                    <h2>Why EV Manager?</h2>
                    <p>We aim to reduce vehicle emissions and improve air quality by enabling individuals and businesses to transition to sustainable electric vehicles.</p>
                </div>
                <div class="content-box1">
                    <h2>Grant Opportunities</h2>
                    <p>Explore a variety of grants tailored to help you afford your dream electric vehicle while contributing to a cleaner planet.</p>
                </div>
                <div class="content-box1">
                    <h2>Environment Impact</h2>
                    <p>Scrap old, polluting vehicles and join the movement towards a zero-emission future with our support and resources.</p>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
</body>
</html>
