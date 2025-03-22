<?php

// Function checks if a child has already completed the form
function isSpringBreakCampFormComplete($childID) 
{
    $connection = connect();

    $query = "SELECT * FROM dbSpringBreakCampForm INNER JOIN dbChildren ON dbSpringBreakCampForm.child_id = dbChildren.id WHERE dbChildren.id = $childID";
    $result = mysqli_query($connection, $query);
    if (!$result->num_rows > 0) {
        mysqli_close($connection);
        return false;
    } else {
        mysqli_close($connection);
        return true;
    }
}

function createSpringBreakCampForm($form)
{
    $child_data = explode("_", $form['child_name']);

	$child_id = $child_data[0];

    // parse the child name so that the id is not 
    // stored in the database for the child name
    $child_name = $child_data[1];
    $connection = connect();

    $email = $form["email"];
    $student_name = $child_name;
    $school_choice = $form["school_date"];
    $isAttending = $form["isAttending"] ? 1 : 0;
    $waiver_completed = $form["hasWaiver"] ? 1 : 0;
    $notes = !empty($form["questions_comments"]) ? $form["questions_comments"] : null;

    $query = "
        INSERT INTO dbSpringBreakCampForm (email, student_name, school_choice, isAttending, waiver_completed, notes, child_id)
        values ('$email', '$student_name', '$school_choice', '$isAttending', '$waiver_completed', '$notes', '$child_id');
    ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    $id = mysqli_insert_id($connection);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $id;
}

function getSpringBreakCampSubmissions() {
    $conn = connect();
    $query = "SELECT * FROM dbSpringBreakCampForm INNER JOIN dbChildren ON dbSpringBreakCampForm.child_id = dbChildren.id;";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $submissions;
    }
    return [];
}

function getSpringBreakCampSubmissionsFromFamily($familyId) {
    require_once("dbChildren.php");
    $children = retrieve_children_by_family_id($familyId);
    if (!$children){
        return [];
    }
    $childrenIds = array_map(function($child) {
        return $child->getId();
    }, $children);
    $joinedIds = join(",",$childrenIds);

    $conn = connect();
    $query = "SELECT * FROM dbSpringBreakCampForm INNER JOIN dbChildren ON dbSpringBreakCampForm.child_id = dbChildren.id WHERE dbSpringBreakCampForm.child_id IN ($joinedIds);";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $submissions;
    }
    return [];
}

function getSpringBreakById($id) {
    $conn = connect(); // Ensure `connect()` establishes the database connection.

    $query = "SELECT * FROM dbSpringBreakCampForm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $formData = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $formData;
}

function updateSpringBreakCampForm($submissionId, $updatedData) {
    $conn = connect();
    
    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $updatedData["email"]);
    $student_name = mysqli_real_escape_string($conn, $updatedData["student_name"]);
    $school_choice = mysqli_real_escape_string($conn, $updatedData["school_choice"]);
    $isAttending = isset($updatedData["isAttending"]) ? 1 : 0;
    $waiver_completed = isset($updatedData["waiver_completed"]) ? 1 : 0;
    $notes = !empty($updatedData["notes"]) ? mysqli_real_escape_string($conn, $updatedData["notes"]) : null;

    // Update query
    $query = "
        UPDATE dbSpringBreakCampForm 
        SET email = ?, student_name = ?, school_choice = ?, isAttending = ?, waiver_completed = ?, notes = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssissi", $email, $student_name, $school_choice, $isAttending, $waiver_completed, $notes, $submissionId);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $success;
}
?>