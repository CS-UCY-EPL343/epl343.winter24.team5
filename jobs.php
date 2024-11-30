<?php
require_once 'navbar.php';
require_once 'db_functions.php';
//require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and if they are an admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     // Redirect to index page if not logged in or not an admin
//     header("Location: index.php");
//     exit();
// }

// Fetch jobs from the database
// Assuming you have a database connection established


// $jobs = [];
// try {
//     $stmt = $db->query("SELECT job_id, creator_id, job_name, job_description, creation_date FROM jobs");
//     $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     echo "Error: " . $e->getMessage();
// }

$jobs = [];
$jobs = getJobListings();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings - Bank IT Department</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Main Content -->
    <div class="hero1">
        <h1>Job Listings</h1>
    </div>
    <div class="job-wrapper">
        <div class="job-content-box">
            <table class="job-listing-table">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Creator ID</th>
                        <th>Job Name</th>
                        <th>Job Description</th>
                        <th>Creation Date</th>
                        <th>Execute</th>
                        <th>Configure</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jobs)): ?>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['Job_ID']); ?></td>
                                <td><?php echo htmlspecialchars($job['Creator_ID']); ?></td>
                                <td><?php echo htmlspecialchars($job['Job_Name']); ?></td>
                                <td><?php echo htmlspecialchars($job['Job_Description']); ?></td>
                                <td><?php echo htmlspecialchars($job['Creation_Date']); ?></td>
                                <td><button type="button">Run</button></td>
                                <td><button type="button">Configure</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No job postings available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
</body>

</html>