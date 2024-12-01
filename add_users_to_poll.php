<?php
require_once 'db_functions.php'; // Include your database functions
require_once 'navbar.php'; // Include navbar
require_once 'session_check.php';

// Ensure only 'Φορέας Υλοποίησης' (Admin) can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Get the poll ID from the GET parameter or session
if (!isset($_GET['poll_id']) || !is_numeric($_GET['poll_id'])) {
    header("Location: select_poll.php"); // Redirect to poll selection page if poll ID is missing
    exit();
}
$pollId = intval($_GET['poll_id']); // Get the selected poll ID

// Handle POST request for adding a user to the poll
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    try {
        if (addUserToPoll($pollId, $userId)) {
            $polltitle=getPollTitleById($pollId);
            sendPollInvitationEmail($users['Email_Address'],$user['First_Name'] . ' ' . $user['Last_Name'], $polltitle);
            $success = "User successfully added to the poll!";
        } else {
            $error = "Failed to add the user to the poll.";
        }
    } catch (PDOException $e) {
        $error = handleSqlError($e);
    }
}



// Fetch users for selection
try {
    $users = getAllUsers($pollId); // Fetch all users
} catch (PDOException $e) {
    $error = handleSqlError($e);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Users to Poll</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Content Section -->
    <div>
        <h1>Add Users to Poll</h1>

        <p>Poll ID: <?= htmlspecialchars($pollId) ?></p>

        <?php if (isset($success)): ?>
            <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <div>
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['User_ID']) ?>">
                            <?= htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name'] . ' (' . $user['Username'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit">Add User</button>
            </div>
        </form>
    </div>
</body>
</html>
