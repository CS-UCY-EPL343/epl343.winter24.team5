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

    // Check if files are uploaded
    if (!empty($_FILES['program_files']['name'][0])) {
        $programs = []; // Array to hold program details
        $unsupported_files = []; // Array to collect unsupported file names

        // Allowed file extensions
        $allowed_extensions = [
            'java', 'py', 'js', 'cs', 'cpp', 'c', 'rb', 'go', 'ts', 'swift', 'php',
            'pl', 'sh', 'r', 'kt', 'sql', 'html', 'css', 'json', 'xml', 'yml', 'yaml'
        ];

        foreach ($_FILES['program_files']['name'] as $index => $file_name) {
            $file_tmp_path = $_FILES['program_files']['tmp_name'][$index];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            if (!in_array($file_extension, $allowed_extensions)) {
                $unsupported_files[] = $file_name; // Add unsupported file to the list
                continue; // Skip further processing for this file
            }

            $program_content = file_get_contents($file_tmp_path);
            $programs[] = [
                'Program_Name' => pathinfo($file_name, PATHINFO_FILENAME),
                'Code' => base64_encode($program_content),
                'Language' => strtoupper($file_extension),
                'Version' => '1.0'
            ];
        }

        // If there are unsupported files, display them in the message
        if (!empty($unsupported_files)) {
            $message = "Unsupported file types:\n" . implode("\n", $unsupported_files);
        } elseif (empty($message)) {
            try {
                $pdo = getDatabaseConnection();

                // Prepare the programs JSON string
                $programs_json = json_encode($programs);

                // Prepare and execute the stored procedure
                $stmt = $pdo->prepare("EXEC InsertJobWithPrograms :Creator_ID, :Job_Name, :Job_Description, :Programs");
                $stmt->bindParam(':Creator_ID', $creator_id, PDO::PARAM_INT);
                $stmt->bindParam(':Job_Name', $job_name, PDO::PARAM_STR);
                $stmt->bindParam(':Job_Description', $job_description, PDO::PARAM_STR);
                $stmt->bindParam(':Programs', $programs_json, PDO::PARAM_STR);
                $stmt->execute();

                // Set success message in session and redirect
                $_SESSION['success_message'] = 'Job and programs inserted successfully!';
                header('Location: success.php');
                exit();
            } catch (PDOException $e) {
                $message = 'Error inserting job: ' . $e->getMessage();
            }
        }
    } else {
        $message = 'Please upload at least one program file.';
    }
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the session message
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
                    <p style="color: <?= strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>;">
                        <?= nl2br(htmlspecialchars($message)); ?>
                    </p>
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
                        <label for="program_files">Upload Program Files</label>
                        <input type="file" id="program_files" name="program_files[]" class="form-control" multiple required>
                        <small>Allowed file types: .java, .py, .js, .cs, .cpp, .c, .rb, .go, .ts, .swift, .php, .pl, .sh, .r, .kt, .sql, .html, .css, .json, .xml, .yml, .yaml</small>
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
