<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Determine the homepage link dynamically based on the user's role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Φορέας Υλοποίησης') {
        $homePage = 'root_page.php';
    } elseif ($_SESSION['role'] === 'Αιτητής/Χρήστης') {
        $homePage = 'user_page.php';
    } elseif ($_SESSION['role'] === 'Αντιπρόσωπος Αυτοκινήτων') {
        $homePage = 'car_dealer_page.php';
    } elseif ($_SESSION['role'] === 'Λειτουργός Τµήµατος Οδικών Μεταφορών') {
        $homePage = 'TOM_page.php';
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
        <a href="<?= $homePage ?>">EV Manager</a>
    </div>

    <!-- Common navigation links -->
    <div class="nav-links-left">
        <a href="about.php">About</a>
        <a href="grant_categories.php">Grant Categories</a>
        <a href="faq.php">FAQ</a>
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

