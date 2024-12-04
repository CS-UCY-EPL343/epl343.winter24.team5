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
            // Get poll title and user details
            $pollTitle = getPollTitleById($pollId);
            $userDetails = getUserDetails($userId); // Fetch user details

            // Send poll invitation email
            if ($userDetails && sendPollInvitationEmail(
                $userDetails['Email_Address'],
                $userDetails['First_Name'] . ' ' . $userDetails['Last_Name'],
                $pollTitle
            )) {
                $success = "User successfully added to the poll and notified via email!";
            } else {
                $error = "User added to the poll, but the email could not be sent.";
            }
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
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">Admin Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="create_tasks.php">Create a Task</a></li> 
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="pending_user_approvals.php" class="active">User Approvals</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php">Tasks</a></li> 
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="content-box2">
                <!-- Title -->
                <h2 class="poll-title">Add Users to Poll</h2>

                <!-- Success/Error Messages -->
                <?php if (isset($success)): ?>
                    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <!-- Poll Information -->
                <p class="poll-description">Poll ID: <?= htmlspecialchars($pollId) ?></p>

                <!-- Form -->
                <form method="post" action="">
                    <div class="poll-option-group">
                        <label for="user_id" class="poll-description">Select User:</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['User_ID']) ?>">
                                    <?= htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name'] . ' (' . $user['Username'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Add User</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>
</body>
</html>
