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

// Initialize variables
$searchTerm = $_GET['search'] ?? ''; // Retrieve search term from the query string
$tasks = [];

// Retrieve tasks based on search term
try {
    if ($searchTerm) {
        $tasks = searchTasksByTitle($searchTerm); // Assuming `searchTasksByTitle` is defined in db_functions.php
    } else {
        $tasks = getAllTasks(); // Retrieve all tasks if no search term
    }
} catch (PDOException $e) {
    $error = handleSqlError($e);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tasks</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your styles.css -->
</head>
<body>
    <!-- Wrapper -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">Admin Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="create_tasks.php">Create a Task</a></li> 
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="pending_user_approvals.php">User Approvals</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php" class="active">Tasks</a></li> 
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <h1>View Tasks</h1>
            
            <!-- Search Form -->
            <form method="GET" action="Tasks.php" class="search-form">
                <input type="text" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit">Search</button>
            </form>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($tasks)): ?>
                <div class="task-list">
                    <?php foreach ($tasks as $task): ?>
                        <table class="task-table">
                            <tr>
                                <th>Title</th>
                                <td><?= htmlspecialchars($task['Title']) ?></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td><?= htmlspecialchars($task['Description']) ?></td>
                            </tr>
                            <tr>
                                <th>Due Date</th>
                                <td><?= htmlspecialchars($task['Date_Due']) ?></td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td><?= htmlspecialchars($task['Created_By']) ?></td>
                            </tr>
                        </table>
                        <br>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No tasks available.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
