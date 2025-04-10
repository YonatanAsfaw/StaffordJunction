<?php

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Staff.php');

//Function that creates a new staff object from staff sign up form
function make_staff_from_signup($result_row){
    $staff = new Staff(
        null,
        $result_row['firstName'],
        $result_row['lastName'],
        $result_row['birthdate'],
        $result_row['address'],
        $result_row['email'],
        $result_row['phone'],
        $result_row['econtactName'],
        $result_row['econtactPhone'],
        $result_row['jobTitle'],
        password_hash($result_row['password'], PASSWORD_BCRYPT),
        $result_row['securityQuestion'],
        password_hash($result_row['securityAnswer'], PASSWORD_BCRYPT)
    );

    return $staff;
}

//Function that gets all the info of a staff user from dbStaff and constructs a staff object from that data
function make_staff_from_db($result_row){
    $staff = new Staff(
        $result_row['id'],
        $result_row['firstName'],
        $result_row['lastName'],
        $result_row['birthdate'],
        $result_row['address'],
        $result_row['email'],
        $result_row['phone'],
        $result_row['econtactName'],
        $result_row['econtactPhone'],
        $result_row['jobTitle'],
        $result_row['password'],
        $result_row['securityQuestion'],
        $result_row['securityAnswer']
    );

    return $staff;
}

//function that inserts staff into dbStaff
function add_staff($staff){
    if(!$staff instanceof Staff){
        die("Add staff mismatch");
    }
    $conn = connect();
    $query = "SELECT * FROM dbStaff WHERE email = '" . $staff->getEmail() . "';";
    $res = mysqli_query($conn, $query);

    if(mysqli_num_rows($res) < 1 || $res == null){
        mysqli_query($conn,'INSERT INTO dbStaff (firstName, lastName, birthdate, address, email,
        phone, econtactName, econtactPhone, jobTitle, password, securityQuestion, securityAnswer) VALUES("' .
        $staff->getFirstName() . '","' .
        $staff->getLastName() . '","' .
        $staff->getBirthdate() . '","' .
        $staff->getAddress() . '","' .
        $staff->getEmail() . '","' .
        $staff->getPhone() . '","' .
        $staff->getEContactName() . '","' .
        $staff->getEContactPhone() . '","' .
        $staff->getJobTitle() . '","' .
        $staff->getPassword() . '","' .
        $staff->getSecurityQuestion() . '","' .
        $staff->getSecurityAnswer() . '");'
    );
        mysqli_close($conn);
        return true;
    }

}

//Function that retrieves staff member from dbStaff by email
function retrieve_staff($email){
    $conn = connect();
    $query = "SELECT * FROM dbStaff where email = '" . $email . "';";
    $res = mysqli_query($conn, $query);

    if(mysqli_num_rows($res) < 1 || $res == null){
        return null;
    }else {
        $row = mysqli_fetch_assoc($res);
        $staff = make_staff_from_db($row);
        return $staff;
    }
}

//Function that retrieves staff member from dbStaff by id
function retrieve_staff_by_id($id){
    $conn = connect();
    $query = "SELECT * FROM dbStaff where id = '" . $id . "';";
    $res = mysqli_query($conn, $query);

    if(mysqli_num_rows($res) < 1 || $res == null){
        mysqli_close($conn);
        return null;
    }else {
        $row = mysqli_fetch_assoc($res);
        $staff = make_staff_from_db($row);
        mysqli_close($conn);
        return $staff;
    }
}

function change_staff_password($id, $newPass) {
    $con=connect();
    $query = 'UPDATE dbStaff SET password = "' . $newPass . '" WHERE id = "' . $id . '"';
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return $result;
}

