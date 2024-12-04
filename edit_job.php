<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch job details
if (!isset($_GET['Job_ID'])) {
    header("Location: jobs.php");
    exit();
}

$job_id = intval($_GET['Job_ID']);
$pdo = getDatabaseConnection();

// Fetch job details
$stmt = $pdo->prepare("SELECT * FROM JOBS WHERE Job_ID = :Job_ID");
$stmt->bindParam(':Job_ID', $job_id, PDO::PARAM_INT);
$stmt->execute();
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header("Location: jobs.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated fields
    $job_name = $_POST['job_name'] ?? '';
    $job_description = $_POST['job_description'] ?? '';

    $updated_programs = [];
    if (!empty($_FILES['program_files']['name'][0])) {
        foreach ($_FILES['program_files']['name'] as $index => $file_name) {
            $file_tmp_path = $_FILES['program_files']['tmp_name'][$index];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_extensions = [
                'java', 'py', 'js', 'cs', 'cpp', 'c', 'rb', 'go', 'ts', 'php',
                'pl', 'sh', 'r', 'sql', 'html', 'css', 'json', 'xml'
            ];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                $message = "Unsupported file type: $file_name.";
                break;
            }

            $program_content = file_get_contents($file_tmp_path);
            $updated_programs[] = [
                'Program_Name' => pathinfo($file_name, PATHINFO_FILENAME),
                'Code' => base64_encode($program_content),
                'Language' => strtoupper($file_extension),
                'Version' => '1.0'
            ];
        }
    }

    if (empty($message)) {
        try {
            $programs_json = json_encode($updated_programs); // Store the result in a variable

            $stmt = $pdo->prepare("EXEC UpdateJobWithPrograms :Job_ID, :Job_Name, :Job_Description, :Programs");
            $stmt->bindParam(':Job_ID', $job_id, PDO::PARAM_INT);
            $stmt->bindParam(':Job_Name', $job_name, PDO::PARAM_STR);
            $stmt->bindParam(':Job_Description', $job_description, PDO::PARAM_STR);
            $stmt->bindParam(':Programs', $programs_json, PDO::PARAM_STR);
            $stmt->execute();

            $message = 'Job updated successfully!';
        } catch (PDOException $e) {
            $message = 'Error updating job: ' . $e->getMessage();
        }

        // Re-fetch the updated programs after updating the job
        $stmt = $pdo->prepare("SELECT * FROM PROGRAMS WHERE Job_ID = :Job_ID");
        $stmt->bindParam(':Job_ID', $job_id, PDO::PARAM_INT);
        $stmt->execute();
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC); // Re-fetch the updated programs
    }
} else {
    // Fetch programs initially when the page loads
    $stmt = $pdo->prepare("SELECT * FROM PROGRAMS WHERE Job_ID = :Job_ID");
    $stmt->bindParam(':Job_ID', $job_id, PDO::PARAM_INT);
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h3 class="sidebar-title">Admin Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="Tasks.php">Tasks</a></li>
                <li><a href="writeAiChat.php">ChatBot</a></li>
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="create_tasks.php">Create a Task</a></li>
                <li><a href="pending_user_approvals.php">User Approvals</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>

        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Edit Job</h1>
            </div>
            <div class="form-container-large">
                <?php if (!empty($message)): ?>
                <p style="color: <?= strpos($message, 'success') !== false ? 'green' : 'red'; ?>;">
                    <?= htmlspecialchars($message); ?>
                </p>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="job_name">Job Name</label>
                        <input type="text" id="job_name" name="job_name" class="form-control"
                            value="<?= htmlspecialchars($job['Job_Name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="job_description">Job Description</label>
                        <textarea id="job_description" name="job_description" class="form-control" rows="4"
                            required><?= htmlspecialchars($job['Job_Description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Current Programs</label>
                        <ul>
                            <?php foreach ($programs as $program): ?>
                            <li><?= htmlspecialchars($program['Program_Name']) ?>
                                (<?= htmlspecialchars($program['Language']) ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label for="program_files">Upload New Program Files</label>
                        <input type="file" id="program_files" name="program_files[]" class="form-control" multiple>
                        <small>Allowed file types: .java, .py, .js, .cs, .cpp, .c, .rb, .go, .ts, .php, .pl, .sh, .r,
                            .sql, .html, .css, .json, .xml</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="configure-button">Save Changes</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>