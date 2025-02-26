<?php

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = false;

if(isset($_SESSION['_id'])){
    //require_once('database/dbStaff.php');
    require_once('include/input-validation.php');
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    die();
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    require_once("database/dbVolunteer.php");

    $args = sanitize($_POST, null);
    $volunteer = make_staff_from_signup($args);
    $success = add_staff($volunteer);
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
        <?php require_once("universal.inc");?>
    </head>
    <body>
        <?php require_once("header.php");?>
        <h1>Create Staff Account</h1>

        <main class="signup-form">
            <form class="signup-form" method="POST">
                <h2>Volunteer Account Registration Form</h2>
                <p>Please fill out each section of the following form if you would like to become a volunteer of Stafford Junction</p>
                <p>An asterisk (*) indicates a required field.</p>

                <!--First Name-->
                <label for="firstName">First Name *</label>
                <input type="text" name="firstName" placeholder="Enter your first name" required>
                <!--middle initial-->
                <label for="middleInitial">Middle Initial*</label>
                <input type="text" name="middleInitial" placeholder="Enter your middle initial" required>
                <!--Last Name-->
                <label for="lastName">Last Name *</label>
                <input type="text" name="lastName" placeholder="Enter your last name" required>

                <!--Birthdate-->
                <label for="birthdate" required>Date of Birth *</label>
                <input type="date" id="birthdate" name="birthdate" required placeholder="Choose your birthday" max="<?php echo date('Y-m-d'); ?>">

                <!--Address-->
                <label for="address">Address *</label>
                <input type="text" name="address" placeholder="Enter your address" required>
                <!--city-->
                <label for="city">City *</label>
                <input type="text" name="city" placeholder="Enter your city" required>
                <!--state-->
                <label for="state">State *</label>
                <input type="text" name="state" placeholder="Enter your state" required>
                <!--zip-->
                <label for="zip">zip *</label>
                <input type="text" name="zip" placeholder="Enter your zipcode" required>
                <!--Email-->

                <label for="email" required>* E-mail</label>
                <p>This will also serve as your username when logging in.</p>
                <input type="email" id="email" name="email" required placeholder="Enter your e-mail address">

                <!--Phone, cell or home-->
                <label for="phone">Phone *</label>
                <input type="tel" id="phone" name="phone" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" required placeholder="Ex. (555) 555-5555">

                <!--Job Title dont need-->
                <label for="jobTitle">Job Title *</label>
                <input type="text" name="jobTitle" placeholder="Enter your job title" required>

                <h3>Emergency Contact</h3>

                <!--Emergency Contact Name relation-->
                <label for="econtactName">Emergency Contact Name *</label>
                <input type="text" name="econtactName" placeholder="Enter emergency contact name" required>
                <label for="econtactRelationship">Emergency Contact Relationship *</label>
                <input type="text" name="econtactRelationship" placeholder="Enter emergency contact relationship" required>

                <!--Emergency Contact Phone relation-->
                <label for="econtactPhone">Emergency Contact Phone *</label>
                <input type="tel" id="phone" name="econtactPhone" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" required placeholder="Ex. (555) 555-5555">

                <!--Allergies-->
                <label for="Allergies">Allergies *</label>
                <input>
                    <select>
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
                </input>
                <h3>Login Credentials</h3>
                <fieldset>
                    <p>You will use the following information to log in to the system.</p>

                    <p><b>Your username is the primary email address entered above.</b></p>

                    <label for="password" required>* Password</label>
                    <p style="margin-bottom: 0;">Password must be eight or more characters in length and include least one special character (e.g., ?, !, @, #, $, &, %)</p>
                    <input type="password" id="password" name="password" pattern="^(?=.*[^a-zA-Z0-9].*).{8,}$" title="Password must be eight or more characters in length and include least one special character (e.g., ?, !, @, #, $, &, %)" placeholder="Enter a strong password" required>

                    <label for="password-reenter" required>* Re-enter Password</label>
                    <input type="password" id="password-reenter" name="password-reenter" placeholder="Re-enter password" required>
                    <p id="password-match-error" class="error hidden">Passwords do not match!</p>

                    <label for="securityQuestion" required>* Enter Security Question</label>
                    <input type="text" id="question" name="securityQuestion" placeholder="Security Question" required>

                    <label for="securityAnswer" required>* Enter Security Answer</label>
                    <input type="text" id="answer" name="securityAnswer" placeholder="Security Answer" required>
                </fieldset>

                <input type="submit" name="registration-form" value="Create Account">
                <?php
                if($success){
                    echo '<script>document.location = "index.php?addStaffSuccess";</script>';
                }
                ?>
                <a class="button cancel" href="index.php" style="margin-top: .5rem">Cancel</a>
            </form>
        </main> 
    </body>
</html>