<?php
/**
 * Function that takes actual activity form and adds data into correct data tables
 */
function createActualActivityForm($form) {
	$connection = connect();
    
    $activity = mysqli_real_escape_string($connection, $form["activity"]);
    $date = $form["date"];
    $program = mysqli_real_escape_string($connection, $form["program"]);
	$start_time = $form["start_time"];
    $end_time = $form["end_time"];
    $start_mile = $form["start_mile"];
    $end_mile = $form["end_mile"];
    $address = mysqli_real_escape_string($connection, $form["address"]);
	$attend_num = $form["attend_num"];
    $volstaff_num = $form["volstaff_num"];
    $materials_used = mysqli_real_escape_string($connection, $form["materials_used"]);
	$meal_info = $form["meal_info"];
    $act_costs = mysqli_real_escape_string($connection, $form["act_costs"]);
    $act_benefits = mysqli_real_escape_string($connection, $form["act_benefits"]);
    $attendance = $form["attendance"];
    
    mysqli_begin_transaction($connection);
    $activityID = null;
    try {
        //insert information into database for Actual Activity Form
        $query = "
            INSERT INTO dbActualActivityForm (activity, date, program, start_time, end_time, start_mile, end_mile, address, 
            attend_num, volstaff_num, materials_used, meal_info, act_costs, act_benefits)
            VALUES ('$activity', '$date', '$program', '$start_time', '$end_time', '$start_mile', '$end_mile', '$address', 
            '$attend_num', '$volstaff_num', '$materials_used', '$meal_info', '$act_costs', '$act_benefits')
        ";
        
        $result = mysqli_query($connection, $query);
        if (!$result) {
            throw new Exception("Error in query: " . mysqli_error($connection));
        }
        
        $activityID = mysqli_insert_id($connection);
        
        //insert attendance into database for Actual Activity Attendees
        if (isset($attendance)) {
            $attendeeIDs = createActualActivityAttendees($attendance, $connection);
            if (empty($attendeeIDs)) {
                throw new Exception("Error in actual activity attendees table insert.");
            }
        } else {
            throw new Exception("No attendance variable transfered from form.");
        }

        //insert into junction table
        $activityAttendeeID = createActivityAttendees($activityID, $attendeeIDs, $connection);
        if (empty($activityAttendeeID)) {
            throw new Exception("Error in junction table insert.");
        }

        mysqli_commit($connection);
    } catch (Exception $e) {
        echo $e->getMessage();
        mysqli_rollback($connection);
        mysqli_close($connection);
        return null;
    }

    mysqli_close($connection);
    return $activityID;
}

/**
 * Function that takes attendees array and adds each attendee into database table
 */
function createActualActivityAttendees($attendees, $connection) {
    mysqli_begin_transaction($connection);
    $ids = [];

    foreach ($attendees as $attendee) {
        $name = trim($attendee);
        $name = mysqli_real_escape_string($connection, $name);
        if ($name != '') {
            $insert_query = "
                INSERT INTO dbActualActivityAttendees (name) VALUES ('$name')
            ";
            $insert_result = mysqli_query($connection, $insert_query);
            if (!$insert_result) {
                mysqli_rollback($connection);
                return null;
            }
            $ids[] = mysqli_insert_id($connection);
        }
    }

    mysqli_commit($connection);
    return $ids;
}

/**
 * Function that takes actual activity form ID and attendees IDs array and adds it into junction table
 */
function createActivityAttendees($activityID, $attendeeIDs, $connection) {
    mysqli_begin_transaction($connection);

    foreach ($attendeeIDs as $attendeeID) {
        $insert_query = "
            INSERT INTO dbActivityAttendees (activityID, attendeeID)
            VALUES ('$activityID', '$attendeeID')
        ";
        $insert_result = mysqli_query($connection, $insert_query);
        if (!$insert_result) {
            mysqli_rollback($connection);
            return null;
        }
    }

    mysqli_commit($connection);
    return true;  // Return success as true
}

function getActualActivitySubmissions() {
    $conn = connect();
    $query = "SELECT * FROM dbActualActivityForm;";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $submissions;
    }
    return [];
}

