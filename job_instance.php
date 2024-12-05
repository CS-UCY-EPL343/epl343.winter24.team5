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
    $recurrence = $_POST['recurrence'] ?? null;
    $recurrenceTime = $_POST['recurrence_time'] ?? null;
    $triggeredBy = $_POST['triggered_by'] ?? null;

    // Validate inputs
    $scheduleTime = !empty($scheduleTime) ? date('Y-m-d H:i:s', strtotime($scheduleTime)) : null;
    $recurrenceTime = !empty($recurrenceTime) ? date('Y-m-d H:i:s', strtotime($recurrenceTime)) : null;

    if (empty($recurrence) && empty($recurrenceTime)) {
        $errorMessage = "Please provide both the reccurunce rate and recurrence time.";
    } elseif (!empty($scheduleTime) && !empty($recurrence)) {
        $errorMessage = "Both Schedule Time and recurrence rate cannot be provided simultaneously.";
    } elseif (!empty($scheduleTime) && !empty($recurrenceTime)) {
        $errorMessage = "Both schedule time and recurrence time cannot be provided simultaneously.";
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

            $successMessage = "Job instance created successfully.";
            header("Location: job_instance.php?Job_Configuration_ID=" . htmlspecialchars($configurationID));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Failed to create job instance: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Instances</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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

        .action-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }

        .action-button:hover {
            background-color: #218838;
        }

        .edit-button {
            background-color: #ffc107;
        }

        .edit-button:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <main class="dashboard-main">
            <!-- Go Back Button -->
            <div class="go-back-container">
                <a href="configuration.php?Job_ID=<?= htmlspecialchars($_SESSION['job_id'] ?? ''); ?>" class="go-back-button">Go Back</a>
            </div>

            <div class="config-container">
                <div class="config-box">
                    <h1>Create Job Instance</h1>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="error-message" style="color: red;"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($successMessage)): ?>
                        <div class="success-message" style="color: green;"><?= htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>

                    <form action="job_instance.php?Job_Configuration_ID=<?= htmlspecialchars($configurationID); ?>" method="POST">
                        <input type="hidden" name="action" value="create_instance">
                        <table class="config-table">
                            <tr>
                                <td><label for="schedule_time">Schedule Time:</label></td>
                                <td><input type="datetime-local" id="schedule_time" name="schedule_time"></td>
                            </tr>
                            <tr>
                                <td><label for="recurrence">Recurrence:</label></td>
                                <td>
                                    <select id="recurrence" name="recurrence">
                                        <option value="None">None</option>
                                        <option value="Daily">Daily</option>
                                        <option value="Weekly">Weekly</option>
                                        <option value="Monthly">Monthly</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="recurrence_time">Recurrence Time:</label></td>
                                <td><input type="datetime-local" id="recurrence_time" name="recurrence_time"></td>
                            </tr>
                            <tr>
                                <td><label for="triggered_by">Triggered By:</label></td>
                                <td>
                                    <select id="triggered_by" name="triggered_by" required>
                                        <option value="System">System (Automatically)</option>
                                        <option value="User">User (Manually)</option>
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
    <?php if (!empty($jobInstances)): ?>
        <?php if (!empty($_SESSION['run_message'])): ?>
            <div class="success-message" style="color: green;"><?= htmlspecialchars($_SESSION['run_message']); ?></div>
            <?php unset($_SESSION['run_message']); ?>
        <?php endif; ?>
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
                        <td><?= htmlspecialchars($instance['Previous_Completion_Time']); ?></td>
                        <td><?= htmlspecialchars($instance['Previous_Run_Status'] ?? 'Not Run'); ?></td>
                        <td><?= htmlspecialchars($instance['Triggered_By']); ?></td>
                        <td><?= htmlspecialchars($instance['Schedule_Time']); ?></td>
                        <td><?= htmlspecialchars($instance['Recurrence']); ?></td>
                        <td><?= htmlspecialchars($instance['Recurrence_Time']); ?></td>
                        <td>
                            <form action="edit_instance.php" method="GET" style="display:inline;">
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
</body>
</html>
