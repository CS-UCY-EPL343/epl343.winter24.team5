<?php
require_once 'navbar.php';
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
$configID = $_GET['Job_Configuration_ID'] ?? null;
$errorMessage = '';
$successMessage = '';
$configuration = [];

// Fetch the existing configuration details
if ($configID) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT * FROM JOB_CONFIGURATION WHERE Job_Configuration_ID = :ConfigID AND User_ID = :UserID");
    $stmt->bindParam(':ConfigID', $configID, PDO::PARAM_INT);
    $stmt->bindParam(':UserID', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $configuration = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle the form submission for updating the configuration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configName = $_POST['config_name'] ?? '';
    $parameters = $_POST['parameters'] ?? '';

    $result = updateJobConfiguration($configID, $configName, $parameters);

    if ($result) {
        $successMessage = "Configuration updated successfully.";
        header("Location: configuration.php?Job_ID=" . htmlspecialchars($configuration['Job_ID']));
        exit();
    } else {
        $errorMessage = "Failed to update configuration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Configuration</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .go-back-button {
            display: inline-block;
            margin: 15px 0;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .go-back-button:hover {
            background-color: #0056b3;
        }

        .go-back-container {
            margin-top: 10px;
            margin-left: 0;
            /* Align the button to the left */
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <main class="dashboard-main">
            <!-- Go Back Button -->
            <div class="go-back-container">
                <a href="javascript:history.back()" class="go-back-button">Go Back</a>
            </div>

            <div class="config-container">
                <div class="config-box">
                    <h1>Edit Configuration</h1>

                    <?php if ($errorMessage): ?>
                        <div class="error-message" style="color: red;"><?= htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                        <div class="success-message" style="color: green;"><?= htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>

                    <form action="edit_configuration.php?Job_Configuration_ID=<?= htmlspecialchars($configID); ?>" method="POST">
                        <table class="config-table">
                            <tr>
                                <td><label for="config_name">Configuration Name:</label></td>
                                <td><input type="text" id="config_name" name="config_name" value="<?= htmlspecialchars($configuration['Configuration_Name'] ?? ''); ?>" required></td>
                            </tr>
                            <tr>
                                <td><label for="parameters">Parameters:</label></td>
                                <td><input type="text" id="parameters" name="parameters"
                                        value="<?= htmlspecialchars($configuration['Parameters'] ?? ''); ?>" vrequired></td>
                            </tr>
                        </table>
                        <button type="submit" class="configure-button">Update Configuration</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php require_once 'footer.php'; ?>

</body>

</html>