<?php
require_once 'session_check.php'; // Include session management and navbar


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Voting Setup</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
</head>
<body>
    <div class="container">
        <h1>Admin Voting Setup</h1>
        
        <!-- Form to Create a New Voting Session -->
        <form action="create-vote.php" method="POST">
            <h2>Create New Voting Session</h2>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="start_time">Start Time:</label>
            <input type="datetime-local" id="start_time" name="start_time" required>

            <label for="end_time">End Time:</label>
            <input type="datetime-local" id="end_time" name="end_time" required>

            <button type="submit">Create Voting Session</button>
        </form>

        <!-- List of Existing Voting Sessions -->
        <h2>Existing Voting Sessions</h2>
        <?php if ($sessions): ?>
            <ul>
                <?php foreach ($sessions as $session): ?>
                    <li>
                        <strong><?= htmlspecialchars($session['title']) ?></strong>
                        <p><?= htmlspecialchars($session['description']) ?></p>
                        <p><em>From:</em> <?= $session['start_time'] ?> <em>To:</em> <?= $session['end_time'] ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No voting sessions available.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>
