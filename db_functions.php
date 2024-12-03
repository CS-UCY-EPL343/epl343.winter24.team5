<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Φόρτωση PHPMailer
function getDatabaseConnection()
{
    try {
        $config = include 'config.php';
        $conn = new PDO("sqlsrv:Server={$config['serverName']};Database={$config['dbName']}", $config['userName'], $config['password']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function insertUser(
    $politicalId,
    $firstName,
    $lastName,
    $username,
    $password,
    $email,
    $mobilePhone,
    $userRoleId,
    $gender,
    $dateOfBirth,
    $address
) {
    try {
        // Establish database connection
        $conn = getDatabaseConnection();

        // Prepare the stored procedure call
        $stmt = $conn->prepare("
            EXEC [dbo].[InsertUser] 
            @Political_ID = :political_id,
            @First_Name = :first_name,
            @Last_Name = :last_name,
            @Username = :username,
            @Password = :password,
            @Email_Address = :email_address,
            @Mobile_Phone = :mobile_phone,
            @User_Role_ID = :user_role_id,
            @Gender = :gender,
            @Date_of_Birth = :date_of_birth,
            @Address = :address
        ");

        // Bind parameters
        $stmt->bindParam(':political_id', $politicalId);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email_address', $email);
        $stmt->bindParam(':mobile_phone', $mobilePhone);
        $stmt->bindParam(':user_role_id', $userRoleId);
        $stmt->bindParam(':gender', $gender, is_null($gender) ? PDO::PARAM_NULL : PDO::PARAM_BOOL);
        $stmt->bindParam(':date_of_birth', $dateOfBirth);
        $stmt->bindParam(':address', $address);

        // Execute the query
        $stmt->execute();

        return null; // Success
    } catch (PDOException $e) {
        handleSqlError($e);
    }
}

function getUserRoles()
{
    try {
        $conn = getDatabaseConnection(); // Establish a database connection
        $stmt = $conn->prepare("EXEC GetUserRolesForDropDown"); // Execute the stored procedure
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all roles as an associative array
    } catch (PDOException $e) {
        handleSqlError($e);
    }
}


function userLogin($username, $password)
{
    try {
        $conn = getDatabaseConnection();

        // Prepare the SQL to call the stored procedure with a parameter
        $stmt = $conn->prepare("EXEC UserLogin @Username = :username, @Password=:password");

        // Bind the input parameter to the stored procedure
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);

        // Execute the stored procedure
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['User_Role'] ?? null; // Return the role or null if not found


    } catch (PDOException $e) {
        handleSqlError($e);
    }
}


function getUserId($username)
{
    try {
        $conn = getDatabaseConnection();

        // Prepare the SQL to call the stored procedure with a parameter
        $stmt = $conn->prepare("EXEC GetUserId @username = :username");

        // Bind the input parameter to the stored procedure
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        // Execute the stored procedure
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['User_ID'] ?? null; // Return the User_ID or null if not found

    } catch (PDOException $e) {
        handleSqlError($e);
    }
}

function getPendingApprovals()
{
    try {
        $conn = getDatabaseConnection();
        $stmt = $conn->prepare("EXEC GetPendingApprovals");
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    } catch (PDOException $e) {
        handleSqlError($e);
    }
}

function getJobListings()
{
    try {
        $conn = getDatabaseConnection();
        $stmt = $conn->prepare("EXEC GetJobListings");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleSqlError($e);
    }
}

function createJobConfiguration($jobId, $userId, $configName, $parameters = null, $scheduleTime = null, $recurrence = null)
{
    global $db; // Assuming `$db` is the PDO connection initialized in `db_functions.php`

    try {
        $sql = "
            INSERT INTO [dbo].[JOB_CONFIGURATION] (
                Job_ID,
                User_ID,
                Configuration_Name,
                Parameters,
                Schedule_Time,
                Recurrence
            ) VALUES (
                :jobId,
                :userId,
                :configName,
                :parameters,
                :scheduleTime,
                :recurrence
            );
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':jobId', $jobId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':configName', $configName, PDO::PARAM_STR);
        $stmt->bindParam(':parameters', $parameters, PDO::PARAM_STR);
        $stmt->bindParam(':scheduleTime', $scheduleTime, PDO::PARAM_STR);
        $stmt->bindParam(':recurrence', $recurrence, PDO::PARAM_STR);

        $stmt->execute();

        return $db->lastInsertId();
    } catch (PDOException $e) {
        handleSqlError($e);
    }
}

function approveOrRejectUser($userId, $approvalStatus)
{
    try {
        $conn = getDatabaseConnection(); // Assuming this is your database connection function

        $stmt = $conn->prepare("
            EXEC [dbo].[ApproveOrRejectUser] 
            @User_ID = :user_id, 
            @Approval_Status = :approval_status
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':approval_status', $approvalStatus, PDO::PARAM_STR);

        $stmt->execute();
        return true; // Indicate success
    } catch (PDOException $e) {
        handleSqlError($e);
    }
}

function createPoll($creatorId, $title, $description, $expirationDate, $status)
{
    try {
        $pdo = getDatabaseConnection(); // Replace with your DB connection function
        $stmt = $pdo->prepare("
            EXEC CreatePoll 
                @Creator_ID = :Creator_ID, 
                @Title = :Title, 
                @Description = :Description, 
                @Expiration_Date = :Expiration_Date, 
                @Status = :Status
        ");
        $stmt->bindParam(':Creator_ID', $creatorId, PDO::PARAM_INT);
        $stmt->bindParam(':Title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':Description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':Expiration_Date', $expirationDate, PDO::PARAM_STR); // Pass as a string in DATETIME format
        $stmt->bindParam(':Status', $status, PDO::PARAM_STR);
        return $stmt->execute();
    } catch (PDOException $e) {
        handleSqlError($e); // Pass to handleSqlError for detailed logging
    }
}

function addUserToPoll($pollId, $userId)
{
    try {
        $pdo = getDatabaseConnection(); // Replace with your DB connection function
        $stmt = $pdo->prepare("EXEC AddUserToPoll :Poll_ID, :User_ID");
        $stmt->bindParam(':Poll_ID', $pollId, PDO::PARAM_INT);
        $stmt->bindParam(':User_ID', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        handleSqlError($e); // Pass to handleSqlError for detailed logging
    }
}

function updatePollStatusAndVerdict($pollID)
{
    try {
        $db = getDatabaseConnection(); // Assuming you have a function to get the DB connection
        $stmt = $db->prepare("EXEC UpdatePollStatusAndVerdict @Poll_ID = :pollID");
        $stmt->bindParam(':pollID', $pollID, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        handleSqlError($e); // Use your error-handling function
    }
}

function getAllPolls()
{
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("EXEC GetAllPolls");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleSqlError($e); // Pass to handleSqlError for detailed logging
    }
}

function getAllUsers($pollId)
{
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("EXEC GetAllUsers :Poll_ID");
        $stmt->bindParam(':Poll_ID', $pollId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleSqlError($e); // Pass to handleSqlError for detailed logging
    }
}

function getPollDetails($pollId)
{
    try {
        $pdo = getDatabaseConnection(); // Replace with your DB connection function
        $stmt = $pdo->prepare("EXEC GetPollDetails :Poll_ID");
        $stmt->bindParam(':Poll_ID', $pollId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleSqlError($e); // Handle the SQL error
    }
}


function getUserPolls($userId)
{
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("EXEC GetUserPolls :UserID");
        $stmt->bindParam(':UserID', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return an empty array if no polls are found
        return $polls ?: [];
    } catch (PDOException $e) {
        handleSqlError($e);
        return [];
    }
}


function getPoll($pollId)
{
    try {
        $pdo = getDatabaseConnection(); // Ensure this function connects to your database
        $stmt = $pdo->prepare("EXEC GetPoll :PollID");
        $stmt->bindParam(':PollID', $pollId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single poll as an associative array
    } catch (PDOException $e) {
        handleSqlError($e); // Handle the SQL error appropriately
    }
}
function addVote($voterId, $pollId, $decision)
{
    try {
        $pdo = getDatabaseConnection(); // Ensure this function connects to your database
        $stmt = $pdo->prepare("EXEC AddVote :Voter_ID, :Poll_ID, :Decision");
        $stmt->bindParam(':Voter_ID', $voterId, PDO::PARAM_INT);
        $stmt->bindParam(':Poll_ID', $pollId, PDO::PARAM_INT);
        $stmt->bindParam(':Decision', $decision, PDO::PARAM_BOOL);
        $stmt->execute();
        return true; // Return true on successful execution
    } catch (PDOException $e) {
        handleSqlError($e); // Handle the SQL error appropriately
        return false; // Return false on failure
    }
}

function getPollTitleById($pollId)
{
    try {
        $pdo = getDatabaseConnection(); // Ensure this function connects to your database

        // Use the correct stored procedure call
        $stmt = $pdo->prepare("EXEC getPollTitleByID @PollID = :PollID");

        // Bind the parameter using the correct name
        $stmt->bindParam(':PollID', $pollId, PDO::PARAM_INT);

        // Execute the stored procedure
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the poll title or null if not found
        return $result['Title'] ?? null;
    } catch (PDOException $e) {
        handleSqlError($e); // Handle the SQL error appropriately
    }
}



function handleSqlError(PDOException $e)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Extract the detailed SQL error message
    $errorMessage = $e->getMessage();

    // Identify specific errors
    if (preg_match('/Cannot insert the value NULL into column \'([^\']+)\'.*?/', $errorMessage, $matches)) {
        // Handle NULL constraint violations
        $errorMessage = "The column '" . htmlspecialchars($matches[1]) . "' does not allow NULL values. Please provide a valid value.";
    } elseif (preg_match('/Violation of UNIQUE KEY constraint.*?\'([^\']+)\'.*?/', $errorMessage, $matches)) {
        // Handle UNIQUE constraint violations
        $errorMessage = "The value violates a unique constraint on '" . htmlspecialchars($matches[1]) . "'. Please ensure the value is unique.";
    } elseif (preg_match('/\[SQL Server\](.*)/', $errorMessage, $matches)) {
        // Extract the message after "[SQL Server]"
        $errorMessage = trim($matches[1]);
    } elseif (preg_match('/\](.*)/', $errorMessage, $matches)) {
        // Extract the part after the last closing bracket "]"
        $errorMessage = trim($matches[1]);
    }

    // Log the full original error for debugging
    error_log('SQL Error: ' . $e->getMessage());

    // Store the cleaned or specific error message in the session
    $_SESSION['error_message'] = $errorMessage;

    // Redirect to the error page
    header('Location: error.php');
    exit();
}

/**
 * Αποστολή email πρόσκλησης για συμμετοχή σε δημοσκόπηση
 */
function sendPollInvitationEmail($recipientEmail, $recipientName, $pollTitle)
{
    $mail = new PHPMailer(true);

    try {
        // Ρυθμίσεις SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mitilineos123@gmail.com'; // Ενημέρωσε με το email σου
        $mail->Password = 'dqog gaos gpce flfj';     // Ενημέρωσε με το App Password σου
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Ρυθμίσεις αποστολέα και παραλήπτη
        $mail->setFrom('mitilineos123@gmail.com', 'System');
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

/**
 * Λήψη και αποστολή email σε χρήστη για δημοσκόπηση
 */
function notifyUserForPoll($voterId, $pollId)
{
    try {
        $conn = getDatabaseConnection();

        // Λήψη πληροφοριών χρήστη και δημοσκόπησης
        $stmt = $conn->prepare("
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
            $emailSent = sendPollInvitationEmail($userInfo['Email_Address'], $userInfo['First_Name'], $userInfo['Title']);
            if ($emailSent) {
                return "Email sent successfully to {$userInfo['First_Name']}!";
            } else {
                return "Failed to send email.";
            }
        } else {
            return "User or Poll not found.";
        }
    } catch (PDOException $e) {
        handleSqlError($e);
    }
}

function editPoll($pollId, $newStatus, $newDescription, $newExpirationDate)
{
    try {
        $pdo = getDatabaseConnection(); // Ensure this function connects to your database

        // Prepare the SQL to call the stored procedure
        $stmt = $pdo->prepare("
            EXEC EditPoll 
                @Poll_ID = :Poll_ID, 
                @New_Status = :New_Status, 
                @New_Description = :New_Description, 
                @New_Expiration_Date = :New_Expiration_Date
        ");

        // Bind the parameters
        $stmt->bindParam(':Poll_ID', $pollId, PDO::PARAM_INT);
        $stmt->bindParam(':New_Status', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':New_Description', $newDescription, PDO::PARAM_STR);
        $stmt->bindParam(':New_Expiration_Date', $newExpirationDate, PDO::PARAM_STR);

        // Execute the stored procedure
        $stmt->execute();

        return "Poll updated successfully.";
    } catch (PDOException $e) {
        handleSqlError($e); // Pass to handleSqlError for detailed logging
    }
}

function createTask($creatorId, $title, $description, $dateDue) {
    try {
        $pdo = getDatabaseConnection(); 
        $stmt = $pdo->prepare("EXEC CreateTask :user_id, :title, :description, :date_due");
        $stmt->bindParam(':user_id', $creatorId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':date_due', $dateDue, PDO::PARAM_STR);

        return $stmt->execute();
    } catch (PDOException $e) {
        handleSqlError($e); // 
        return false; // Return false if an error occurs
    }
}

function getAllTasks() {
    try {
        $pdo = getDatabaseConnection(); 
        $stmt = $pdo->query("EXEC GetAllTasks"); // Call the stored procedure
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results as an associative array
    } catch (PDOException $e) {
        handleSqlError($e); // Log and handle the error
        return []; // Return an empty array on error
    }
}

function searchTasksByTitle($searchTerm) {
    try {
        $pdo = getDatabaseConnection(); // Ensure this returns a valid PDO connection
        $stmt = $pdo->prepare("EXEC SearchTasksByTitle :search");
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        handleSqlError($e); // Log or display the error
        return []; // Return an empty array on error
    }
}

