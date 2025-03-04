<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set("America/New_York");

// Redirect if user is not logged in or has insufficient access level
if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
    if (isset($_SESSION['change-password'])) {
        header('Location: changePassword.php');
        exit;
    } else {
        header('Location: login.php');
        exit;
    }
}

// Include necessary files
include_once('database/dbVolunteers.php');
include_once('domain/Volunteer.php');
include_once('database/dbPersons.php');
include_once('domain/Person.php');
include_once('domain/Staff.php');
include_once('database/dbStaff.php');

// Check what kind of account is logged in
if (isset($_SESSION['_id'])) {
    if ($_SESSION['account_type'] == 'admin') {
        $person = retrieve_person($_SESSION['_id']);
        $notRoot = ($person->get_id() != 'vmsroot'); // True if not root user
    } elseif ($_SESSION['account_type'] == 'family') { 
        header("Location: familyAccountDashboard.php");
        exit;
    } elseif ($_SESSION['account_type'] == 'staff') {
        $staff = retrieve_staff_by_id($_SESSION['_id']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('universal.inc'); ?>
    <title>Stafford Junction | Dashboard</title>
</head>
<body>
    <?php require('header.php'); ?>

    <?php 
    // Check if account type is defined before using it
    $acct = isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'staff' ? 'Staff' : 'Admin';
    ?>
    
    <h1>Stafford Junction <?php echo htmlspecialchars($acct, ENT_QUOTES, 'UTF-8'); ?> Dashboard</h1>
    
    <main class='dashboard'>
        <?php if (isset($_GET['addStaffSuccess'])): ?>
            <div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Staff account created!</div>
        <?php elseif (isset($_GET['formSubmitSuccess'])): ?>
            <div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Form submitted successfully!</div>
        <?php elseif (isset($_GET['pcSuccess'])): ?>
            <div class="happy-toast">Password changed successfully!</div>
        <?php elseif (isset($_GET['registerSuccess'])): ?>
            <div class="happy-toast">Volunteer registered successfully!</div>
        <?php elseif (isset($_GET['familyRegisterSuccess'])): ?>
            <div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Family Account Registration Successful!</div>
        <?php elseif (isset($_GET['updateSuccess'])): ?>
            <div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Family Profile Updated!</div>
        <?php elseif (isset($_GET['addVolunteerSuccess'])): ?>
            <div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Volunteer account created!</div>
        <?php elseif (isset($_GET['failedAccountCreate'])): ?>
            <div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">
                Unable to create account, account already in system!
            </div>
        <?php endif; ?>

        <?php if (isset($staff)): ?>
            <p>Welcome back, <?php echo htmlspecialchars($staff->getFirstName(), ENT_QUOTES, 'UTF-8'); ?>!</p>
        <?php endif; ?>

        <p>Today is <?php echo date('l, F j, Y'); ?>.</p>
        
        <div id="dashboard">
            <!-- Dashboard Items Based on Access Level -->
            <?php if (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 2): ?>
                <div class="dashboard-item" data-link="findFamily.php">
                    <img src="images/person-search.svg">
                    <span>Find Family Account</span>
                </div>
                
                <div class="dashboard-item" data-link="findChildren.php">
                    <img src="images/person-search.svg">
                    <span>Find Children</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['access_level']) && ($_SESSION['access_level'] >= 2 || $_SESSION['account_type'] == 'admin')): ?>
                <div class="dashboard-item" data-link="formSearch.php">
                    <img src="images/form-dropdown-svgrepo-com.svg">
                    <span>View Form Submissions</span>
                </div>
            <?php endif; ?>
          
            <?php if (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 2): ?>
                <div class="dashboard-item" data-link="fillFormStaff.php">
                    <img src="images/form-dropdown-svgrepo-com.svg">
                    <span>Fill Out Attendance Forms</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'admin'): ?>
                <div class="dashboard-item" data-link="createStaffAccount.php">
                    <img src="images/staffUsers.svg">
                    <span>Create Staff Account</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 2): ?>
                <div class="dashboard-item" data-link="familySignUpStaff.php">
                    <img src="images/family-svgrepo-com.svg">
                    <span>Create Family Account</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'admin'): ?>
                <div class="dashboard-item" data-link="createVolunteerAccount.php">
                    <img src="images/staffUsers.svg">
                    <span>Create Volunteer Account</span>
                </div>
            <?php endif; ?>

            <div class="dashboard-item" data-link="changePassword.php">
                <img src="images/change-password.svg">
                <span>Change Password</span>
            </div>

            <div class="dashboard-item" data-link="logout.php">
                <img src="images/logout.svg">
                <span>Log out</span>
            </div>
        </div>
    </main>
</body>
</html>
