<?php

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Family.php');
include_once('dbChildren.php');
include_once(dirname(__FILE__) . '/../domain/Children.php');

/**
 * Function to create a child object from a database row
 */
function make_a_child_from_database($result_row) {
    $child = new Child (
        $result_row['id'],
        $result_row['first_name'], // Fixed column name
        $result_row['last_name'],  // Fixed column name
        $result_row['dob'],        // Fixed column name
        $result_row['address'],
        $result_row['neighborhood'],
        $result_row['city'],
        $result_row['state'],
        $result_row['zip'],
        $result_row['gender'],
        $result_row['school'],
        $result_row['grade'],
        $result_row['is_hispanic'],
        $result_row['race'],
        $result_row['medical_notes'],
        $result_row['notes']
    );
    return $child;
}

/**
 * Retrieve children by family ID
 */
function retrieve_children_by_family_id($family_id) {
    $conn = connect();

    if (!$family_id || !is_numeric($family_id)) {
        error_log("ERROR: Invalid family_id in retrieve_children_by_family_id() - received: " . $family_id);
        return [];
    }

    $query = "SELECT * FROM dbChildren WHERE family_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $family_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $children = [];
    while ($row = $result->fetch_assoc()) {
        $children[] = $row;
    }

    if (empty($children)) {
        error_log("DEBUG: No children found for family ID: " . $family_id);
    }

    $stmt->close();
    $conn->close();

    return $children;
}

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = null;

/**
 * Simply prints var_dump results in a more readable fashion
 */
function prettyPrint($val) {
    echo "<pre>";
    var_dump($val);
    echo "</pre>";
    die();
}

/**
 * Constructs a Family object from the sign-up form data
 */
function make_a_family($result_row) {
    return new Family(
        null,
        $result_row['first-name'],
        $result_row['last-name'],
        $result_row['birthdate'],
        $result_row['address'],
        $result_row['neighborhood'],
        $result_row['city'],
        $result_row['state'],
        $result_row['zip'],
        $result_row['email'],
        $result_row['phone'],
        $result_row['phone-type'],
        $result_row['secondary-phone'],
        $result_row['secondary-phone-type'],
        $result_row['isHispanic'],
        $result_row['race'],
        $result_row['income'],
        $result_row['first-name2'] ?? null,
        $result_row['last-name2'] ?? null,
        $result_row['birthdate2'] ?? null,
        $result_row['address2'] ?? null,
        $result_row['neighborhood2'] ?? null,
        $result_row['city2'] ?? null,
        $result_row['state2'] ?? null,
        $result_row['zip2'] ?? null,
        $result_row['email2'] ?? null,
        $result_row['phone2'] ?? null,
        $result_row['phone-type2'] ?? null,
        $result_row['secondary-phone2'] ?? null,
        $result_row['secondary-phone-type2'] ?? null,
        $result_row['isHispanic2'] ?? null,
        $result_row['race2'] ?? null,
        $result_row['econtact-first-name'],
        $result_row['econtact-last-name'],
        $result_row['econtact-phone'],
        $result_row['econtact-relation'],
        password_hash($result_row['password'], PASSWORD_BCRYPT),
        $result_row['question'],
        password_hash($result_row['answer'], PASSWORD_BCRYPT),
        0
    );
}

/**
 * Constructs a Family object from database fields
 */
