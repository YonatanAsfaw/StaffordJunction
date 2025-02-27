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
    require_once('database/dbStaff.php');
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

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $args = sanitize($_POST, null);
    if (isset($args['first-name'])) {
        $first_name = $args['first-name'];
        $staff = retrieve_staff_by_first_name($first_name);
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
        <label>Enter first name to search for staff account to remove</label>
            <div class="search-container">
                <div class="search-label">
                    <label>First Name:</label>
                </div>
                <div>
                    <input type="text" id="first-name" name='first-name'>
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
                        <label>Phone</label>
                        <p><?php echo $staff->getPhone(); ?></p>
                        <label>Email</label>
                        <p><?php echo $staff->getEmail(); ?></p>
                        <label>Job Title</label>
                        <p><?php echo $staff->getJobTitle(); ?></p>
                    </fieldset>
                </main>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == "POST"): ?>
            <p style="color: red;">No staff member found with that first name.</p>
        <?php endif; ?>
        
        <a class="button cancel button_style" href="index.php">Return to Dashboard</a>
    </body>
</html>
