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

// Get the user role from the database
$user_id = $_SESSION['user_id'];
$is_admin = true;

// Fetch the user's role from the database
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    $is_admin = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Job_ID'])) {
    $_SESSION['Job_ID'] = intval($_POST['Job_ID']); // Store job_id in the session
    header("Location: configuration.php"); // Redirect to configuration.php
    exit();
}

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
                <li><a href="admin_page.php">Polls</a></li>
                <li><a href="pending_user_approvals.php" class="active">User Approvals</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Job Listings</h1>

                <!-- Create New Job Button (Visible to Admins Only) -->
                <?php if ($is_admin): ?>
                    <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 1px;">
                        <form method="GET" action="create_job.php" style="display:inline;">
                            <button type="submit" class="button">Create New Job</button>
                        </form>
                    </div>
                <?php endif; ?>
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
                                <th>Actions</th>
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
                                            <div style="display: flex; justify-content: center; gap: 10px;">
                                                <!-- Configure Button -->
                                                <form method="POST" action="" style="display:inline;">
                                                    <input type="hidden" name="Job_ID" value="<?= $job['Job_ID']; ?>">
                                                    <button type="submit" class="button">Configure</button>
                                                </form>

                                                <!-- Edit Job Button (Visible to Admins Only) -->
                                                <?php if ($is_admin): ?>
                                                    <form method="GET" action="edit_job.php" style="display:inline;">
                                                        <input type="hidden" name="Job_ID" value="<?= $job['Job_ID']; ?>">
                                                        <button type="submit" class="button">Edit Job</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No job postings available.</td>
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

</body>

</html>