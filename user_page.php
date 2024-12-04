<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'session_check.php';
require_once 'db_functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User') {
    header("Location: index.php");
    exit();
}

$userID = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'User';
$is_admin = $user_role === 'Admin';

try {
    $polls = getUserPolls($userID);
} catch (PDOException $e) {
    $error = handleSqlError($e);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title"><?= $is_admin ? 'Admin Dashboard' : 'User Dashboard'; ?></h3>
            <ul class="sidebar-links">
                <li><a href="pollpage.php">Polls</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="writeAiChat.php">ChatBot</a></li>
                <li><a href="#settings">Settings</a></li>
                <?php if ($is_admin): ?>
                    <li><a href="create_poll.php">Create Poll</a></li>
                    <li><a href="create_tasks.php">Create a Task</a></li>
                    <li><a href="pending_user_approvals.php">User Approvals</a></li>
                    <li><a href="Tasks.php">Tasks</a></li>
                <?php endif; ?>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Polls</h1>
            </div>
            <div class="poll-container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php elseif (empty($polls)): ?>
                    <p>No polls available for this user.</p>
                <?php else: ?>
                    <?php foreach ($polls as $poll): ?>
                        <div class="poll-card1">
                            <h3 class="poll-title"><?= htmlspecialchars($poll['Title']) ?></h3>
                            <p class="poll-description"><?= htmlspecialchars($poll['Description']) ?></p>
                            <p class="poll-votes">Votes: Yes <?= htmlspecialchars($poll['Votes_For']) ?> | No <?= htmlspecialchars($poll['Votes_Against']) ?></p>
                            <?php if ($poll['Status'] === "Finished"): ?>
                                <p class="poll-status">Poll has concluded, Final Result: </p><?php htmlspecialchars($poll['Final_Verdict']) ?>
                            <?php else: ?>
                                <a href="pollpage.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="poll-button">Vote</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>
</body>

</html>