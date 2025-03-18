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
    require_once('database/dbVolunteers.php'); // Include the volunteer database file
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: ../login.php');
    die();
}

// Admin-only access
if ($accessLevel < 2) {
    header('Location: ../index.php');
    die();
}

$volunteer = null;
$updateSuccess = false;
$searchError = "";
$updateError = "";

// Handle Search
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['search'])) {
    // Use PHP trim() to remove extra spaces
    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);

    if (!empty($first_name) && !empty($last_name)) {
        $volunteer = retrieve_volunteer_by_name($first_name, $last_name);

        if (!$volunteer) {
            $searchError = "No volunteer found with that name.";
        }
    } else {
        $searchError = "Please enter both first and last name.";
    }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update']) && isset($_POST['volunteer_id'])) {
    $volunteer = retrieve_volunteer_by_id($_POST['volunteer_id']);  // Fetch volunteer by ID

    if ($volunteer) {
        $volunteer->setFirstName(trim($_POST['first-name']));
        $volunteer->setMiddleInitial(trim($_POST['middle-initial']));
        $volunteer->setLastName(trim($_POST['last-name']));
        $volunteer->setAddress(trim($_POST['address']));
        $volunteer->setCity(trim($_POST['city']));
        $volunteer->setState(trim($_POST['state']));
        $volunteer->setZip(trim($_POST['zip']));
        $volunteer->setHomePhone(trim($_POST['home-phone']));
        $volunteer->setCellPhone(trim($_POST['cell-phone']));
        $volunteer->setAge(trim($_POST['age']));
        $volunteer->setBirthDate(trim($_POST['birthdate']));
        $volunteer->setHasDriversLicense(trim($_POST['has-drivers-license']));
        $volunteer->setTransportation(trim($_POST['transportation']));
        $volunteer->setEmergencyContact1Name(trim($_POST['econtact1-name']));
        $volunteer->setEmergencyContact1Relation(trim($_POST['econtact1-relation']));
        $volunteer->setEmergencyContact1Phone(trim($_POST['econtact1-phone']));
        $volunteer->setEmergencyContact2Name(trim($_POST['econtact2-name']));
        $volunteer->setEmergencyContact2Relation(trim($_POST['econtact2-relation']));
        $volunteer->setEmergencyContact2Phone(trim($_POST['econtact2-phone']));
        $volunteer->setAllergies(trim($_POST['allergies']));
        $volunteer->setSunStart(trim($_POST['sun-start']));
        $volunteer->setSunEnd(trim($_POST['sun-end']));
        $volunteer->setMonStart(trim($_POST['mon-start']));
        $volunteer->setMonEnd(trim($_POST['mon-end']));
        $volunteer->setTueStart(trim($_POST['tue-start']));
        $volunteer->setTueEnd(trim($_POST['tue-end']));
        $volunteer->setWedStart(trim($_POST['wed-start']));
        $volunteer->setWedEnd(trim($_POST['wed-end']));
        $volunteer->setThuStart(trim($_POST['thu-start']));
        $volunteer->setThuEnd(trim($_POST['thu-end']));
        $volunteer->setFriStart(trim($_POST['fri-start']));
        $volunteer->setFriEnd(trim($_POST['fri-end']));
        $volunteer->setSatStart(trim($_POST['sat-start']));
        $volunteer->setSatEnd(trim($_POST['sat-end']));
        $volunteer->setDateAvailable(trim($_POST['date-available']));
        $volunteer->setMinHours(trim($_POST['min-hours']));
        $volunteer->setMaxHours(trim($_POST['max-hours']));

        if (update_volunteer($volunteer)) {
            $updateSuccess = true;
            header('Location: index.php?modifyVolunteerSuccess'); // Redirect to index after successful update
            exit();
        } else {
            $updateError = "Failed to update volunteer.";
        }
    } else {
        $updateError = "Volunteer not found for update.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Custom CSS (Ensure this is correctly linked) -->
    <link rel="stylesheet" href="/StaffordJunction/css/styles.css">

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <title>Modify Volunteer Account</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Modify Volunteer Account</h1>
        
        <!-- Search Form -->
        <div class="card p-4 shadow-sm">
            <h4 class="mb-3">Search Volunteer Account</h4>
            <form method="POST">
                <div class="form-group">
                    <label for="first-name">First Name:</label>
                    <input type="text" name="first-name" id="first-name" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="last-name">Last Name:</label>
                    <input type="text" name="last-name" id="last-name" required class="form-control">
                </div>

                <button type="submit" name="search" class="btn btn-primary">Search</button>
            </form>
        </div>

        <?php if ($searchError): ?>
            <p class="alert alert-danger mt-3"><?php echo $searchError; ?></p>
        <?php endif; ?>

        <?php if ($volunteer): ?>
            <div class="card mt-4 p-4 shadow-sm">
                <h4 class="mb-3">Edit Volunteer Information</h4>
                <form method="POST">
                    <input type="hidden" name="volunteer_id" value="<?php echo $volunteer->getId(); ?>">

                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" name="first-name" value="<?php echo $volunteer->getFirstName(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Middle Initial:</label>
                        <input type="text" name="middle-initial" value="<?php echo $volunteer->getMiddleInitial(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" name="last-name" value="<?php echo $volunteer->getLastName(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Address:</label>
                        <input type="text" name="address" value="<?php echo $volunteer->getAddress(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>City:</label>
                        <input type="text" name="city" value="<?php echo $volunteer->getCity(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>State:</label>
                        <input type="text" name="state" value="<?php echo $volunteer->getState(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Zip:</label>
                        <input type="text" name="zip" value="<?php echo $volunteer->getZip(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Home Phone:</label>
                        <input type="text" name="home-phone" value="<?php echo $volunteer->getHomePhone(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Cell Phone:</label>
                        <input type="text" name="cell-phone" value="<?php echo $volunteer->getCellPhone(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Age:</label>
                        <input type="text" name="age" value="<?php echo $volunteer->getAge(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Birth Date:</label>
                        <input type="date" name="birthdate" value="<?php echo $volunteer->getBirthDate(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Has Driver's License:</label>
                        <input type="text" name="has-drivers-license" value="<?php echo $volunteer->getHasDriversLicense(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Transportation:</label>
                        <input type="text" name="transportation" value="<?php echo $volunteer->getTransportation(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact 1 Name:</label>
                        <input type="text" name="econtact1-name" value="<?php echo $volunteer->getEmergencyContact1Name(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact 1 Relation:</label>
                        <input type="text" name="econtact1-relation" value="<?php echo $volunteer->getEmergencyContact1Relation(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact 1 Phone:</label>
                        <input type="text" name="econtact1-phone" value="<?php echo $volunteer->getEmergencyContact1Phone(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact 2 Name:</label>
                        <input type="text" name="econtact2-name" value="<?php echo $volunteer->getEmergencyContact2Name(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact 2 Relation:</label>
                        <input type="text" name="econtact2-relation" value="<?php echo $volunteer->getEmergencyContact2Relation(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact 2 Phone:</label>
                        <input type="text" name="econtact2-phone" value="<?php echo $volunteer->getEmergencyContact2Phone(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Allergies:</label>
                        <input type="text" name="allergies" value="<?php echo $volunteer->getAllergies(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Sun Start:</label>
                        <input type="text" name="sun-start" value="<?php echo $volunteer->getSunStart(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Sun End:</label>
                        <input type="text" name="sun-end" value="<?php echo $volunteer->getSunEnd(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Mon Start:</label>
                        <input type="text" name="mon-start" value="<?php echo $volunteer->getMonStart(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Mon End:</label>
                        <input type="text" name="mon-end" value="<?php echo $volunteer->getMonEnd(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tue Start:</label>
                        <input type="text" name="tue-start" value="<?php echo $volunteer->getTueStart(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tue End:</label>
                        <input type="text" name="tue-end" value="<?php echo $volunteer->getTueEnd(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Wed Start:</label>
                        <input type="text" name="wed-start" value="<?php echo $volunteer->getWedStart(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Wed End:</label>
                        <input type="text" name="wed-end" value="<?php echo $volunteer->getWedEnd(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Thu Start:</label>
                        <input type="text" name="thu-start" value="<?php echo $volunteer->getThuStart(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Thu End:</label>
                        <input type="text" name="thu-end" value="<?php echo $volunteer->getThuEnd(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Fri Start:</label>
                        <input type="text" name="fri-start" value="<?php echo $volunteer->getFriStart(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Fri End:</label>
                        <input type="text" name="fri-end" value="<?php echo $volunteer->getFriEnd(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Sat Start:</label>
                        <input type="text" name="sat-start" value="<?php echo $volunteer->getSatStart(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Sat End:</label>
                        <input type="text" name="sat-end" value="<?php echo $volunteer->getSatEnd(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Date Available:</label>
                        <input type="date" name="date-available" value="<?php echo $volunteer->getDateAvailable(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Min Hours:</label>
                        <input type="text" name="min-hours" value="<?php echo $volunteer->getMinHours(); ?>" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Max Hours:</label>
                        <input type="text" name="max-hours" value="<?php echo $volunteer->getMaxHours(); ?>" class="form-control">
                    </div>

                    <button type="submit" name="update" class="btn btn-success mt-3">Update Account</button>
                </form>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['search'])): ?>
            <p class="alert alert-danger mt-3">No volunteer found with that name.</p>
        <?php elseif ($updateSuccess): ?>
            <p class="alert alert-success mt-3">Volunteer information successfully updated.</p>
        <?php elseif ($updateError): ?>
            <p class="alert alert-danger mt-3"><?php echo $updateError; ?></p>
        <?php endif; ?>

        <a class="button cancel button_style" href="index.php">Return to Dashboard</a>
    </div>
</body>
</html>