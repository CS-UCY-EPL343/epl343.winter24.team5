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

// Handle POST request for creating a task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['date_due'])) {
    $creatorId = $_SESSION['user_id']; // Assuming user_id is stored in the session
    $title = $_POST['title'];
    $description = $_POST['description'];
    $dateDue = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['date_due'])->format('Y-m-d H:i:s'); // Convert to SQL DATETIME

    try {
        if (createTask($creatorId, $title, $description, $dateDue)) {
            $success = "Task successfully created!";
        } else {
            $error = "Failed to create the task.";
        }
    } catch (PDOException $e) {
        $error = handleSqlError($e);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
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
                <li><a href="create_tasks.php" class="active">Create a Task</a></li> 
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="pending_user_approvals.php">User Approvals</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php">Tasks</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="form-container-large">
                <h1>Create Task</h1>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control" maxlength="40" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" maxlength="255" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="date_due">Due Date</label>
                        <input type="datetime-local" name="date_due" id="date_due" class="form-control" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit">Create Task</button>
                    </div>
                </form>
            </div>

            <div class="task-list-container">
               
            </div>
        </main>
    </div>
</body>
</html>