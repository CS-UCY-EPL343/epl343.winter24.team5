<?php
require_once 'db_functions.php'; // Include your database functions
require_once 'navbar.php'; // Include navbar

// Ensure only 'Φορέας Υλοποίησης' (Admin) can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Get the poll ID from the GET parameter
if (!isset($_GET['poll_id']) || !is_numeric($_GET['poll_id'])) {
    header("Location: polls.php"); // Redirect to polls list if no poll ID is provided
    exit();
}
$pollId = intval($_GET['poll_id']);

// Fetch poll details
$pollDetails = getPollDetails($pollId);

if (!$pollDetails) {
    echo "Poll not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Details - Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h2 class="mb-4"><?= htmlspecialchars($pollDetails['Title']) ?></h2>
        <p class="mb-4"><?= htmlspecialchars($pollDetails['Description']) ?></p>

        <div class="mb-4">
            <h5>Vote Results</h5>
            <p><strong>Yes:</strong> <?= htmlspecialchars($pollDetails['YesVotes']) ?> votes</p>
            <p><strong>No:</strong> <?= htmlspecialchars($pollDetails['NoVotes']) ?> votes</p>
        </div>

        <!-- Back Button -->
        <div class="mb-4">
            <a href="admin_page.php" class="poll-button">Go Back</a>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>
</body>

</html>