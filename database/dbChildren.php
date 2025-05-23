<?php

include_once('dbinfo.php');
include_once(dirname(__FILE__) . '/../domain/Children.php');
include_once(dirname(__FILE__) . '/../database/dbFamily.php'); // Ensure this file is included only once

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

/**
 * Constructs a Child object from the sign-up form data
 */
function make_a_child_from_sign_up($childData, $family_id) {
    return new Child(
        null,
        $childData['first_name'],
        $childData['last_name'],
        $childData['dob'],
        $childData['address'],
        $childData['neighborhood'],
        $childData['city'],
        $childData['state'],
        $childData['zip'],
        $childData['gender'],
        $childData['school'],
        $childData['grade'],
        $childData['is_hispanic'],
        $childData['race'],
        $childData['medical_notes'],
        $childData['notes'],
        $family_id // Add this
    );
}

/**
 * Finds children based on various criteria
 */
function find_children($first_name, $last_name, $gender, $school, $grade, $is_hispanic, $race) {
    $conn = connect();
    $query = "SELECT * FROM dbChildren WHERE 1=1";
    $params = [];
    $types = "";

    if ($first_name) {
        $query .= " AND first_name LIKE ?";
        $params[] = "%" . $first_name . "%";
        $types .= "s";
    }
    if ($last_name) {
        $query .= " AND last_name LIKE ?";
        $params[] = "%" . $last_name . "%";
        $types .= "s";
    }
    if ($gender) {
        $query .= " AND gender = ?";
        $params[] = $gender;
        $types .= "s";
    }
    if ($school) {
        $query .= " AND school LIKE ?";
        $params[] = "%" . $school . "%";
        $types .= "s";
    }
    if ($grade) {
        $query .= " AND grade = ?";
        $params[] = $grade;
        $types .= "s";
    }
    if ($is_hispanic !== null) {
        $query .= " AND is_hispanic = ?";
        $params[] = $is_hispanic;
        $types .= "i";
    }
    if ($race) {
        $query .= " AND race LIKE ?";
        $params[] = "%" . $race . "%";
        $types .= "s";
    }

    $stmt = $conn->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
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