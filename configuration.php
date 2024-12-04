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

// Retrieve Job_ID from GET request
$jobID = $_GET['Job_ID'] ?? null;

// Store the Job_ID in a session if needed
if ($jobID) {
    $_SESSION['job_id'] = $jobID;
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve Job_ID and user_id from POST request
    // Retrieve Job_ID from the POST request or session
    $jobID = $_POST['Job_ID'] ?? $_SESSION['job_id'] ?? null;

    // Retrieve user_id from session
    $user_id = $_SESSION['user_id'];
    $scheduleTime = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['schedule_time'])->format('Y-m-d H:i:s'); // Convert to SQL DATETIME$_POST['schedule_time'] ?? null;
    $recurrence = $_POST['recurrence'] ?? null;
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


    // Call the function to insert the job configuration
    $result = insertJobConfiguration($jobID, $user_id, $configName, $parameters, $scheduleTime, $recurrence);
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
                <li><a href="create_tasks.php">Create a Task</a></li>
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="pending_user_approvals.php" class="active">User Approvals</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php">Tasks</a></li>
                <li><a href="writeAiChat.php">ChatBot</a></li>
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
            <div class="config-container">
                <div class="config-box">
                    <h1>Configure Job</h1>

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
                                <td><input type="text" id="config_name" name="config_name" required></td>
                            </tr>
                            <tr>
                                <td><label for="param1">Parameter 1:</label></td>
                                <td><input type="text" id="param1" name="param1"></td>
                            </tr>
                            <tr>
                                <td><label for="param2">Parameter 2:</label></td>
                                <td><input type="text" id="param2" name="param2"></td>
                            </tr>
                            <tr>
                                <td><label for="schedule_time">Schedule Time:</label></td>
                                <td><input type="datetime-local" id="schedule_time" name="schedule_time"></td>
                            </tr>
                            <tr>
                                <td><label for="recurrence">Recurrence:</label></td>
                                <td>
                                    <select id="recurrence" name="recurrence">
                                        <option value="">None</option>
                                        <option value="Daily">Daily</option>
                                        <option value="Weekly">Weekly</option>
                                        <option value="Monthly">Monthly</option>
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