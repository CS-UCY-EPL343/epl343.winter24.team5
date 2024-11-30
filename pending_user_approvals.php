<?php
require_once 'db_functions.php';
require_once 'navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure only 'Φορέας Υλοποίησης' can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
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
    <!-- Content Section -->
    <div class="content">
        <h1>Pending Approvals</h1>

        <?php if (!empty($pendingApprovals)): ?>
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
                                    <div class="button-container">
                                        <form method="post" action="process_approval.php" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($approval['User_ID']) ?>">
                                            <button type="submit" name="action" value="approve" class="view-documents-btn">Approve</button>
                                        </form>
                                    </div>
                            </td>
                            <td>
                                    <div class="button-container">
                                        <form method="post" action="process_approval.php" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($approval['User_ID']) ?>">
                                            <button type="submit" name="action" value="reject" class="view-documents-btn">Reject</button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending approvals at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
