<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Φόρτωση PHPMailer

// Σύνδεση με τη βάση δεδομένων
try {
    $pdo = new PDO('sqlsrv:Server=epl343project.database.windows.net;Database=EPL343DB', 'epl343project', '5thSemesterPomba!', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Λειτουργία για αποστολή email
function sendEmail($recipientEmail, $recipientName, $pollTitle) {
    $mail = new PHPMailer(true);

    try {
        // Ρυθμίσεις SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mitilineos123@gmail.com'; 
        $mail->Password = 'dqog gaos gpce flfj'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Ρυθμίσεις αποστολέα και παραλήπτη
        $mail->setFrom('mitilineos123@gmail.com', 'Lil Indian');
        $mail->addAddress($recipientEmail, $recipientName);

        // Περιεχόμενο email
        $mail->isHTML(true);
        $mail->Subject = 'You have been added to a poll!';
        $mail->Body = "
            <h1>Hello, $recipientName!</h1>
            <p>You have been added to the poll: <strong>$pollTitle</strong>.</p>
            <p>Please login to the system to participate.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Παράμετροι εισόδου (π.χ., από request ή σύστημα)
$voterId = $_POST['Voter_ID'] ?? null;
$pollId = $_POST['Poll_ID'] ?? null;

if ($voterId && $pollId) {
    try {
        // Λήψη πληροφοριών χρήστη και δημοσκόπησης
        $stmt = $pdo->prepare("
            SELECT 
                u.Email_Address, 
                u.First_Name, 
                p.Title 
            FROM dbo.USER u
            JOIN dbo.POLL p ON p.Poll_ID = :pollId
            WHERE u.User_ID = :voterId
        ");
        $stmt->execute(['voterId' => $voterId, 'pollId' => $pollId]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userInfo) {
            $emailSent = sendEmail($userInfo['Email_Address'], $userInfo['First_Name'], $userInfo['Title']);
            if ($emailSent) {
                echo "Email sent successfully to {$userInfo['First_Name']}!";
            } else {
                echo "Failed to send email.";
            }
        } else {
            echo "User or Poll not found.";
        }
    } catch (PDOException $e) {
        error_log("Database query failed: " . $e->getMessage());
        echo "An error occurred.";
    }
} else {
    echo "Invalid input parameters.";
}
