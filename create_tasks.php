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
$role = $_SESSION['role'];
$users = getUsers();
$user_id = $_SESSION['user_id'];
// Handle POST request for creating a task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['date_due'])) {
    $creatorId = $_SESSION['user_id']; // Assuming user_id is stored in the session
    $title = $_POST['title'];
    $description = $_POST['description'];
    $dateDue = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['date_due'])->format('Y-m-d H:i:s'); // Convert to SQL DATETIME
    $usertaskid = $_POST['user_id'];
    try {
        if ($usertaskid == "-1" || $role = 'User')
            if (createTask($creatorId, $title, $description, $dateDue)) {
                $success = "Task successfully created!";
            } else {
                $error = "Failed to create the task.";
            }
        else {
            createTaskForUser($usertaskid, $title, $description, $dateDue, $user_id);
            $success = "Task successfully created!";
        }
    } catch (PDOException $e) {
        $error = handleSqlError($e);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<style>
    .sidebar-links a.active {
        background-color: #175494;
    }

    .sidebar-links a.active:hover {
        background-color: #175494;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your styles.css -->
</head>

<body>
    <!-- Wrapper -->
    <div class="dashboard-container">
        <aside class="sidebar">
            <h3 class="sidebar-title"><?= $role == 'Admin' ? 'Admin Dashboard' : 'User Dashboard'; ?></h3>
            <ul class="sidebar-links">
                <!-- Common Links -->
                <li>
                    <a href="<?= $role == 'Admin' ? 'admin_page.php' : 'user_page.php'; ?>"
                        class="<?= basename($_SERVER['PHP_SELF']) == ($role == 'Admin' ? 'admin_page.php' : 'user_page.php') ? 'active' : ''; ?>">Polls</a>
                </li>
                <li>
                    <a href="jobs.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'active' : ''; ?>">Jobs</a>
                </li>
                <?php if ($role != 'Admin'): ?>
                    <li>
                        <a href="assigned_tasks.php"
                            class="<?= basename($_SERVER['PHP_SELF']) === 'assigned_tasks.php' ? 'active' : ''; ?>">
                            Assigned Tasks
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="Tasks.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'Tasks.php' ? 'active' : ''; ?>">Tasks</a>
                </li>


                <!-- Admin-Only Links -->
                <?php if ($role == 'Admin'): ?>
                    <li>
                        <a href="create_poll.php"
                            class="<?= basename($_SERVER['PHP_SELF']) == 'create_poll.php' ? 'active' : ''; ?>">Create
                            Poll</a>
                    </li>

                    <li>
                        <a href="pending_user_approvals.php"
                            class="<?= basename($_SERVER['PHP_SELF']) == 'pending_user_approvals.php' ? 'active' : ''; ?>">User
                            Approvals</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="create_tasks.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'create_tasks.php' ? 'active' : ''; ?>">Create
                        Task</a>
                </li>
                <li>
                    <a href="writeAiChat.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'writeAiChat.php' ? 'active' : ''; ?>">ChatBot</a>
                </li>
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
                        <input type="text" name="title" id="title" class="form-control" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" class="form-control"
                            style="height: 70px; padding-bottom: 45px;" required>
                    </div>
                    <div class="form-group">
                        <label for="date_due">Due Date</label>
                        <input type="datetime-local" name="date_due" id="date_due" class="form-control" required>
                    </div>
                    <?php if ($role == 'Admin'): ?>
                        <div class="form-group">
                            <select name="user_id" id="user_id" class="form-control" required>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= htmlspecialchars($user['User_ID']) ?>">
                                        <?= htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name'] . ' (' . $user['Username'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="-1">
                                    Personal(Task To Self)
                                </option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="form-actions">
                        <button type="submit">Create Task</button>
                    </div>
                </form>
            </div>

            <div class="task-list-container">

            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>
</body>

</html>