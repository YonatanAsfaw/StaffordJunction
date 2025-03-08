<?php
/**
 * Function to log volunteer hours into dbVolunteerReport table.
 */
function logVolunteerHours($volunteerID, $form) {
    $connection = connect();
    
    $activityID = (int) $form["activity_id"];
    $date = $form["date"];
    $hours = (float) $form["hours"];
    $description = mysqli_real_escape_string($connection, $form["description"]);

    mysqli_begin_transaction($connection);
    try {
        // Insert volunteer hours log with activity_id reference
        $query = "
            INSERT INTO dbVolunteerReport (volunteer_id, activity_id, date, hours, description)
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "iisis", $volunteerID, $activityID, $date, $hours, $description);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            throw new Exception("Error inserting log: " . mysqli_error($connection));
        }

        $logID = mysqli_insert_id($connection);
        mysqli_commit($connection);
    } catch (Exception $e) {
        error_log($e->getMessage());
        mysqli_rollback($connection);
        mysqli_close($connection);
        return null;
    }

    mysqli_close($connection);
    return $logID;
}

/**
 * Function to retrieve available activities from dbActualActivityForm.
 */
function getAvailableActivities() {
    $connection = connect();
    $query = "SELECT id, activity FROM dbActualActivityForm ORDER BY activity ASC";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        $activities = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($connection);
        return $activities;
    }

    mysqli_close($connection);
    return [];
}
?>
