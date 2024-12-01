<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'session_check.php';
require_once 'db_functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User') {
    header("Location: index.php");
    exit();
}

$userID = $_SESSION['user_id'];

try{

    $polls = getUserPolls($userID);
} catch( PDOException $e){
    $error = handleSqlError($e);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
                            <a class="nav-link active" aria-current="page" href="#">
                                <span data-feather="home"></span>
                                Polls
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
                    <?php elseif (empty($polls)): ?>
                        <p>No polls available for this user.</p>
                    <?php else: ?>
                        <?php foreach ($polls as $poll): ?>
                            <div class="card poll-card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($poll['Title']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($poll['Description']) ?></p>
                                    <p class="poll-votes">Votes: Yes <?= htmlspecialchars($poll['Votes_For']) ?> | No <?= htmlspecialchars($poll['Votes_Against']) ?></p>
                                    <a href="pollpage.php?poll_id=<?= htmlspecialchars($poll['Poll_ID']) ?>" class="btn btn-primary mt-auto">View</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
