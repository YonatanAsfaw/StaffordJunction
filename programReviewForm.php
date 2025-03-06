<?php

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
require_once('database/dbProgramReviewForm.php');

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = false;

// Ensure user is logged in
if (isset($_SESSION['_id'])) {
    require_once('include/input-validation.php'); 
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['programReviewForm'])) {
    // Retrieve data from the form
    $family = $_POST['family'];
    $reviewText = $_POST['reviewText'];

    // Connect to the database
    $connection = connect(); 

    // Fetch route_id based on route_direction and neighborhood
    $query = "SELECT id FROM dbFamily WHERE lastName = ? OR lastName2 = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $family, $family);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // No families with the last name found
        $stmt->close();
        $connection->close();
        die("Error: No family with that name. Please make a family account with us before proceeding.");
    }

    //get the family id
    $row = $result->fetch_assoc();
    $family_id = $row['id'];
    $stmt->close();

    //insert into dbProgramReviewForm
    $insertQuery = "INSERT INTO dbProgramReviewForm (family_id, reviewText) VALUES (?, ?)";
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param("is", $family_id, $reviewText);

    if ($stmt->execute()) {
        // Success
        $stmt->close();
        $connection->close();        
        header("Location: fillForm.php");
        exit();
    } else {
        // Error
        $error_message = $stmt->error;
        $stmt->close();
        $connection->close();
        die("Error: Failed to submit feedback. " . $error_message);
    }
}

require_once('database/dbProgramReviewForm.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("universal.inc"); ?>
    <title>Program Review Form</title>
    <link rel="stylesheet" href="base.css">
    <style>
    .location-section {
        display: none;
        /* Hide sections by default */
    }
    </style>
</head>
<body>
    <h1>Program Review Form</h1>
    <div id="formatted_form">
        <!--last name field -->
        <form action="" method="post">
            <label for="family">Last Name:</label>
            <input type="text" id="family" name="family">
            <br><br>
            <!--feedback field-->
            <label for="reviewText">Comments:</label>
            <textarea id="reviewText" name="reviewText" rows="6" cols="80">Type feedback here.</textarea>
            <br><br>
            <script>

            <?php //If the user is an admin or staff, the message should appear at index.php
                if($_SERVER['REQUEST_METHOD'] == "POST" && $success){
                    if (isset($_GET['id'])) {
                        echo '<script>document.location = "fillForm.php?formSubmitSuccess&id=' . $_GET['id'] . '";</script>';
                    } else {
                        echo '<script>document.location = "fillForm.php?formSubmitSuccess";</script>';
                    }
                } else if ($_SERVER['REQUEST_METHOD'] == "POST" && !$success) {
                    if (isset($_GET['id'])) {
                        echo '<script>document.location = "fillForm.php?formSubmitFail&id=' . $_GET['id'] . '";</script>';
                    } else {
                        echo '<script>document.location = "fillForm.php?formSubmitFail";</script>';
                    }  
                }
            ?>
            </script>
            <br><br>
            <button type="submit" name="programReviewForm">Submit Feedback</button>
        </form>
        <br>
        <a href="fillForm.php" style="text-decoration: none;">
        <button style="padding: 10px 20px; font-size: 16px;">Cancel</button>
    </a>
</body>
</html>