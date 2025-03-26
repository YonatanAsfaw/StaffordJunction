<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("dbinfo.php");


/**
 * Inserts a new childcare waiver form into the database.
 */
/**
 * Inserts or updates a childcare waiver form in the database.
 */
function createOrUpdateChildCareForm($form) {
    $connection = connect();
    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Validate child name format
    if (!isset($form['name']) || empty($form['name'])) {
        die("<pre>ERROR: Child Name not received in form submission.</pre>");
    }

    $child_data = explode("_", $form['name']);
    if (count($child_data) < 2) {
        die("<pre>ERROR: 'name' format incorrect. Expected 'ID_Name', got: " . print_r($form['name'], true) . "</pre>");
    }

    $child_id = trim($child_data[0]);
    $child_name = trim($child_data[1]);

    if (empty($child_id) || empty($child_name) || !is_numeric($child_id)) {
        die("<pre>ERROR: Child ID or Name is missing or invalid.\nChild ID: $child_id\nChild Name: $child_name</pre>");
    }

    // Prepare form fields
    $birth_date = $form["child_dob"] ?? '';
    $gender = $form["child_gender"] ?? '';
    $child_address = $form["child_address"] ?? '';
    $child_city = $form['child_city'] ?? '';
    $child_state = $form['child_state'] ?? '';
    $child_zip = $form['child_zip'] ?? '';

    // Parent 1
    $parent1_first_name = $form['parent1_first_name'] ?? '';
    $parent1_last_name = $form['parent1_last_name'] ?? '';
    $parent1_address = $form['parent1_address'] ?? '';
    $parent1_city = $form['parent1_city'] ?? '';
    $parent1_state = $form['parent1_state'] ?? '';
    $parent1_zip_code = $form['parent1_zip'] ?? '';  // Match the form field name
    $parent1_email = $form['parent1_email'] ?? '';
    $parent1_cell_phone = $form['parent1_cell_phone'] ?? '';
    $parent1_home_phone = $form['parent1_home_phone'] ?? '';
    $parent1_work_phone = $form['parent1_work_phone'] ?? '';

    // Parent 2 (Optional)
    $parent2_first_name = $form['parent2_first_name'] ?? '';
    $parent2_last_name = $form['parent2_last_name'] ?? '';
    $parent2_address = $form['parent2_address'] ?? '';
    $parent2_city = $form['parent2_city'] ?? '';
    $parent2_state = $form['parent2_state'] ?? '';
    $parent2_zip_code = $form['parent2_zip'] ?? '';  // Match the form field name
    $parent2_email = $form['parent2_email'] ?? '';
    $parent2_cell_phone = $form['parent2_cell_phone'] ?? '';
    $parent2_home_phone = $form['parent2_home_phone'] ?? '';
    $parent2_work_phone = $form['parent2_work_phone'] ?? '';

    // Guardian signature
    $guardian_signature = $form["guardian_signature"] ?? '';
    $signature_date = $form["signature_date"] ?? '';

    // Check if updating an existing form
    if (!empty($form['form_id'])) {
        $form_id = $form['form_id'];
        $query = "UPDATE dbChildCareWaiverForm SET 
                    birth_date = ?, gender = ?, child_address = ?, child_city = ?, child_state = ?, child_zip = ?,
                    parent1_first_name = ?, parent1_last_name = ?, parent1_address = ?, parent1_city = ?, 
                    parent1_state = ?, parent1_zip_code = ?, parent1_email = ?, parent1_cell_phone = ?, 
                    parent1_home_phone = ?, parent1_work_phone = ?,
                    parent2_first_name = ?, parent2_last_name = ?, parent2_address = ?, parent2_city = ?, 
                    parent2_state = ?, parent2_zip_code = ?, parent2_email = ?, parent2_cell_phone = ?, 
                    parent2_home_phone = ?, parent2_work_phone = ?,
                    parent_guardian_signature = ?, signature_date = ?
                  WHERE id = ?";

        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssssssssssssssssssssssi", 
            $birth_date, $gender, $child_address, $child_city, $child_state, $child_zip, 
            $parent1_first_name, $parent1_last_name, $parent1_address, $parent1_city, 
            $parent1_state, $parent1_zip_code, $parent1_email, $parent1_cell_phone, 
            $parent1_home_phone, $parent1_work_phone,
            $parent2_first_name, $parent2_last_name, $parent2_address, $parent2_city, 
            $parent2_state, $parent2_zip_code, $parent2_email, $parent2_cell_phone, 
            $parent2_home_phone, $parent2_work_phone,
            $guardian_signature, $signature_date, $form_id
        );

        if (!mysqli_stmt_execute($stmt)) {
            die("<pre>Update failed: " . mysqli_error($connection) . "</pre>");
        }
        return $form_id;
    } else {
        return createChildCareForm($form);
    }
}
/**
 * Inserts a new childcare waiver form into the database.
 */
