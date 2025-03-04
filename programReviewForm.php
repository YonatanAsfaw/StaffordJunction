<?php
//everything commented out is a relic of the bus volunteer thing I was copying from

//todo:
//make it so this webpage actually talks to dbProgramReviewForm

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
    //$route_direction = $_POST['route_direction']; // e.g., "South"
    //$neighborhood = $_POST['neighborhood'];       // e.g., "Meadows"
    //$volunteer_id = intval($_POST['volunteer_id']); // Volunteer ID (from the dropdown)

    // Connect to the database
    $connection = connect(); 

    // Fetch route_id based on route_direction and neighborhood
    $query = "SELECT id FROM dbFamily WHERE lastName = ? OR lastName2 = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $family, $family);
    $stmt->execute();
    $result = $stmt->get_result();
    //$query = "SELECT route_id FROM dbRoute WHERE route_direction = ? AND route_name = ?";
    //$stmt = $connection->prepare($query);
    //$stmt->bind_param("ss", $route_direction, $neighborhood);
    //$stmt->execute();
    //$result = $stmt->get_result();

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
    // Get the route_id
    //$row = $result->fetch_assoc();
    //$route_id = $row['route_id'];
    //$stmt->close();

    //insert into dbProgramReviewForm
    $insertQuery = "INSERT INTO dbProgramReviewForm (family_id, reviewText) VALUES (?, ?)";
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param("is", $family_id, $reviewText);
    
    // Insert into dbRouteVolunteers
    //$insertQuery = "INSERT INTO dbRouteVolunteers (route_id, volunteer_id) VALUES (?, ?)";
    //$stmt = $connection->prepare($insertQuery);
    //$stmt->bind_param("ii", $route_id, $volunteer_id);

    // Fetch the volunteer's full name from the array or database
    //$volunteers = getVolunteers(); // Fetch all volunteers
    //$volunteer_name = '';

    // get the actual name of the volunteer
    /*foreach ($volunteers as $volunteer) {
        if ($volunteer['id'] == $volunteer_id) {
            $volunteer_name = $volunteer['fullName'];
            break;
        }
    }*/

    if ($stmt->execute()) {
        // Success
        $stmt->close();
        $connection->close();
        header("Location: editBusMonitorData.php?message=" . urlencode("feedback was successfully provided!"));
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
            <!--<select name="volunteer_id" id="volunteer_id" required>-->
            <input type="text" id="family" name="family">
                <?php
        // Fetch all volunteers from the database
        //$families = getVolunteers(); // This function should return a list of volunteers
        //foreach ($volunteers as $volunteer) {
        //    echo "<option value='{$volunteer['id']}'>{$volunteer['fullName']}</option>";
        //}
        ?>
            <!--</select>-->
            <br><br>
            <label for="reviewText">Comments:</label>
            <textarea id="reviewText" name="reviewText" rows="6" cols="80">Type feedback here.</textarea>
            <!--<select name="route_direction" id="route_direction" required onchange="updateNeighborhoods(this.value)">
                <option value="" disabled selected>Select a Route</option> Default placeholder
                <option value="North">North</option>
                <option value="South">South</option>
            </select>-->
            <br><br>
            <!--for removal
            <label for="neighborhood">Select Neighborhood:</label>
            <select name="neighborhood" id="neighborhood" required disabled>
                <option value="" disabled selected>Select a Neighborhood</option>  Default placeholder
            </select>-->
            <script>

            // Update neighborhoods based on the selected route direction
            /*function updateNeighborhoods(routeDirection) {
                const neighborhoodSelect = document.getElementById('neighborhood');
                neighborhoodSelect.innerHTML = ''; // Clear existing options

                if (routeDirection) { // Enable the neighborhood dropdown only if a route is selected
                    neighborhoodSelect.disabled = false; // Enable the dropdown

                    if (routeDirection === 'North') { // North route
                        neighborhoodSelect.innerHTML = `
                        <option value="Foxwood">Foxwood</option>
                    `;
                    } else if (routeDirection === 'South') { // South route
                        neighborhoodSelect.innerHTML = `
                        <option value="Meadows">Meadows</option>
                        <option value="Jefferson Place">Jefferson Place</option>
                        <option value="Olde Forge">Olde Forge</option>
                        <option value="England Run">England Run</option>
                    `;
                    }
                } else {
                    neighborhoodSelect.disabled = true; // Disable the dropdown if no route is selected
                    neighborhoodSelect.innerHTML = `<option value="" disabled selected>Select a Neighborhood</option>`;
                }
            }*/
            </script>
            <br><br>
            <button type="submit" name="programReviewForm">Submit Feedback</button>
        </form>
        <br>
        <a href="editBusMonitor.php" style="text-decoration: none;">
        <button style="padding: 10px 20px; font-size: 16px;">Cancel</button>
    </a>
</body>
</html>