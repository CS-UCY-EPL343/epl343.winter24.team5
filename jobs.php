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
                        <th>Status</th> <!-- New Status Column -->
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
                                <td>
                                    <button type="button" class="run-button" data-job-id="<?php echo $job['Job_ID']; ?>">Run</button>
                                </td>
                                <td>
                                    <button type="button">Configure</button>
                                </td>
                                <td class="status-column" id="status-<?php echo $job['Job_ID']; ?>">Pending</td> <!-- Status Column -->
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No job postings available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'footer.php'; ?>

    <script>
        // Simulating a job running result (Replace this with actual server-side handling)
        function runJob(jobId) {
            // Simulate random success or failure
            return Math.random() > 0.2; // 20% chance of failure
        }

        // Add event listeners to "Run" buttons
        document.querySelectorAll('.run-button').forEach(button => {
            button.addEventListener('click', function() {
                const jobId = this.getAttribute('data-job-id');
                const statusCell = document.getElementById(`status-${jobId}`);

                // Simulate job running
                const isSuccessful = runJob(jobId);

                if (isSuccessful) {
                    statusCell.textContent = "Success";
                    statusCell.classList.remove('status-failure');
                    statusCell.classList.add('status-success'); // Apply green background
                } else {
                    statusCell.textContent = "Failed";
                    statusCell.classList.remove('status-success');
                    statusCell.classList.add('status-failure'); // Apply red background
                }

                // Display success message (Optional)
                const successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';

                // Optionally, hide the message after a few seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000); // 3 seconds
            });
        });
    </script>
</body>

</html>