/**
 * Inserts a new childcare waiver form into the database.
 */
/**
 * Inserts a new childcare waiver form into the database.
 */
function createChildCareForm($form) {
    $connection = connect();

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    if (!isset($form['name']) || empty($form['name'])) {
        die("ERROR: Child Name not received in form submission.");
    }

    $child_data = explode("_", $form['name']);
    if (count($child_data) < 2) {
        die("ERROR: Child ID or Name is missing from submitted data.");
    }

    $child_id = $child_data[0];
    $child_name = $child_data[1];

    if (empty($child_id) || empty($child_name)) {
        die("ERROR: Child ID or Name is missing.");
    }

    // Ensure all fields exist (use empty string if missing)
    $birth_date = $form["child_dob"] ?? '';
    $gender = $form["child_gender"] ?? '';
    $child_address = $form["child_address"] ?? '';
    $child_city = $form['child_city'] ?? '';
    $child_state = $form['child_state'] ?? '';
    $child_zip = $form['child_zip'] ?? '';

    // Parent 1 information
    $parent1_first_name = $form['parent1_first_name'] ?? '';
    $parent1_last_name = $form['parent1_last_name'] ?? '';
    $parent1_address = $form['parent1_address'] ?? '';  
    $parent1_city = $form['parent1_city'] ?? '';  
    $parent1_state = $form['parent1_state'] ?? '';  
    $parent1_zip_code = $form['parent1_zip'] ?? '';  // Match exactly with form field
    $parent1_email = $form['parent1_email'] ?? '';
    $parent1_cell_phone = $form['parent1_cell_phone'] ?? '';  
    $parent1_home_phone = $form['parent1_home_phone'] ?? '';  
    $parent1_work_phone = $form['parent1_work_phone'] ?? '';  

    // Parent 2 information
    $parent2_first_name = $form['parent2_first_name'] ?? '';
    $parent2_last_name = $form['parent2_last_name'] ?? '';
    $parent2_address = $form['parent2_address'] ?? '';  
    $parent2_city = $form['parent2_city'] ?? '';  
    $parent2_state = $form['parent2_state'] ?? '';  
    $parent2_zip_code = $form['parent2_zip'] ?? '';  // Match exactly with form field
    $parent2_email = $form['parent2_email'] ?? '';  
    $parent2_cell_phone = $form['parent2_cell_phone'] ?? '';  
    $parent2_home_phone = $form['parent2_home_phone'] ?? '';  
    $parent2_work_phone = $form['parent2_work_phone'] ?? '';  

    // Guardian signature
    $guardian_signature = $form["guardian_signature"] ?? '';
    $signature_date = $form["signature_date"] ?? '';

    

    $query = "INSERT INTO dbChildCareWaiverForm (
        child_id, child_name, birth_date, gender, child_address, child_city, child_state, child_zip, 
        parent1_first_name, parent1_last_name, parent1_address, parent1_city, parent1_state, parent1_zip_code, 
        parent1_email, parent1_cell_phone, parent1_home_phone, parent1_work_phone, 
        parent2_first_name, parent2_last_name, parent2_address, parent2_city, parent2_state, parent2_zip_code, 
        parent2_email, parent2_cell_phone, parent2_home_phone, parent2_work_phone, 
        parent_guardian_signature, signature_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        die("ERROR: SQL preparation failed - " . mysqli_error($connection));
    }
    $types = "isssssssssssssssssssssssssssss";
    



    mysqli_stmt_bind_param($stmt, $types, 
    $child_id, $child_name, $birth_date, $gender, $child_address, $child_city, $child_state, $child_zip, 
    $parent1_first_name, $parent1_last_name, $parent1_address, $parent1_city, $parent1_state, $parent1_zip_code, 
    $parent1_email, $parent1_cell_phone, $parent1_home_phone, $parent1_work_phone, 
    $parent2_first_name, $parent2_last_name, $parent2_address, $parent2_city, $parent2_state, $parent2_zip_code, 
    $parent2_email, $parent2_cell_phone, $parent2_home_phone, $parent2_work_phone, 
    $guardian_signature, $signature_date
);

    if (!mysqli_stmt_execute($stmt)) {
        die("Insert failed: " . mysqli_error($connection));
    }

   

    $id = mysqli_insert_id($connection);
    mysqli_commit($connection);
    mysqli_close($connection);

    return $id;
}

