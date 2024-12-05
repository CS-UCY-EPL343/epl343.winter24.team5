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

$user_role = $_SESSION['role'] ?? 'User';
$is_admin = $user_role === 'Admin';

$jobID = $_GET['Job_ID'] ?? $_SESSION['job_id'] ?? null;
if ($jobID) {
    $_SESSION['job_id'] = $jobID;
}

$user_id = $_SESSION['user_id'];
$programs = [];
$configurations = [];
$errorMessage = '';
$successMessage = '';

// Fetch programs linked to the Job_ID
if ($jobID) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT Program_ID, Program_Name, Language FROM PROGRAMS WHERE Job_ID = :Job_ID");
    $stmt->bindParam(':Job_ID', $jobID, PDO::PARAM_INT);
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch configurations for the current user and job
    $configurations = getJobConfigurations($jobID, $user_id);
}

// Handle Create Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {
    $configName = $_POST['config_name'] ?? null;
    $parameters = $_POST['parameters'] ?? null;

    $result = insertJobConfiguration($jobID, $user_id, $configName, $parameters);

    if ($result) {
        $successMessage = "Job configuration created successfully.";
        header("Location: configuration.php?Job_ID=" . htmlspecialchars($jobID));
        exit();
    } else {
        $errorMessage = "Failed to create job configuration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configure Job</title>
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

        .job-instances-button {
            display: inline-block;
            margin-left: 15px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .job-instances-button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <main class="dashboard-main">
            <!-- Go Back Button -->
            <div class="go-back-container">
                <a href="jobs.php" class="go-back-button">Go Back</a>
            </div>

            <div class="config-container">
                <div class="config-box">
                    <h1>Create New Job Configuration</h1>

                    <?php if ($errorMessage): ?>
                        <div class="error-message" style="color: red;"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                        <div class="success-message" style="color: green;"><?= htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>

                    <form action="configuration.php?Job_ID=<?= htmlspecialchars($jobID); ?>" method="POST">
                        <input type="hidden" name="action" value="create">
                        <table class="config-table">
                            <tr>
                                <td><label for="config_name">Configuration Name:</label></td>
                                <td><input type="text" id="config_name" name="config_name" required></td>
                            </tr>
                            <tr>
                                <td><label for="parameters">Parameters:</label></td>
                                <td><input type="text" id="parameters" name="parameters" required></td>
                            </tr>
                        </table>
                        <button type="submit" class="configure-button">Submit Configuration</button>
                    </form>
                </div>
            </div>

            <div class="existing-configurations">
                <h2>Existing Configurations</h2>
                <?php if (!empty($configurations)): ?>
                    <table class="configurations-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Configuration Name</th>
                                <th>Parameters</th>
                                <th>Created At</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($configurations as $config): ?>
                                <tr>
                                    <td><?= htmlspecialchars($config['Job_Configuration_ID']); ?></td>
                                    <td><?= htmlspecialchars($config['Configuration_Name']); ?></td>
                                    <td><?= htmlspecialchars($config['Parameters']); ?></td>
                                    <td><?= htmlspecialchars($config['Created_At']); ?></td>
                                    <td><?= htmlspecialchars($config['Last_Modified']); ?></td>
                                    <td>
                                        <form action="job_instance.php" method="GET" style="display:inline;">
                                            <input type="hidden" name="Job_Configuration_ID" value="<?= htmlspecialchars($config['Job_Configuration_ID']); ?>">
                                            <button type="submit">Job Instances</button>
                                        </form>
                                        <form action="edit_configuration.php" method="GET" style="display:inline;">
                                            <input type="hidden" name="Job_Configuration_ID" value="<?= htmlspecialchars($config['Job_Configuration_ID']); ?>">
                                            <button type="submit">Edit Job Configuration</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No configurations found for this job.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>

</body>

</html>