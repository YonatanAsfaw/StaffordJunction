<?php

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Staff.php');
require_once(dirname(__FILE__) . '/../database/dbStaff.php');  // Adjust if needed
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
function make_staff_from_db($result_row){
    if (!$result_row || empty($result_row)) {
        die("Error: Staff data is missing or incorrect.");
    }

    return new Staff(
        $result_row['id'] ?? null,
        $result_row['firstName'] ?? "Unknown",
        $result_row['lastName'] ?? "Unknown",
        $result_row['birthdate'] ?? null,
        $result_row['address'] ?? null,
        $result_row['email'] ?? null,
        $result_row['phone'] ?? null,
        $result_row['econtactName'] ?? null,
        $result_row['econtactPhone'] ?? null,
        $result_row['jobTitle'] ?? null,
        $result_row['password'] ?? null,
        $result_row['securityQuestion'] ?? null,
        $result_row['securityAnswer'] ?? null
    );
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

function retrieve_staff_by_name($first_name, $last_name){
    $conn = connect(); // Ensure this function is defined in dbinfo.php

    $query = "SELECT * FROM dbStaff WHERE firstName LIKE ? AND lastName LIKE ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("SQL Prepare Error: " . mysqli_error($conn));
    }

    // Add wildcard search for flexibility
    $first_name = "%".$first_name."%";
    $last_name = "%".$last_name."%";

    mysqli_stmt_bind_param($stmt, "ss", $first_name, $last_name);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (!$res) {
        die("Query execution failed: " . mysqli_error($conn));
    }

    $num_rows = mysqli_num_rows($res);
   

    if ($num_rows < 1) {
        mysqli_close($conn);
        return null;
    } else {
        $row = mysqli_fetch_assoc($res);

        // Check if fetch_assoc() worked
        if (!$row) {
            die("Error: Unable to fetch staff details.");
        }

        $staff = make_staff_from_db($row);
        mysqli_close($conn);
        return $staff;
    }
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

function retrieve_all_staff_paginated($sortColumn = 'lastName', $sortOrder = 'ASC', $limit = 10, $offset = 0) {
    $conn = connect();

    // Ensure sort column and order are safe
    $allowedColumns = ['firstName', 'lastName', 'email', 'phone', 'jobTitle'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'lastName'; // Default to last name if invalid column provided
    }
    
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC'; // Only allow ASC or DESC

    // SQL Query: Fetch paginated results
    $query = "SELECT * FROM dbStaff ORDER BY $sortColumn $sortOrder LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $staffList = [];
    while ($row = $result->fetch_assoc()) {
        $staffList[] = make_staff_from_db($row);
    }

    $stmt->close();
    mysqli_close($conn);
    return $staffList;
}
