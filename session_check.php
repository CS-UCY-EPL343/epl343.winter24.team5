<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in_account.php");
    exit();
}

// Include navbar
require_once 'navbar.php';
?>
