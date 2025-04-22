<?php
include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Volunteer.php');

function make_volunteer_from_signup($result_row) {
    $volunteer = new Volunteer(
        $result_row['id'],
        $result_row['email'],
        $result_row['password'],
        $result_row['securityQuestion'],
        $result_row['securityAnswer'],
        $result_row['firstName'],
        $result_row['middleInitial'],
        $result_row['lastName'],
        $result_row['address'],
        $result_row['city'],
        $result_row['state'],
        $result_row['zip'],
        $result_row['homePhone'],
        $result_row['cellPhone'],
        $result_row['age'],
        $result_row['birthDate'],
        $result_row['hasDriversLicense'],
        $result_row['transportation'],
        $result_row['emergencyContact1Name'],
        $result_row['emergencyContact1Relation'],
        $result_row['emergencyContact1Phone'],
        $result_row['emergencyContact2Name'],
        $result_row['emergencyContact2Relation'],
        $result_row['emergencyContact2Phone'],
        $result_row['allergies'],
        $result_row['sunStart'],
        $result_row['sunEnd'],
        $result_row['monStart'],
        $result_row['monEnd'],
        $result_row['tueStart'],
        $result_row['tueEnd'],
        $result_row['wedStart'],
        $result_row['wedEnd'],
        $result_row['thurStart'],
        $result_row['thurEnd'],
        $result_row['friStart'],
        $result_row['friEnd'],
        $result_row['satStart'],
        $result_row['satEnd'],
        $result_row['dateAvailable'],
        $result_row['minHours'],
        $result_row['maxHours'],
        $result_row['access_level'],
    );
    return $volunteer;
}

function create_volunteer_from_db($result_row) {
    return new Volunteer(
        $result_row['id'],
        $result_row['email'],
        $result_row['password'],
        $result_row['securityQuestion'],
        $result_row['securityAnswer'],
        $result_row['firstName'],
        $result_row['middleInitial'],
        $result_row['lastName'],
        $result_row['address'],
        $result_row['city'],
        $result_row['state'],
        $result_row['zip'],
        $result_row['homePhone'],
        $result_row['cellPhone'],
        $result_row['age'],
        $result_row['birthDate'],
        $result_row['hasDriversLicense'],
        $result_row['transportation'],
        $result_row['emergencyContact1Name'],
        $result_row['emergencyContact1Relation'],
        $result_row['emergencyContact1Phone'],
        $result_row['emergencyContact2Name'],
        $result_row['emergencyContact2Relation'],
        $result_row['emergencyContact2Phone'],
        $result_row['allergies'],
        $result_row['sunStart'],
        $result_row['sunEnd'],
        $result_row['monStart'],
        $result_row['monEnd'],
        $result_row['tueStart'],
        $result_row['tueEnd'],
        $result_row['wedStart'],
        $result_row['wedEnd'],
        $result_row['thurStart'],
        $result_row['thurEnd'],
        $result_row['friStart'],
        $result_row['friEnd'],
        $result_row['satStart'],
        $result_row['satEnd'],
        $result_row['dateAvailable'],
        $result_row['minHours'],
        $result_row['maxHours'],
        $result_row['access_level'],
    );
}

