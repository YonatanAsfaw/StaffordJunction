<?php


function createAngelGiftForm($form) {
    $conn = connect();

    $query = "INSERT INTO dbAngelGiftForm (child_id, email, parent_name, phone, child_name, gender, age, 
        pants_size, shirt_size, shoe_size, coat_size, underwear_size, sock_size, wants, interests, photo_release)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error: " . $conn->error);
    }

    $stmt->bind_param("isssssisississsi",
        $form["child_id"], $form["email"], $form["parent_name"], $form["phone"], 
        $form["child_name"], $form["gender"], $form["age"], 
        $form["pants_size"], $form["shirt_size"], $form["shoe_size"], $form["coat_size"], 
        $form["underwear_size"], $form["sock_size"], $form["wants"], $form["interests"], $form["photo_release"]
    );

    $success = $stmt->execute();
    if ($success) {
        mysqli_commit($conn);  // ✅ Ensure MySQL commits the new data
        error_log("DEBUG: Form successfully inserted!");
    } else {
        die("Error: " . $stmt->error);
    }

    $stmt->close();
    mysqli_close($conn);
    
    return $success;
}



// Function checks if a child has already completed the form
function isAngelGiftFormComplete($childID) {
    $connection = connect(); // Ensure database connection

    if (!is_numeric($childID)) {
        error_log("ERROR: childID is not a valid number: " . print_r($childID, true));
        return false;
    }

    $query = "SELECT COUNT(*) AS count FROM dbAngelGiftForm WHERE child_id = ?";
    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        die("ERROR: SQL Prepare failed - " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "i", $childID);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    // Ensure array key exists before accessing it
    return isset($row['count']) && $row['count'] > 0;
}



// Function to retrieve completed Angel Gift Forms by family ID
function get_angel_gift_forms_by_family_id($familyID) {
    $connection = connect();

    // Query to join dbAngelGiftForm and dbChildren tables to filter by family_id
    $query = "
        SELECT agf.*
        FROM dbAngelGiftForm agf
        INNER JOIN dbChildren c ON agf.child_id = c.id
        WHERE c.family_id = $familyID
    ";

    $result = mysqli_query($connection, $query);
    $forms = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $forms[] = $row;
        }
    }

    mysqli_close($connection);

    return $forms; // Return an array of completed forms
}

?>

<?php
function getAngelGiftSubmissions() {
    $conn = connect();
    $query = "SELECT * FROM dbAngelGiftForm INNER JOIN dbChildren ON dbAngelGiftForm.child_id = dbChildren.id;";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $submissions;
    }
    return [];
}

function getAngelGiftSubmissionsFromFamily($familyID) {
    $conn = connect();
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Force fresh query and sort by latest entry
    $query = "SELECT * FROM dbAngelGiftForm WHERE child_id IN 
              (SELECT id FROM dbChildren WHERE family_id = ?) 
              ORDER BY id DESC";  // ✅ Newest forms first

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $familyID);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $submissions = [];

    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }

    mysqli_commit($conn);  // ✅ Force fresh data retrieval
    $stmt->close();
    mysqli_close($conn);

    error_log("DEBUG: Retrieved submissions: " . json_encode($submissions));

    return $submissions;
}




?>