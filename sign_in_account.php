<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_functions.php';
require_once 'navbar.php';

$error_message = '';
if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials') {
    $error_message = 'Incorrect username or password. Please try again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $_SESSION['user_id'] = getUserId($username);
    $user_id = $_SESSION['user_id'];
   
    $role = userLogin($username,$password);

    // Save necessary data in session
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;

    // Redirect based on role
    if ($role === 'Φορέας Υλοποίησης') {
        header("Location: root_page.php");
        exit();
    } else if ($role === 'Αιτητής/Χρήστης') {
        header("Location: user_page.php");
        exit();
    } else if($role === 'Αντιπρόσωπος Αυτοκινήτων') {
        header("Location: car_dealer_page.php");
        exit();
    }
    else if($role === 'Λειτουργός Τµήµατος Οδικών Μεταφορών') {
        header("Location: Tom_page.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign in Page</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external stylesheet -->
</head>

<body>

    <!-- Sign In Form -->
    <div class="login-container">
        <h2>Sign in to your Account</h2>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="action" value="login">Sign In</button>
        </form>

        <div class="create-account">
            <a href="create_account.php">Create Account</a>
        </div>
    </div>
</body>
</html>
