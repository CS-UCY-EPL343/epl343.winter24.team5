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
<style>
    .sidebar-links a.active {
        background-color: #6db4ff;
    }
</style>

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
                <!-- Common Links -->
                <li>
                    <a href="<?= $is_admin ? 'admin_page.php' : 'user_page.php'; ?>"
                        class="<?= basename($_SERVER['PHP_SELF']) === ($is_admin ? 'admin_page.php' : 'user_page.php') ? 'active' : ''; ?>">
                        Polls
                    </a>
                </li>
                <li>
                    <a href="jobs.php"
                        class="<?= basename($_SERVER['PHP_SELF']) === 'jobs.php' ? 'active' : ''; ?>">
                        Jobs
                    </a>
                </li>
                <li>
                    <a href="Tasks.php"
                        class="<?= basename($_SERVER['PHP_SELF']) === 'Tasks.php' ? 'active' : ''; ?>">
                        Tasks
                    </a>
                </li>
                <li>
                    <a href="writeAiChat.php"
                        class="<?= basename($_SERVER['PHP_SELF']) === 'writeAiChat.php' ? 'active' : ''; ?>">
                        ChatBot
                    </a>
                </li>

                <!-- Admin-Only Links -->
                <?php if ($is_admin): ?>
                    <li>
                        <a href="create_poll.php"
                            class="<?= basename($_SERVER['PHP_SELF']) === 'create_poll.php' ? 'active' : ''; ?>">
                            Create Poll
                        </a>
                    </li>
                    <li>
                        <a href="create_tasks.php"
                            class="<?= basename($_SERVER['PHP_SELF']) === 'create_tasks.php' ? 'active' : ''; ?>">
                            Create a Task
                        </a>
                    </li>
                    <li>
                        <a href="pending_user_approvals.php"
                            class="<?= basename($_SERVER['PHP_SELF']) === 'pending_user_approvals.php' ? 'active' : ''; ?>">
                            User Approvals
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="create_tasks_user.php"
                            class="<?= basename($_SERVER['PHP_SELF']) == 'create_tasks_user.php' ? 'active' : ''; ?>">Create Task</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="#settings"
                        class="<?= basename($_SERVER['PHP_SELF']) === '#settings' ? 'active' : ''; ?>">
                        Settings
                    </a>
                </li>
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
                            <p class="poll-votes">Votes: Yes <?= htmlspecialchars($poll['Votes_For']) ?> | No
                                <?= htmlspecialchars($poll['Votes_Against']) ?></p>
                            <?php if ($poll['Status'] === "Finished"): ?>
                                <p class="poll-status">Poll has concluded. Final Result: <?php if ($poll['Final_Verdict'] == 1): ?>
                                        Decision Will Go Through
                                    <?php else: ?>
                                        Decision Will Not Go Through
                                    <?php endif; ?> </p>

                            <?php else: ?>
                                <a href="pollpage.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>"
                                    class="poll-button">Vote</a>
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