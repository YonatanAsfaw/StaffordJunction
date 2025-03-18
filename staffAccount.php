<?php
session_cache_expire(30);
session_start();
require_once("database/dbStaff.php");

if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 2) {
    header("Location: index.php");
    die();
}

// Ensure first name and last name are retrieved properly
$firstName = $_GET['first-name'] ?? null;
$lastName = $_GET['last-name'] ?? null;
$staff = null;
$deleteSuccess = false;

// Handle staff deletion
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete'])) {
    $firstName = $_POST['first-name'] ?? null;
    $lastName = $_POST['last-name'] ?? null;

    if ($firstName && $lastName) {
        $deleteSuccess = remove_staff_by_name($firstName, $lastName);
        if ($deleteSuccess) {
            // Redirect back to staff list with success message
            header("Location: removeStaffAccount.php?deleteSuccess=1");
            exit();
        } else {
            $errorMessage = "Error: Could not delete staff member.";
        }
    }
}

// Retrieve staff details if parameters are set
if ($firstName && $lastName) {
    $staff = retrieve_staff_by_name($firstName, $lastName);
}

//echo "First Name: " . htmlspecialchars($firstName) . "<br>";
//echo "Last Name: " . htmlspecialchars($lastName) . "<br>";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stafford Junction | Staff Account</title>
    <?php require_once('universal.inc'); ?>
</head>
<body>
    <?php require_once('header.php'); ?>

    <h1>Account Page for 
        <?php 
        if ($staff) {
            echo htmlspecialchars($staff->getFirstName() . " " . $staff->getLastName());
        } else {
            echo "Staff Member Not Found";
        }
        ?>
    </h1>

    <?php if ($staff): ?>
        <div id="view-staff" style="margin-left: 20px; margin-right: 20px">
            <main class="general">
                <fieldset>
                    <legend>General Information</legend>
                    <label>Name:</label>
                    <p><?= htmlspecialchars($staff->getFirstName() . " " . $staff->getLastName()); ?></p>
                    <label>Date of Birth:</label>
                    <p><?= htmlspecialchars($staff->getBirthdate()); ?></p>
                    <label>Address:</label>
                    <p><?= htmlspecialchars($staff->getAddress()); ?></p>
                    <label>Phone:</label>
                    <p><?= htmlspecialchars($staff->getPhone()); ?></p>
                    <label>Email:</label>
                    <p><?= htmlspecialchars($staff->getEmail()); ?></p>
                    <label>Job Title:</label>
                    <p><?= htmlspecialchars($staff->getJobTitle()); ?></p>
                </fieldset>
            </main>
	</div>

        <!-- Delete Form -->
        <form method="POST" onsubmit="return confirm('Are you sure you want to remove this staff member?');">
            <input type="hidden" name="first-name" value="<?= htmlspecialchars($staff->getFirstName()); ?>">
            <input type="hidden" name="last-name" value="<?= htmlspecialchars($staff->getLastName()); ?>">
            <button type="submit" name="delete" class="button_style">Remove Account</button>
        </form>
    <?php else: ?>
        <p style="color: red;">No staff member found with the given details. Please try again.</p>
    <?php endif; ?>


    <a class="button cancel button_style" href="removeStaffAccount.php">Back to Staff List</a>
</body>
</html>