function make_a_family2($result_row) {
    return new Family(
        $result_row['id'],
        $result_row['firstName'],
        $result_row['lastName'],
        $result_row['birthdate'],
        $result_row['address'],
        $result_row['neighborhood'],
        $result_row['city'],
        $result_row['state'],
        $result_row['zip'],
        $result_row['email'],
        $result_row['phone'],
        $result_row['phoneType'],
        $result_row['secondaryPhone'],
        $result_row['secondaryPhoneType'],
        $result_row['isHispanic'],
        $result_row['race'],
        $result_row['income'],
        $result_row['firstName2'] ?? null,
        $result_row['lastName2'] ?? null,
        $result_row['birthdate2'] ?? null,
        $result_row['address2'] ?? null,
        $result_row['neighborhood2'] ?? null,
        $result_row['city2'] ?? null,
        $result_row['state2'] ?? null,
        $result_row['zip2'] ?? null,
        $result_row['email2'] ?? null,
        $result_row['phone2'] ?? null,
        $result_row['phoneType2'] ?? null,
        $result_row['secondaryPhone2'] ?? null,
        $result_row['secondaryPhoneType2'] ?? null,
        $result_row['isHispanic2'] ?? null,
        $result_row['race2'] ?? null,
        $result_row['econtactFirstName'],
        $result_row['econtactLastName'],
        $result_row['econtactPhone'],
        $result_row['econtactRelation'],
        $result_row['password'],
        $result_row['securityQuestion'],
        $result_row['securityAnswer'],
        $result_row['isArchived']
    );
}
/**
 * Retrieves family data by ID
 */
function retrieve_family_by_id($id) {
    $conn = connect();

    // Debugging: Print out the ID to check if it's valid
    error_log("DEBUG: Family ID being used: " . print_r($id, true));

    // Ensure ID is an integer
    if (!is_numeric($id)) {
        die("ERROR: Invalid Family ID. Expected an integer but received: " . htmlspecialchars($id));
    }

    $query = "SELECT * FROM dbFamily WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("ERROR: Query preparation failed - " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        die("ERROR: No family found with ID: " . htmlspecialchars($id));
    }

    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    return make_a_family2($row);
}

/**
 * Inserts a Family object into the database
 */
function add_family($family) {
    $conn = connect();

    $query = "INSERT INTO dbFamily (firstName, lastName, birthdate, address, neighborhood, city, state, zip, email, phone, phoneType, secondaryPhone, secondaryPhoneType, isHispanic, race, income, firstName2, lastName2, birthdate2, address2, neighborhood2, city2, state2, zip2, email2, phone2, phoneType2, secondaryPhone2, secondaryPhoneType2, isHispanic2, race2, econtactFirstName, econtactLastName, econtactPhone, econtactRelation, password, securityQuestion, securityAnswer, isArchived) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("ERROR: Query preparation failed - " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssssssssssssssssssssssssssssss",
        $family->getFirstName(),
        $family->getLastName(),
        $family->getBirthdate(),
        $family->getAddress(),
        $family->getNeighborhood(),
        $family->getCity(),
        $family->getState(),
        $family->getZip(),
        $family->getEmail(),
        $family->getPhone(),
        $family->getPhoneType(),
        $family->getSecondaryPhone(),
        $family->getSecondaryPhoneType(),
        $family->getIsHispanic(),
        $family->getRace(),
        $family->getIncome(),
        $family->getFirstName2(),
        $family->getLastName2(),
        $family->getBirthdate2(),
        $family->getAddress2(),
        $family->getNeighborhood2(),
        $family->getCity2(),
        $family->getState2(),
        $family->getZip2(),
        $family->getEmail2(),
        $family->getPhone2(),
        $family->getPhoneType2(),
        $family->getSecondaryPhone2(),
        $family->getSecondaryPhoneType2(),
        $family->getIsHispanic2(),
        $family->getRace2(),
        $family->getEcontactFirstName(),
        $family->getEcontactLastName(),
        $family->getEcontactPhone(),
        $family->getEcontactRelation(),
        $family->getPassword(),
        $family->getSecurityQuestion(),
        $family->getSecurityAnswer()
    );

    $result = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $result;
}

function retrieve_family($args) {
    $conn = connect();
    $query = "SELECT * FROM dbFamily WHERE firstName = ? AND lastName = ? AND email = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $args['firstName'], $args['lastName'], $args['email']);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows < 1) {
        return null;
    }

    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    return make_a_family2($row);
}

