<?php

include_once('dbinfo.php');
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

/**
 * Retrieve all children (for admins)
 */
function retrieve_all_children() {
    $conn = connect();
    $query = "SELECT * FROM dbChildren ORDER BY last_name ASC"; 
    $result = mysqli_query($conn, $query);

    $children = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $children[] = $row;
    }

    mysqli_close($conn);
    return $children;
}

/**
 * Retrieve a single child by ID
 */
function retrieve_child_by_id($id) {
    $conn = connect();
    $query = "SELECT * FROM dbChildren WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        error_log("DEBUG: No child found with ID: " . $id);
        return null;
    }

    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    return make_a_child_from_database($row);
}

/**
 * Add child to database
 */
function add_child($child, $fam_id) {
    $conn = connect();
    
    $query = "INSERT INTO dbChildren (family_id, first_name, last_name, dob, gender, medical_notes, notes, 
        neighborhood, address, city, state, zip, school, grade, is_hispanic, race) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "isssssssssssssis",
        $fam_id,
        $child->getFirstName(),
        $child->getLastName(),
        $child->getBirthDate(),
        $child->getGender(),
        $child->getMedicalNotes(),
        $child->getNotes(),
        $child->getNeighborhood(),
        $child->getAddress(),
        $child->getCity(),
        $child->getState(),
        $child->getZip(),
        $child->getSchool(),
        $child->getGrade(),
        $child->isHispanic(),
        $child->getRace()
    );

    $success = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $success;
}

/**
 * Retrieve children by email
 */
function retrieve_children_by_email($email) {
    $conn = connect();
    $query = "SELECT dbChildren.* FROM dbChildren 
              INNER JOIN dbFamily ON dbChildren.family_id = dbFamily.id 
              WHERE dbFamily.email = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $children = [];
    while ($row = $result->fetch_assoc()) {
        $children[] = make_a_child_from_database($row);
    }

    $stmt->close();
    $conn->close();

    return empty($children) ? null : $children;
}

/**
 * Retrieve children by last name
 */
function retrieve_children_by_last_name($last_name) {
    $conn = connect();
    $query = "SELECT * FROM dbChildren WHERE last_name LIKE ?";

    $stmt = $conn->prepare($query);
    $searchTerm = "%{$last_name}%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $children = [];
    while ($row = $result->fetch_assoc()) {
        $children[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $children;
}

/**
 * Retrieve a child by first name, last name, and family ID
 */
function retrieve_child_by_firstName_lastName_famID($fn, $ln, $famID) {
    $conn = connect();
    $query = "SELECT * FROM dbChildren WHERE first_name = ? AND last_name = ? AND family_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $fn, $ln, $famID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt->close();
        $conn->close();
        return null;
    }

    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    return $row;
}
?>
