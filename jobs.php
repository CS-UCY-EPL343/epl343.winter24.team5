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

        </aside>


        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Job Listings</h1>

                <!-- Create New Job Button (Visible to Admins Only) -->
                <?php if ($is_admin): ?>
                <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 15px;">
                    <form method="GET" action="create_job.php" style="display:inline;">
                        <button type="submit" class="configure-button">Create New Job</button>
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
                                        <form action="configuration.php" method="GET">
                                            <input type="hidden" name="Job_ID" value="<?= $job['Job_ID']; ?>">
                                            <button type="submit" name="submit_config" class="button">Configure</button>
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