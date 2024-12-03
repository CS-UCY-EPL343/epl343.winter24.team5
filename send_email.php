<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// Database connection
try {
    echo "Connecting to the database...<br>";
    $pdo = new PDO('sqlsrv:Server=epl343project.database.windows.net;Database=EPL343DB', 'epl343project', '5thSemesterPomba!', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "Database connection successful.<br>";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Email sending function
function sendEmail($recipientEmail, $recipientName, $pollTitle) {
    $mail = new PHPMailer(true);

    try {
        echo "Initializing email sending process...<br>";

        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mitilineos123@gmail.com';
        $mail->Password = 'dqog gaos gpce flfj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email sender and recipient
        $mail->setFrom('mitilineos123@gmail.com', 'Lil Indian');
        $mail->addAddress($recipientEmail, $recipientName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'You have been added to a poll!';
        $mail->Body = "
            <h1>Hello, $recipientName!</h1>
            <p>You have been added to the poll: <strong>$pollTitle</strong>.</p>
            <p>Please login to the system to participate.</p>
        ";

        $mail->send();
        echo "Email successfully sent to $recipientName ($recipientEmail).<br>";
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
        echo "Failed to send email. Error: {$mail->ErrorInfo}<br>";
        return false;
    }
}

// Input parameters
$voterId = $_POST['Voter_ID'] ?? null;
$pollId = $_POST['Poll_ID'] ?? null;

// Debug input parameters
if ($voterId && $pollId) {
    echo "Voter_ID: $voterId, Poll_ID: $pollId<br>";

    try {
        // Fetch user and poll information
        echo "Fetching user and poll information...<br>";
        $stmt = $pdo->prepare("
        SELECT 
            u.Email_Address, 
            u.First_Name, 
            p.Title 
        FROM [dbo].[USER] u
        JOIN [dbo].[POLL] p ON p.Poll_ID = :pollId
        WHERE u.User_ID = :voterId
    ");
    
        $stmt->execute(['voterId' => $voterId, 'pollId' => $pollId]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userInfo) {
            echo "User and poll information fetched successfully:<br>";
            print_r($userInfo);

            $emailSent = sendEmail($userInfo['Email_Address'], $userInfo['First_Name'], $userInfo['Title']);
            if ($emailSent) {
                echo "Email sent successfully to {$userInfo['First_Name']}!<br>";
            } else {
                echo "Failed to send email.<br>";
            }
        } else {
            echo "User or Poll not found.<br>";
        }
    } catch (PDOException $e) {
        error_log("Database query failed: " . $e->getMessage());
        echo "Database query error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "Invalid input parameters.<br>";
}
?>

