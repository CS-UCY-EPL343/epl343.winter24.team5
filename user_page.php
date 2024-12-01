<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
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
                <!-- Poll Card -->
                <div class="poll-card1">
                    <h3 class="poll-title">Poll Title 1</h3>
                    <p class="poll-description">Description of the poll goes here. It gives an overview of the poll's context.</p>
                    <p class="poll-votes">Votes: Yes 20% | No 80%</p>
                    <a href="pollpage.php?poll_id=1" class="poll-button">View</a>
                </div>
                <div class="poll-card1">
                    <h3 class="poll-title">Poll Title 2</h3>
                    <p class="poll-description">Another poll description, offering details about the poll's purpose.</p>
                    <p class="poll-votes">Votes: Yes 60% | No 40%</p>
                    <a href="pollpage.php?poll_id=2" class="poll-button">View</a>
                </div>
                <div class="poll-card1">
                    <h3 class="poll-title">Poll Title 3</h3>
                    <p class="poll-description">This is a description for the third poll.</p>
                    <p class="poll-votes">Votes: Yes 75% | No 25%</p>
                    <a href="pollpage.php?poll_id=3" class="poll-button">View</a>
                </div>
                <div class="poll-card1">
                    <h3 class="poll-title">Poll Title 4</h3>
                    <p class="poll-description">This is a description for the fourth poll.</p>
                    <p class="poll-votes">Votes: Yes 55% | No 45%</p>
                    <a href="pollpage.php?poll_id=4" class="poll-button">View</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
