<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// Handle POST request for creating a poll
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['expiration_date'], $_POST['status'])) {
    $creatorId = $_SESSION['user_id']; // Assuming user_id is stored in the session
    $title = $_POST['title'];
    $description = $_POST['description'];
    $expirationDate = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['expiration_date'])->format('Y-m-d H:i:s'); // Convert to SQL DATETIME
    $status = $_POST['status'];

    try {
        if (createPoll($creatorId, $title, $description, $expirationDate, $status)) {
            $success = "Poll successfully created!";
        } else {
            $error = "Failed to create the poll.";
        }
    } catch (PDOException $e) {
        $error = handleSqlError($e);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Poll</title>
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
                                <span data-feather="bar-chart-2"></span>
                                Create Poll
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_page.php">
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
                    <h1 class="h2">Create Poll</h1>
                </div>

                <!-- Form Section -->
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success" role="alert">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" maxlength="40" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" maxlength="255" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="expiration_date" class="form-label">Expiration Date</label>
                                <input type="datetime-local" name="expiration_date" id="expiration_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">Create Poll</button>
                            </div>
                        </form>
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
