<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in_account.php"); // Redirect to login if not authenticated
    exit();
}

$jobId = $_GET['Job_ID'] ?? null;


$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    // Retrieve Job_ID and user_id from POST request
    $scheduleTime = $_POST['schedule_time'] ?? null;
    $recurrence = $_POST['recurrence'] ?? null;

    if (!$jobId || !$user_id) {
        header("Location: jobs.php"); // Redirect to jobs page if any ID is missing
        exit();
    }

    $configName = $_POST['config_name'] ?? null;
    $param1 = $_POST['param1'] ?? ''; // Get param1 or default to empty string
    $param2 = $_POST['param2'] ?? ''; // Get param2 or default to empty string

    // Check if both parameters are not empty
    if (!empty($param1) && !empty($param2)) {
        // Concatenate both parameters with a delimiter (e.g., comma or space)
        $parameters = $param1 . ' , ' . $param2; // Concatenate with a space, or use another delimiter
    } else {
        // If one or both parameters are empty, use only the non-empty one
        $parameters = !empty($param1) ? $param1 : $param2;
    }

    // Ensure that configuration name is provided
    if (empty($configName)) {
        $errorMessage = "Configuration Name is required.";
    } else {
        // Call the function to insert the job configuration
        $result = insertJobConfiguration($jobId, $user_id, $configName, $parameters, $scheduleTime, $recurrence);

        if ($result === null) {
            $successMessage = "Configuration successfully created.";
        } else {
            $errorMessage = "Error: Configuration could not be created.";
        }
    }
    echo $errorMessage;
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configure Job</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
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
            <div class="config-container">
                <div class="config-box">
                    <h1>Configure Job (ID: <?= htmlspecialchars($jobId); ?>)</h1>

                    <!-- Display Success or Error Messages -->
                    <?php if (isset($successMessage)): ?>
                        <div class="success-message"><?= htmlspecialchars($successMessage); ?></div>
                    <?php elseif (isset($errorMessage)): ?>
                        <div class="error-message"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>

                    <!-- Job Configuration Form -->
                    <form action="configuration.php" method="POST">
                        <table class="config-table">
                            <tr>
                                <td><label for="config_name">Configuration Name:</label></td>
                                <td><input type="text" id="config_name" name="config_name" value="<?= htmlspecialchars($_POST['config_name'] ?? ''); ?>" required></td>
                            </tr>
                            <tr>
                                <td><label for="param1">Parameter 1:</label></td>
                                <td><input type="text" id="param1" name="param1" value="<?= htmlspecialchars($_POST['param1'] ?? ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="param2">Parameter 2:</label></td>
                                <td><input type="text" id="param2" name="param2" value="<?= htmlspecialchars($_POST['param2'] ?? ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="schedule_time">Schedule Time:</label></td>
                                <td><input type="datetime-local" id="schedule_time" name="schedule_time" value="<?= htmlspecialchars($_POST['schedule_time'] ?? ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="recurrence">Recurrence:</label></td>
                                <td>
                                    <select id="recurrence" name="recurrence">
                                        <option value="" <?= isset($_POST['recurrence']) && $_POST['recurrence'] === '' ? 'selected' : ''; ?>>None</option>
                                        <option value="Daily" <?= isset($_POST['recurrence']) && $_POST['recurrence'] === 'Daily' ? 'selected' : ''; ?>>Daily</option>
                                        <option value="Weekly" <?= isset($_POST['recurrence']) && $_POST['recurrence'] === 'Weekly' ? 'selected' : ''; ?>>Weekly</option>
                                        <option value="Monthly" <?= isset($_POST['recurrence']) && $_POST['recurrence'] === 'Monthly' ? 'selected' : ''; ?>>Monthly</option>
                                    </select>
                                </td>
                            </tr>
                        </table>

                        <button type="submit" name="submit_config">Submit Configuration</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>