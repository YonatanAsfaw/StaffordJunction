<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    die();
}

$accessLevel = $_SESSION['access_level'];
$userID = $_SESSION['_id'];
$success = false;

// Include necessary files
include_once("database/dbFamily.php");
include_once("database/dbChildren.php");
require_once('database/dbSchoolSuppliesForm.php');

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $form_id = intval($_POST['form_id']);
    if ($form_id > 0) {
        $delete_success = deleteSchoolSuppliesForm($form_id);
        if ($delete_success) {
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Failed to delete form.";
        }
    }
}

// Retrieve familyID
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $familyID = (int)$_GET['id'];
} elseif (isset($userID) && is_numeric($userID)) {
    $familyID = (int)$userID;
} else {
    die("ERROR: Invalid Family ID. Expected an integer but received: " . htmlspecialchars($_GET['id'] ?? 'NULL'));
}

// Retrieve family information
$family = retrieve_family_by_id($familyID);
if (!$family) {
    die("ERROR: No family found with ID: " . htmlspecialchars($familyID));
}

$family_email = $family->getEmail();
$children = retrieve_children_by_family_id($familyID);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['action'])) {
    require_once('include/input-validation.php');
    $args = sanitize($_POST, null);
    $required = array("email", "name", "grade", "school", "community", "need_backpack");

    if (!wereRequiredFieldsSubmitted($args, $required)) {
        $error_message = "Not all fields complete";
    } else {
        $community = ($_POST['community'] == 'other') ? $_POST['community_other'] : $_POST['community'];
        $args['community'] = $community;
        $success = createBackToSchoolForm($args);
        if ($success) {
            header("Location: index.php?success=1");
            exit();
        } else {
            $error_message = "Failed to submit form. Please try again.";
        }
    }
}

// Retrieve existing form data
$form_data = null;
if (isset($_GET['form_id']) && ctype_digit($_GET['form_id'])) {
    $form_id = intval($_GET['form_id']);
    $form_data = getSchoolSuppliesFormById($form_id);
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Form submitted successfully";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("universal.inc") ?>
    <title>School Supplies Form</title>
</head>
<body>

<h1>Stafford Junction School Supplies / Utiles Escolares</h1>

<div id="formatted_form">
    <p>Stafford Junction is holding a Back-to-School Community Day on August 10. This form will guarantee that your child will have a premade
       backpack before Community Day that can be picked up during the event. Please submit a form for each child.</p>
    <p>Stafford Junction llevará a cabo un Día de la Comunidad de Regreso a la Escuela el 10 de agosto. Este formulario garantizará que su hijo tendrá una mochila con 
       útiles escolares antes del Día de la Comunidad que se puede recoger durante el evento. Por favor, envíe un formulario para cada niño.</p>

    <br>
    <span>* Indicates required</span><br><br>

    <?php if (isset($error_message)) { echo "<p style='color:red;'>{$error_message}</p>"; } ?>
    <?php if (isset($successMessage)) { echo "<p style='color:green;'>{$successMessage}</p>"; } ?>

    <form method="POST" action="">
        <label for="email">1. Email*</label><br><br>
        <input type="text" name="email" id="email" required value="<?php echo htmlspecialchars($family_email); ?>" <?php if ($form_data) echo 'disabled'; ?>><br><br>

        <label for="name">2. Child Name / Nombre del Estudiante*</label><br><br>
        <select name="name" id="name" required <?php if ($form_data) echo 'disabled'; ?>>
            <option disabled selected>Select a child</option>
            <?php
                if (!is_array($children) || empty($children)) {
                    echo "<option disabled>No children available</option>";
                } else {
                    foreach ($children as $c) {
                        $id = $c['id'];
                        $name = $c['first_name'] . " " . $c['last_name'];
                        $value = $id . "_" . $name;
                        $selected = ($form_data && $form_data['child_name'] == $name) ? 'selected' : '';
                        echo "<option value='$value' $selected>$name</option>";
                    }
                }
            ?>
        </select><br><br>

        <label for="grade">3. Grade / Grado*</label><br><br>
        <input type="text" name="grade" id="grade" required value="<?php echo htmlspecialchars($form_data['grade'] ?? ''); ?>" <?php if ($form_data) echo 'disabled'; ?>><br><br>

        <label for="school">4. School / Escuela*</label><br><br>
        <input type="text" name="school" id="school" required value="<?php echo htmlspecialchars($form_data['school'] ?? ''); ?>" <?php if ($form_data) echo 'disabled'; ?>><br><br>

        <label>5. Will you pick up the bag during Community Day?</label><br><br>
        <input type="radio" id="choice_1" name="community" value="pick_up" required <?php if ($form_data && $form_data['community'] == 'pick_up') echo 'checked'; ?>>
        <label for="choice_1">I will pick up the bag on Community day (August 10).</label><br><br>

        <input type="radio" id="choice_2" name="community" value="no_pick_up" required <?php if ($form_data && $form_data['community'] == 'no_pick_up') echo 'checked'; ?>>
        <label for="choice_2">I will need the bag brought to me.</label><br><br>

        <input type="radio" id="choice_3" name="community" value="other" required <?php if ($form_data && !in_array($form_data['community'], ['pick_up', 'no_pick_up'])) echo 'checked'; ?>>
        <label for="choice_3">Other</label>
        <input type="text" name="community_other" id="other" value="<?php echo ($form_data && !in_array($form_data['community'], ['pick_up', 'no_pick_up'])) ? htmlspecialchars($form_data['community']) : ''; ?>" <?php if (!$form_data || in_array($form_data['community'], ['pick_up', 'no_pick_up'])) echo 'disabled'; ?>><br><br>

        <label>6. Will you need a backpack?*</label><br><br>
        <input type="radio" id="choice_a" name="need_backpack" value="have_backpack_already" required <?php if ($form_data && $form_data['need_backpack'] == 'have_backpack_already') echo 'checked'; ?>>
        <label for="choice_a">I already have a backpack.</label><br><br>

        <input type="radio" id="choice_b" name="need_backpack" value="need_backpack" required <?php if ($form_data && $form_data['need_backpack'] == 'need_backpack') echo 'checked'; ?>>
        <label for="choice_b">I need a backpack.</label><br><br>

        <?php if (!$form_data): ?>
            <button type="submit">Submit</button>
            <a class="button cancel" href="fillForm.php?id=<?php echo $familyID; ?>">Cancel</a>
        <?php else: ?>
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="form_id" value="<?php echo $form_data['id']; ?>">
                <button type="submit" name="deleteForm">Delete</button>
            </form>
            <a class="button cancel" href="index.php">Return to Dashboard</a>
        <?php endif; ?>
    </form>

    <script>
        document.getElementById("choice_3").addEventListener("change", () => {
            document.getElementById("other").disabled = !document.getElementById("choice_3").checked;
        });
        <?php if ($form_data && !in_array($form_data['community'], ['pick_up', 'no_pick_up'])): ?>
            document.getElementById("other").disabled = false;
        <?php endif; ?>
    </script>
</div>

</body>
</html>