function getActualActivitySubmissionsFromFamily($familyId) {
    $conn = connect();
    $query = "SELECT * FROM dbActualActivityForm WHERE child_id IN (SELECT id FROM dbChildren WHERE family_id = $familyId)";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        $submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $submissions;
    }
    return [];
}

function get_attendance_trends() {
    $connection = connect();
    
    $query = "
        SELECT id AS activity_id, date, activity, SUM(attend_num) AS total_attendance
        FROM dbActualActivityForm
        GROUP BY id, date, activity
        ORDER BY date DESC;
    ";
    
    $result = mysqli_query($connection, $query);
    
    $attendance_data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $attendance_data[] = $row;
    }
    
    mysqli_close($connection);
    
    return $attendance_data;
}

function get_attendance_statistics() {
    $connection = connect();

    $query = "
        SELECT 
            COUNT(id) AS total_events,
            SUM(attend_num) AS total_attendance,
            AVG(attend_num) AS avg_attendance,
            MAX(attend_num) AS max_attendance,
            (SELECT activity FROM dbActualActivityForm ORDER BY attend_num DESC LIMIT 1) AS most_attended_event
        FROM dbActualActivityForm;
    ";
    
    $result = mysqli_query($connection, $query);
    $stats = mysqli_fetch_assoc($result);
    
    mysqli_close($connection);
    
    return $stats; // Returns an associative array with statistics
}

/**
 * Function to get total volunteer hours per event.
 */
function get_volunteer_details_per_event() {
    $connection = connect();
    
    $query = "
        SELECT 
            a.id AS activity_id, 
            a.activity, 
            v.volunteer_id, 
            u.firstName AS volunteer_name, 
            v.hours
        FROM dbActualActivityForm a
        LEFT JOIN dbVolunteerReport v ON a.id = v.activity_id
        LEFT JOIN dbVolunteers u ON v.volunteer_id = u.id
        ORDER BY a.date DESC, a.activity;
    ";

    $result = mysqli_query($connection, $query);

    if (!$result) {
        error_log("Error fetching volunteer details: " . mysqli_error($connection));
        mysqli_close($connection);
        return [];
    }

    $volunteerData = [];
    $totalHoursPerEvent = []; // Track total hours per event

    while ($row = mysqli_fetch_assoc($result)) {
        $activityID = $row['activity_id'];
        
        // Store individual volunteer hours
        $volunteerData[$activityID][] = [
            'volunteer_name' => $row['volunteer_name'] ?? 'Unknown',
            'hours' => $row['hours'] ?? 0.00
        ];

        // Sum total hours per event
        if (!isset($totalHoursPerEvent[$activityID])) {
            $totalHoursPerEvent[$activityID] = 0;
        }
        $totalHoursPerEvent[$activityID] += (float) $row['hours'];
    }

    mysqli_close($connection);

    return ['volunteers' => $volunteerData, 'totals' => $totalHoursPerEvent];
}

function getActualActivityById($id) {
    $conn = connect(); // Make sure `connect()` connects to your DB properly.

    $query = "SELECT * FROM dbActualActivityForm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $formData = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $formData;
}

function updateActualActivityForm($submissionId, $updatedData) {
    $conn = connect();
    if (!$conn) {
        die("ERROR: Database connection is NULL in updateActualActivityForm.");
    }

    $query = "
        UPDATE dbActualActivityForm SET
            activity = ?,
            date = ?,
            program = ?,
            start_time = ?,
            end_time = ?,
            start_mile = ?,
            end_mile = ?,
            address = ?,
            attend_num = ?,
            volstaff_num = ?,
            materials_used = ?,
            meal_info = ?,
            act_costs = ?,
            act_benefits = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Database prepare() failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssdissssssi",
        $updatedData["activity"],
        $updatedData["date"],
        $updatedData["program"],
        $updatedData["start_time"],
        $updatedData["end_time"],
        $updatedData["start_mile"],
        $updatedData["end_mile"],
        $updatedData["address"],
        $updatedData["attend_num"],
        $updatedData["volstaff_num"],
        $updatedData["materials_used"],
        $updatedData["meal_info"],
        $updatedData["act_costs"],
        $updatedData["act_benefits"],
        $submissionId
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
