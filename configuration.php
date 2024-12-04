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

$user_role = $_SESSION['role'] ?? 'User';
$is_admin = $user_role === 'Admin';

// Retrieve Job_ID from GET request
$jobID = $_GET['Job_ID'] ?? null;

// Store the Job_ID in a session if needed
if ($jobID) {
    $_SESSION['job_id'] = $jobID;
}

$user_id = $_SESSION['user_id'];
$programs = [];

// Fetch programs linked to the Job_ID
if ($jobID) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT Program_ID, Program_Name, Language FROM PROGRAMS WHERE Job_ID = :Job_ID");
    $stmt->bindParam(':Job_ID', $jobID, PDO::PARAM_INT);
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve Job_ID from the POST request or session
    $jobID = $_POST['Job_ID'] ?? $_SESSION['job_id'] ?? null;

    // Retrieve configuration details from the form
    $configName = $_POST['config_name'] ?? null;

    // Concatenate parameters based on dynamic input (if programs exist)
    $parameters = [];
    if (!empty($programs)) {
        foreach ($programs as $index => $program) {
            $param_key = 'param' . $index;
            if (isset($_POST[$param_key]) && trim($_POST[$param_key]) !== '') {
                // Add program name (prefix) with language in parentheses and parameter as "Program_Name (Language): Parameter"
                $parameters[] = $program['Program_Name'] . ' (' . $program['Language'] . '): ' . $_POST[$param_key];
            }
        }
    }

    // Join parameters only if there are programs and parameters are provided
    $parameters_string = !empty($parameters) ? implode('; ', $parameters) : null;

    // Call the function to insert the job configuration
    $result = insertJobConfiguration($jobID, $user_id, $configName, $parameters_string);

    if ($result) {
        $successMessage = "Job configuration saved successfully.";
    } else {
        $errorMessage = "Failed to save job configuration.";
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
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title"><?= $is_admin ? 'Admin Dashboard' : 'User Dashboard'; ?></h3>
            <ul class="sidebar-links">
                <!-- Common Links -->
                <li>
                    <a href="<?= $is_admin ? 'admin_page.php' : 'user_page.php'; ?>">Polls</a>
                </li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php">Tasks</a></li>
                <li><a href="writeAiChat.php">ChatBot</a></li>
                <!-- Admin-Only Links -->
                <?php if ($is_admin): ?>
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="create_tasks.php">Create a Task</a></li>
                <li><a href="pending_user_approvals.php">User Approvals</a></li>
                <?php endif; ?>
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
                    <div class="success-message" style="color: green;"><?= htmlspecialchars($successMessage); ?></div>
                    <?php elseif (isset($errorMessage)): ?>
                    <div class="error-message" style="color: red;"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>

                    <!-- Job Configuration Form -->
                    <form action="configuration.php" method="POST">
                        <table class="config-table">
                            <tr>
                                <td><label for="config_name">Configuration Name:</label></td>
                                <td><input type="text" id="config_name" name="config_name" required></td>
                            </tr>

                            <!-- Dynamic Parameter Fields -->
                            <?php if (!empty($programs)): ?>
                                <?php foreach ($programs as $index => $program): ?>
                                <tr>
                                    <td>
                                        <label for="param<?= $index; ?>">
                                            Parameter(s) for <?= htmlspecialchars($program['Program_Name']) . ' (' . htmlspecialchars($program['Language']) . ')'; ?>:
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" id="param<?= $index; ?>" name="param<?= $index; ?>">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center;">No programs linked to this job.</td>
                                </tr>
                            <?php endif; ?>
                        </table>

                        <input type="hidden" name="Job_ID" value="<?= htmlspecialchars($jobID); ?>">
                        <button type="submit" name="submit_config">Submit Configuration</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