/**
 * Checks if a childcare waiver form already exists for a specific child.
 */
function isChildCareWaiverFormComplete($childID) {
    $connection = connect();
    $query = "SELECT id FROM dbChildCareWaiverForm WHERE child_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $childID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $exists = ($result && mysqli_num_rows($result) > 0);
    mysqli_close($connection);

    return $exists;
}
function getChildCareWaiverByChildName($childName) {
    $conn = connect(); // Ensure your db connection function exists

    $query = "SELECT * FROM dbChildCareWaiverForm WHERE child_name LIKE ?";
    $stmt = mysqli_prepare($conn, $query);
    $searchParam = "%" . $childName . "%";  // Allow partial matches
    mysqli_stmt_bind_param($stmt, "s", $searchParam);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $waiverForms = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $waiverForms;
}
function getChildCareWaiverById($id) {
    $conn = connect(); // Ensure the database connection function exists

    $query = "SELECT * FROM dbChildCareWaiverForm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result); // Fetch single row

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $data;
}
function updateChildCareWaiverForm($id, $updatedData) {
    $conn = connect(); // Ensure you have a database connection function

    // Build the SQL query dynamically based on the fields provided
    $query = "UPDATE dbChildCareWaiverForm SET ";
    $setFields = [];
    $values = [];

    foreach ($updatedData as $key => $value) {
        $setFields[] = "$key = ?";
        $values[] = $value;
    }

    $query .= implode(", ", $setFields);
    $query .= " WHERE id = ?";
    $values[] = $id;

    $stmt = mysqli_prepare($conn, $query);
    
    // Dynamically bind parameters
    $types = str_repeat("s", count($updatedData)) . "i"; // "s" for strings, "i" for ID
    mysqli_stmt_bind_param($stmt, $types, ...$values);

    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $success;
}
function getChildCareWaiverSubmissionsFromFamily($familyId) {
    $conn = connect(); // assuming you already have this

    $query = "SELECT w.* FROM dbChildCareWaiverForm w
              JOIN dbChildren c ON w.child_id = c.id
              WHERE c.family_id = ?";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $familyId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $forms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $forms[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $forms;
}

function getChildCareWaiverSubmissions() {
    $conn = connect(); // Use your DB connection function

    $query = "SELECT * FROM dbChildCareWaiverForm ORDER BY id DESC";
    $result = mysqli_query($conn, $query);

    $forms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $forms[] = $row;
    }

    mysqli_close($conn);
    return $forms;
}




?>
