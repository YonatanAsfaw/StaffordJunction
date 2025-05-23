<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_cache_expire(30);
    session_start();
}

ini_set("display_errors", 1);
error_reporting(E_ALL);

// Initialize family data variables
$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = false;
$family = null;
$family_email = null;
$family_first_name = null;
$family_last_name = null;
$family_phone = null;
$family_zip = null;
$family_city = null;
$family_address = null;
$family_neighborhood = null;
$family_state = null;
$children = null;
$children_count = null;
$family_home_phone = null;
$family_cell_phone = null;

if (isset($_SESSION['_id'])) {
    require_once('domain/Family.php');
    require_once('include/input-validation.php');
    require_once('database/dbProgramInterestForm.php');
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    exit();
}

include_once("database/dbFamily.php");
include_once("database/dbChildren.php");

// Determine family_id based on user type
$family_id = null;
if ($accessLevel == 1) {
    $family_id = $_SESSION['_id'];
} else {
    if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
        die("ERROR: Invalid Family ID. Expected an integer but received: " . htmlspecialchars($_GET['id'] ?? 'none'));
    }
    $family_id = (int) $_GET['id'];
}

// Get family data for autopopulating form
$family = retrieve_family_by_id($family_id);
$family_email = $family->getEmail();
$family_first_name = $family->getFirstName();
$family_last_name = $family->getLastName();
$family_phone = $family->getPhone();
$family_zip = $family->getZip();
$family_city = $family->getCity();
$family_address = $family->getAddress();
$family_neighborhood = $family->getNeighborhood();
$family_state = $family->getState();
$children = retrieve_children_by_family_id($family_id);
$children_count = count($children);

// Get phone numbers
if ($family->getPhoneType() == "home") {
    $family_home_phone = $family->getPhone();
} else if ($family->getSecondaryPhoneType() == "home") {
    $family_home_phone = $family->getSecondaryPhone();
}
if ($family->getPhoneType() == "cellphone") {
    $family_cell_phone = $family->getPhone();
} else if ($family->getSecondaryPhoneType() == "cellphone") {
    $family_cell_phone = $family->getSecondaryPhone();
}

// Fetch existing form data
$data = getProgramInterestFormData($family_id);
$programData = null;
$topicData = null;
$availabilityData = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    error_log("POST request received");
    require_once('include/input-validation.php');
    $ignoreList = array('days');
    $args = sanitize($_POST, $ignoreList);
    if (isset($args['days'])) {
        foreach ($args['days'] as &$day) {
            $day = sanitize($day, null);
        }
        unset($day); // Unset reference
    }
    $required = array("first_name", "last_name", "address", "city", "neighborhood", "state", "zip", "cell_phone",
        "home_phone", "email", "child_num", "child_ages", "adult_num");
    $args['cell_phone'] = validateAndFilterPhoneNumber($args['cell_phone']);
    $args['home_phone'] = validateAndFilterPhoneNumber($args['home_phone']);
    error_log("Sanitized POST data: " . print_r($args, true));
    if (!wereRequiredFieldsSubmitted($args, $required)) {
        error_log("Required fields missing");
        $error_message = "Not all fields complete";
    } else {
        $success = createProgramInterestForm($args, $family_id);
        error_log("createProgramInterestForm result: " . var_export($success, true));
        if ($success && is_numeric($success) && $success > 0) {
            $redirect = "fillForm.php?formSubmitSuccess" . (isset($_GET['id']) ? "&id=" . urlencode($_GET['id']) : "");
            header("Location: $redirect");
            exit();
        } else {
            $error_message = "Failed to submit form. Please try again.";
        }
    }
}

// Load form data if it exists
if ($data) {
    $programData = getProgramInterestData($family_id);
    $topicData = getTopicInterestData($family_id);
    $availabilityData = getAvailabilityData($family_id);
}
?>

