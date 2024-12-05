<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$instanceID = $_GET['Job_Instance_ID'] ?? null;
$errorMessage = '';
$successMessage = '';
$instance = [];

// Fetch the existing instance details
if ($instanceID) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT * FROM JOB_INSTANCE WHERE Job_Instance_ID = :InstanceID AND Creator_ID = :CreatorID");
        $stmt->bindParam(':InstanceID', $instanceID, PDO::PARAM_INT);
        $stmt->bindParam(':CreatorID', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $instance = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$instance) {
            $errorMessage = "Job instance not found or you do not have permission to edit it.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Database error: " . htmlspecialchars($e->getMessage());
    }
}

// Handle the form submission for updating the instance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$errorMessage) {
    // Retrieve and sanitize POST data
    $scheduleTimeInput = $_POST['schedule_time'] ?? null;
    $recurrence = $_POST['recurrence'] ?? 'None'; // Default to 'None' if not set
    $recurrenceTimeInput = $_POST['recurrence_time'] ?? null;
    $triggeredBy = $_POST['triggered_by'] ?? null;

    // Convert inputs to desired formats or null
    $scheduleTime = !empty($scheduleTimeInput) ? date('Y-m-d H:i:s', strtotime($scheduleTimeInput)) : null;
    $recurrenceTime = !empty($recurrenceTimeInput) ? date('Y-m-d H:i:s', strtotime($recurrenceTimeInput)) : null;

    // If recurrence is 'None', ensure recurrenceTime is null
    if ($recurrence === 'None') {
        $recurrenceTime = null;
    }

    // Initialize an array to collect error messages
    $errors = [];

    // Validation Rules

    // Rule 1: Both schedule time and recurrence (other than 'None') cannot be set simultaneously
    if (!empty($scheduleTime) && $recurrence !== 'None') {
        $errors[] = "Both schedule time and recurrence cannot be provided simultaneously.";
    }

    // Rule 2: Both schedule time and recurrence time cannot be set simultaneously
    if (!empty($scheduleTime) && !empty($recurrenceTime)) {
        $errors[] = "Both schedule time and recurrence time cannot be provided simultaneously.";
    }

    // Rule 3: If recurrence is not 'None', recurrence time must be provided
    if ($recurrence !== 'None' && empty($recurrenceTime)) {
        $errors[] = "Recurrence time must be provided when recurrence is set.";
    }

    // Rule 4: If recurrence is 'None', recurrence time should not be set
    if ($recurrence === 'None' && !empty($recurrenceTime)) {
        $errors[] = "Recurrence time should not be provided when recurrence is set to 'None'.";
    }

    // Additional validations can be added here as needed

    if (count($errors) > 0) {
        // Concatenate all error messages
        $errorMessage = implode("<br>", $errors);
    } else {
        try {
            // Prepare the UPDATE statement
            $stmt = $pdo->prepare("
                UPDATE JOB_INSTANCE
                SET Schedule_Time = :ScheduleTime,
                    Recurrence = :Recurrence,
                    Recurrence_Time = :RecurrenceTime,
                    Triggered_By = :TriggeredBy
                WHERE Job_Instance_ID = :InstanceID AND Creator_ID = :CreatorID
            ");

            // Bind parameters
            $stmt->bindParam(':InstanceID', $instanceID, PDO::PARAM_INT);
            $stmt->bindParam(':CreatorID', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':ScheduleTime', $scheduleTime, $scheduleTime ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':Recurrence', $recurrence !== 'None' ? $recurrence : null, $recurrence !== 'None' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':RecurrenceTime', $recurrenceTime, $recurrenceTime ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':TriggeredBy', $triggeredBy, PDO::PARAM_STR);

            // Execute the statement
            $stmt->execute();

            // Log the modification
            $logTitle = "Job Instance Modified";
            $logDescription = "Job Instance ID {$instanceID} was modified by User ID {$user_id}.";

            // Insert log entry
            insertJobInstanceLog($pdo, $instanceID, $logTitle, $logDescription);

            // Update was successful
            $successMessage = "Job instance updated successfully.";

            // Redirect to the job_instance page after successful update
            header("Location: job_instance.php?Job_Configuration_ID=" . urlencode($instance['Job_Configuration_ID']));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Failed to update job instance: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Existing head content... -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Instance</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Existing styles... */
        .go-back-button {
            display: inline-block;
            margin: 15px 0;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }

        .go-back-button:hover {
            background-color: #0056b3;
        }

        .go-back-container {
            margin-top: 10px;
            margin-left: 0;
        }
    </style>
    <script>
        // JavaScript to handle conditional display and clearing of recurrence_time
        document.addEventListener('DOMContentLoaded', function() {
            const recurrenceSelect = document.getElementById('recurrence');
            const recurrenceTimeRow = document.getElementById('recurrence_time_row');
            const recurrenceTimeInput = document.getElementById('recurrence_time');

            function toggleRecurrenceTime() {
                if (recurrenceSelect.value === 'None') {
                    recurrenceTimeRow.style.display = 'none';
                    recurrenceTimeInput.value = ''; // Clear the value
                } else {
                    recurrenceTimeRow.style.display = 'table-row';
                }
            }

            recurrenceSelect.addEventListener('change', toggleRecurrenceTime);

            // Initialize on page load
            toggleRecurrenceTime();
        });
    </script>
</head>

<body>
    <div class="dashboard-container">
        <main class="dashboard-main">
            <!-- Go Back Button -->
            <div class="go-back-container">
                <a href="job_instance.php?Job_Configuration_ID=<?= htmlspecialchars($instance['Job_Configuration_ID'] ?? ''); ?>" class="go-back-button">Go Back</a>
            </div>

            <div class="config-container">
                <div class="config-box">
                    <h1>Edit Job Instance</h1>

                    <?php if ($errorMessage): ?>
                        <div class="error-message"><?= $errorMessage; ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                        <div class="success-message"><?= htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>

                    <form action="edit_instance.php?Job_Instance_ID=<?= htmlspecialchars($instanceID); ?>" method="POST">
                        <table class="config-table">
                            <tr>
                                <td><label for="schedule_time">Schedule Time:</label></td>
                                <td>
                                    <input type="datetime-local" id="schedule_time" name="schedule_time" value="<?= htmlspecialchars(!empty($instance['Schedule_Time']) ? date('Y-m-d\TH:i', strtotime($instance['Schedule_Time'])) : ''); ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="recurrence">Recurrence:</label></td>
                                <td>
                                    <select id="recurrence" name="recurrence">
                                        <option value="None" <?= (empty($instance['Recurrence']) || $instance['Recurrence'] === 'None') ? 'selected' : ''; ?>>None</option>
                                        <option value="Daily" <?= ($instance['Recurrence'] === 'Daily') ? 'selected' : ''; ?>>Daily</option>
                                        <option value="Weekly" <?= ($instance['Recurrence'] === 'Weekly') ? 'selected' : ''; ?>>Weekly</option>
                                        <option value="Monthly" <?= ($instance['Recurrence'] === 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="recurrence_time_row">
                                <td><label for="recurrence_time">Recurrence Time:</label></td>
                                <td>
                                    <input type="datetime-local" id="recurrence_time" name="recurrence_time" value="<?= htmlspecialchars(!empty($instance['Recurrence_Time']) ? date('Y-m-d\TH:i', strtotime($instance['Recurrence_Time'])) : ''); ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="triggered_by">Triggered By:</label></td>
                                <td>
                                    <select id="triggered_by" name="triggered_by" required>
                                        <option value="System" <?= ($instance['Triggered_By'] === 'System') ? 'selected' : ''; ?>>System (Automatically)</option>
                                        <option value="User" <?= ($instance['Triggered_By'] === 'User') ? 'selected' : ''; ?>>User (Manually)</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <button type="submit" class="configure-button">Update Job Instance</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>

</body>

</html>