<?php
require_once 'db_functions.php'; // Include your database functions
require_once 'navbar.php'; // Include navbar
require_once 'session_check.php';

// Ensure only 'Admin' can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Handle POST request for approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $userId = intval($_POST['user_id']);
    $approvalStatus = $_POST['action'] === 'approve' ? 'Approved' : 'Rejected';

    if (approveOrRejectUser($userId, $approvalStatus)) {
        // Redirect to refresh the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "Failed to update user approval status.";
    }
}

// Fetch pending approvals
$pendingApprovals = getPendingApprovals();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals</title>
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
                <li><a href="#" class="active">User Approvals</a></li>
                <li><a href="jobs.php">Jobs</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Pending Approvals</h1>
            </div>
            <div class="approval-container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pendingApprovals)): ?>
                    <div class="approval-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Political ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Mobile Phone</th>
                                    <th>User Role</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Address</th>
                                    <th colspan="2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingApprovals as $approval): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($approval['User_ID']) ?></td>
                                        <td><?= htmlspecialchars($approval['Political_ID']) ?></td>
                                        <td><?= htmlspecialchars($approval['First_Name']) ?></td>
                                        <td><?= htmlspecialchars($approval['Last_Name']) ?></td>
                                        <td><?= htmlspecialchars($approval['Username']) ?></td>
                                        <td><?= htmlspecialchars($approval['Email_Address']) ?></td>
                                        <td><?= htmlspecialchars($approval['Mobile_Phone']) ?></td>
                                        <td><?= htmlspecialchars($approval['User_Role_ID']) ?></td>
                                        <td><?= $approval['Gender'] ? 'Male' : 'Female' ?></td>
                                        <td><?= htmlspecialchars($approval['Date_of_Birth']) ?></td>
                                        <td><?= htmlspecialchars($approval['Address']) ?></td>
                                        <td>
                                            <form method="post" action="" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($approval['User_ID']) ?>">
                                                <button type="submit" name="action" value="approve" class="poll-button">Approve</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="post" action="" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($approval['User_ID']) ?>">
                                                <button type="submit" name="action" value="reject" class="poll-button-yellow">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No pending approvals at the moment.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
