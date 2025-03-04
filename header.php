<?php
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect unauthorized users
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    die();
}

// Define permission levels
$permission_array = [
    'index.php' => 0, 'about.php' => 0, 'apply.php' => 0, 'logout.php' => 0, 'register.php' => 0,
    'help.php' => 1, 'dashboard.php' => 1, 'calendar.php' => 1, 'eventsearch.php' => 1,
    'changepassword.php' => 1, 'editprofile.php' => 1, 'inbox.php' => 1, 'date.php' => 1,
    'event.php' => 1, 'viewprofile.php' => 1, 'viewnotification.php' => 1, 'volunteerreport.php' => 1,
    'fillform.php' => 1, 'familyaccountdashboard.php' => 1, 'familyview.php' => 1,
    'childrenview.php' => 1, 'childreninaccount.php' => 1, 'childaccount.php' => 1,
    'completedforms.php' => 1, 'holidaymealbagcomplete.php' => 1, 'addchild.php' => 1,
    'forgotpassword.php' => 1, 'personsearch.php' => 2, 'viewschedule.php' => 2,
    'log.php' => 2, 'reports.php' => 2, 'eventedit.php' => 2, 'modifyuserrole.php' => 2,
    'addevent.php' => 2, 'editevent.php' => 2, 'roster.php' => 2, 'report.php' => 2,
    'reportspage.php' => 2, 'resetpassword.php' => 2, 'addappointment.php' => 2,
    'addlocation.php' => 2, 'viewservice.php' => 2, 'viewlocation.php' => 2,
    'findfamily.php' => 2, 'findchildren.php' => 2, 'formsearch.php' => 2,
    'formsearchresult.php' => 2, 'fillformstaff.php' => 2, 'familysignupstaff.php' => 2,
    'modify_staff_account.php' => 2, 'createstaffaccount.php' => 3, 'removestaffaccount.php' => 3,
    'createvolunteeraccount.php' => 3
];

// Get current page
$current_page = strtolower(basename($_SERVER['PHP_SELF']));

// Restrict access if needed
if (isset($permission_array[$current_page]) && $permission_array[$current_page] > $_SESSION['access_level']) {
    header("Location: index.php");
    die();
}

// Define path for asset links
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <title>Stafford Junction</title>

    <style>
        .navbar {
            background-color: white !important; /* Match with index.php */
            padding: 10px 0;
        }
        .navbar-brand img {
            height: 50px;
            margin-right:20px;
        }

        .navbar-nav .nav-item {
             margin-right: 20px;
             white-space: nowrap;
        }


        .navbar-nav .nav-link {
            font-size:18px;
            font-weight:bold;
            color:red !important;
        }
        .navbar-nav .nav-link:hover {
            text-decoration: underline;
        }

        .dropdown-menu {
           background-color: white;
           border: none;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }


        .logout-btn {
            white-space: nowrap; /* Prevent breaking into two lines */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $path; ?>index.php">
            <img src="<?php echo $path; ?>images/staffordjunction.png" alt="Stafford Junction">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-danger" href="<?php echo $path; ?>index.php">Home</a></li>

                <!-- Families Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-danger" href="#" id="familiesDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Families</a>
                    <div class="dropdown-menu" aria-labelledby="familiesDropdown">
                        <a class="dropdown-item" href="<?php echo $path; ?>findFamily.php">Find Family</a>
                        <a class="dropdown-item" href="<?php echo $path; ?>familySignUpStaff.php">Add Family Account</a>
                        <a class="dropdown-item" href="<?php echo $path; ?>formSearch.php">Reports</a>
                    </div>
                </li>

                <!-- Staff Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-danger" href="#" id="staffDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Staff</a>
                    <div class="dropdown-menu" aria-labelledby="staffDropdown">
                        <a class="dropdown-item" href="<?php echo $path; ?>createStaffAccount.php">Create Staff Account</a>
                        
                        <a class="dropdown-item" href="<?php echo $path; ?>removeStaffAccount.php">Remove Staff Account</a>
                        <a class="dropdown-item" href="/StaffordJunction/database/modify_staff_account.php">Modify Staff Account</a>

                    </div>
                </li>

                <!-- Volunteers Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-danger" href="#" id="volunteersDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Volunteers</a>
                    <div class="dropdown-menu" aria-labelledby="volunteersDropdown">
                        <a class="dropdown-item" href="<?php echo $path; ?>createVolunteerAccount.php">Create Volunteer Account</a>
                        <a class="dropdown-item" href="<?php echo $path; ?>formSearch.php">View Volunteer Reports</a>
                    </div>
                </li>

                <li class="nav-item"><a class="nav-link text-danger" href="<?php echo $path; ?>changePassword.php">Change Password</a></li>
                <li class="nav-item"><a class="nav-link text-danger font-weight-bold" href="<?php echo $path; ?>logout.php">Log out</a></li>
            </ul>
        </div>
    </div>
</nav>

</body>
</html>
