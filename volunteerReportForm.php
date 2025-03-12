<?php

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$volunteerID = null;

if(isset($_SESSION['_id'])){
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $volunteerID = $_SESSION['_id'];

} else {
    header('Location: login.php');
    die();
}

require_once('database/dbVolunteerReportForm.php');
require_once('include/input-validation.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $args = sanitize($_POST, null);
    
    $required = ["activity_id", "date", "hours", "description"];
    
    // Insert volunteer log
    $success = add_hour_log($volunteerID, $args);

    if ($success) {
        echo '<script>document.location = "volunteerReportForm.php?formSubmitSuccess";</script>';
    } else {
        echo '<script>document.location = "volunteerReportForm.php?formSubmitFail";</script>';
    }
}

?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include_once("universal.inc")?>
        <title>Volunteer Hour Log</title>
        <link rel="stylesheet" href="base.css">
    </head>
    
    <body>
        <h1>Volunteer Hour Log</h1>
        <?php 
            if (isset($_GET['formSubmitFail'])) {
                echo '<div class="happy-toast" style="text-align: center; background-color: red;">Error Submitting Form</div>';
            } elseif (isset($_GET['formSubmitSuccess'])) {
                echo '<div class="happy-toast" style="text-align: center; background-color: green;">Hours Logged Successfully!</div>';
            }
        ?>
        
        <div id="formatted_form">
            
        <p><b>* Indicates a required field</b></p><hr><br>

        <form id="volunteerHourForm" action="volunteerReportForm.php" method="post">
            <!-- 1. Activity Selection -->
            <label for="activity_id">1. Activity*</label><br><br>
            <select name="activity_id" id="activity_id" style="width: 300px" required>
                <option value="">-- Select Activity --</option>
                <?php foreach ($activities as $activity): ?>
                    <option value="<?= $activity['id'] ?>"><?= htmlspecialchars($activity['activity']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <!-- 2. Date -->
            <label for="date">2. Date*</label><br><br>
            <input type="date" name="date" id="date" style="width: 200px" required><br><br>

            <!-- 3. Hours Worked -->
            <label for="hours">3. Number of Hours Worked*</label><br><br>
            <input type="number" name="hours" id="hours" min="0.5" max="12" step="0.5" style="width: 100px" required><br><br>

            <!-- 4. Description -->
            <label for="description">4. Description of Work Performed*</label><br><br>
            <textarea name="description" id="description" rows="5" style="width: 800px" required></textarea><br><br>

            <button type="submit" id="submit" style="width: 150px">Submit</button>
            <a class="button cancel" href="index.php" style="width: 150px">Cancel</a><br><br>
        </form>
        </div>
    </body>
</html>