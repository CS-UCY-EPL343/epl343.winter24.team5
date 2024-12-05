<?php
require_once 'db_functions.php';
require_once 'session_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Job_Instance_ID'])) {
    $instanceID = $_POST['Job_Instance_ID'];
    $configurationID = $_POST['Job_Configuration_ID']; // Ensure the configuration ID is passed
    $pdo = getDatabaseConnection();

    try {
        // Generate a random result: 95% success, 5% failure
        $runSuccess = rand(1, 100) <= 95;

        $stmt = $pdo->prepare("
            UPDATE JOB_INSTANCE
            SET 
                Previous_Run_Status = :Run_Status,
                Previous_Completion_Time = GETDATE()
            WHERE Job_Instance_ID = :Job_Instance_ID
        ");

        $runStatus = $runSuccess ? 'Completed' : 'Failed';
        $stmt->bindParam(':Run_Status', $runStatus, PDO::PARAM_STR);
        $stmt->bindParam(':Job_Instance_ID', $instanceID, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['run_message'] = $runSuccess ? 'Run Completed Successfully!' : 'Run Failed!';
        header("Location: job_instance.php?Job_Configuration_ID=" . htmlspecialchars($configurationID));
        exit();
    } catch (PDOException $e) {
        $_SESSION['run_message'] = "Failed to run instance: " . $e->getMessage();
        header("Location: job_instance.php?Job_Configuration_ID=" . htmlspecialchars($configurationID));
        exit();
    }
}
