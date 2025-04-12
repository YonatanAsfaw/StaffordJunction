<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = null;
$errorMessage = null;

if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    $loggedIn = false;
    header("Location: login.php");
    exit;
}

require_once('header.php');
require('universal.inc');
require_once("database/dbFamily.php");
require_once("database/dbChildren.php");
require_once("database/dbHolidayPartyForm.php");

// Retrieve family and children of family by userID
$family = retrieve_family_by_id($_GET['id'] ?? $userID);
$children = retrieve_children_by_family_id($family->getId());

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once('include/input-validation.php');

    $args = sanitize($_POST, null);

    $required = ['email', 'name', 'isAttending', 'transportation', 'neighborhood', 'question_comments'];

    if (!wereRequiredFieldsSubmitted($args, $required)) {
        $errorMessage = "Please complete all required fields.";
    } elseif (!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } else {
        $childName = explode(" ", $args['name']);
        $row = retrieve_child_by_firstName_lastName_famID($childName[0], $childName[1], $family->getId());
        $args['family_id'] = $_GET['id'] ?? $family->getId();
        $success = insert_into_dbHolidayPartyForm($args, $row['id']);

        if ($success) {
            $successMessage = "Form submitted successfully!";
        } else {
            $errorMessage = "Failed to submit the form. The child may already be registered.";
        }
    }
}
?>

<html>
<head>
    <?php include_once("universal.inc"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stafford Junction | Holiday Party Form</title>

<html>

<head>
    <!-- Include universal styles, scripts, or configurations via external file -->
    <?php include_once("universal.inc") ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stafford Junction | Holiday Party Form</title>
</head>

<body>

    <!-- Main heading of the page -->
    <h1>Stafford Junction Brain Builders Holiday - Party Celebración de días festivos</h1>

    <!-- Container for formatted form content -->
    <div id="formatted_form">

        <!-- Event details in English -->
        <p><b>When: Thursday, December 21, 1:00-4:00 PM</b></p>
        <br>
        <p><b>Where: 791 Truslow Road, Fredericksburg, VA. 22406</b></p>
        <br>
        <p>
            Please let us know if your student will be able to attend the Holiday Party. Transportation pick-up/drop-off
            times will be distributed on December 18. Meals will be provided. <br><br>
            Please email <b>crice@staffordjunction.org</b> with any questions you may have.
        </p>
        <hr>
        <br>

        <!-- Event details in Spanish -->
        <p><b>Cuándo: jueves 21 de diciembre de 13:00 a 16:00 horas.</b></p>
        <br>
        <p><b>Dónde: 791 Truslow Road, Fredericksburg, VA. 22406</b></p>
        <br>
        <p>
            Háganos saber si su estudiante podrá asistir a la fiesta navideña. Los horarios de recogida y entrega del
            transporte se distribuirán el 18 de diciembre. Se proporcionarán comidas. <br><br>
            Envíe un correo electrónico a <b>crice@staffordjunction.org</b> con cualquier pregunta que pueda tener.
        </p>
        <hr>
        <br>

        <!-- Required question indicator -->
        <p><strong>* Indicates required question</strong></p>
    </div>
    <br><br>
    <div id="formatted_form">
        <form method="POST">
            <!-- Email -->
            <label for="email">Email - Correo Electrónico* </label>
            <input type="text" name="email" id="email" placeholder="Email - Correo Electrónico"  value="<?php echo htmlspecialchars($family->getEmail());?>" required>
            <br><br>

            <!-- Child Name -->
            <label for="name">Registered Brain Builder Student Name / Nombre del Estudiante*</label><br><br>
            <select name="name" id="name" required>
                <option value="" disabled selected>Select Child</option>    
                <?php
                require_once('domain/Children.php');
                require_once('database/dbBrainBuildersRegistration.php');
                foreach ($children as $child) {
                    $id = $child['id'];
                    if (!isHolidayPartyFormComplete($id) && isBrainBuildersRegistrationComplete($id)) {
                        $name = $child['first_name'] . ' ' . $child['last_name'];
                        $dob = $child['birth_date'];
                        echo "<option value='$name'>$name</option>"; 
                    }
                }
                ?>
            </select>

            <br><br>
            <!-- Attendance Section -->
            <div>
                <p><strong>Will your student be attending? * ¿Asistirá su estudiante?</strong></p>

                <!-- Option for "Yes" -->
                <label>
                    <input type="radio" name="isAttending" value="1" required> Yes / Sí
                </label>
                <br><br>

                <!-- Option for "No" -->
                <label>
                    <input type="radio" name="isAttending" value="0" required> No
                </label>
            </div>
            <br><br>

            <!-- Transportation Section -->
            <div>
                <p><strong>Transportation * Transporte</strong></p>

                <!-- Option for providing own transportation -->
                <label>
                    <input type="radio" name="transportation" value="provide_own" required> I will provide transportation for my
                    student. / Proporcionaré transporte para mi estudiante.
                </label>
                <br><br>

                <!-- Option for needing Stafford Junction transportation -->
                <label>
                    <input type="radio" name="transportation" value="stafford_junction" required> My student will need Stafford
                    Junction to provide transportation. / Mi estudiante necesitará Stafford Junction para proporcionar
                    transporte.
                </label>
            </div>
            <br><br>

            <!-- Neighborhood Pickup Section -->
            <div>
                <p><strong>Which neighborhood will your student be picked up from? * ¿De qué vecindario recogerán a su
                        estudiante?</strong></p>

                <!-- Option for "Other" with text input for specifying neighborhood -->
                <label>
                    <input type="text" name="neighborhood" placeholder="Specify neighborhood" value="<?php echo htmlspecialchars($family->getNeighborhood());?>" required>
                </label>

            </div>
            <br><br>

            <!-- Additional Information Section -->
            <div>
                <p><strong>Question or Comments: Pregunta o comentarios:</strong></p>

                <!-- Large text area for additional comments or information -->
                <label>
                    <textarea name="question_comments" rows="6" cols="50"
                        placeholder="Enter any additional information here / Ingrese cualquier información adicional aquí"></textarea>
                </label>
            </div>
            <br><br>

            <!-- Submit and Cancel Buttons -->
            <button type="submit">Submit</button>
            <?php 
                if (isset($_GET['id'])) {
                    echo '<a class="button cancel" href="fillForm.php?id=' . $_GET['id'] . '" style="margin-top: .5rem">Cancel</a>';
                } else {
                    echo '<a class="button cancel" href="fillForm.php" style="margin-top: .5rem">Cancel</a>';
                }
            ?>
        </div>
            <?php //If the user is an admin or staff, the message should appear at index.php
            if($_SERVER['REQUEST_METHOD'] == "POST" && $success){
                if (isset($_GET['id'])) {
                    echo '<script>document.location = "fillForm.php?formSubmitSuccess&id=' . $_GET['id'] . '";</script>';
                } else {
                    echo '<script>document.location = "fillForm.php?formSubmitSuccess";</script>';
                }
            } else if ($_SERVER['REQUEST_METHOD'] == "POST" && !$success) {
                if (isset($_GET['id'])) {
                    echo '<script>document.location = "fillForm.php?formSubmitFail&id=' . $_GET['id'] . '";</script>';
                } else {
                    echo '<script>document.location = "fillForm.php?formSubmitFail";</script>';
                }
            }
            ?>
            </div>
        </form>
    </div>
    
</body>

</html>