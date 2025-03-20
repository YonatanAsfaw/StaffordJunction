<?php

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
require_once('database/dbProgramReviewForm.php');

$loggedIn = false;
$accessLevel = 0;
$userID = null;


if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}
// admin-only access
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

$family = null;
$feedback = null;
$id = null;

/*if($_SERVER['REQUEST_METHOD'] == "GET"){
    require_once("include/input-validation.php");
    $args = sanitize($_GET, null);
    if(isset($args['family'])){
        $family = $args['family'];
    }
    if(isset($args['feedback'])){
        $feedback = $args['feedback'];
    }
    if(isset($args['id'])){
        $id = $args['id'];
    }
}*/

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $family = $_POST['family'];
    $program = $_POST['program'];
    $feedback = $_POST['feedback'];
    $id = $_POST['id'];
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deleteFeedback'])){
    $id = $_POST['id'];
    deleteFeedback($id);
}

function deleteFeedback($id){
    $connection = mysqli_connect("localhost", "stafforddb", "stafforddb", "stafforddb"); //this will break if you abbreviate it to connect() and I don't know why
    //var_dump($id);
    $query = "DELETE FROM dbProgramReviewForm WHERE id=?";
    //var_dump($query);
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            // Success
            $stmt->close();
            $connection->close();        
            header("Location: index.php");
            exit();
        } else {
            // Error
            $error_message = $stmt->error;
            $stmt->close();
            $connection->close();
            die("Error: Failed to delete feedback. " . $error_message);
        }
    }
?>

<!DOCTYPE html>
<HTML>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?php require_once('universal.inc') ?>
        <title>Stafford Junction | Delete Feedback</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .general a {
                        color: #fcdd2b;
                        text-decoration: none;
                    }

            .general tbody tr:hover {
                background-color: #cccccc; /* Light grey color */
            }
        </style>
    </head>
    <body>
        <?php //require_once('header.php') ?>
        <h1>View Feedback</h1>
        <h3>Are you sure you want to delete this feedback?</h3>
        <br/>
        <?php
            echo '
            <div class="table-wrapper">
                <table class = "general">
                    <thead>
                        <tr>
                            <th>Family ID</th>
                            <th>Program</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
            <tbody class="standout">';
            echo '<tr><td>' . $family . '</td>';
            echo '<td>' . $program . '</td>';
            echo '<td>' . $feedback . '</td></tr>';
            echo '</table></div>';
            echo '<br>';
        
            //echo '<!--' . $id . '-->';
            echo '<form action="deleteFeedback.php" method="post">';
            echo '<input type="hidden" name="id" value="' . $id . '" />';
            echo '<input type="submit" name="deleteFeedback" value="delete" />';
            //echo '<button type="submit" name="delete">Delete Feedback</button>';
            echo '</form>'
        ?>
        <a class="button cancel button_style"  href="index.php"">Return to Dashboard</a>
    </body>
</HTML>