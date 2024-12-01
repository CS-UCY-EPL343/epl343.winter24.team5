<?php
require_once 'navbar.php';
require_once 'db_functions.php';
//require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$jobId = isset($_GET['job_id']) ? intval($_GET['job_id']) : null;

if (!$jobId) {
    // Redirect back to the jobs page or show an error if Job_ID is missing
    header("Location: jobs.php");
    exit("Job ID is required.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $userId = $_POST['user_id'];
    $configName = $_POST['config_name'];
    $parameters = json_encode([
        'param1' => $_POST['param1'] ?? null,
        'param2' => $_POST['param2'] ?? null,
    ]); // Convert parameters to JSON
    $scheduleTime = $_POST['schedule_time'] ?? null;
    $recurrence = $_POST['recurrence'] ?? null;

    try {
        // Call the function from db_functions.php to insert the configuration
        $configId = createJobConfiguration($jobId, $userId, $configName, $parameters, $scheduleTime, $recurrence);

        // Display success message
        echo "<script>alert('Configuration successfully created with ID: $configId');</script>";
    } catch (Exception $e) {
        // Display error message
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
    <div class="config-container">
        <div class="config-box">
            <h1>Procedure Configuration</h1>
            <form action="configuration.php?job_id=<?php echo $jobId; ?>" method="POST">
                <!-- User ID Field -->
                <label for="user_id">User ID:</label>
                <input type="number" id="user_id" name="user_id" required>

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
                <button type="submit">Submit Configuration</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
</body>

</html>