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

try {
    // Call the GetAllPolls stored procedure
    $polls = getAllPolls(); // Assuming getAllPolls() is defined in db_functions.php
} catch (PDOException $e) {
    $error = handleSqlError($e);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poll_id'])) {
    $pollID = intval($_POST['poll_id']);
    updatePollStatusAndVerdict($pollID);
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh the page
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your existing styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <div class="dashboard-header">
                <h1>Polls</h1>
            </div>
            <div class="poll-container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php elseif (empty($polls)): ?>
                    <p>No polls available.</p>
                <?php else: ?>
                    <?php foreach ($polls as $poll): ?>
                        <div class="poll-card1" style="position: relative;">
                            <?php if ($poll['Status'] !== 'Finished'): ?>
                                <!-- Add Users Button -->
                                <a href="add_users_to_poll.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>"
                                    style="position: absolute; top: 10px; right: 10px; font-size: 1.5rem; color: black; cursor: pointer;"
                                    title="Add Users to Poll">
                                    <i class="bi bi-person-fill-add"></i>
                                </a>
                                <h3 class="poll-title"><?= htmlspecialchars($poll['Title']) ?></h3>
                                <p class="poll-description"><?= htmlspecialchars($poll['Description']) ?></p>
                                <p class="poll-votes">Expiration: <?= htmlspecialchars($poll['Expiration_Date']) ?></p>
                                <div class="poll-actions">
                                    <a href="admin_view_polls.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="poll-button">View</a>
                                    <a href="admin_edit_polls.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="poll-button-yellow">Edit</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="poll_id" value="<?= htmlspecialchars($poll['Poll_ID']) ?>">
                                        <button type="submit" class="poll-button-red">Finish Poll</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <h3 class="poll-title"><?= htmlspecialchars($poll['Title']) ?></h3>
                                <p class="poll-description"><?= htmlspecialchars($poll['Description']) ?></p>
                                <p class="poll-votes">Expiration: <?= htmlspecialchars($poll['Expiration_Date']) ?></p>
                                <span class="poll-status">Poll Finished</span>
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