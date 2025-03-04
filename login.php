<?php
// Break the redirect loop for debugging
if (isset($_GET['clear'])) {
    session_start();
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600);
    echo "Session cleared. <a href='login.php'>Continue to login</a>";
    exit;
}
session_start(); // Ensure session starts before anything else
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Add at the top of problematic pages

// Clear session variables in case the user is coming from a reset flow
unset($_SESSION['familyEmail'], $_SESSION['familyVerified']);

// Redirect logged-in users to their respective dashboards
if (isset($_SESSION['_id'])) { 
    if ($_SESSION['account_type'] == 'Family') {
        header("Location: familyAccountDashboard.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

$badLogin = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('include/input-validation.php');
    require_once('domain/Person.php');
    require_once('database/dbPersons.php');
    require_once('database/dbMessages.php');
    require_once("domain/Family.php");
    require_once("database/dbFamily.php");
    require_once("domain/Staff.php");
    require_once("database/dbStaff.php");
    require_once("domain/Volunteer.php");
    require_once("database/dbVolunteers.php");

    $ignoreList = array('password');
    $args = sanitize($_POST, $ignoreList);
    $required = array('username', 'password');

    if (wereRequiredFieldsSubmitted($args, $required)) {
        $username = strtolower($args['username']);
        $password = $args['password'];
        $accountType = $args['account'];

        // Debugging: Check input values
        echo "Username: $username, Account Type: $accountType<br>";

        switch ($accountType) {
            case 'admin':
                $user = retrieve_person($username);
                if (!$user) {
                    $badLogin = true;
                } elseif (password_verify($password, $user->get_password())) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['_id'] = $user->get_id();
                    $_SESSION['account_type'] = 'admin';
                    $_SESSION['f_name'] = $user->get_first_name();
                    $_SESSION['l_name'] = $user->get_last_name();
                    $_SESSION['venue'] = $user->get_venue();
                    $_SESSION['type'] = $user->get_type();
                    $_SESSION['access_level'] = ($user->get_id() == 'vmsroot') ? 3 : 2;

                    
                    header("Location: index.php");
                    exit;
                } else {
                    $badLogin = true;
                }
                break;

            case 'family':
                $user = retrieve_family_by_email($username);
                if (!$user) {
                    $badLogin = true;
                } elseif (password_verify($password, $user->getPassword())) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['access_level'] = 1;
                    $_SESSION['_id'] = $user->getId();
                    $_SESSION['f_name'] = $user->getFirstName();
                    $_SESSION['l_name'] = $user->getLastName();
                    $_SESSION['account_type'] = "family";
                    $_SESSION['venue'] = "-";

                    header("Location: familyAccountDashboard.php");
                    exit;
                } else {
                    $badLogin = true;
                }
                break;

            case 'staff':
                $user = retrieve_staff($username);
                if (!$user) {
                    $badLogin = true;
                } elseif (password_verify($password, $user->getPassword())) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['access_level'] = 2;
                    $_SESSION['_id'] = $user->getId();
                    $_SESSION['f_name'] = $user->getFirstName();
                    $_SESSION['l_name'] = $user->getLastName();
                    $_SESSION['account_type'] = "staff";
                    $_SESSION['venue'] = "-";

                    header("Location: index.php");
                    exit;
                } else {
                    $badLogin = true;
                }
                break;

            case 'volunteer':
                $user = retrieve_volunteer_by_email($username);
                if (!$user) {
                    $badLogin = true;
                } elseif (password_verify($password, $user->getPassword())) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['access_level'] = 3;
                    $_SESSION['_id'] = $user->getId();
                    $_SESSION['f_name'] = $user->getFirstName();
                    $_SESSION['l_name'] = $user->getLastName();
                    $_SESSION['account_type'] = "volunteer";
                    $_SESSION['venue'] = "-";

                    header("Location: index.php");
                    exit;
                }
                break;

            default:
                $badLogin = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>Stafford Junction | Log In</title>
</head>
<body>
    <?php require_once('header.php') ?>
    <main class="login">
        <h1>Stafford Junction Login</h1>

        <?php if (isset($_GET['registerSuccess'])): ?>
            <div class="happy-toast">
                Your registration was successful! Please log in below.
            </div>
        <?php elseif (isset($_GET['failedAccountCreate'])): ?>
            <div class="happy-toast">
                Unable to create account, account already in system
            </div>
        <?php else: ?>
            <p>Welcome! Please log in below.</p>
        <?php endif ?>

        <form method="post">
            <?php if ($badLogin): ?>
                <span class="error">No login with that e-mail and password combination currently exists.</span>
            <?php endif ?>

            <label for="account">Select Account Type</label>
            <select name="account" id="account">
                <option value="admin">Admin</option>
                <option value="family">Family</option>
                <option value="staff">Staff</option>
                <option value="volunteer">Volunteer</option>
            </select>

            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Enter your e-mail address" required>
            
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <input type="submit" name="login" value="Log in">
        </form>

        <p><a href="verifyEmail.php">Forgot Password?</a></p>
        <p>Don't have an account? Sign up <a href="familyAccount.php">here</a></p>
        <p>Looking for <a href="https://staffordjunction.org/">Stafford Junction's website</a>?</p>
    </main>
</body>
</html>
