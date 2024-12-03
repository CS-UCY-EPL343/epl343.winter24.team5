<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$jobId = isset($_SESSION['Job_ID']) ? $_SESSION['Job_ID'] : null;
$userId = isset($_SESSION['User_ID']) ? $_SESSION['User_ID'] : null;

if (!$jobId || !$userId) {
    // Redirect back to jobs.php or show an error if either is missing
    header("Location: jobs.php");
    exit("Job ID and User ID are required.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configName = $_POST['config_name'];
    $parameters = json_encode([
        'param1' => $_POST['param1'] ?? null,
        'param2' => $_POST['param2'] ?? null,
    ]);
    $scheduleTime = $_POST['schedule_time'] ?? null;
    $recurrence = $_POST['recurrence'] ?? null;

    try {
        // Call the function with job_id and user_id
        $configId = createJobConfiguration($jobId, $userId, $configName, $parameters, $scheduleTime, $recurrence);
        echo "<script>alert('Configuration successfully created with ID: $configId');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Page</title>
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
            <div class="config-container">
                <div class="config-box">
                    <h1>Procedure Configuration</h1>
                    <form action="configuration.php?job_id=<?php echo $jobId; ?>" method="POST">

                        <!-- Configuration Name -->
                        <label for="config_name">Configuration Name:</label>
                        <input type="text" id="config_name" name="config_name" required>

                        <!-- Parameter 1 -->
                        <label for="param1">Parameter 1:</label>
                        <input type="text" id="param1" name="param1">

                        <!-- Parameter 2 -->
                        <label for="param2">Parameter 2:</label>
                        <input type="text" id="param2" name="param2">

                        <!-- Schedule Time -->
                        <label for="schedule_time">Schedule Time:</label>
                        <input type="datetime-local" id="schedule_time" name="schedule_time">

                        <!-- Recurrence -->
                        <label for="recurrence">Recurrence:</label>
                        <select id="recurrence" name="recurrence">
                            <option value="">None</option>
                            <option value="Daily">Daily</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Monthly">Monthly</option>
                        </select>

                        <!-- Submit Button -->

                        <button class="configure-button">Submit Configuration</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
</body>

</html>