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

try{

    $polls = getUserPolls($userID);
} catch( PDOException $e){
    $error = handleSqlError($e);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="#">Polls</a></li>
                <li><a href="#reports">Reports</a></li>
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
                    <p>No polls available for this user.</p>
                <?php else: ?>
                    <?php foreach ($polls as $poll): ?>
                        <div class="poll-card1">
                                <h3 class="poll-title"><?= htmlspecialchars($poll['Title']) ?></h5>
                                <p class="poll-description"><?= htmlspecialchars($poll['Description']) ?></p>
                                <p class="poll-votes">Votes: Yes <?= htmlspecialchars($poll['Votes_For']) ?> | No <?= htmlspecialchars($poll['Votes_Against']) ?></p>
                                <a href="pollpage.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="poll-button">View</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
