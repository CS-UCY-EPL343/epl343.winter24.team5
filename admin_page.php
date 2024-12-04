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
<style>
.sidebar {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
    background-color: #f8f9fa;
    /* Adjust background as needed */
    padding: 10px;
}

.sidebar-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    text-align: left;
}

.sidebar-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-links li {
    margin-bottom: 0.5rem;
}

.sidebar-links a {
    text-decoration: none;
    color: #000;
    font-size: 1rem;
    transition: color 0.2s ease;
}

.sidebar-links a:hover {
    color: #007bff;
}

.sidebar-bottom {
    text-align: center;
    margin-top: auto;
    /* Push to the bottom */
}

    .sidebar-link {
        display: inline-block;
        text-decoration: none;
        text-align: center;
    }

    .sidebar-icon {
        width: 100px;
        /* Increase width */
        height: 100px;
        /* Increase height */
        transition: transform 0.2s ease;
    }

.sidebar-link:hover .sidebar-icon {
    transform: scale(1.2);
}
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title style="text-align: center;">Admin Dashboard</title>
    <link rel=" stylesheet" href="styles.css"> <!-- Link to your existing styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
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

            <!-- SVG at the bottom -->
            <div class="sidebar-bottom">
                <a href="admin_easter_egg.html" class="sidebar-link">
                    <img src="videos/dinoegg.png" alt="Dino Egg" class="sidebar-icon">
                </a>
            </div>
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
                        <a href="admin_view_polls.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>"
                            class="poll-button">View</a>
                        <a href="admin_edit_polls.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>"
                            class="poll-button-yellow">Edit</a>
                        <form method="POST">
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