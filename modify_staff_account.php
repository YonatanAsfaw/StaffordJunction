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
    header('Location: ../login.php');
    die();
}

// Admin-only access
if ($accessLevel < 2) {
    header('Location: ../index.php');
    die();
}

$staff = null;
$updateSuccess = false;
$searchError = "";
$updateError = "";


// Handle Search
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['search'])) {
    require_once('database/dbStaff.php');  // Ensure it's included

    // Use PHP trim() to remove extra spaces
    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);

   

    if (!empty($first_name) && !empty($last_name)) {
        $staff = retrieve_staff_by_name($first_name, $last_name);

        if (!$staff) {
            echo "<p class='alert alert-danger mt-3'>No staff member found with that name.</p>";
        }
    } else {
        echo "<p class='alert alert-warning mt-3'>Please enter both first and last name.</p>";
    }
}
// Handle Update
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update']) && isset($_POST['staff_id'])) {
    require_once('database/dbStaff.php');  // Ensure dbStaff.php is included

    $staff = retrieve_staff_by_id($_POST['staff_id']);  // Fetch staff by ID

    if ($staff) {
        $staff->setFirstName(trim($_POST['first-name']));
        $staff->setLastName(trim($_POST['last-name']));
        $staff->setBirthdate(trim($_POST['birthdate']));
        $staff->setAddress(trim($_POST['address']));
        $staff->setEmail(trim($_POST['email']));
        $staff->setPhone(trim($_POST['phone']));
        $staff->setEContactName(trim($_POST['econtactName']));
        $staff->setEContactPhone(trim($_POST['econtactPhone']));
        $staff->setJobTitle(trim($_POST['jobTitle']));

        if (update_staff($staff)) {
            $updateSuccess = true;
            $staff = retrieve_staff_by_id($_POST['staff_id']); // Refresh staff details
        } else {
            $updateError = "Failed to update staff member.";
        }
    } else {
        $updateError = "Staff member not found for update.";
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

    <title>Modify Staff Account</title>
</head>
<body>
   

    <div class="container mt-5">
        <h1 class="mb-4">Modify Staff Account</h1>
        
        <!-- Search Form -->
        <div class="card p-4 shadow-sm">
            <h4 class="mb-3">Search Staff Account</h4>
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

        <?php if ($staff): ?>
            <div class="card mt-4 p-4 shadow-sm">
                <h4 class="mb-3">Edit Staff Information</h4>
                <form method="POST">
                    <input type="hidden" name="staff_id" value="<?php echo $staff->getId(); ?>">

                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" name="first-name" value="<?php echo $staff->getFirstName(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" name="last-name" value="<?php echo $staff->getLastName(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Birthdate:</label>
                        <input type="date" name="birthdate" value="<?php echo $staff->getBirthdate(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Address:</label>
                        <input type="text" name="address" value="<?php echo $staff->getAddress(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo $staff->getEmail(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="phone" value="<?php echo $staff->getPhone(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact Name:</label>
                        <input type="text" name="econtactName" value="<?php echo $staff->getEContactName(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Emergency Contact Phone:</label>
                        <input type="text" name="econtactPhone" value="<?php echo $staff->getEContactPhone(); ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Job Title:</label>
                        <input type="text" name="jobTitle" value="<?php echo $staff->getJobTitle(); ?>" required class="form-control">
                    </div>

                    <button type="submit" name="update" class="btn btn-success mt-3">Update Account</button>
                </form>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['search'])): ?>
            <p class="alert alert-danger mt-3">No staff member found with that name.</p>
        <?php elseif ($updateSuccess): ?>
            <p class="alert alert-success mt-3">Staff member information successfully updated.</p>
        <?php endif; ?>

        <a class="button cancel button_style" href="index.php">Return to Dashboard</a>
    </div>
</body>
</html>