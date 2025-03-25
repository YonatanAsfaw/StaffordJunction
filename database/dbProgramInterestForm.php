<?php
require_once("dbinfo.php");
require_once("dbFamily.php");

// Session and access control for deletion endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (session_status() === PHP_SESSION_NONE) {
        session_cache_expire(30);
        session_start();
    }
    if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 1) {
        header('Location: login.php');
        exit();
    }
    $family_id = ($_SESSION['access_level'] == 1) ? $_SESSION['_id'] : (int) $_GET['id'];
    if (deleteProgramInterestForm($family_id)) {
        $redirect = "fillForm.php?status=deleted" . (isset($_GET['id']) ? "&id=" . $family_id : "");
        header("Location: $redirect");
    } else {
        $redirect = "programInterestForm.php?status=errordelete" . (isset($_GET['id']) ? "&id=" . $family_id : "");
        header("Location: $redirect");
    }
    exit();
}

function deleteProgramInterestForm($family_id) {
    $connection = connect();
    mysqli_begin_transaction($connection);

    try {
        $formQuery = "SELECT id FROM dbProgramInterestForm WHERE family_id = ?";
        $stmt = mysqli_prepare($connection, $formQuery);
        mysqli_stmt_bind_param($stmt, "i", $family_id);
        mysqli_stmt_execute($stmt);
        $formResult = mysqli_stmt_get_result($stmt);
        if (!$formResult || mysqli_num_rows($formResult) <= 0) {
            throw new Exception("Form not found for family ID: $family_id");
        }
        $formRow = mysqli_fetch_assoc($formResult);
        $form_id = $formRow['id'];
        mysqli_stmt_close($stmt);

        $deleteProgramInterests = "DELETE FROM dbProgramInterestsForm_ProgramInterests WHERE form_id = ?";
        $stmt = mysqli_prepare($connection, $deleteProgramInterests);
        mysqli_stmt_bind_param($stmt, "i", $form_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $deleteTopicInterests = "DELETE FROM dbProgramInterestsForm_TopicInterests WHERE form_id = ?";
        $stmt = mysqli_prepare($connection, $deleteTopicInterests);
        mysqli_stmt_bind_param($stmt, "i", $form_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $deleteAvailability = "DELETE FROM dbAvailability WHERE form_id = ?";
        $stmt = mysqli_prepare($connection, $deleteAvailability);
        mysqli_stmt_bind_param($stmt, "i", $form_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $deleteForm = "DELETE FROM dbProgramInterestForm WHERE id = ?";
        $stmt = mysqli_prepare($connection, $deleteForm);
        mysqli_stmt_bind_param($stmt, "i", $form_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($connection);
        error_log("Form deleted for family_id: $family_id");
        return true;
    } catch (Exception $e) {
        error_log("Delete failed: " . $e->getMessage());
        mysqli_rollback($connection);
        return false;
    } finally {
        mysqli_close($connection);
    }
}

function createProgramInterestForm($form, $family_id) {
    $connection = connect();
    mysqli_begin_transaction($connection);

    $first_name = $form["first_name"] ?? '';
    $last_name = $form["last_name"] ?? '';
    $address = $form["address"] ?? '';
    $city = $form["city"] ?? '';
    $neighborhood = $form["neighborhood"] ?? '';
    $state = $form["state"] ?? '';
    $zip = $form["zip"] ?? '';
    $cell_phone = $form["cell_phone"] ?? '';
    $home_phone = $form["home_phone"] ?? '';
    $email = $form["email"] ?? '';
    $child_num = $form["child_num"] ?? 0;
    $child_ages = $form["child_ages"] ?? '';
    $adult_num = $form["adult_num"] ?? 0;

    try {
        $query = "
            INSERT INTO dbProgramInterestForm (family_id, first_name, last_name, address, city, neighborhood, state, zip, cell_phone, home_phone, email, child_num, child_ages, adult_num)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "issssssssssiss", $family_id, $first_name, $last_name, $address, $city, $neighborhood, $state, $zip, $cell_phone, $home_phone, $email, $child_num, $child_ages, $adult_num);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to insert form data: " . mysqli_error($connection));
        }
        $id = mysqli_insert_id($connection);
        mysqli_stmt_close($stmt);

        if (isset($form['programs']) && !empty($form['programs'])) {
            insertProgramInterests($form['programs'], $id, $connection);
        }
        if (isset($form['topics']) && !empty($form['topics'])) {
            insertTopicInterests($form['topics'], $id, $connection);
        }
        if (isset($form['days']) && !empty($form['days'])) {
            insertAvailability($form['days'], $id, $connection);
        }

        mysqli_commit($connection);
        error_log("Form inserted with ID: $id for family_id: $family_id");
        return $id;
    } catch (Exception $e) {
        error_log("Insert failed: " . $e->getMessage());
        mysqli_rollback($connection);
        return false;
    } finally {
        mysqli_close($connection);
    }
}

function insertProgramInterests($programs, $form_id, $connection) {
    $query = "INSERT INTO dbProgramInterestsForm_ProgramInterests (form_id, interest_id) VALUES ";
    $placeholders = array_fill(0, count($programs), "($form_id, (SELECT id FROM dbProgramInterests WHERE interest = ?))");
    $query .= implode(", ", $placeholders);
    $stmt = mysqli_prepare($connection, $query);
    
    // Create reference variables for binding
    $refs = [];
    foreach ($programs as $key => $program) {
        $refs[$key] = &$programs[$key]; // Use reference to original array element
    }
    $params = array_merge([$stmt, str_repeat('s', count($programs))], $refs);
    call_user_func_array('mysqli_stmt_bind_param', $params);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to insert program interests: " . mysqli_error($connection));
    }
    mysqli_stmt_close($stmt);
}

function insertTopicInterests($topics, $form_id, $connection) {
    foreach ($topics as $topic) {
        $result = mysqli_query($connection, "SELECT * FROM dbTopicInterests WHERE interest = '" . mysqli_real_escape_string($connection, $topic) . "'");
        if (mysqli_num_rows($result) <= 0) {
            $insertTopicQuery = "INSERT INTO dbTopicInterests (interest) VALUES (?)";
            $stmt = mysqli_prepare($connection, $insertTopicQuery);
            mysqli_stmt_bind_param($stmt, "s", $topic);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    $query = "INSERT INTO dbProgramInterestsForm_TopicInterests (form_id, interest_id) VALUES ";
    $placeholders = array_fill(0, count($topics), "($form_id, (SELECT id FROM dbTopicInterests WHERE interest = ?))");
    $query .= implode(", ", $placeholders);
    $stmt = mysqli_prepare($connection, $query);
    
    // Create reference variables for binding
    $refs = [];
    foreach ($topics as $key => $topic) {
        $refs[$key] = &$topics[$key]; // Use reference to original array element
    }
    $params = array_merge([$stmt, str_repeat('s', count($topics))], $refs);
    call_user_func_array('mysqli_stmt_bind_param', $params);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to insert topic interests: " . mysqli_error($connection));
    }
    mysqli_stmt_close($stmt);
}

function insertAvailability($days, $form_id, $connection) {
    $query = "INSERT INTO dbAvailability (form_id, day, morning, afternoon, evening, specific_time) VALUES ";
    $values = [];
    $types = "";
    $params = [];
    foreach ($days as $key => $val) {
        $day_name = $key;
        $morning = (int) ($val["morning"] ?? 0);
        $afternoon = (int) ($val["afternoon"] ?? 0);
        $evening = (int) ($val["evening"] ?? 0);
        $specific_time = $val["specific_time"] ?? "";
        $values[] = "(?, ?, ?, ?, ?, ?)";
        $types .= "isiiis";
        $params[] = $form_id;
        $params[] = $day_name;
        $params[] = $morning;
        $params[] = $afternoon;
        $params[] = $evening;
        $params[] = $specific_time;
    }
    $query .= implode(", ", $values);
    $stmt = mysqli_prepare($connection, $query);
    
    // Create reference variables for binding
    $refs = [];
    foreach ($params as $key => $value) {
        $refs[$key] = &$params[$key]; // Use reference to original array element
    }
    $bind_params = array_merge([$stmt, $types], $refs);
    call_user_func_array('mysqli_stmt_bind_param', $bind_params);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to insert availability: " . mysqli_error($connection));
    }
    mysqli_stmt_close($stmt);
}

function getProgramInterestFormData($family_id) {
    $conn = connect();
    $query = "SELECT dbProgramInterestForm.* FROM dbFamily INNER JOIN dbProgramInterestForm ON dbFamily.id = dbProgramInterestForm.family_id WHERE dbFamily.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    mysqli_close($conn);
    return (mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
}
function getProgramInterestFormById($id) {
    $conn = connect(); // Ensure `connect()` establishes the database connection.

    $query = "SELECT * FROM dbProgramInterestForm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $formData = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $formData;
}
function updateProgramInterestForm($submissionId, $updatedData) {
    $connection = connect(); // Assuming connect() is defined in dbinfo.php

    if (!$connection) {
        die("ERROR: Database connection is NULL in updateProgramInterestForm.");
    }

    error_log("updateProgramInterestForm called for ID: " . $submissionId . " with data: " . json_encode($updatedData));

    // Begin transaction to ensure atomic updates
    mysqli_begin_transaction($connection);

    try {
        // Update main form data in dbProgramInterestForm
        $query = "
            UPDATE dbProgramInterestForm SET
                family_id = ?,
                first_name = ?,
                last_name = ?,
                address = ?,
                city = ?,
                neighborhood = ?,
                state = ?,
                zip = ?,
                cell_phone = ?,
                home_phone = ?,
                email = ?,
                child_num = ?,
                child_ages = ?,
                adult_num = ?
            WHERE id = ?
        ";

        $stmt = mysqli_prepare($connection, $query);
        if (!$stmt) {
            throw new Exception("Database prepare() failed: " . mysqli_error($connection));
        }

        // Ensure NULL values are properly handled
        $family_id = isset($updatedData['family_id']) ? (int)$updatedData['family_id'] : null;
        $child_num = isset($updatedData['child_num']) ? (int)$updatedData['child_num'] : null;
        $adult_num = isset($updatedData['adult_num']) ? (int)$updatedData['adult_num'] : null;

        // Bind parameters for main form update
        mysqli_stmt_bind_param(
            $stmt,
            "issssssssssissi",
            $family_id,
            $updatedData['first_name'] ?? '',
            $updatedData['last_name'] ?? '',
            $updatedData['address'] ?? '',
            $updatedData['city'] ?? '',
            $updatedData['neighborhood'] ?? '',
            $updatedData['state'] ?? '',
            $updatedData['zip'] ?? '',
            $updatedData['cell_phone'] ?? '',
            $updatedData['home_phone'] ?? '',
            $updatedData['email'] ?? '',
            $child_num,
            $updatedData['child_ages'] ?? '',
            $adult_num,
            $submissionId
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed for main form update: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);

        // Update program interests (delete existing and re-insert)
        if (isset($updatedData['programs']) && !empty($updatedData['programs'])) {
            $deleteQuery = "DELETE FROM dbProgramInterestsForm_ProgramInterests WHERE form_id = ?";
            $stmt = mysqli_prepare($connection, $deleteQuery);
            mysqli_stmt_bind_param($stmt, "i", $submissionId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            insertProgramInterests($updatedData['programs'], $submissionId, $connection);
        }

        // Update topic interests (delete existing and re-insert)
        if (isset($updatedData['topics']) && !empty($updatedData['topics'])) {
            $deleteQuery = "DELETE FROM dbProgramInterestsForm_TopicInterests WHERE form_id = ?";
            $stmt = mysqli_prepare($connection, $deleteQuery);
            mysqli_stmt_bind_param($stmt, "i", $submissionId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            insertTopicInterests($updatedData['topics'], $submissionId, $connection);
        }

        // Update availability (delete existing and re-insert)
        if (isset($updatedData['days']) && !empty($updatedData['days'])) {
            $deleteQuery = "DELETE FROM dbAvailability WHERE form_id = ?";
            $stmt = mysqli_prepare($connection, $deleteQuery);
            mysqli_stmt_bind_param($stmt, "i", $submissionId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            insertAvailability($updatedData['days'], $submissionId, $connection);
        }

        // Commit transaction
        mysqli_commit($connection);
        error_log("Program Interest Form updated successfully for ID: $submissionId");
        return true;
    } catch (Exception $e) {
        error_log("Update failed: " . $e->getMessage());
        mysqli_rollback($connection);
        return false;
    } finally {
        mysqli_close($connection);
    }
}
function getProgramInterestData($family_id) {
    $conn = connect();
    $query = "SELECT dbProgramInterests.interest FROM dbProgramInterests INNER JOIN dbProgramInterestsForm_ProgramInterests ON 
        dbProgramInterests.id = dbProgramInterestsForm_ProgramInterests.interest_id INNER JOIN dbProgramInterestForm ON 
        dbProgramInterestsForm_ProgramInterests.form_id = dbProgramInterestForm.id WHERE dbProgramInterestForm.family_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $programs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row['interest'];
    }
    mysqli_close($conn);
    return $programs;
}

function getTopicInterestData($family_id) {
    $conn = connect();
    $query = "SELECT dbTopicInterests.interest FROM dbTopicInterests INNER JOIN dbProgramInterestsForm_TopicInterests ON 
        dbTopicInterests.id = dbProgramInterestsForm_TopicInterests.interest_id INNER JOIN dbProgramInterestForm ON 
        dbProgramInterestsForm_TopicInterests.form_id = dbProgramInterestForm.id WHERE dbProgramInterestForm.family_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $topics = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $topics[] = $row['interest'];
    }
    mysqli_close($conn);
    return $topics;
}

function getOtherTopicInterestData($topics) {
    $other_topics = [];
    $ignore_topics = ["Legal Services", "Finance", "Tenant Rights", "Health/Wellness/Nutrition", "Continuing Education", "Parenting", "Mental Health",
        "Job/Career Guidance", "Citizenship Classes"];
    foreach ($topics as $topic) {
        if (!in_array($topic, $ignore_topics)) {
            $other_topics[] = $topic;
        }
    }
    return $other_topics;
}

function getAvailabilityData($family_id) {
    $conn = connect();
    $query = "SELECT dbAvailability.* FROM dbAvailability INNER JOIN dbProgramInterestForm ON 
        dbAvailability.form_id = dbProgramInterestForm.id WHERE dbProgramInterestForm.family_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $availabilities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $availabilities[$row['day']] = [
            "morning" => $row["morning"],
            "afternoon" => $row["afternoon"],
            "evening" => $row["evening"],
            "specific_time" => $row["specific_time"],
        ];
    }
    mysqli_close($conn);
    return $availabilities;
}

function showProgramInterestData($data, $value) {
    $value = $value ?? '';
    $data = $data ?? '';
    if ($data === '') {
        echo "value=\"" . htmlspecialchars($value) . "\"";
    } else {
        echo "disabled style='background-color: yellow; color: black;' value=\"" . htmlspecialchars($data) . "\"";
    }
}

function showProgramInterestCheckbox($data, $value) {
    if ($data != null) {
        if (in_array($value, $data)) {
            echo "style='pointer-events: none;' checked";
        } else {
            echo "style='pointer-events: none;'";
        }
    }
}

function showAvailabilityCheckbox($data) {
    if ($data == 1) {
        echo "style='pointer-events: none;' checked";
    } else if ($data != null) {
        echo "disabled";
    }
}

function getProgramInterestSubmissions() {
    $conn = connect();
    $query = "SELECT 
        dbProgramInterestForm.*, 
        GROUP_CONCAT(DISTINCT dbProgramInterests.interest) as program_interests,
        GROUP_CONCAT(DISTINCT dbTopicInterests.interest) as topic_interests
    FROM dbProgramInterestForm
    LEFT JOIN dbProgramInterestsForm_ProgramInterests ON dbProgramInterestForm.id = dbProgramInterestsForm_ProgramInterests.form_id
    LEFT JOIN dbProgramInterests ON dbProgramInterestsForm_ProgramInterests.interest_id = dbProgramInterests.id
    LEFT JOIN dbProgramInterestsForm_TopicInterests ON dbProgramInterestForm.id = dbProgramInterestsForm_TopicInterests.form_id
    LEFT JOIN dbTopicInterests ON dbProgramInterestsForm_TopicInterests.interest_id = dbTopicInterests.id";
    $result = mysqli_query($conn, $query);
    $submissions = (mysqli_num_rows($result) > 0) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_close($conn);
    return $submissions;
}

function getProgramInterestSubmissionsFromFamily($familyId) {
    $conn = connect();
    $query = "SELECT 
        dbProgramInterestForm.*, 
        GROUP_CONCAT(DISTINCT dbProgramInterests.interest) as program_interests,
        GROUP_CONCAT(DISTINCT dbTopicInterests.interest) as topic_interests
    FROM dbProgramInterestForm
    LEFT JOIN dbProgramInterestsForm_ProgramInterests ON dbProgramInterestForm.id = dbProgramInterestsForm_ProgramInterests.form_id
    LEFT JOIN dbProgramInterests ON dbProgramInterestsForm_ProgramInterests.interest_id = dbProgramInterests.id
    LEFT JOIN dbProgramInterestsForm_TopicInterests ON dbProgramInterestForm.id = dbProgramInterestsForm_TopicInterests.form_id
    LEFT JOIN dbTopicInterests ON dbProgramInterestsForm_TopicInterests.interest_id = dbTopicInterests.id
    WHERE dbProgramInterestForm.family_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $familyId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $submissions = (mysqli_num_rows($result) > 0) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_close($conn);
    return $submissions;
}
?>