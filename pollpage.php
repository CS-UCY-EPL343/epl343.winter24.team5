<?php
require_once "navbar.php";
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
    <!-- Main Wrapper -->
    <div class="wrapper1">
        <!-- Poll Container -->
        <div class="content-box1">
            <h2 class="poll-title">Poll Title</h2>
            <p class="poll-description">
                Here is the detailed description of the poll. It explains the purpose of the poll and why it is important. You can vote on the poll below.
            </p>

            <div class="poll-results">
                <h5>Vote Results</h5>
                <p><strong>Yes:</strong> 60% (120 votes)</p>
                <p><strong>No:</strong> 40% (80 votes)</p>
            </div>

            <form>
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
                <button type="button" class="btn-submit" onclick="confirmVote()">Submit Vote</button>
            </form>
        </div>
    </div>

    <script>
    function confirmVote() {
        const yesOption = document.getElementById("voteYes");
        const noOption = document.getElementById("voteNo");

        // Check if either of the options is selected
        if (yesOption.checked || noOption.checked) {
            alert('Your vote has been submitted!');
        } else {
            alert('Please select an option before submitting your vote!');
        }
    }
    </script>

    <?php require_once 'footer.php'; ?>
</body>
</html>