function add_volunteer($volunteer) {
    $conn = connect();
    $query = "SELECT * FROM dbVolunteers WHERE email = ?";
    $stmt = $conn->prepare($query);
    $email = $volunteer->getEmail();
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $query = "INSERT INTO dbVolunteers (
            email, password, securityQuestion, securityAnswer, firstName, middleInitial, lastName, 
            address, city, state, zip, homePhone, cellPhone, age, birthDate, hasDriversLicense, 
            transportation, emergencyContact1Name, emergencyContact1Relation, emergencyContact1Phone, 
            emergencyContact2Name, emergencyContact2Relation, emergencyContact2Phone, allergies, 
            sunStart, sunEnd, monStart, monEnd, tueStart, tueEnd, wedStart, wedEnd, thurStart, 
            thurEnd, friStart, friEnd, satStart, satEnd, dateAvailable, minHours, maxHours, access_level
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $email = $volunteer->getEmail();
        $password = $volunteer->getPassword();
        $securityQuestion = $volunteer->getSecurityQuestion();
        $securityAnswer = $volunteer->getSecurityAnswer();
        $firstName = $volunteer->getFirstName();
        $middleInitial = $volunteer->getMiddleInitial();
        $lastName = $volunteer->getLastName();
        $address = $volunteer->getAddress();
        $city = $volunteer->getCity();
        $state = $volunteer->getState();
        $zip = $volunteer->getZip();
        $homePhone = $volunteer->getHomePhone();
        $cellPhone = $volunteer->getCellPhone();
        $age = $volunteer->getAge();
        $birthDate = $volunteer->getBirthDate();
        $hasDriversLicense = $volunteer->getHasDriversLicense();
        $transportation = $volunteer->getTransportation();
        $emergencyContact1Name = $volunteer->getEmergencyContact1Name();
        $emergencyContact1Relation = $volunteer->getEmergencyContact1Relation();
        $emergencyContact1Phone = $volunteer->getEmergencyContact1Phone();
        $emergencyContact2Name = $volunteer->getEmergencyContact2Name();
        $emergencyContact2Relation = $volunteer->getEmergencyContact2Relation();
        $emergencyContact2Phone = $volunteer->getEmergencyContact2Phone();
        $allergies = $volunteer->getAllergies();
        $sunStart = $volunteer->getSunStart();
        $sunEnd = $volunteer->getSunEnd();
        $monStart = $volunteer->getMonStart();
        $monEnd = $volunteer->getMonEnd();
        $tueStart = $volunteer->getTueStart();
        $tueEnd = $volunteer->getTueEnd();
        $wedStart = $volunteer->getWedStart();
        $wedEnd = $volunteer->getWedEnd();
        $thurStart = $volunteer->getThuStart();
        $thurEnd = $volunteer->getThuEnd();
        $friStart = $volunteer->getFriStart();
        $friEnd = $volunteer->getFriEnd();
        $satStart = $volunteer->getSatStart();
        $satEnd = $volunteer->getSatEnd();
        $dateAvailable = $volunteer->getDateAvailable();
        $minHours = $volunteer->getMinHours();
        $maxHours = $volunteer->getMaxHours();
        $accessLevel = $volunteer->getAccessLevel();

        $stmt->bind_param(
            "sssssssssssisisssssssssssssssssssssssiiiii",
            $email, $password, $securityQuestion, $securityAnswer, $firstName, $middleInitial, $lastName,
            $address, $city, $state, $zip, $homePhone, $cellPhone, $age, $birthDate, $hasDriversLicense,
            $transportation, $emergencyContact1Name, $emergencyContact1Relation, $emergencyContact1Phone,
            $emergencyContact2Name, $emergencyContact2Relation, $emergencyContact2Phone, $allergies,
            $sunStart, $sunEnd, $monStart, $monEnd, $tueStart, $tueEnd, $wedStart, $wedEnd, $thurStart,
            $thurEnd, $friStart, $friEnd, $satStart, $satEnd, $dateAvailable, $minHours, $maxHours, $accessLevel
        );
        $result = $stmt->execute();
        if (!$result) {
            die("Execute failed: " . $stmt->error);
        }
        $stmt->close();
        $conn->close();
        return true;
    }
    $stmt->close();
    $conn->close();
    return false;
}

function retrieve_volunteer_by_email($email) {
    $conn = connect();
    $query = "SELECT * FROM dbVolunteers WHERE email = '" . $email . "';";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) < 1 || $result == null) {
        mysqli_close($conn);
        return null;
    } else {
        $row = mysqli_fetch_assoc($result);
        $volunteer = create_volunteer_from_db($row);
        mysqli_close($conn);
        return $volunteer;
    }
}

function retrieve_volunteer_by_id($id) {
    $conn = connect();
    $query = "SELECT * FROM dbVolunteers WHERE id = '" . $id . "';";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) < 1 || $result == null) {
        mysqli_close($conn);
        return null;
    } else {
        $row = mysqli_fetch_assoc($result);
        $volunteer = create_volunteer_from_db($row);
        mysqli_close($conn);
        return $volunteer;
    }
}

function change_volunteer_password($id, $newPass) {
    $con = connect();
    $query = 'UPDATE dbVolunteers SET password = "' . $newPass . '" WHERE id = "' . $id . '"';
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return $result;
}

//function that removes a staff member from dbStaff by first and last name
function remove_volunteer_by_name($firstName, $lastName) {
    $conn = connect();

    //sanitize inputs
    $firstName = mysqli_real_escape_string($conn, $firstName);
    $lastName = mysqli_real_escape_string($conn, $lastName);

    //ensure the staff member exists before attempting to delete
    $query_check = "SELECT * FROM dbVolunteers WHERE firstName = '$firstName' AND lastName = '$lastName'";
    $res_check = mysqli_query($conn, $query_check);

    if (!$res_check || mysqli_num_rows($res_check) < 1) {
        mysqli_close($conn);
        return false;
    }

    $query_delete = "DELETE FROM dbVolunteers WHERE firstName = '$firstName' AND lastName = '$lastName'";
    $res_delete = mysqli_query($conn, $query_delete);

    mysqli_close($conn);
    return $res_delete; // Returns true if successful, false otherwise
}


//function that retrieves staff member from dbStaff by full name
function retrieve_volunteer_by_name($firstName, $lastName) {
    $conn = connect();

     $firstName = mysqli_real_escape_string($conn, $firstName);
     $lastName = mysqli_real_escape_string($conn, $lastName);

     $query = "SELECT * FROM dbVolunteers WHERE firstName = '$firstName' AND lastName = '$lastName';";
     $res = mysqli_query($conn, $query);

    if (mysqli_num_rows($res) < 1 || $res == null) {
        mysqli_close($conn);
        return null;
    } else {
        $row = mysqli_fetch_assoc($res);
        $staff = create_volunteer_from_db($row);
        mysqli_close($conn);
        return $staff;
    }
}

