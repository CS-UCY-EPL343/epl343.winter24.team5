<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_role = $_SESSION['role'] ?? 'User';
$is_admin = $user_role === 'Admin';
$user_id = $_SESSION['user_id'];

// Initialize variables
$tasks = [];

try {
    // Retrieve assigned tasks
    $tasks = getAssignedTasks($user_id);
} catch (PDOException $e) {
    $error = handleSqlError($e);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Tasks</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your styles.css -->
</head>

<style>
    .dashboard-main {
        padding: 20px;
        background-color: #f4f4f9;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .task-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .task-card {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        width: 300px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .task-card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }

    .task-header {
        background-color: #007bff;
        color: white;
        padding: 15px;
        border-radius: 10px 10px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .task-header h2 {
        font-size: 1.2rem;
        margin: 0;
    }

    .task-header .task-date {
        font-size: 0.9rem;
    }

    .task-body {
        padding: 15px;
        color: #555;
    }

    .task-body p {
        margin: 10px 0;
    }

    .sidebar-links a.active {
        background-color: #6db4ff;
    }
</style>

<body>
    <!-- Wrapper -->
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
                    <a href="assigned_tasks.php"
                        class="<?= basename($_SERVER['PHP_SELF']) === 'assigned_tasks.php' ? 'active' : ''; ?>">
                        Assigned Tasks
                    </a>
                </li>
                <li>
                    <a href="create_tasks.php"
                        class="<?= basename($_SERVER['PHP_SELF']) === 'create_tasks.php' ? 'active' : ''; ?>">
                        Create a Task
                    </a>
                </li>
                <li>
                    <a href="writeAiChat.php"
                        class="<?= basename($_SERVER['PHP_SELF']) === 'writeAiChat.php' ? 'active' : ''; ?>">
                        ChatBot
                    </a>
                </li>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <h1>Assigned Tasks</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($tasks)): ?>
                <div class="task-list">
                    <?php foreach ($tasks as $task): ?>
                        <div class="task-card">
                            <div class="task-header">
                                <h2><?= htmlspecialchars($task['Title']) ?></h2>
                                <span class="task-date">Due: <?= htmlspecialchars($task['Date_Due']) ?></span>
                            </div>
                            <div class="task-body">
                                <p><strong>Description:</strong> <?= htmlspecialchars($task['Description']) ?></p>
                                <p><strong>Assigned By:</strong> <?= htmlspecialchars($task['AssignedBy']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No assigned tasks available.</p>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>