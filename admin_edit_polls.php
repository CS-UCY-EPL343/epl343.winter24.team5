<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['poll_id']) || !is_numeric($_GET['poll_id'])) {
    header("Location: admin_edit_polls.php");
    exit();
}

$pollId = intval($_GET['poll_id']);
$pollDetails = getPollDetails($pollId);

if (!$pollDetails) {
    echo "Poll not found.";
    exit();
}

$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? $pollDetails['Title'];
    $description = $_POST['description'] ?? $pollDetails['Description'];
    $expirationDate = null;

    // Validate and parse the expiration date
    if (isset($_POST['expiration_date']) && !empty($_POST['expiration_date'])) {
        // Attempt to parse the input date
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['expiration_date']);
        if ($dateTime) {
            $expirationDate = $dateTime->format('Y-m-d H:i:s');
        } else {
            $error = "Invalid expiration date format. Please use a valid date.";
        }
    } else {
        $error = "Expiration date is required.";
    }

    if (!isset($error)) {
        try {
            // Update the poll
            editPoll($pollId, $title, $description, $expirationDate);
            $success = "Poll updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating poll: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Poll</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Wrapper -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">Admin Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="pending_user_approvals.php" class="active">User Approvals</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="form-container-large">
            <div class="form-actions" style="text-align: right;">
                    <a href="admin_page.php" class="poll-button">Back to Admin Page</a>
                </div>
                <h1>Edit Poll</h1>
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
                        <input type="text" name="title" id="title" class="form-control" maxlength="40" required
                            value="<?= htmlspecialchars($pollDetails['Title']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" maxlength="255"
                            required><?= htmlspecialchars($pollDetails['Description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="expiration_date">Expiration Date</label>
                        <input type="datetime-local" name="expiration_date" id="expiration_date" class="form-control"
                            required
                            value="<?= isset($pollDetails['Expiration_Date']) ? date('Y-m-d\TH:i', strtotime($pollDetails['Expiration_Date'])) : '' ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit">Update Poll</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>
