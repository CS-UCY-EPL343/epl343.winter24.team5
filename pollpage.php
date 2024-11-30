<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
            max-width: 800px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">EV Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Sign Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Poll Details Container -->
    <div class="container">
        <h1 class="mb-4">Poll Title</h1>
        <p class="mb-4">
            This is the detailed description of the poll. It explains the purpose of the poll and why it is important. You can vote on the poll below.
        </p>

        <div class="mb-4">
            <h5>Vote Results</h5>
            <p>Yes: 60% (120 votes)</p>
            <p>No: 40% (80 votes)</p>
        </div>

        <form>
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="vote" id="voteYes" value="yes" required>
                <label class="form-check-label" for="voteYes">Yes</label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="vote" id="voteNo" value="no" required>
                <label class="form-check-label" for="voteNo">No</label>
            </div>
            <button type="button" class="btn btn-primary" onclick="confirmVote()">Submit Vote</button>
        </form>
    </div>

    <script>
        function confirmVote() {
            alert('Your vote has been submitted!');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
