<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">Admin Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="admin_view_polls.php">Polls</a></li>
                <li><a href="pending_user_approvals.php">User Approvals</a></li>
                <li><a href="#" class="active">Jobs</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="dashboard-header">
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
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($jobs)): ?>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($job['Job_ID']); ?></td>
                                        <td><?= htmlspecialchars($job['Creator_ID']); ?></td>
                                        <td><?= htmlspecialchars($job['Job_Name']); ?></td>
                                        <td><?= htmlspecialchars($job['Job_Description']); ?></td>
                                        <td><?= htmlspecialchars($job['Creation_Date']); ?></td>
                                        <td>
                                            <button type="button" class="run-button" data-job-id="<?= $job['Job_ID']; ?>">Run</button>
                                        </td>
                                        <td>
                                            <button class="configure-button" onclick="window.location.href='configuration.php?job_id=<?= $job['Job_ID']; ?>'">
                                                Configure
                                            </button>
                                        </td>
                                        <td class="status-column" id="status-<?= $job['Job_ID']; ?>">Pending</td>
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
        </main>
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
            });
        });
    </script>
</body>

</html>