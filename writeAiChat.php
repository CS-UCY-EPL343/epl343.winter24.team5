<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'session_check.php';
require_once 'db_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in_account.php");
    exit();
}

$user_role = $_SESSION['role'] ?? 'User';
$is_admin = $user_role === 'Admin';
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
    <title>Admin Dashboard - ChatBot</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your existing styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                <?php endif; ?>
                <li>
                    <a href="create_tasks_user.php"
                        class="<?= basename($_SERVER['PHP_SELF']) === 'create_tasks_user.php' ? 'active' : ''; ?>">
                        Create Task
                    </a>
                </li>
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
                <h1>ChatBot</h1>
            </div>
            <div class="chat-container" style="text-align: center; margin-top: 50px;">
                <iframe src="https://www.yeschat.ai/i/gpts-2OToA80DRo-Write-For-Me" width="800" height="500"
                    style="max-width: 100%; border: 1px solid #ccc; border-radius: 10px;">
                </iframe>
            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>
</body>

</html>