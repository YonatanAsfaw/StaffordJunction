<?php

include_once("dbinfo.php");

//Function that inserts data into dbBrainBuilderHolidayPartyForm
function insert_into_dbHolidayPartyForm($args, $child_id){
    $conn = connect();
    $email = $args['email'];
    $nameOfChild = explode(" ", $args['name']);
    //$fn = $args['child_first_name'];
    //$ln = $args['child_last_name'];
    $fn = $nameOfChild[0];
    $ln = $nameOfChild[1];
    $isAttending = $args['isAttending'];
    $transportation = $args['transportation'];
    $neighborhood = $args['neighborhood'];
    $comments = $args['question_comments'];
    $family_id = $args['family_id']; // Add family_id from $args

    //Find if child exists in dbHolidayPartForm first
    $query = "SELECT * FROM dbBrainBuildersHolidayPartyForm where child_first_name = '$fn' AND child_last_name = '$ln' AND child_id = '$child_id';";
    $result = mysqli_query($conn, $query);

    //If child doesn't exist
    if(mysqli_num_rows($result) == 0 || $result == null){
        mysqli_query($conn, "INSERT INTO dbBrainBuildersHolidayPartyForm (child_id, family_id, email, child_first_name, child_last_name,
        transportation, neighborhood, comments, isAttending) VALUES ('$child_id', '$family_id', '$email', '$fn', '$ln', '$transportation', '$neighborhood', '$comments', '$isAttending');");
        mysqli_close($conn);

        return true;
    } else if (mysqli_num_rows($result) > 0) {
        // Child already exists, do not insert
        echo '<div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Error: ' . $fn . ' ' . $ln . ' is already registered.</div>';
        mysqli_close($conn);
        return false;
    }
}

//Function that checks to see if the form was completed for a specific child or not
function isHolidayPartyFormComplete($childId){
    $conn = connect();
    $query = "SELECT * FROM dbBrainBuildersHolidayPartyForm where child_id = $childId";
    $res = mysqli_query($conn, $query);

    $complete = $res && mysqli_num_rows($res) > 0;
    mysqli_close($conn);
    return $complete;
}

function getHolidayPartySubmissions() {
    $conn = connect();
    $query = "SELECT * FROM dbBrainBuildersHolidayPartyForm;";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $submissions;
    }
    return [];
}

function getHolidayPartySubmissionsFromFamily($familyId) {
    $conn = connect();
    $query = "SELECT * FROM dbBrainBuildersHolidayPartyForm WHERE child_id IN (SELECT id FROM dbChildren WHERE family_id = $familyId)";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        $submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $submissions;
    }
    return [];
}

function getHolidayPartyById($id) {
    $conn = connect(); // Make sure `connect()` connects to your DB properly.

    $query = "SELECT * FROM dbBrainBuildersHolidayPartyForm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $formData = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $formData;
}

function updateHolidayPartyForm($submissionId, $updatedData) {
    $conn = connect();
    if (!$conn) {
        die("ERROR: Database connection is NULL in updateHolidayPartyForm.");
    }

    $query = "
        UPDATE dbBrainBuildersHolidayPartyForm SET
            child_first_name = ?,
            child_last_name = ?,
            email = ?,
            transportation = ?,
            neighborhood = ?,
            comments = ?,
            isAttending = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Database prepare() failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssii",
        $updatedData["child_first_name"],
        $updatedData["child_last_name"],
        $updatedData["email"],
        $updatedData["transportation"],
        $updatedData["neighborhood"],
        $updatedData["comments"],
        $updatedData["isAttending"],
        $submissionId
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}