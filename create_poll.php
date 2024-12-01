<?php
require_once 'db_functions.php'; // Include your database functions
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Content Section -->
    <div>
        <h1>Create Poll</h1>

        <?php if (isset($success)): ?>
            <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <div>
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" maxlength="40" required>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea name="description" id="description" maxlength="255" required></textarea>
            </div>
            <div>
                <label for="expiration_date">Expiration Date:</label>
                <input type="datetime-local" name="expiration_date" id="expiration_date" required>
            </div>
            <div>
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div>
                <button type="submit">Create Poll</button>
            </div>
        </form>
    </div>
</body>
</html>
