<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

$creator_id = $_SESSION['user_id']; // ID of the logged-in user
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get job details from the form
    $job_name = $_POST['job_name'] ?? '';
    $job_description = $_POST['job_description'] ?? '';

    // Check if file is uploaded
    if (isset($_FILES['program_file']) && $_FILES['program_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['program_file']['tmp_name'];
        $file_contents = file_get_contents($file_tmp_path);
        $programs_json = json_decode($file_contents, true);

        if ($programs_json === null) {
            $message = 'Invalid JSON file format.';
        } else {
            try {
                $pdo = getDatabaseConnection();

                // Prepare and execute the stored procedure
                $stmt = $pdo->prepare("EXEC InsertJobWithPrograms :Creator_ID, :Job_Name, :Job_Description, :Programs");
                $stmt->bindParam(':Creator_ID', $creator_id, PDO::PARAM_INT);
                $stmt->bindParam(':Job_Name', $job_name, PDO::PARAM_STR);
                $stmt->bindParam(':Job_Description', $job_description, PDO::PARAM_STR);
                $stmt->bindParam(':Programs', $file_contents, PDO::PARAM_STR);
                $stmt->execute();

                $message = 'Job and programs inserted successfully!';
            } catch (PDOException $e) {
                $message = 'Error inserting job: ' . $e->getMessage();
            }
        }
    } else {
        $message = 'Please upload a valid JSON file for the programs.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Job</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-container">
        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Create New Job</h1>
            </div>
            <div class="form-container-large">
                <?php if (!empty($message)): ?>
                    <p style="color: <?= strpos($message, 'success') !== false ? 'green' : 'red'; ?>;"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="job_name">Job Name</label>
                        <input type="text" id="job_name" name="job_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="job_description">Job Description</label>
                        <textarea id="job_description" name="job_description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="program_file">Upload Programs JSON File</label>
                        <input type="file" id="program_file" name="program_file" class="form-control" accept=".json" required>
                        <small>File should be in JSON format with the following structure:</small>
                        <pre>
[
  {
    "Program_Name": "Program1",
    "Program_Description": "Description of Program1",
    "Code": "Base64EncodedCodeHere",
    "Language": "Python",
    "Version": "1.0"
  },
  ...
]
                        </pre>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="configure-button">Create Job</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>
