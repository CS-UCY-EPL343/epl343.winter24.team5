<?php
require_once 'navbar.php';
//require_once 'session_check.php';

// Check if user is logged in and if they are an admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     // Redirect to index page if not logged in or not an admin
//     header("Location: index.php");
//     exit();
// }

// Handle form submission to create a new job
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobTitle = $_POST['job_title'] ?? '';
    $description = $_POST['description'] ?? '';
    $requirements = $_POST['requirements'] ?? '';
    $salary = $_POST['salary'] ?? '';

    // Here you would typically add code to validate inputs and insert the job into the database
    // For example:
    // $db->insertJob($jobTitle, $description, $requirements, $salary);

    // Redirect to the jobs list or show a success message (this is just a placeholder)
    $message = "Job successfully created!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Job - Bank IT Department</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Main Content -->
    <div class="wrapper1">
        <div class="hero1">
            <h1>Create a New Job Posting</h1>
            <?php if (isset($message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="jobs.php" method="post">
                <div class="form-group">
                    <label for="job_title">Job Title:</label>
                    <input type="text" id="job_title" name="job_title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="requirements">Requirements:</label>
                    <textarea id="requirements" name="requirements" required></textarea>
                </div>
                <div class="form-group">
                    <label for="salary">Salary:</label>
                    <input type="text" id="salary" name="salary" required>
                </div>
                <button type="submit">Create Job</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
</body>

</html>