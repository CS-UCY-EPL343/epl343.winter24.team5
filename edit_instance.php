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
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT * FROM JOB_INSTANCE WHERE Job_Instance_ID = :InstanceID AND Creator_ID = :CreatorID");
    $stmt->bindParam(':InstanceID', $instanceID, PDO::PARAM_INT);
    $stmt->bindParam(':CreatorID', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $instance = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$instance) {
        $errorMessage = "Job instance not found or you do not have permission to edit it.";
    }
}

// Handle the form submission for updating the instance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleTime = $_POST['schedule_time'] ?? null;
    $recurrence = $_POST['recurrence'] ?? null;
    $recurrenceTime = $_POST['recurrence_time'] ?? null;
    $triggeredBy = $_POST['triggered_by'] ?? null;

    // Validate inputs
    $scheduleTime = !empty($scheduleTime) ? $scheduleTime : null;
    $recurrenceTime = !empty($recurrenceTime) ? $recurrenceTime : null;

    try {
        $stmt = $pdo->prepare("EXEC UpdateJobInstance :Job_Instance_ID, :Schedule_Time, :Recurrence, :Recurrence_Time, :Triggered_By");
        $stmt->bindParam(':Job_Instance_ID', $instanceID, PDO::PARAM_INT);
        $stmt->bindParam(':Schedule_Time', $scheduleTime, PDO::PARAM_STR);
        $stmt->bindParam(':Recurrence', $recurrence, PDO::PARAM_STR);
        $stmt->bindParam(':Recurrence_Time', $recurrenceTime, PDO::PARAM_STR);
        $stmt->bindParam(':Triggered_By', $triggeredBy, PDO::PARAM_STR);
        $stmt->execute();

        $successMessage = "Job instance updated successfully.";
        // Fetch the updated instance details
        $stmt = $pdo->prepare("SELECT * FROM JOB_INSTANCE WHERE Job_Instance_ID = :InstanceID AND Creator_ID = :CreatorID");
        $stmt->bindParam(':InstanceID', $instanceID, PDO::PARAM_INT);
        $stmt->bindParam(':CreatorID', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $instance = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMessage = "Failed to update job instance: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Instance</title>
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

        .configure-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .configure-button:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .success-message {
            color: green;
            margin-bottom: 15px;
        }
    </style>
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
                        <div class="error-message"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                        <div class="success-message"><?= htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>

                    <?php if ($instance): ?>
                        <form action="edit_instance.php?Job_Instance_ID=<?= htmlspecialchars($instanceID); ?>" method="POST">
                            <table class="config-table">
                                <tr>
                                    <td><label for="schedule_time">Schedule Time:</label></td>
                                    <td><input type="datetime-local" id="schedule_time" name="schedule_time" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($instance['Schedule_Time']))) ?>"></td>
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
                                <tr>
                                    <td><label for="recurrence_time">Recurrence Time:</label></td>
                                    <td><input type="datetime-local" id="recurrence_time" name="recurrence_time" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($instance['Recurrence_Time']))) ?>"></td>
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
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