function update_volunteer($volunteer) {
    if (!$volunteer instanceof Volunteer) {
        die("Update volunteer mismatch");
    }
    $conn = connect();

    $query = "UPDATE dbVolunteers SET
        email = '" . $volunteer->getEmail() . "',
        password = '" . $volunteer->getPassword() . "',
        securityQuestion = '" . $volunteer->getSecurityQuestion() . "',
        securityAnswer = '" . $volunteer->getSecurityAnswer() . "',
        firstName = '" . $volunteer->getFirstName() . "',
        middleInitial = '" . $volunteer->getMiddleInitial() . "',
        lastName = '" . $volunteer->getLastName() . "',
        address = '" . $volunteer->getAddress() . "',
        city = '" . $volunteer->getCity() . "',
        state = '" . $volunteer->getState() . "',
        zip = '" . $volunteer->getZip() . "',
        homePhone = '" . $volunteer->getHomePhone() . "',
        cellPhone = '" . $volunteer->getCellPhone() . "',
        age = '" . $volunteer->getAge() . "',
        birthDate = '" . $volunteer->getBirthDate() . "',
        hasDriversLicense = '" . $volunteer->getHasDriversLicense() . "',
        transportation = '" . $volunteer->getTransportation() . "',
        emergencyContact1Name = '" . $volunteer->getEmergencyContact1Name() . "',
        emergencyContact1Relation = '" . $volunteer->getEmergencyContact1Relation() . "',
        emergencyContact1Phone = '" . $volunteer->getEmergencyContact1Phone() . "',
        emergencyContact2Name = '" . $volunteer->getEmergencyContact2Name() . "',
        emergencyContact2Relation = '" . $volunteer->getEmergencyContact2Relation() . "',
        emergencyContact2Phone = '" . $volunteer->getEmergencyContact2Phone() . "',
        allergies = '" . $volunteer->getAllergies() . "',
        sunStart = '" . $volunteer->getSunStart() . "',
        sunEnd = '" . $volunteer->getSunEnd() . "',
        monStart = '" . $volunteer->getMonStart() . "',
        monEnd = '" . $volunteer->getMonEnd() . "',
        tueStart = '" . $volunteer->getTueStart() . "',
        tueEnd = '" . $volunteer->getTueEnd() . "',
        wedStart = '" . $volunteer->getWedStart() . "',
        wedEnd = '" . $volunteer->getWedEnd() . "',
        thurStart = '" . $volunteer->getThuStart() . "',
        thurEnd = '" . $volunteer->getThuEnd() . "',
        friStart = '" . $volunteer->getFriStart() . "',
        friEnd = '" . $volunteer->getFriEnd() . "',
        satStart = '" . $volunteer->getSatStart() . "',
        satEnd = '" . $volunteer->getSatEnd() . "',
        dateAvailable = '" . $volunteer->getDateAvailable() . "',
        minHours = '" . $volunteer->getMinHours() . "',
        maxHours = '" . $volunteer->getMaxHours() . "',
        accessLevel = '" . $volunteer->getAccessLevel() . "'
        WHERE id = '" . $volunteer->getId() . "'";

    $result = mysqli_query($conn, $query);
    mysqli_close($conn);
    return $result;
}

function retrieve_all_volunteers_paginated($sortColumn, $sortOrder, $limit, $offset, $searchFilters = []) {
    $conn = connect();
    $query = "SELECT * FROM dbVolunteers WHERE 1=1";

    if (!empty($searchFilters['first_name'])) {
        $query .= " AND firstName LIKE '%" . mysqli_real_escape_string($conn, $searchFilters['first_name']) . "%'";
    }
    if (!empty($searchFilters['last_name'])) {
        $query .= " AND lastName LIKE '%" . mysqli_real_escape_string($conn, $searchFilters['last_name']) . "%'";
    }
    if (!empty($searchFilters['email'])) {
        $query .= " AND email LIKE '%" . mysqli_real_escape_string($conn, $searchFilters['email']) . "%'";
    }

    $query .= " ORDER BY $sortColumn $sortOrder LIMIT $limit OFFSET $offset";
    $res = mysqli_query($conn, $query);

    $volunteerList = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $volunteerList[] = create_volunteer_from_db($row);
    }

    mysqli_close($conn);
    return $volunteerList;
}

function count_all_volunteers($filters = []) {
    $conn = connect();
    $whereClauses = [];

    if (!empty($filters['first_name'])) {
        $whereClauses[] = "firstName LIKE '%" . mysqli_real_escape_string($conn, $filters['first_name']) . "%'";
    }
    if (!empty($filters['last_name'])) {
        $whereClauses[] = "lastName LIKE '%" . mysqli_real_escape_string($conn, $filters['last_name']) . "%'";
    }
    if (!empty($filters['email'])) {
        $whereClauses[] = "email LIKE '%" . mysqli_real_escape_string($conn, $filters['email']) . "%'";
    }

    $whereSQL = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

    $query = "SELECT COUNT(*) as total FROM dbVolunteers $whereSQL";
    $res = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($res);

    mysqli_close($conn);
    return $row['total'] ?? 0;
}
