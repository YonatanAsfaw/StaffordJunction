<?php

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Family.php');
include_once('dbChildren.php');

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

?>
