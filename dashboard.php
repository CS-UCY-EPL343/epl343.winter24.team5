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
                    <!-- Poll 1 -->
                    <div class="card poll-card">
                        <div class="card-body">
                            <h5 class="card-title">Poll Title 1</h5>
                            <p class="card-text">Description of the poll goes here. It gives an overview of the poll's context.</p>
                            <p class="poll-votes">Votes: Yes 20% | No 80%</p>
                            <a href="pollpage.php?poll_id=1" class="btn btn-primary mt-auto">View</a>
                        </div>
                    </div>
                    <!-- Poll 2 -->
                    <div class="card poll-card">
                        <div class="card-body">
                            <h5 class="card-title">Poll Title 2</h5>
                            <p class="card-text">Another poll description, offering details about the poll's purpose.</p>
                            <p class="poll-votes">Votes: Yes 60% | No 40%</p>
                            <a href="pollpage.php?poll_id=2" class="btn btn-primary mt-auto">View</a>
                        </div>
                    </div>
                    <!-- Poll 3 -->
                    <div class="card poll-card">
                        <div class="card-body">
                            <h5 class="card-title">Poll Title 3</h5>
                            <p class="card-text">This is a description for the third poll.</p>
                            <p class="poll-votes">Votes: Yes 75% | No 25%</p>
                            <a href="pollpage.php?poll_id=3" class="btn btn-primary mt-auto">View</a>
                        </div>
                    </div>
                    <div class="card poll-card">
                        <div class="card-body">
                            <h5 class="card-title">Poll Title 4</h5>
                            <p class="card-text">This is a description for the fourth poll.</p>
                            <p class="poll-votes">Votes: Yes 55% | No 45%</p>
                            <a href="pollpage.php?poll_id=4" class="btn btn-primary mt-auto">View</a>
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
