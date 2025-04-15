<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once('database/dbStaff.php');
require_once('include/input-validation.php');

$loggedIn = isset($_SESSION['_id']);
$accessLevel = $_SESSION['access_level'] ?? 0;

if (!$loggedIn || $accessLevel < 2) {
    header("Location: login.php");
    exit();
}

$updateSuccess = false;
$updateError = "";
$staff = null;

if (isset($_GET['first-name'], $_GET['last-name'])) {
    $firstName = trim($_GET['first-name']);
    $lastName = trim($_GET['last-name']);
    $staff = retrieve_staff_by_name($firstName, $lastName);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'], $_POST['staff_id'])) {
    $staff = retrieve_staff_by_id($_POST['staff_id']);

    if ($staff) {
        $staff->setFirstName(trim($_POST['first-name']));
        $staff->setLastName(trim($_POST['last-name']));
        $staff->setAddress(trim($_POST['address']));
        $staff->setEmail(trim($_POST['email']));
        $staff->setPhone(trim($_POST['phone']));
        $staff->setEContactName(trim($_POST['econtactName']));
        $staff->setEContactPhone(trim($_POST['econtactPhone']));
        $staff->setJobTitle(trim($_POST['jobTitle']));

        if (update_staff($staff)) {
            $updateSuccess = true;
        } else {
            $updateError = "Failed to update staff member.";
        }
    } else {
        $updateError = "Staff member not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>Modify Staff Account</title>
    <link rel="stylesheet" href="css/base.css">
</head>
<body>
<?php require_once('header.php'); ?>
<main class="form-container">
    <h1>Modify Staff Account</h1>

    <?php if ($updateSuccess): ?>
        <div class="happy-toast" style="text-align: center;">Staff member successfully updated!</div>
    <?php elseif ($updateError): ?>
        <div class="error-toast" style="text-align: center; color: red; font-weight: bold;"><?= htmlspecialchars($updateError) ?></div>
    <?php endif; ?>

    <?php if ($staff): ?>
    <form method="POST" class="form-card">
        <input type="hidden" name="staff_id" value="<?= $staff->getId() ?>">

        <label>First Name: <input type="text" name="first-name" value="<?= htmlspecialchars($staff->getFirstName()) ?>" required></label>
        <label>Last Name: <input type="text" name="last-name" value="<?= htmlspecialchars($staff->getLastName()) ?>" required></label>
        <label>Address: <input type="text" name="address" value="<?= htmlspecialchars($staff->getAddress()) ?>" required></label>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($staff->getEmail()) ?>" required></label>
        <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($staff->getPhone()) ?>" required></label>
        <label>Emergency Contact Name: <input type="text" name="econtactName" value="<?= htmlspecialchars($staff->getEContactName()) ?>" required></label>
        <label>Emergency Contact Phone: <input type="text" name="econtactPhone" value="<?= htmlspecialchars($staff->getEContactPhone()) ?>" required></label>
        <label>Job Title: <input type="text" name="jobTitle" value="<?= htmlspecialchars($staff->getJobTitle()) ?>" required></label>

        <button type="submit" name="update" class="button_style">Update Account</button>
    </form>
    <?php else: ?>
        <p class="error-toast" style="color: red; text-align: center; font-weight: bold;">Staff member not found.</p>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 2rem;">
        <a class="button cancel" href="modifyStaffAccount.php">Return to Staff List</a>
    </div>
</main>
</body>
</html>
