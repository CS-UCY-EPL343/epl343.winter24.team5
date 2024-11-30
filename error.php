<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "navbar.php";

// Get the error message from the session
$error_message = $_SESSION['error_message'] ?? 'An unexpected error occurred.';
unset($_SESSION['error_message']); // Clear the error message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="error-container">
        <h2>Error</h2>
        <p><?= htmlspecialchars($error_message) ?></p>
        <div class="button-container">
        <button onclick="history.back()">Go Back</button>
        </div>
    </div>
</body>
</html>
