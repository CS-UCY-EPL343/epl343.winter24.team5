<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "navbar.php";

// Get the success message from the session
$success_message = $_SESSION['success_message'] ?? 'Operation completed successfully.';
unset($_SESSION['success_message']); // Clear the success message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Success Container -->
    <div class="error-container">
        <h2>Success</h2>
        <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
        <div class="button-container">
            <button onclick="window.location.href='admin_page.php'">Back to Home Page</button>
        </div>
    </div>
</body>
</html>
