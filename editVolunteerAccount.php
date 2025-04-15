<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once('database/dbVolunteers.php');
require_once('include/input-validation.php');

$loggedIn = isset($_SESSION['_id']);
$accessLevel = $_SESSION['access_level'] ?? 0;

if (!$loggedIn || $accessLevel < 2) {
    header("Location: login.php");
    exit();
}

$updateSuccess = false;
$updateError = "";
$volunteer = null;

if (isset($_GET['first-name'], $_GET['last-name'])) {
    $firstName = trim($_GET['first-name']);
    $lastName = trim($_GET['last-name']);
    $volunteer = retrieve_volunteer_by_name($firstName, $lastName);
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['volunteer_id'])) {
    $volunteer = retrieve_volunteer_by_id($_POST['volunteer_id']);

    if ($volunteer) {
        $args = sanitize($_POST, null);

        $volunteer->setFirstName($args['first-name']);
        $volunteer->setMiddleInitial($args['middle-initial']);
        $volunteer->setLastName($args['last-name']);
        $volunteer->setAddress($args['address']);
        $volunteer->setCity($args['city']);
        $volunteer->setState($args['state']);
        $volunteer->setZip($args['zip']);
        $volunteer->setHomePhone($args['home-phone']);
        $volunteer->setCellPhone($args['cell-phone']);
        $volunteer->setAge($args['age']);
        $volunteer->setHasDriversLicense($args['has-drivers-license']);
        $volunteer->setTransportation($args['transportation']);
        $volunteer->setEmergencyContact1Name($args['econtact1-name']);
        $volunteer->setEmergencyContact1Relation($args['econtact1-relation']);
        $volunteer->setEmergencyContact1Phone($args['econtact1-phone']);
        $volunteer->setEmergencyContact2Name($args['econtact2-name']);
        $volunteer->setEmergencyContact2Relation($args['econtact2-relation']);
        $volunteer->setEmergencyContact2Phone($args['econtact2-phone']);
        $volunteer->setAllergies($args['allergies']);
        $volunteer->setSunStart($args['sun-start']);
        $volunteer->setSunEnd($args['sun-end']);
        $volunteer->setMonStart($args['mon-start']);
        $volunteer->setMonEnd($args['mon-end']);
        $volunteer->setTueStart($args['tue-start']);
        $volunteer->setTueEnd($args['tue-end']);
        $volunteer->setWedStart($args['wed-start']);
        $volunteer->setWedEnd($args['wed-end']);
        $volunteer->setThuStart($args['thu-start']);
        $volunteer->setThuEnd($args['thu-end']);
        $volunteer->setFriStart($args['fri-start']);
        $volunteer->setFriEnd($args['fri-end']);
        $volunteer->setSatStart($args['sat-start']);
        $volunteer->setSatEnd($args['sat-end']);
        $volunteer->setDateAvailable($args['date-available']);
        $volunteer->setMinHours($args['min-hours']);
        $volunteer->setMaxHours($args['max-hours']);

        if (update_volunteer($volunteer)) {
            $updateSuccess = true;
        } else {
            $updateError = "Failed to update volunteer.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>Edit Volunteer Account</title>
    <link rel="stylesheet" href="css/base.css">
</head>
<body>
<?php require_once('header.php') ?>
<main class="form-container">
    <h1>Edit Volunteer Account</h1>
    <?php if ($updateSuccess): ?>
        <div class="alert alert-success">Volunteer account updated successfully!</div>
    <?php elseif ($updateError): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($updateError) ?></div>
    <?php endif; ?>

    <?php if ($volunteer): ?>
        <form method="POST">
            <input type="hidden" name="volunteer_id" value="<?= $volunteer->getId() ?>">
            <div class="form-section">
                <h3>Basic Information</h3>
                <label>First Name:</label>
                <input type="text" name="first-name" value="<?= $volunteer->getFirstName() ?>" required>
                <label>Middle Initial:</label>
                <input type="text" name="middle-initial" value="<?= $volunteer->getMiddleInitial() ?>">
                <label>Last Name:</label>
                <input type="text" name="last-name" value="<?= $volunteer->getLastName() ?>" required>
                <label>Address:</label>
                <input type="text" name="address" value="<?= $volunteer->getAddress() ?>" required>
                <label>City:</label>
                <input type="text" name="city" value="<?= $volunteer->getCity() ?>" required>
                <label>State:</label>
                <input type="text" name="state" value="<?= $volunteer->getState() ?>" required>
                <label>Zip:</label>
                <input type="text" name="zip" value="<?= $volunteer->getZip() ?>" required>
                <label>Home Phone:</label>
                <input type="text" name="home-phone" value="<?= $volunteer->getHomePhone() ?>">
                <label>Cell Phone:</label>
                <input type="text" name="cell-phone" value="<?= $volunteer->getCellPhone() ?>">
                <label>Age:</label>
                <input type="text" name="age" value="<?= $volunteer->getAge() ?>">
                <label>Has Driver's License:</label>
                <input type="text" name="has-drivers-license" value="<?= $volunteer->getHasDriversLicense() ?>">
                <label>Transportation:</label>
                <input type="text" name="transportation" value="<?= $volunteer->getTransportation() ?>">
            </div>
            <div class="form-section">
                <h3>Emergency Contacts</h3>
                <label>Emergency Contact 1 Name:</label>
                <input type="text" name="econtact1-name" value="<?= $volunteer->getEmergencyContact1Name() ?>">
                <label>Emergency Contact 1 Relation:</label>
                <input type="text" name="econtact1-relation" value="<?= $volunteer->getEmergencyContact1Relation() ?>">
                <label>Emergency Contact 1 Phone:</label>
                <input type="text" name="econtact1-phone" value="<?= $volunteer->getEmergencyContact1Phone() ?>">
                <label>Emergency Contact 2 Name:</label>
                <input type="text" name="econtact2-name" value="<?= $volunteer->getEmergencyContact2Name() ?>">
                <label>Emergency Contact 2 Relation:</label>
                <input type="text" name="econtact2-relation" value="<?= $volunteer->getEmergencyContact2Relation() ?>">
                <label>Emergency Contact 2 Phone:</label>
                <input type="text" name="econtact2-phone" value="<?= $volunteer->getEmergencyContact2Phone() ?>">
            </div>
            <div class="form-section">
                <h3>Availability & Additional Info</h3>
                <label>Allergies:</label>
                <input type="text" name="allergies" value="<?= $volunteer->getAllergies() ?>">
                <label>Availability (Sun - Sat Start/End):</label>
                <?php foreach (["sun","mon","tue","wed","thu","fri","sat"] as $day): ?>
                    <label><?= ucfirst($day) ?> Start:</label>
                    <input type="text" name="<?= $day ?>-start" value="<?= $volunteer->{"get" . ucfirst($day) . "Start"}() ?>">
                    <label><?= ucfirst($day) ?> End:</label>
                    <input type="text" name="<?= $day ?>-end" value="<?= $volunteer->{"get" . ucfirst($day) . "End"}() ?>">
                <?php endforeach; ?>
                <label>Date Available:</label>
                <input type="date" name="date-available" value="<?= $volunteer->getDateAvailable() ?>">
                <label>Min Hours:</label>
                <input type="text" name="min-hours" value="<?= $volunteer->getMinHours() ?>">
                <label>Max Hours:</label>
                <input type="text" name="max-hours" value="<?= $volunteer->getMaxHours() ?>">
            </div>
            <button type="submit" class="btn btn-success">Update Volunteer</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

