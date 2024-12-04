<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Handle POST request for creating a poll
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['expiration_date'], $_POST['status'])) {
    $creatorId = $_SESSION['user_id']; // Assuming user_id is stored in the session
    $title = $_POST['title'];
    $description = $_POST['description'];
    $expirationDate = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['expiration_date'])->format('Y-m-d H:i:s'); // Convert to SQL DATETIME
    $status = $_POST['status'];

    try {
        if (createPoll($creatorId, $title, $description, $expirationDate, $status)) {
            $success = "Poll successfully created!";
        } else {
            $error = "Failed to create the poll.";
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
    <title>Create Poll</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your styles.css -->
</head>

<body>
    <!-- Wrapper -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">Admin Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php">Tasks</a></li>
                <li><a href="writeAiChat.php">ChatBot</a></li>
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="create_tasks.php">Create a Task</a></li>
                <li><a href="pending_user_approvals.php">User Approvals</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>


        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="form-container-large">
                <div style="text-align: right; margin-top: 5px;">
                    <a href="admin_page.php" class="poll-button">Back to Admin Page</a>
                </div>

                <h1>Create Poll</h1>
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
                        <textarea name="description" id="description" class="form-control" maxlength="255"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="expiration_date">Expiration Date</label>
                        <input type="datetime-local" name="expiration_date" id="expiration_date" class="form-control"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit">Create Poll</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>