/**
 * Retrieves family by email
 */
function retrieve_family_by_email($email) {
    $conn = connect();
    $query = "SELECT * FROM dbFamily WHERE email = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows < 1) {
        return null;
    }

    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    return make_a_family2($row);
}

/**
 * Retrieves families by last name
 */
function retrieve_family_by_lastName($lastName) {
    $conn = connect();
    $query = "SELECT * FROM dbFamily WHERE lastName LIKE ?";
    
    $stmt = $conn->prepare($query);
    $param = "%" . $lastName . "%";
    $stmt->bind_param("s", $param);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $families = [];

    if ($result->num_rows < 1) {
        return null;
    }

    while ($row = $result->fetch_assoc()) {
        $families[] = make_a_family2($row);
    }

    $stmt->close();
    $conn->close();
    
    return $families;
}

/**
 * Retrieves all families
 */
function find_all_families() {
    $conn = connect();
    $query = "SELECT * FROM dbFamily ORDER BY lastName";

    $result = $conn->query($query);
    
    if (!$result) {
        $conn->close();
        return [];
    }

    $families = [];
    while ($row = $result->fetch_assoc()) {
        $families[] = make_a_family2($row);
    }

    $conn->close();
    return $families;
}

/**
 * Archives a family
 */
function archive_family($id) {
    $conn = connect();
    $query = "UPDATE dbFamily SET isArchived=1 WHERE id=?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

/**
 * Unarchives a family
 */
function unarchive_family($id) {
    $conn = connect();
    $query = "UPDATE dbFamily SET isArchived=0 WHERE id=?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

/**
 * Inserts family languages into the database
 */
function insert_family_languages($languages, $familyId) {
    $conn = connect();
    $query = "INSERT INTO dbFamilyLanguages (familyId, language) VALUES (?, ?)";

    $stmt = $conn->prepare($query);

    foreach ($languages as $language) {
        $stmt->bind_param("is", $familyId, $language);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
}

/**
 * Inserts family assistance into the database
 */
function insert_family_assistance($assistance, $familyId) {
    $conn = connect();
    $query = "INSERT INTO dbFamilyAssistance (familyId, assistanceType) VALUES (?, ?)";

    $stmt = $conn->prepare($query);

    foreach ($assistance as $type) {
        $stmt->bind_param("is", $familyId, $type);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
}

/**
 * Finds families based on various criteria
 */
function find_families($last_name, $email, $neighborhood, $address, $city, $zip, $income, $assistance, $is_archived) {
    $conn = connect();
    $query = "SELECT * FROM dbFamily WHERE 1=1";
    $params = [];
    $types = "";

    if ($last_name) {
        $query .= " AND lastName LIKE ?";
        $params[] = "%" . $last_name . "%";
        $types .= "s";
    }
    if ($email) {
        $query .= " AND email LIKE ?";
        $params[] = "%" . $email . "%";
        $types .= "s";
    }
    if ($neighborhood) {
        $query .= " AND neighborhood LIKE ?";
        $params[] = "%" . $neighborhood . "%";
        $types .= "s";
    }
    if ($address) {
        $query .= " AND address LIKE ?";
        $params[] = "%" . $address . "%";
        $types .= "s";
    }
    if ($city) {
        $query .= " AND city LIKE ?";
        $params[] = "%" . $city . "%";
        $types .= "s";
    }
    if ($zip) {
        $query .= " AND zip LIKE ?";
        $params[] = "%" . $zip . "%";
        $types .= "s";
    }
    if ($income) {
        $query .= " AND income = ?";
        $params[] = $income;
        $types .= "s";
    }
    if ($assistance) {
        $query .= " AND id IN (SELECT familyId FROM dbFamilyAssistance WHERE assistanceType LIKE ?)";
        $params[] = "%" . $assistance . "%";
        $types .= "s";
    }
    if ($is_archived !== null) {
        $query .= " AND isArchived = ?";
        $params[] = $is_archived;
        $types .= "i";
    }

    $stmt = $conn->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $families = [];
    while ($row = $result->fetch_assoc()) {
        $families[] = make_a_family2($row);
    }

    $stmt->close();
    $conn->close();

    return $families;
}

/**
 * Retrieves children associated with a family ID
 */
function getChildren($familyId) {
    $conn = connect();
    $query = "SELECT * FROM dbChildren WHERE family_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $familyId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $children = [];

    while ($row = $result->fetch_assoc()) {
        $children[] = make_a_child_from_database($row);
    }

    $stmt->close();
    $conn->close();

    return $children;
}

if(isset($_GET['id'])){
    require_once("database/dbFamily.php");
    require_once("database/dbChildren.php");
    require_once('database/dbBrainBuildersRegistration.php');
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_GET['id'];
    $family = retrieve_family_by_id($userID);
    $children = retrieve_children_by_family_id($userID);
    // Debugging: Check if children data is fetched correctly
    error_log("DEBUG: Children data: " . print_r($children, true));
    $address = $family->getAddress();
    $city = $family->getCity();
    $phone = $family->getPhone();
    $zip = $family->getZip();
    $email = $family->getEmail();
    $emergency_contact_name = $family->getEContactFirstName() . " " . $family->getEContactLastName();
    $econtactRelation = $family->getEContactRelation();
    $econtactPhone = $family->getEContactPhone();
    
    $parent2Name = null;
}

// include the header .php files
if($_SERVER['REQUEST_METHOD'] == "POST"){
    require_once('include/input-validation.php');
    require_once('database/dbChildren.php');
    require_once('database/dbBrainBuildersRegistration.php');
    $args = sanitize($_POST, null);
    $n = explode(" ", $args['name']);
    //data_dump($n);
    //$childToRegister = retrieve_child_by_firstName_lastName_famID($args['child-first-name'], $args['child-last-name'], $_GET['id']);
    $childToRegister = retrieve_child_by_firstName_lastName_famID($n[0], $n[1], $_GET['id']);
    $success = register($args, $childToRegister['id']);
}
?>

<html>
<head>
    <!-- Include universal styles formatting -->
    <?php include_once("universal.inc") ?>
    <title>Stafford Junction | Brain Builders Student Registration Form</title>
    <script>
        function populateChildInfo(childId) {
            const childrenData = <?php echo json_encode($children); ?>;
            console.log(childrenData); // Debugging: Check if children data is passed correctly
            const selectedChild = childrenData.find(child => child.id == childId);

            if (selectedChild) {
                document.getElementById('child-first-name').value = selectedChild.first_name;
                document.getElementById('child-last-name').value = selectedChild.last_name;
                document.getElementById('gender').value = selectedChild.gender;
                document.getElementById('school-name').value = selectedChild.school;
                document.getElementById('grade').value = selectedChild.grade;
                document.getElementById('birthdate').value = selectedChild.dob;
                document.getElementById('child-address').value = selectedChild.address;
                document.getElementById('child-city').value = selectedChild.city;
                document.getElementById('child-state').value = selectedChild.state;
                document.getElementById('child-zip').value = selectedChild.zip;
                document.getElementById('child-medical-allergies').value = selectedChild.medical_notes;
                document.getElementById('child-food-avoidances').value = selectedChild.notes;
            }
        }
    </script>
</head>
<body>
    <h1>Brain Builders Registration Form 2024-2025</h1>
    <div id="formatted_form">
        <p><b>* Indicates a required field</b></p><br>

        <h2>Student Information</h2><br>
        <form id="brainBuildersStudentRegistrationForm" action="" method="post">             
            <!-- Child Name -->
            <label for="name">Child Name / Nombre del Hijo*</label><br><br>
            <select name="name" id="name" required onchange="populateChildInfo(this.value)">
                <option value="" disabled selected>Select Child</option>
                <?php
                foreach ($children as $c){ //cycle through each child of family account user
                    $id = $c['id']; // Use array key instead of method
                    // Check if form was already completed for the child
                    if (!isBrainBuildersRegistrationComplete($id)) {
                        $name = $c['first_name'] . " " . $c['last_name']; // Use array keys instead of methods
                        echo "<option value=\"$id\">$name</option>";
                    }
                }
                ?>
            </select><br><br>

            <!--Child First Name-->
            <label for="child-first-name">Child First Name *</label><br><br>
            <input type="text" name="child-first-name" id="child-first-name" required placeholder="Child First Name" required><br><br>
            
            <!--Child Last Name-->
            <label for="child-last-name">Child Last Name *</label><br><br>
            <input type="text" name="child-last-name" id="child-last-name" required placeholder="Child Last Name" required><br><br>

            <!--Gender-->
            <label for="gender">Gender *</label><br><br>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select><br><br>

            <!--School Name-->
            <label for="school-name">School Name *</label><br><br>
            <input type="text" name="school-name" id="school-name" required placeholder="School Name" required><br><br>

            <!--Grade-->
            <label for="grade">Grade *</label><br><br>
            <input type="text" name="grade" id="grade" required placeholder="Grade/Grado" required><br><br>

            <!--Date of Birth-->
            <label for="birthdate">Date of Birth *</label><br><br>
            <input type="date" id="birthdate" name="birthdate" required placeholder="Choose your birthday" max="<?php echo date('Y-m-d'); ?>"><br><br>

            <!--Street Address-->
            <label for="child-address">Street Address *</label><br><br>
            <input type="text" id="child-address" name="child-address" required placeholder="Enter your street address"><br><br>

            <!--City-->
            <label for="child-city">City *</label><br><br>
            <input type="text" id="child-city" name="child-city" required placeholder="Enter your city"><br><br>

            <!--State-->
            <label for="child-state">State *</label><br><br>
            <select id="child-state" name="child-state" required>
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
                <option value="VA" selected>Virginia</option>
                <option value="WA">Washington</option>
                <option value="WV">West Virginia</option>
                <option value="WI">Wisconsin</option>
                <option value="WY">Wyoming</option>
            </select><br><br>

            <!--Zip-->
            <label for="child-zip" required>Zip Code *</label><br><br>
            <input type="text" id="child-zip" name="child-zip" pattern="[0-9]{5}" title="5-digit zip code" required placeholder="Enter your 5-digit zip code"><br><br>

            <!--Medical issues or allergies-->
            <label for="child-medical-allergies" required>Medical issues or allergies</label><br><br>
            <input type="text" id="child-medical-allergies" name="child-medical-allergies" placeholder="Medical issues or allergies"><br><br>

            <!--Foods to avoid due to religious beliefs-->
            <label for="child-food-avoidances" required>Foods to avoid due to religious beliefs</label><br><br>
            <input type="text" id="child-food-avoidances" name="child-food-avoidances" placeholder="Foods to avoid due to religious beliefs"><br><br>

            <!-- Additional form fields... -->

            <button type="submit" id="submit">Submit</button>

            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST" && $success){
                    if (isset($_GET['id'])) {
                        echo '<script>document.location = "fillForm.php?formSubmitSuccess&id=' . $_GET['id'] . '";</script>';
                    } else {
                        echo '<script>document.location = "fillForm.php?formSubmitSuccess";</script>';
                    }
                } 
            ?>

            <?php 
            if (isset($_GET['id'])) {
                echo '<a class="button cancel" href="fillForm.php?id=' . $_GET['id'] . '" style="margin-top: .5rem">Cancel</a>';
            } else {
                echo '<a class="button cancel" href="fillForm.php" style="margin-top: .5rem">Cancel</a>';
            }
            ?>
        </form>
    </div>
</body>
</html>