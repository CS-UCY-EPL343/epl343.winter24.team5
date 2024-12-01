<?php
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User') {
    header("Location: index.php");
    exit();
}

// Get the poll ID from the GET parameter or session
if (!isset($_GET['poll_id']) || !is_numeric($_GET['poll_id'])) {
    header("Location: select_poll.php"); // Redirect to poll selection page if poll ID is missing
    exit();
}

$pollId = intval($_GET['poll_id']); // Get the selected poll ID

// Fetch poll details
try {
    $poll = getPoll($pollId); // Fetch poll details using the function
    if (!$poll) {
        header("Location: select_poll.php"); // Redirect if poll not found
        exit();
    }
} catch (PDOException $e) {
    $error = handleSqlError($e);
    $poll = null;
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
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif ($poll): ?>
            <!-- Poll Title -->
            <h2 class="mb-4"><?= htmlspecialchars($poll['Title']) ?></h2>

            <!-- Poll Description -->
            <p class="mb-4">
                <?= htmlspecialchars($poll['Description']) ?>
            </p>

            <!-- Poll Vote Results -->
            <div class="mb-4">
                <h5>Vote Results</h5>
                <p><strong>Yes:</strong> <?= htmlspecialchars($poll['Votes_For']) ?> votes</p>
                <p><strong>No:</strong> <?= htmlspecialchars($poll['Votes_Against']) ?> votes</p>
            </div>

            <!-- Voting Form -->
            <form action="submit_vote.php" method="POST">
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
                <input type="hidden" name="poll_id" value="<?= htmlspecialchars($poll['Poll_ID']) ?>">
                <button type="submit" class="btn-submit">Submit Vote</button>
            </form>
        <?php else: ?>
            <p>Poll not found.</p>
        <?php endif; ?>
    </div>

    <script>
        function confirmVote() {
            alert('Your vote has been submitted!');
        }
    </script>

    <?php require_once 'footer.php'; ?>
</body>
</html>
