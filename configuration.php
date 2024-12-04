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
    $parameters = $_POST['parameters'] ?? '';

    // Call the function to insert the job configuration
    $result = insertJobConfiguration($jobID, $user_id, $configName, $parameters);

    if ($result) {
        header("Location: jobs.php"); // Redirect to jobs page after success
        exit();
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
    <style>
        /* Make the parameters box non-resizable and styled like the config_name box */
        textarea#parameters {
            width: 100%;
            height: 2em;
            padding: 0.5em;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            resize: none; /* Prevent resizing */
        }

        ul.program-list {
            list-style-type: disc;
            padding-left: 20px;
        }

        ul.program-list li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
        <h3 class="sidebar-title"><?= $is_admin ? 'Admin Dashboard' : 'User Dashboard'; ?></h3>
            <ul class="sidebar-links">
                <li><a href="<?= $is_admin ? 'admin_page.php' : 'user_page.php'; ?>">Polls</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php">Tasks</a></li>
                <li><a href="writeAiChat.php">ChatBot</a></li>
                <?php if ($is_admin): ?>
                <li>
                    <a href="create_poll.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'create_poll.php' ? 'active' : ''; ?>">Create
                        Poll</a>
                </li>

                <li>
                    <a href="pending_user_approvals.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'pending_user_approvals.php' ? 'active' : ''; ?>">User
                        Approvals</a>
                </li>
                <?php endif; ?>
                <li>
                    <a href="create_tasks.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'create_tasks.php' ? 'active' : ''; ?>">Create
                        Task</a>
                </li>
                <li>
                    <a href="writeAiChat.php"
                        class="<?= basename($_SERVER['PHP_SELF']) == 'writeAiChat.php' ? 'active' : ''; ?>">ChatBot</a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="config-container">
                <div class="config-box">
                    <h1>Configure Job</h1>

                    <!-- Display Success or Error Messages -->
                    <?php if (isset($errorMessage)): ?>
                    <div class="error-message" style="color: red;"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>

                    <!-- Job Configuration Form -->
                    <form action="configuration.php" method="POST">
                        <table class="config-table">
                            <tr>
                                <td><label for="config_name">Configuration Name:</label></td>
                                <td><input type="text" id="config_name" name="config_name" class="form-control" required></td>
                            </tr>
                            <tr>
                                <td><label for="parameters">Parameters:</label></td>
                                <td><textarea id="parameters" name="parameters" required></textarea></td>
                            </tr>
                        </table>

                        <?php if (!empty($programs)): ?>
                            <h3>Linked Programs:</h3>
                            <ul class="program-list">
                                <?php foreach ($programs as $program): ?>
                                    <li><?= htmlspecialchars($program['Program_Name']) . ' (' . htmlspecialchars($program['Language']) . ')'; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p style="color: red;">No programs linked to this job.</p>
                        <?php endif; ?>

                        <input type="hidden" name="Job_ID" value="<?= htmlspecialchars($jobID); ?>">
                        <button type="submit" name="submit_config" class="configure-button">Submit Configuration</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
