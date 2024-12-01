<?php


// Include the navbar
require_once 'navbar.php';
require_once 'session_check.php';
require_once 'db_functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

try {
    // Call the GetAllPolls stored procedure
    $polls = getAllPolls(); // Assuming getAllPolls() is defined in db_functions.php
} catch (PDOException $e) {
    $error = handleSqlError($e);
}


?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'db_functions.php'; // Include database functions
require_once 'session_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

try {
    // Call the GetAllPolls stored procedure
    $polls = getAllPolls(); // Assuming getAllPolls() is defined in db_functions.php
} catch (PDOException $e) {
    $error = handleSqlError($e);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="create_poll.php">
                                <span data-feather="bar-chart-2"></span>
                                Create Poll
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">
                                <span data-feather="home"></span>
                                Polls
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pending_user_approvals.php">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                                Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#reports">
                                <span data-feather="bar-chart-2"></span>
                                Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#settings">
                                <span data-feather="layers"></span>
                                Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Polls</h1>
                </div>

                <!-- Poll List -->
                <div class="poll-container">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php else: ?>
                        <?php if (empty($polls)): ?>
                            <p>No polls available.</p>
                        <?php else: ?>
                            <?php foreach ($polls as $poll): ?>
                                <div class="card poll-card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($poll['Title']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($poll['Description']) ?></p>
                                        <p class="poll-votes">Expiration: <?= htmlspecialchars($poll['Expiration_Date']) ?></p>
                                        <a href="pollpage.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="btn btn-primary mt-auto">View</a>
                                        <a href="polleditpage.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="btn btn-warning mt-2">Edit</a>
                                        <a href="add_users_to_poll.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="btn btn-info d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-add me-2" viewBox="0 0 16 16">
                                                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                                <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                                            </svg>
                                            Add Users
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>