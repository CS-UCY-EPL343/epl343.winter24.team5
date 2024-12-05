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
$configurationID = $_GET['Job_Configuration_ID'] ?? null;
$errorMessage = ''; // Initialize variable
$successMessage = ''; // Initialize variable
$jobInstances = [];

// Fetch the job instances for the given configuration
if ($configurationID) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("EXEC GetJobInstances :Job_Configuration_ID, :Creator_ID");
    $stmt->bindParam(':Job_Configuration_ID', $configurationID, PDO::PARAM_INT);
    $stmt->bindParam(':Creator_ID', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $jobInstances = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle Create Job Instance Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_instance') {
    $scheduleTime = $_POST['schedule_time'] ?? null;
    $recurrence = $_POST['recurrence'] ?? 'None'; // Default to 'None' if not set
    $recurrenceTime = $_POST['recurrence_time'] ?? null;
    $triggeredBy = $_POST['triggered_by'] ?? null;

    // Convert inputs to desired formats or null
    $scheduleTime = !empty($scheduleTime) ? date('Y-m-d H:i:s', strtotime($scheduleTime)) : null;
    $recurrenceTime = !empty($recurrenceTime) ? date('Y-m-d H:i:s', strtotime($recurrenceTime)) : null;

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
            $stmt = $pdo->prepare("EXEC InsertJobInstance :Job_Configuration_ID, :Creator_ID, :Schedule_Time, :Recurrence, :Recurrence_Time, :Triggered_By");
            $stmt->bindParam(':Job_Configuration_ID', $configurationID, PDO::PARAM_INT);
            $stmt->bindParam(':Creator_ID', $user_id, PDO::PARAM_INT);

            // Bind parameters, explicitly passing NULL if the field is empty
            if ($scheduleTime) {
                $stmt->bindParam(':Schedule_Time', $scheduleTime, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':Schedule_Time', null, PDO::PARAM_NULL);
            }

            if ($recurrenceTime) {
                $stmt->bindParam(':Recurrence_Time', $recurrenceTime, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(':Recurrence_Time', null, PDO::PARAM_NULL);
            }

            $stmt->bindParam(':Recurrence', $recurrence, PDO::PARAM_STR);
            $stmt->bindParam(':Triggered_By', $triggeredBy, PDO::PARAM_STR);
            $stmt->execute();

            // Retrieve the last inserted Job_Instance_ID
            $lastJobInstanceID = $pdo->lastInsertId();

            // Log the creation
            $logTitle = "Job Instance Created";
            $logDescription = "Job Instance ID {$lastJobInstanceID} was created by User ID {$user_id}.";

            // Insert log entry
            insertJobInstanceLog($pdo, $lastJobInstanceID, $logTitle, $logDescription);

            $successMessage = "Job instance created successfully.";
            header("Location: job_instance.php?Job_Configuration_ID=" . htmlspecialchars($configurationID));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Failed to create job instance: " . htmlspecialchars($e->getMessage());
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
    <title>Job Instances</title>
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
            text-decoration: none;
            font-size: 14px;
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
            <div class="go-back-container">
                <a href="configuration.php?Job_ID=<?= htmlspecialchars($_SESSION['job_id'] ?? ''); ?>" class="go-back-button">Go Back</a>
            </div>

            <div class="config-container">
                <div class="config-box">
                    <h1>Create Job Instance</h1>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="error-message"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($successMessage)): ?>
                        <div class="success-message"><?= htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>

                    <form action="job_instance.php?Job_Configuration_ID=<?= htmlspecialchars($configurationID); ?>" method="POST">
                        <input type="hidden" name="action" value="create_instance">
                        <table class="config-table">
                            <tr>
                                <td><label for="schedule_time">Schedule Time:</label></td>
                                <td><input type="datetime-local" id="schedule_time" name="schedule_time" value="<?= htmlspecialchars(!empty($_POST['schedule_time']) ? date('Y-m-d\TH:i', strtotime($_POST['schedule_time'])) : ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="recurrence">Recurrence:</label></td>
                                <td>
                                    <select id="recurrence" name="recurrence">
                                        <option value="None" <?= (isset($_POST['recurrence']) && $_POST['recurrence'] === 'None') ? 'selected' : ''; ?>>None</option>
                                        <option value="Daily" <?= (isset($_POST['recurrence']) && $_POST['recurrence'] === 'Daily') ? 'selected' : ''; ?>>Daily</option>
                                        <option value="Weekly" <?= (isset($_POST['recurrence']) && $_POST['recurrence'] === 'Weekly') ? 'selected' : ''; ?>>Weekly</option>
                                        <option value="Monthly" <?= (isset($_POST['recurrence']) && $_POST['recurrence'] === 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="recurrence_time_row">
                                <td><label for="recurrence_time">Recurrence Time:</label></td>
                                <td><input type="datetime-local" id="recurrence_time" name="recurrence_time" value="<?= htmlspecialchars(!empty($_POST['recurrence_time']) ? date('Y-m-d\TH:i', strtotime($_POST['recurrence_time'])) : ''); ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="triggered_by">Triggered By:</label></td>
                                <td>
                                    <select id="triggered_by" name="triggered_by" required>
                                        <option value="System" <?= (isset($_POST['triggered_by']) && $_POST['triggered_by'] === 'System') ? 'selected' : ''; ?>>System (Automatically)</option>
                                        <option value="User" <?= (isset($_POST['triggered_by']) && $_POST['triggered_by'] === 'User') ? 'selected' : ''; ?>>User (Manually)</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <button type="submit" class="configure-button">Create Job Instance</button>
                    </form>
                </div>
            </div>

            <div class="existing-instances">
                <h2>Existing Job Instances</h2>
                <?php if (!empty($_SESSION['run_message'])): ?>
                    <div class="success-message"><?= htmlspecialchars($_SESSION['run_message']); ?></div>
                    <?php unset($_SESSION['run_message']); ?>
                <?php endif; ?>
                <?php if (!empty($jobInstances)): ?>
                    <table class="instances-table">
                        <thead>
                            <tr>
                                <th>Instance ID</th>
                                <th>Previous Completion Time</th>
                                <th>Previous Run Status</th>
                                <th>Triggered By</th>
                                <th>Schedule Time</th>
                                <th>Recurrence</th>
                                <th>Recurrence Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobInstances as $instance): ?>
                                <tr>
                                    <td><?= htmlspecialchars($instance['Job_Instance_ID']); ?></td>
                                    <td><?= htmlspecialchars($instance['Previous_Completion_Time'] ?? 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($instance['Previous_Run_Status'] ?? 'Not Run'); ?></td>
                                    <td><?= htmlspecialchars($instance['Triggered_By']); ?></td>
                                    <td><?= htmlspecialchars($instance['Schedule_Time'] ?? 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($instance['Recurrence'] ?? 'None'); ?></td>
                                    <td><?= htmlspecialchars($instance['Recurrence_Time'] ?? 'N/A'); ?></td>
                                    <td>
                                        <form action="edit_instance.php" method="GET">
                                            <input type="hidden" name="Job_Instance_ID" value="<?= htmlspecialchars($instance['Job_Instance_ID']); ?>">
                                            <button type="submit" class="action-button edit-button">Edit</button>
                                        </form>
                                        <form action="run_instance.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="Job_Instance_ID" value="<?= htmlspecialchars($instance['Job_Instance_ID']); ?>">
                                            <input type="hidden" name="Job_Configuration_ID" value="<?= htmlspecialchars($configurationID); ?>">
                                            <button type="submit" class="action-button">Run Instance</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No job instances found for this configuration.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>

</body>

</html>