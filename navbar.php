<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Determine the homepage link dynamically based on the user's role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'User') {
        $homePage = 'user_page.php';
    } elseif ($_SESSION['role'] === 'Admin') {
        $homePage = 'admin_page.php';
    } else {
        $homePage = 'index.php'; // Default for unknown roles
    }
} else {
    $homePage = 'index.php'; // Default for guests
}
?>

<div class="navbar">
    <!-- Brand that redirects based on user's home page -->
    <div class="brand">
        <a href="<?= $homePage ?>">IT System</a>
    </div>

    <!-- Authentication link -->
    <div class="auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Sign Out</a>
        <?php else: ?>
            <a href="sign_in_account.php">Sign In</a>
        <?php endif; ?>
    </div>
</div>

