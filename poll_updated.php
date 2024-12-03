<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

$pollId = $_GET['poll_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Updated</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-container">
        <main class="dashboard-main">
            <div class="form-container-large">
                <h1>Poll Updated Successfully!</h1>
                <p>The poll with ID <?= htmlspecialchars($pollId) ?> has been updated successfully.</p>
                <div class="form-actions">
                    <a href="admin_edit_polls.php" class="btn btn-primary">Back to Polls</a>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
