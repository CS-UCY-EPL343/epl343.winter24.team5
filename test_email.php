<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Sending</title>
</head>
<body>
    <form action="send_email.php" method="POST">
        <label for="Voter_ID">Voter ID:</label>
        <input type="number" id="Voter_ID" name="Voter_ID" required>
        <br>
        <label for="Poll_ID">Poll ID:</label>
        <input type="number" id="Poll_ID" name="Poll_ID" required>
        <br>
        <button type="submit">Send Email</button>
    </form>
</body>
</html>
