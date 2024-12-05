<?php
require_once 'db_functions.php';
require_once 'navbar.php';

// Fetch user roles for the dropdown
$userRoles = getUserRoles();

// Function to calculate the maximum and minimum valid dates
function getDateRange()
{
    $maxDate = date('Y-m-d', strtotime('-18 years'));
    $minDate = date('Y-m-d', strtotime('-100 years'));
    return ['min' => $minDate, 'max' => $maxDate];
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $politicalId = htmlspecialchars($_POST['political_id'] ?? null);
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $email = htmlspecialchars($_POST['email_address']);
    $mobilePhone = htmlspecialchars($_POST['mobile_phone']);
    $userRoleId = (int) $_POST['user_role_id'];
    $gender = $_POST['gender'] === "" ? null : (bool) $_POST['gender'];
    $dateOfBirth = htmlspecialchars($_POST['date_of_birth'] ?? '');
    $address = htmlspecialchars($_POST['address']);

    $dateRange = getDateRange();
    if (strtotime($dateOfBirth) > strtotime($dateRange['max']) || strtotime($dateOfBirth) < strtotime($dateRange['min'])) {
        $error = "Your age must be between 18 and 100 years old to create an account.";
    } else
        try {
            $error = insertUser(
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
            );

            if ($error === null) {
                header("Location: sign_in_account.php");
                exit();
            }
        } catch (PDOException $e) {
            die("Account creation failed: " . $e->getMessage());
        }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Account</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <div class="container-account">
        <h2>Create Account</h2>
        <?php if (isset($error)): ?>
            <p1><?= htmlspecialchars($error) ?></p1>
        <?php endif; ?>
        <form action="create_account.php" method="post">
            <label for="political_id">ID/PPN:</label>
            <input type="text" id="political_id" name="political_id" required>

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="email_address">E-mail Address:</label>
            <input type="email" id="email_address" name="email_address" required>

            <label for="mobile_phone">Mobile Phone:</label>
            <input type="text" id="mobile_phone" name="mobile_phone" required>

            <label for="user_role_id">Role:</label>
            <select id="user_role_id" name="user_role_id" required>
                <option disabled selected value="">Select your role.</option>
                <?php foreach ($userRoles as $role): ?>
                    <option value="<?= htmlspecialchars($role['User_Role_ID']) ?>">
                        <?= htmlspecialchars($role['User_Role_Name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option disabled selected value="">Select your gender.</option>
                <option value="1">Male</option>
                <option value="0">Female</option>
                <option value="">Other</option>
            </select>

            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required>



            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <button type="submit">Create Account</button>
        </form>
    </div>
    <?php require_once 'footer.php'; ?>

    <script>
        const birthdateInput = document.getElementById('date_of_birth');

        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
        const minDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate());

        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        birthdateInput.min = formatDate(minDate);
        birthdateInput.max = formatDate(maxDate);

        function showCalendar() {
            birthdateInput.focus();
        }
    </script>
</body>

</html>