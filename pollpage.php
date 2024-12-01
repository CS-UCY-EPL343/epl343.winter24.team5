<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User') {
    header("Location: index.php");
    exit();
}

// Check if User_ID exists in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect if User_ID is not set in the session
    exit();
}

$voterId = $_SESSION['user_id']; // Retrieve the User_ID from the session

// Get the poll ID from the GET parameter
if (!isset($_GET['poll_id']) || !is_numeric($_GET['poll_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if poll ID is missing
    exit();
}

$pollId = intval($_GET['poll_id']); // Get the selected poll ID

// Fetch poll details
try {
    $poll = getPoll($pollId); // Fetch poll details using the function
    if (!$poll) {
        header("Location: dashboard.php"); // Redirect if poll not found
        exit();
    }
} catch (PDOException $e) {
    $error = handleSqlError($e);
    $poll = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['vote']) && ($_POST['vote'] === 'yes' || $_POST['vote'] === 'no')) {
        $decision = ($_POST['vote'] === 'yes') ? 1 : 0; // Convert 'yes' or 'no' to 1 or 0

        try {
            if (addVote($voterId, $pollId, $decision)) {
                header("Location: dashboard.php?vote=success"); // Redirect to dashboard on success
                exit();
            } else {
                $error = "Failed to record your vote. Please try again.";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "Invalid vote selection.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Details</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your existing styles -->
</head>

<body>
    <div class="wrapper1">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif ($poll): ?>
            <div class="content-box1">
                <!-- Poll Title -->
                <h2 class="poll-title"><?= htmlspecialchars($poll['Title']) ?></h2>

                <!-- Poll Description -->
                <p class="poll-description">
                    <?= htmlspecialchars($poll['Description']) ?>
                </p>

                <!-- Poll Vote Results -->
                <div class="poll-results">
                    <h5>Vote Results</h5>
                    <p><strong>Yes:</strong> <?= htmlspecialchars($poll['Votes_For']) ?> votes</p>
                    <p><strong>No:</strong> <?= htmlspecialchars($poll['Votes_Against']) ?> votes</p>
                </div>

                <!-- Voting Form -->
                <form method="POST">
                    <div class="poll-option-group">
                        <div class="poll-option">
                            <input class="custom-radio" type="radio" name="vote" id="voteYes" value="yes" required>
                            <label for="voteYes">Yes</label>
                        </div>
                        <div class="poll-option">
                            <input class="custom-radio" type="radio" name="vote" id="voteNo" value="no" required>
                            <label for="voteNo">No</label>
                        </div>
                    </div>
                    <button type="button" class="btn-submit">Submit Vote</button>
                </form>
            </div>
        <?php else: ?>
            <p>Poll not found.</p>
        <?php endif; ?>
    </div>

    <?php require_once 'footer.php'; ?>
</body>
</html>
