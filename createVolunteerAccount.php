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
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    die();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once("database/dbVolunteers.php");
    $args = sanitize($_POST, null);
    // Validate password match
    if ($args['password'] !== $args['password-reenter']) {
        echo "<p class='error'>Passwords do not match!</p>";
        exit;
    }
    $args['password'] = password_hash($args['password'], PASSWORD_DEFAULT);
    // Calculate age from birthDate
    $birthDate = new DateTime($args['birthDate']);
    $today = new DateTime();
    $args['age'] = $today->diff($birthDate)->y;
    // Map form fields to expected keys
    $args['homePhone'] = $args['phone'];
    $args['emergencyContact1Name'] = $args['econtactName'];
    $args['emergencyContact1Relation'] = $args['econtactRelationship'];
    $args['emergencyContact1Phone'] = $args['econtactPhone'];
    // Set defaults for missing fields
    $args['id'] = null; // Auto-incremented in DB
    $args['cellPhone'] = '';
    $args['hasDriversLicense'] = 0;
    $args['transportation'] = '';
    $args['emergencyContact2Name'] = '';
    $args['emergencyContact2Relation'] = '';
    $args['emergencyContact2Phone'] = '';
    $args['sunStart'] = ''; $args['sunEnd'] = '';
    $args['monStart'] = ''; $args['monEnd'] = '';
    $args['tueStart'] = ''; $args['tueEnd'] = '';
    $args['wedStart'] = ''; $args['wedEnd'] = '';
    $args['thurStart'] = ''; $args['thurEnd'] = '';
    $args['friStart'] = ''; $args['friEnd'] = '';
    $args['satStart'] = ''; $args['satEnd'] = '';
    $args['dateAvailable'] = null;
    $args['minHours'] = 0;
    $args['maxHours'] = 0;
    $volunteer = make_volunteer_from_signup($args);
    $success = add_volunteer($volunteer);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Create Volunteer Account</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once("universal.inc"); ?>
</head>
<body>
    <?php require_once("header.php"); ?>
    <h1>Create Volunteer Account</h1>
    <main class="signup-form">
        <form class="signup-form" method="POST">
            <h2>Volunteer Account Registration Form</h2>
            <p>Please fill out each section of the following form if you would like to become a volunteer of Stafford Junction</p>
            <p>An asterisk (*) indicates a required field.</p>

            <!-- First Name -->
            <label for="firstName">First Name *</label>
            <input type="text" name="firstName" placeholder="Enter your first name" required>
            <!-- Middle Initial -->
            <label for="middleInitial">Middle Initial *</label>
            <input type="text" name="middleInitial" placeholder="Enter your middle initial" required>
            <!-- Last Name -->
            <label for="lastName">Last Name *</label>
            <input type="text" name="lastName" placeholder="Enter your last name" required>
            <!-- Birthdate -->
            <label for="birthDate">Date of Birth *</label>
            <input type="date" id="birthDate" name="birthDate" required max="<?php echo date('Y-m-d'); ?>">
            <!-- Address -->
            <label for="address">Address *</label>
            <input type="text" name="address" placeholder="Enter your address" required>
            <!-- City -->
            <label for="city">City *</label>
            <input type="text" name="city" placeholder="Enter your city" required>
            <!-- State -->
            <label for="state">State *</label>
            <input type="text" name="state" placeholder="Enter your state" required>
            <!-- Zip -->
            <label for="zip">Zip *</label>
            <input type="text" name="zip" placeholder="Enter your zipcode" required>
            <!-- Email -->
            <label for="email">* E-mail</label>
            <p>This will also serve as your username when logging in.</p>
            <input type="email" id="email" name="email" required placeholder="Enter your e-mail address">
            <!-- Home Phone -->
            <label for="phone">Phone *</label>
            <input type="tel" id="phone" name="phone" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" required placeholder="Ex. (555) 555-5555">
            <!-- Job Title (optional) -->
            <label for="jobTitle">Job Title</label>
            <input type="text" name="jobTitle" placeholder="Enter your job title">
            <!-- Emergency Contact -->
            <h3>Emergency Contact</h3>
            <label for="econtactName">Emergency Contact Name *</label>
            <input type="text" name="econtactName" placeholder="Enter emergency contact name" required>
            <label for="econtactRelationship">Emergency Contact Relationship *</label>
            <input type="text" name="econtactRelationship" placeholder="Enter emergency contact relationship" required>
            <label for="econtactPhone">Emergency Contact Phone *</label>
            <input type="tel" id="econtactPhone" name="econtactPhone" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" required placeholder="Ex. (555) 555-5555">
            <!-- Allergies -->
            <label for="allergies">Allergies *</label>
            <select name="allergies" required>
                <option value="Nut Allergy">Nut Allergy</option>
                <option value="Soy Allergy">Soy Allergy</option>
                <option value="Egg Allergy">Egg Allergy</option>
                <option value="Dairy Allergy">Dairy Allergy</option>
                <option value="Shellfish Allergy">Shellfish Allergy</option>
                <option value="Animal Allergy">Animal Allergy</option>
                <option value="Spices Allergy">Spices Allergy</option>
                <option value="Gluten Allergy">Gluten Allergy</option>
                <option value="Latex Allergy">Latex Allergy</option>
            </select>
            <!-- Login Credentials -->
            <h3>Login Credentials</h3>
            <fieldset>
                <p>You will use the following information to log in to the system.</p>
                <p><b>Your username is the primary email address entered above.</b></p>
                <label for="password">* Password</label>
                <p style="margin-bottom: 0;">Password must be eight or more characters in length and include at least one special character (e.g., ?, !, @, #, $, &, %)</p>
                <input type="password" id="password" name="password" pattern="^(?=.*[^a-zA-Z0-9].*).{8,}$" title="Password must be eight or more characters in length and include at least one special character" placeholder="Enter a strong password" required>
                <label for="password-reenter">* Re-enter Password</label>
                <input type="password" id="password-reenter" name="password-reenter" required placeholder="Re-enter password">
                <p id="password-match-error" class="error hidden">Passwords do not match!</p>
                <label for="securityQuestion">* Security Question</label>
                <input type="text" id="securityQuestion" name="securityQuestion" placeholder="Security Question" required>
                <label for="securityAnswer">* Security Answer</label>
                <input type="text" id="securityAnswer" name="securityAnswer" placeholder="Security Answer" required>
            </fieldset>
            <input type="submit" name="registration-form" value="Create Account">
            <?php
            if ($success) {
                echo '<script>document.location = "index.php?addVolunteerSuccess";</script>';
            }
            ?>
            <a class="button cancel" href="index.php" style="margin-top: .5rem">Cancel</a>
        </form>
    </main>
</body>
</html>