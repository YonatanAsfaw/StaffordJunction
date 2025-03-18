<?php
session_cache_expire(30);
session_start();
require_once("database/dbVolunteers.php");

if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 2) {
    header("Location: index.php");
    die();
}

$firstName = $_GET['first-name'] ?? null;
$lastName = $_GET['last-name'] ?? null;
$volunteer = null;
$deleteSuccess = false;

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete'])) {
    $firstName = $_POST['first-name'] ?? null;
    $lastName = $_POST['last-name'] ?? null;

    if ($firstName && $lastName) {
        $deleteSuccess = remove_volunteer_by_name($firstName, $lastName);
        if ($deleteSuccess) {
            header("Location: removeVolunteerAccount.php?deleteSuccess=1");
            exit();
        } else {
            $errorMessage = "Error: Could not delete volunteer.";
        }
    }
}

if ($firstName && $lastName) {
    $volunteer = retrieve_volunteer_by_name($firstName, $lastName);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Volunteer Account</title>
    <?php require_once('universal.inc'); ?>
</head>
<body>
    <?php require_once('header.php'); ?>

    <h1>Account Page for 
        <?php 
        if ($volunteer) {
            echo htmlspecialchars($volunteer->getFirstName() . " " . $volunteer->getLastName());
        } else {
            echo "Volunteer Not Found";
        }
        ?>
    </h1>

    <?php if ($volunteer): ?>
        <div id="view-volunteer" style="margin: 20px;">
            <main class="general">
                <fieldset>
                    <legend>General Information</legend>
                    <label>Name:</label>
                    <p><?= htmlspecialchars($volunteer->getFirstName() . " " . $volunteer->getLastName()); ?></p>
                    <label>Date of Birth:</label>
                    <p><?= htmlspecialchars($volunteer->getBirthdate()); ?></p>
                    <label>Address:</label>
                    <p><?= htmlspecialchars($volunteer->getAddress()); ?></p>
                    <label>Phone:</label>
                    <p><?= htmlspecialchars($volunteer->getCellPhone()); ?></p>
                    <label>Email:</label>
                    <p><?= htmlspecialchars($volunteer->getEmail()); ?></p>
                </fieldset>
            </main>
        </div>

        <form method="POST" onsubmit="return confirm('Are you sure you want to remove this volunteer?');">
            <input type="hidden" name="first-name" value="<?= htmlspecialchars($volunteer->getFirstName()); ?>">
            <input type="hidden" name="last-name" value="<?= htmlspecialchars($volunteer->getLastName()); ?>">
            <button type="submit" name="delete" class="button_style">Remove Account</button>
        </form>
    <?php else: ?>
        <!-- <p style="color: red;">No volunteer found with the given details. Please try again.</p> -->
    <?php endif; ?>

    <a class="button cancel button_style" href="removeVolunteerAccount.php">Back to Volunteer List</a>
</body>
</html>