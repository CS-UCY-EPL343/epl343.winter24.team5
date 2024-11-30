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
    <title>Bank IT Department</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Main Content -->
    <div class="wrapper1">
        <div class="hero1">
                <h1>Welcome to the ACLouRNeCHDem System</h1>
                <p>Sign in now to access your dashboard.</p>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
</body>
</html>