<html>
    <head>
        <?php include_once("universal.inc")?>
        <title>ODHS Medicine Tracker | Program Interest Form</title>
        <style>
            .form-section { margin-bottom: 20px; }
            .form-section label { display: block; margin-bottom: 5px; }
            .checkbox-group { margin-left: 20px; }
            .availability-day-form { margin-bottom: 10px; }
            .availability-day-form label { margin-right: 10px; }
            .error { color: red; margin-bottom: 10px; text-align: center; }
            .success { color: green; margin-bottom: 10px; }
            .day-label { font-weight: bold; }
            .topic-form { display: flex; margin-bottom: 10px; }
            .topic-form input[type="text"] { width: 25rem; margin-right: 10px; }
            .topic-form button { height: 2.33rem; }
        </style>
    </head>
    <body>
    <?php require('header.php'); ?>
        <h1>Program Interest Form / formulario de interés del programa</h1>
        <?php 
        if (isset($_GET['formSubmitFail'])) {
            echo '<div class="error">Error Submitting Form</div>';
        }
        if (isset($error_message)) {
            echo '<div class="error">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>
        <div id="formatted_form">
            <p>
                Please fill out this survey to help us better understand the needs of the community and schedule future classes
                and workshops. Please return to Stafford Junction at 791 Truslow Road, Fredericksburg, VA 22406, or email us
                at info@staffordjunction.org. Questions? Please call us at 540-368-0081.
                <br><br>
                Complete esta encuesta para ayudarnos a comprender mejor las necesidades de la comunidad y programar clases futuras.
                y talleres. Regrese a Stafford Junction en 791 Truslow Road, Fredericksburg, VA 22406, o envíenos un correo electrónico.
                en info@staffordjunction.org. ¿Preguntas? Por favor llámenos al 540-368-0081.
            </p>
            <br>
            <span>* Indicates required</span><br><br>

            <?php if ($data): ?>
                <div class="error">
                    A form submission already exists for this family. You can delete the existing submission to submit a new one.
                </div>
            <?php endif; ?>

            <form id="programInterestForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . (isset($_GET['id']) ? '?id=' . $_GET['id'] : '')); ?>" method="post">
                <h2>General Information / Información general</h2>
                <br>

                <label for="first_name">* First Name / Nombre</label><br><br>
                <input type="text" name="first_name" id="first_name" placeholder="First Name/Nombre" required <?php showProgramInterestData($data['first_name'] ?? null, $family_first_name)?>>
                <br><br>

                <label for="last_name">* Last Name / Apellido</label><br><br>
                <input type="text" name="last_name" id="last_name" placeholder="Last Name/Apellido " required <?php showProgramInterestData($data['last_name'] ?? null, $family_last_name)?>>
                <br><br>

                <label for="address">* Address / Dirección</label><br><br>
                <input type="text" name="address" id="address" placeholder="Address/Dirección" required <?php showProgramInterestData($data['address'] ?? null, $family_address)?>>
                <br><br>

                <label for="neighborhood">* Neighborhood / Vecindario</label><br><br>
                <input type="text" name="neighborhood" id="neighborhood" placeholder="Neighborhood/Vecindario" required <?php showProgramInterestData($data['neighborhood'] ?? null, $family_neighborhood)?>>
                <br><br>

                <label for="city">* City / Ciudad</label><br><br>
                <input type="text" name="city" id="city" placeholder="City/Ciudad" required <?php showProgramInterestData($data['city'] ?? null, $family_city)?>>
                <br><br>

                <label for="state">* State / Estado</label><br><br>
                <select id="state" name="state" <?php echo ($data && $data['state'] !== '') ? 'disabled style="background-color: yellow; color: black;"' : ''; ?>>
                    <option value="">--</option>
                    <option value="AL">Alabama</option>
                    <option value="AK">Alaska</option>
                    <option value="AZ">Arizona</option>
                    <option value="AR">Arkansas</option>
                    <option value="CA">California</option>
                    <option value="CO">Colorado</option>
                    <option value="CT">Connecticut</option>
                    <option value="DE">Delaware</option>
                    <option value="DC">District Of Columbia</option>
                    <option value="FL">Florida</option>
                    <option value="GA">Georgia</option>
                    <option value="HI">Hawaii</option>
                    <option value="ID">Idaho</option>
                    <option value="IL">Illinois</option>
                    <option value="IN">Indiana</option>
                    <option value="IA">Iowa</option>
                    <option value="KS">Kansas</option>
                    <option value="KY">Kentucky</option>
                    <option value="LA">Louisiana</option>
                    <option value="ME">Maine</option>
                    <option value="MD">Maryland</option>
                    <option value="MA">Massachusetts</option>
                    <option value="MI">Michigan</option>
                    <option value="MN">Minnesota</option>
                    <option value="MS">Mississippi</option>
                    <option value="MO">Missouri</option>
                    <option value="MT">Montana</option>
                    <option value="NE">Nebraska</option>
                    <option value="NV">Nevada</option>
                    <option value="NH">New Hampshire</option>
                    <option value="NJ">New Jersey</option>
                    <option value="NM">New Mexico</option>
                    <option value="NY">New York</option>
                    <option value="NC">North Carolina</option>
                    <option value="ND">North Dakota</option>
                    <option value="OH">Ohio</option>
                    <option value="OK">Oklahoma</option>
                    <option value="OR">Oregon</option>
                    <option value="PA">Pennsylvania</option>
                    <option value="RI">Rhode Island</option>
                    <option value="SC">South Carolina</option>
                    <option value="SD">South Dakota</option>
                    <option value="TN">Tennessee</option>
                    <option value="TX">Texas</option>
                    <option value="UT">Utah</option>
                    <option value="VT">Vermont</option>
                    <option value="VA">Virginia</option>
                    <option value="WA">Washington</option>
                    <option value="WV">West Virginia</option>
                    <option value="WI">Wisconsin</option>
                    <option value="WY">Wyoming</option>
                </select><br><br>

                <script>
                    var data_state = '<?php echo $data['state'] ?? ''; ?>';
                    var family_state = '<?php echo $family_state ?? ''; ?>';
                    if (data_state !== '') {
                        element = document.getElementById("state");
                        option = element.querySelector("option[value = " + data_state + "]");
                        option.selected = true;
                        element.disabled = true;
                        element.style.backgroundColor = 'yellow';
                        element.style.color = 'black';
                    } else if (family_state !== '') {
                        element = document.getElementById("state");
                        option = element.querySelector("option[value = " + family_state + "]");
                        option.selected = true;
                    }
                </script>

                <label for="zip">* Zip Code / Código postal</label><br><br>
                <input type="text" name="zip" id="zip" placeholder="Zip Code/Código postal" required <?php showProgramInterestData($data['zip'] ?? null, $family_zip)?>>
                <br><br>

                <label for="cell_phone">* Cell Phone / Teléfono móvil</label><br><br>
                <input type="text" name="cell_phone" id="cell_phone" placeholder="Cell Phone/Teléfono móvil" required <?php showProgramInterestData($data['cell_phone'] ?? null, $family_cell_phone)?>>
                <br><br>

                <label for="home_phone">* Home Phone / Teléfono residencial</label><br><br>
                <input type="text" name="home_phone" id="home_phone" placeholder="Home Phone/Teléfono residencial" required <?php showProgramInterestData($data['home_phone'] ?? null, $family_home_phone)?>>
                <br><br>

                <label for="email">* Email / Correo electrónico</label><br><br>
                <input type="email" name="email" id="email" placeholder="Email/Correo electrónico" required <?php showProgramInterestData($data['email'] ?? null, $family_email)?>>
                <br><br>

                <label for="child_num">* How Many Children in Household? / ¿Cuántos niños hay en el hogar?</label><br><br>
                <input type="number" oninput="getChildNum()" pattern="[0-9]*" name="child_num" id="child_num" placeholder="Number of Children/Número de niños" required <?php showProgramInterestData($data['child_num'] ?? null, $children_count)?>>
                <br><br>

                <label for="child_ages">Ages of Children (comma-separated, e.g., 10, 12, 14) / ¿Cuántos años?</label><br><br>
                <input type="text" name="child_ages" id="child_ages" placeholder="Ages/Años" <?php showProgramInterestData($data['child_ages'] ?? null, '')?>>
                <br><br>

                <label for="adult_num">* How Many Adults in Household? / ¿Cuántos adultos hay en el hogar?</label><br><br>
                <input type="number" name="adult_num" id="adult_num" required placeholder="Number of Adults/Número de adultos" <?php showProgramInterestData($data['adult_num'] ?? null, null)?>>
                <br><br>

                <h2>Programs of Interest / Programas de interés</h2>
                <br>
                <p>If you are interested in any programs listed below, please mark your choices \
                Si está interesado en alguno de los programas a continuación, por favor marque sus opciones
                </p><br>

                <input type="checkbox" id="brain_builders" name="programs[]" value="Brain Builders" <?php if ($data) echo showProgramInterestCheckbox($programData, "Brain Builders");?>>
                <label>Brain Builders (Tutoring Program for grades K – 12)</label><br>
                <input type="checkbox" id="camp_junction" name="programs[]" value="Camp Junction" <?php showProgramInterestCheckbox($programData, "Camp Junction")?>>
                <label>Camp Junction (Camp Program for grades K – 5)</label><br>
                <input type="checkbox" id="sports_camp" name="programs[]" value="Stafford County Sheriff’s Office Sports Camp" <?php showProgramInterestCheckbox($programData, "Stafford County Sheriff’s Office Sports Camp")?>>
                <label>Stafford County Sheriff’s Office Sports Camp (grades K – 12)</label><br>
                <input type="checkbox" id="steam" name="programs[]" value="STEAM" <?php showProgramInterestCheckbox($programData, "STEAM")?>>
                <label>STEAM (Science, Technology, Engineering, Arts, Math) Camp (grades 6 – 12)</label><br>
                <input type="checkbox" id="ymca" name="programs[]" value="YMCA" <?php showProgramInterestCheckbox($programData, "YMCA")?>>
                <label>YMCA (Membership, Activities) (All Ages)</label><br>
                <input type="checkbox" id="tide" name="programs[]" value="Tide Me Over Bags" <?php showProgramInterestCheckbox($programData, "Tide Me Over Bags")?>>
                <label>Tide Me Over Bags (Shelf Stable Meal) / Produce</label><br>
                <input type="checkbox" id="english_classes" name="programs[]" value="English Language Conversation Classes" <?php showProgramInterestCheckbox($programData, "English Language Conversation Classes")?>>
                <label>English Language Conversation Classes (Adults)</label><br><br>

                <h2>Topics of Interest / Temas de interés</h2>
                <br>
                <p>We want to know what topics you are interested in learning more about /
                Queremos saber qué temas le interesan
                </p><br>

                <input type="checkbox" id="legal_services" name="topics[]" value="Legal Services" <?php showProgramInterestCheckbox($topicData, "Legal Services")?>>
                <label>Legal Services</label><br>
                <input type="checkbox" id="finance" name="topics[]" value="Finance" <?php showProgramInterestCheckbox($topicData, "Finance")?>>
                <label>Finances</label><br>
                <input type="checkbox" id="tenant_rights" name="topics[]" value="Tenant Rights" <?php showProgramInterestCheckbox($topicData, "Tenant Rights")?>>
                <label>Tenant Rights</label><br>
                <input type="checkbox" id="health" name="topics[]" value="Health/Wellness/Nutrition" <?php showProgramInterestCheckbox($topicData, "Health/Wellness/Nutrition")?>>
                <label>Health/Wellness/Nutrition</label><br>
                <input type="checkbox" id="continuing_education" name="topics[]" value="Continuing Education" <?php showProgramInterestCheckbox($topicData, "Continuing Education")?>>
                <label>Continuing Education</label><br>
                <input type="checkbox" id="parenting" name="topics[]" value="Parenting" <?php showProgramInterestCheckbox($topicData, "Parenting")?>>
                <label>Parenting</label><br>
                <input type="checkbox" id="mental_health" name="topics[]" value="Mental Health" <?php showProgramInterestCheckbox($topicData, "Mental Health")?>>
                <label>Mental Health</label><br>
                <input type="checkbox" id="job_guidance" name="topics[]" value="Job/Career Guidance" <?php showProgramInterestCheckbox($topicData, "Job/Career Guidance")?>>
                <label>Job/Career Guidance</label><br>
                <input type="checkbox" id="citizenship_classes" name="topics[]" value="Citizenship Classes" <?php showProgramInterestCheckbox($topicData, "Citizenship Classes")?>>
                <label>Citizenship Classes</label><br><br>

                <label for="other_topics">Are there any other topics not listed you might be interested in?</label><br><br>
                <fieldset style="border: none;">
                    <div id="topic-container"></div>
                    <?php if (!$data) {
                        echo '<button type="button" onclick="addTopicForm()" style="width: 35.12rem;">+ Add Topic</button>';
                    } else {
                        $other_topics = implode(', ', getOtherTopicInterestData($topicData));
                        echo '<input type="text" disabled style="background-color: yellow; color: black;" value="' . htmlspecialchars($other_topics) . '">';
                    }?>
                </fieldset>
                <script>
                    let topicCount = 0;
                    function addTopicForm() {
                        topicCount++;
                        const container = document.getElementById('topic-container');
                        const topicDiv = document.createElement('div');
                        topicDiv.className = 'topic-form';
                        topicDiv.id = `topic-form-${topicCount}`;
                        topicDiv.innerHTML = `
                            <div style="display: flex; flex: 1;">
                            <div><input type="text" id="other_topic" name="topics[]" required placeholder="Topic/Temas" style="width: 25rem;"></div>
                            <div><button type="button" onclick="removeTopicForm(${topicCount})" style="height: 2.33rem;">Remove Topic</button></div>
                            </div>
                        `;
                        container.appendChild(topicDiv);
                    }
                    function removeTopicForm(topicId) {
                        const topicDiv = document.getElementById(`topic-form-${topicId}`);
                        if (topicDiv) {
                            topicDiv.remove();
                            topicCount--;
                        }
                    }
                </script>
                <br><br>

                <h2>Availability</h2>
                <br>
                <p>What days/times work best for you? / ¿Qué días/horas funcionan mejor?</p><br>

                <div class="availability-day-form">
                    <div class="day-label"><label>Monday:</label></div>
                    <div><input type='hidden' name='days[Monday][morning]' value='0'>
                    <input type="checkbox" id="monday_morning" name='days[Monday][morning]' value='1' <?php showAvailabilityCheckbox($availabilityData['Monday']['morning'] ?? null)?>>
                    <label for="monday_morning">Morning</label><br></div>
                    <div><input type='hidden' name='days[Monday][afternoon]' value='no'>
                    <input type="checkbox" id="monday_afternoon" name='days[Monday][afternoon]' value='1' <?php showAvailabilityCheckbox($availabilityData['Monday']['afternoon'] ?? null)?>>
                    <label for="monday_afternoon">Afternoon</label><br></div>
                    <div><input type='hidden' name='days[Monday][evening]' value='no'>
                    <input type="checkbox" id="monday_evening" name='days[Monday][evening]' value='1' <?php showAvailabilityCheckbox($availabilityData['Monday']['evening'] ?? null)?>>
                    <label for="monday_evening">Evening</label><br></div>
                    <div><label>Only Specific Times:</label><br></div>
                    <div><input type="text" name='days[Monday][specific_time]' id="monday_times" placeholder="Time/Horas" <?php showProgramInterestData($availabilityData['Monday']['specific_time'] ?? null, null)?>></div>
                </div>
                <br>

                <div class="availability-day-form">
                    <div class="day-label"><label>Tuesday:</label></div>
                    <div><input type='hidden' name='days[Tuesday][morning]' value='0'>
                    <input type="checkbox" id="tuesday_morning" name='days[Tuesday][morning]' value='1' <?php showAvailabilityCheckbox($availabilityData['Tuesday']['morning'] ?? null)?>>
                    <label for="tuesday_morning">Morning</label><br></div>
                    <div><input type='hidden' name='days[Tuesday][afternoon]' value='no'>
                    <input type="checkbox" id="tuesday_afternoon" name='days[Tuesday][afternoon]' value='1' <?php showAvailabilityCheckbox($availabilityData['Tuesday']['afternoon'] ?? null)?>>
                    <label for="tuesday_afternoon">Afternoon</label><br></div>
                    <div><input type='hidden' name='days[Tuesday][evening]' value='no'>
                    <input type="checkbox" id="tuesday_evening" name='days[Tuesday][evening]' value='1' <?php showAvailabilityCheckbox($availabilityData['Tuesday']['evening'] ?? null)?>>
                    <label for="tuesday_evening">Evening</label><br></div>
                    <div><label>Only Specific Times:</label><br></div>
                    <div><input type="text" name='days[Tuesday][specific_time]' id="tuesday_times" placeholder="Time/Horas" <?php showProgramInterestData($availabilityData['Tuesday']['specific_time'] ?? null, null)?>></div>
                </div>
                <br>

                <div class="availability-day-form">
                    <div class="day-label"><label>Wednesday:</label></div>
                    <div><input type='hidden' name='days[Wednesday][morning]' value='0'>
                    <input type="checkbox" id="wednesday_morning" name='days[Wednesday][morning]' value='1' <?php showAvailabilityCheckbox($availabilityData['Wednesday']['morning'] ?? null)?>>
                    <label for="wednesday_morning">Morning</label><br></div>
                    <div><input type='hidden' name='days[Wednesday][afternoon]' value='no'>
                    <input type="checkbox" id="wednesday_afternoon" name='days[Wednesday][afternoon]' value='1' <?php showAvailabilityCheckbox($availabilityData['Wednesday']['afternoon'] ?? null)?>>
                    <label for="wednesday_afternoon">Afternoon</label><br></div>
                    <div><input type='hidden' name='days[Wednesday][evening]' value='no'>
                    <input type="checkbox" id="wednesday_evening" name='days[Wednesday][evening]' value='1' <?php showAvailabilityCheckbox($availabilityData['Wednesday']['evening'] ?? null)?>>
                    <label for="wednesday_evening">Evening</label><br></div>
                    <div><label>Only Specific Times:</label><br></div>
                    <div><input type="text" name='days[Wednesday][specific_time]' id="wednesday_times" placeholder="Time/Horas" <?php showProgramInterestData($availabilityData['Wednesday']['specific_time'] ?? null, null)?>></div>
                </div>
                <br>

                <div class="availability-day-form">
                    <div class="day-label"><label>Thursday:</label></div>
                    <div><input type='hidden' name='days[Thursday][morning]' value='0'>
                    <input type="checkbox" id="thursday_morning" name='days[Thursday][morning]' value='1' <?php showAvailabilityCheckbox($availabilityData['Thursday']['morning'] ?? null)?>>
                    <label for="thursday_morning">Morning</label><br></div>
                    <div><input type='hidden' name='days[Thursday][afternoon]' value='no'>
                    <input type="checkbox" id="thursday_afternoon" name='days[Thursday][afternoon]' value='1' <?php showAvailabilityCheckbox($availabilityData['Thursday']['afternoon'] ?? null)?>>
                    <label for="thursday_afternoon">Afternoon</label><br></div>
                    <div><input type='hidden' name='days[Thursday][evening]' value='no'>
                    <input type="checkbox" id="thursday_evening" name='days[Thursday][evening]' value='1' <?php showAvailabilityCheckbox($availabilityData['Thursday']['evening'] ?? null)?>>
                    <label for="thursday_evening">Evening</label><br></div>
                    <div><label>Only Specific Times:</label><br></div>
                    <div><input type="text" name='days[Thursday][specific_time]' id="thursday_times" placeholder="Time/Horas" <?php showProgramInterestData($availabilityData['Thursday']['specific_time'] ?? null, null)?>></div>
                </div>
                <br><br><br>

                <p>Are there any other days or times that would work best for you? / ¿Hay otro horario que funcione mejor?</p><br><br>

                <div class="availability-day-form">
                    <div class="day-label"><label>Friday:</label></div>
                    <div><input type='hidden' name='days[Friday][morning]' value='0'>
                    <input type="checkbox" id="friday_morning" name='days[Friday][morning]' value='1' <?php showAvailabilityCheckbox($availabilityData['Friday']['morning'] ?? null)?>>
                    <label for="friday_morning">Morning</label><br></div>
                    <div><input type='hidden' name='days[Friday][afternoon]' value='no'>
                    <input type="checkbox" id="friday_afternoon" name='days[Friday][afternoon]' value='1' <?php showAvailabilityCheckbox($availabilityData['Friday']['afternoon'] ?? null)?>>
                    <label for="friday_afternoon">Afternoon</label><br></div>
                    <div><input type='hidden' name='days[Friday][evening]' value='no'>
                    <input type="checkbox" id="friday_evening" name='days[Friday][evening]' value='1' <?php showAvailabilityCheckbox($availabilityData['Friday']['evening'] ?? null)?>>
                    <label for="friday_evening">Evening</label><br></div>
                    <div><label>Only Specific Times:</label><br></div>
                    <div><input type="text" name='days[Friday][specific_time]' id="friday_times" placeholder="Time/Horas" <?php showProgramInterestData($availabilityData['Friday']['specific_time'] ?? null, null)?>></div>
                </div>
                <br>

                <div class="availability-day-form">
                    <div class="day-label"><label>Saturday:</label></div>
                    <div><input type='hidden' name='days[Saturday][morning]' value='0'>
                    <input type="checkbox" id="saturday_morning" name='days[Saturday][morning]' value='1' <?php showAvailabilityCheckbox($availabilityData['Saturday']['morning'] ?? null)?>>
                    <label for="saturday_morning">Morning</label><br></div>
                    <div><input type='hidden' name='days[Saturday][afternoon]' value='no'>
                    <input type="checkbox" id="saturday_afternoon" name='days[Saturday][afternoon]' value='1' <?php showAvailabilityCheckbox($availabilityData['Saturday']['afternoon'] ?? null)?>>
                    <label for="saturday_afternoon">Afternoon</label><br></div>
                    <div><input type='hidden' name='days[Saturday][evening]' value='no'>
                    <input type="checkbox" id="saturday_evening" name='days[Saturday][evening]' value='1' <?php showAvailabilityCheckbox($availabilityData['Saturday']['evening'] ?? null)?>>
                    <label for="saturday_evening">Evening</label><br></div>
                    <div><label>Only Specific Times:</label><br></div>
                    <div><input type="text" name='days[Saturday][specific_time]' id="saturday_times" placeholder="Time/Horas" <?php showProgramInterestData($availabilityData['Saturday']['specific_time'] ?? null, null)?>></div>
                </div>
                <br>

                <div class="availability-day-form">
                    <div class="day-label"><label>Sunday:</label></div>
                    <div><input type='hidden' name='days[Sunday][morning]' value='0'>
                    <input type="checkbox" id="sunday_morning" name='days[Sunday][morning]' value='1' <?php showAvailabilityCheckbox($availabilityData['Sunday']['morning'] ?? null)?>>
                    <label for="sunday_morning">Morning</label><br></div>
                    <div><input type='hidden' name='days[Sunday][afternoon]' value='no'>
                    <input type="checkbox" id="sunday_afternoon" name='days[Sunday][afternoon]' value='1' <?php showAvailabilityCheckbox($availabilityData['Sunday']['afternoon'] ?? null)?>>
                    <label for="sunday_afternoon">Afternoon</label><br></div>
                    <div><input type='hidden' name='days[Sunday][evening]' value='no'>
                    <input type="checkbox" id="sunday_evening" name='days[Sunday][evening]' value='1' <?php showAvailabilityCheckbox($availabilityData['Sunday']['evening'] ?? null)?>>
                    <label for="sunday_evening">Evening</label><br></div>
                    <div><label>Only Specific Times:</label><br></div>
                    <div><input type="text" name='days[Sunday][specific_time]' id="sunday_times" placeholder="Time/Horas" <?php showProgramInterestData($availabilityData['Sunday']['specific_time'] ?? null, null)?>></div>
                </div>
                <br><br>

                <?php if (!$programData): ?>
                    <button type="submit" id="submit">Submit</button>
                    <?php
                    if (isset($_GET['id'])) {
                        echo '<a class="button cancel" href="fillForm.php?id=' . htmlspecialchars($_GET['id']) . '" style="margin-top: .5rem">Cancel</a>';
                    } else {
                        echo '<a class="button cancel" href="fillForm.php" style="margin-top: .5rem">Cancel</a>';
                    }
                    ?>
                <?php endif; ?>

                <?php if ($programData): ?>
                    <form method="POST" action="database/dbProgramInterestForm.php<?php echo isset($_GET['id']) ? '?id=' . htmlspecialchars($_GET['id']) : ''; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" name="deleteForm" value="delete">Delete</button>
                    </form>
                    <?php if ($accessLevel > 1): ?>
                        <a class="button cancel" href="index.php">Return to Dashboard</a>
                    <?php else: ?>
                        <a class="button cancel" href="familyAccountDashboard.php">Return to Dashboard</a>
                    <?php endif; ?>
                <?php endif; ?>
            </form>
        </div>
    </body>
</html>