//function that removes a staff member from dbStaff by first and last name
function remove_staff_by_name($firstName, $lastName) {
    $conn = connect();

    //sanitize inputs
    $firstName = mysqli_real_escape_string($conn, $firstName);
    $lastName = mysqli_real_escape_string($conn, $lastName);

    //ensure the staff member exists before attempting to delete
    $query_check = "SELECT * FROM dbStaff WHERE firstName = '$firstName' AND lastName = '$lastName'";
    $res_check = mysqli_query($conn, $query_check);

    if (!$res_check || mysqli_num_rows($res_check) < 1) {
        mysqli_close($conn);
        return false;
    }

    $query_delete = "DELETE FROM dbStaff WHERE firstName = '$firstName' AND lastName = '$lastName'";
    $res_delete = mysqli_query($conn, $query_delete);

    mysqli_close($conn);
    return $res_delete; // Returns true if successful, false otherwise
}

function retrieve_staff_by_name($firstName, $lastName) {
    $conn = connect();

    // Ensure inputs are strings and not arrays
    $firstName = is_array($firstName) ? '' : trim($firstName);
    $lastName = is_array($lastName) ? '' : trim($lastName);

    // Escape inputs to prevent SQL injection
    $firstName = mysqli_real_escape_string($conn, $firstName);
    $lastName = mysqli_real_escape_string($conn, $lastName);

    // Query to find the staff member
    $query = "SELECT * FROM dbStaff WHERE firstName = '$firstName' AND lastName = '$lastName';";

    $res = mysqli_query($conn, $query);

    // Handle cases where no results are found
    if (!$res || mysqli_num_rows($res) < 1) {
        mysqli_close($conn);
        return null;
    }

    // Fetch result and create staff object
    $row = mysqli_fetch_assoc($res);
    $staff = make_staff_from_db($row);

    mysqli_close($conn);
    return $staff;
}

function update_staff($staff) {
    if (!$staff instanceof Staff) {
        die("Update staff mismatch");
    }
    $conn = connect();

    $query = "UPDATE dbStaff SET
        firstName = '" . $staff->getFirstName() . "',
        lastName = '" . $staff->getLastName() . "',
        birthdate = '" . $staff->getBirthdate() . "',
        address = '" . $staff->getAddress() . "',
        email = '" . $staff->getEmail() . "',
        phone = '" . $staff->getPhone() . "',
        econtactName = '" . $staff->getEContactName() . "',
        econtactPhone = '" . $staff->getEContactPhone() . "',
        jobTitle = '" . $staff->getJobTitle() . "'
        WHERE id = '" . $staff->getId() . "'";

    $result = mysqli_query($conn, $query);
    mysqli_close($conn);
    return $result;
}

function retrieve_all_staff_paginated($sortColumn, $sortOrder, $limit, $offset, $searchFilters = []) {
    $conn = connect();
    $query = "SELECT * FROM dbStaff WHERE 1=1";

    if (!empty($searchFilters['first_name'])) {
        $query .= " AND firstName LIKE '%" . mysqli_real_escape_string($conn, $searchFilters['first_name']) . "%'";
    }
    if (!empty($searchFilters['last_name'])) {
        $query .= " AND lastName LIKE '%" . mysqli_real_escape_string($conn, $searchFilters['last_name']) . "%'";
    }

    $query .= " ORDER BY $sortColumn $sortOrder LIMIT $limit OFFSET $offset";
    $res = mysqli_query($conn, $query);

    $staffList = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $staffList[] = make_staff_from_db($row);
    }

    mysqli_close($conn);
    return $staffList;
}

function count_all_staff($filters = []) {
    $conn = connect();
    $whereClauses = [];

    if (!empty($filters['first_name'])) {
        $whereClauses[] = "firstName LIKE '%" . mysqli_real_escape_string($conn, $filters['first_name']) . "%'";
    }
    if (!empty($filters['last_name'])) {
        $whereClauses[] = "lastName LIKE '%" . mysqli_real_escape_string($conn, $filters['last_name']) . "%'";
    }

    $whereSQL = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

    $query = "SELECT COUNT(*) as total FROM dbStaff $whereSQL";
    $res = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($res);

    mysqli_close($conn);
    return $row['total'] ?? 0;
}
