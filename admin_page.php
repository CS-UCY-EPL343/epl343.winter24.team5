<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
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
                    <!-- Poll 1 -->
                    <div class="card poll-card">
                        <div class="card-body">
                            <h5 class="card-title">Poll Title 1</h5>
                            <p class="card-text">Description of the poll goes here. It gives an overview of the poll's context.</p>
                            <p class="poll-votes">Votes: Yes 20% | No 80%</p>
                            <a href="pollpage.php?poll_id=1" class="btn btn-primary mt-auto">View</a>
                            <a href="polleditpage.php?poll_id=1" class="btn btn-warning mt-2">Edit</a>
                        </div>
                    </div>
                    <!-- Poll 2 -->
                    <div class="card poll-card">
                        <div class="card-body">
                            <h5 class="card-title">Poll Title 2</h5>
                            <p class="card-text">Another poll description, offering details about the poll's purpose.</p>
                            <p class="poll-votes">Votes: Yes 60% | No 40%</p>
                            <a href="pollpage.php?poll_id=2" class="btn btn-primary mt-auto">View</a>
                            <a href="polleditpage.php?poll_id=2" class="btn btn-warning mt-2">Edit</a>
                        </div>
                    </div>
                    <!-- Poll 3 -->
                    <div class="card poll-card">
                        <div class="card-body">
                            <h5 class="card-title">Poll Title 3</h5>
                            <p class="card-text">This is a description for the third poll.</p>
                            <p class="poll-votes">Votes: Yes 75% | No 25%</p>
                            <a href="pollpage.php?poll_id=3" class="btn btn-primary mt-auto">View</a>
                            <a href="polleditpage.php?poll_id=3" class="btn btn-warning mt-2">Edit</a>
                        </div>
                    </div>
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
