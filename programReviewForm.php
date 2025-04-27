<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
require_once('database/dbProgramReviewForm.php');

$loggedIn = false;
$accessLevel = 0;
$userID = null;

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

$family = null;
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])){
    $family = $_GET['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['programReviewForm'])) {
    // Retrieve data from the form
    $family_id = $_POST['family'];
    $event_name = $_POST['programName'];
    $reviewText = $_POST['reviewText'];

    // Connect to the database
    $connection = connect(); 

    // Insert into dbProgramReviewForm
    $insertQuery = "INSERT INTO dbProgramReviewForm (family_id, event_name, reviewText) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param("iss", $family_id, $event_name, $reviewText);

    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        header("Location: fillForm.php?formSubmitSuccess=1&id=$family_id");
        exit();
    } else {
        $error_message = $stmt->error;
        $stmt->close();
        $connection->close();
        header("Location: fillForm.php?formSubmitFail=1&id=$family_id");
        exit();
    }
}
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
    }
    </style>
</head>
<body>
<?php require('header.php'); ?>
    <h1>Program Review Form</h1>

    <?php
    if (isset($_GET['formSubmitSuccess'])) {
        echo "<p style='color: green; font-weight: bold;'>Feedback submitted successfully!</p>";
    } elseif (isset($_GET['formSubmitFail'])) {
        echo "<p style='color: red; font-weight: bold;'>Error submitting feedback. Please try again.</p>";
    }
    ?>

    <div id="formatted_form">
        <form action="" method="post">
            <?php
            if ($family !== null) {
                echo '<input type="hidden" id="family" name="family" value="' . htmlspecialchars($family) . '">';
            }
            ?>
            <label for="programName">Program Name:</label>
            <input type="text" id="programName" name="programName" required>
            <br><br>

            <label for="reviewText">Comments:</label>
            <textarea id="reviewText" name="reviewText" rows="6" cols="80" required>Type feedback here.</textarea>
            <br><br>

            <button type="submit" name="programReviewForm">Submit Feedback</button>
        </form>

        <a href="fillForm.php" style="text-decoration: none;">
            <br>
            <button style="padding: 10px 20px; font-size: 16px;">Cancel</button>
        </a>
    </div>
</body>
</html>
