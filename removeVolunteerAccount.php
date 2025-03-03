<?php

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = false;

if (isset($_SESSION['_id'])) {
    require_once('include/input-validation.php');
    require_once('database/dbVolunteers.php');
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    die();
}

// Admin-only access
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

$staff = null;
$deleteSuccess = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $args = sanitize($_POST, null);
    if (isset($args['first-name']) && isset($args['last-name'])) {
        $first_name = $args['first-name'];
        $last_name = $args['last-name'];
        $staff = retrieve_volunteer_by_name($first_name, $last_name);
    }
    if (isset($args['delete']) && $staff) {
        $deleteSuccess = remove_volunteer_by_name($staff->getFirstName(), $staff->getLastName());
        $staff = null; // Clear staff data after deletion
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?php require_once('universal.inc') ?>
        <title>Search Staff Account</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/base.css">
        <style>
            .general tbody tr:hover {
                background-color: #cccccc; /* Light grey color */
            }
        </style>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Search Staff Account</h1>

        <form id="search_form" method="POST">
            <label>Enter first and last name to search for staff account to remove</label>
            <div class="search-container">
                <div class="search-label">
                    <label>First Name:</label>
                </div>
                <div>
                    <input type="text" id="first-name" name='first-name'>
                </div>
                <div class="search-label">
                    <label>Last Name:</label>
                </div>
                <div>
                    <input type="text" id="last-name" name='last-name' required>
                </div>
                <button type="submit" class="button_style">Search</button>
            </div>
        </form>
        
        <?php if ($staff): ?>
            <h3>Staff Account Information</h3>
            <div id="view-staff" style="margin-left: 20px; margin-right: 20px">
                <main class="general">
                    <fieldset>
                        <legend>General Information</legend>
                        <label>Name</label>
                        <p><?php echo $staff->getFirstName() . " " . $staff->getLastName(); ?></p>
                        <label>Date of Birth</label>
                        <p><?php echo $staff->getBirthdate(); ?></p>
                        <label>Address</label>
                        <p><?php echo $staff->getAddress(); ?></p>
                        <label>Cell</label>
                        <p><?php echo $staff->getCellPhone(); ?></p>
                        <label>Email</label>
                        <p><?php echo $staff->getEmail(); ?></p>
                        <label>Age</label>
                        <p><?php echo $staff->getAge(); ?></p>
                    </fieldset>
                </main>
            </div>
            <form method="POST" onsubmit="return confirm('Are you sure you want to remove this volunteer?');">
                <input type="hidden" name="first-name" value="<?php echo htmlspecialchars($staff->getFirstName()); ?>">
                <input type="hidden" name="last-name" value="<?php echo htmlspecialchars($staff->getLastName()); ?>">
                <button type="submit" name="delete" class="button_style">Remove Account</button>
            </form>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !$deleteSuccess): ?>
            <p style="color: red;">No volunteer found with that name.</p>
        <?php elseif ($deleteSuccess): ?>
            <p style="color: green;">Volunteer successfully removed.</p>
        <?php endif; ?>
        
        <a class="button cancel button_style" href="index.php">Return to Dashboard</a>
    </body>
</html>
