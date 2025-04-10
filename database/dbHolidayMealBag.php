<?php

require_once("dbinfo.php"); 
require_once("dbFamily.php");

// Ensure the global database connection is available
global $conn;
if (!$conn) {
    die("Database connection failed in dbHolidayMealBag.php");
}

// Function to retrieve Holiday Meal Bag form data for a specific family
function get_data_by_family_id($familyID) {
    global $conn; 

    $stmt = $conn->prepare("SELECT * FROM dbHolidayMealBagForm WHERE family_id = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("i", $familyID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $stmt->close();
    return $data;
}

// Delete a specific Holiday Meal Bag form entry by family ID
function deleteHolidayMealBagForm($family_id) {
    global $conn;

    $query = "DELETE FROM dbHolidayMealBagForm WHERE family_id=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("i", $family_id);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}// Add this function to dbHolidayMealBag.php:

function deleteHolidayMealBagFormById($id) {
    global $conn;

    $query = "DELETE FROM dbHolidayMealBagForm WHERE id=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}




// Retrieve all Holiday Meal Bag submissions
function getHolidayMealBagSubmissions() {
    global $conn;

    $query = "SELECT hmb.*, CONCAT(f.firstName, ' ', f.lastName) AS family_name 
              FROM dbHolidayMealBagForm hmb
              LEFT JOIN dbFamily f ON f.id = hmb.family_id";
              
    $result = $conn->query($query);

    if (!$result) {
        error_log("Query Error: " . $conn->error);
        return [];
    }

    error_log("Number of records retrieved: " . $result->num_rows);
    $submissions = $result->fetch_all(MYSQLI_ASSOC);

    error_log("Fetched Submissions: " . json_encode($submissions));
    return $submissions;
}


// Retrieve all submissions for a specific family ID

function getHolidayMealBagFormBySubmissionId($submissionId) {
    global $conn; // Ensure this is your database connection

    $query = "SELECT * FROM dbHolidayMealBagForm WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($data)) {
        error_log("No data found for submission ID: " . $submissionId);
    }

    return $data;
}


// Update a Holiday Meal Bag form submission
function updateHolidayMealBagForm($submissionId, $updatedData) {
    global $conn; // Ensure we're using the global database connection

    if (!$conn) {
        die("ERROR: Database connection is NULL in updateHolidayMealBagForm.");
    }

    error_log("updateHolidayMealBagForm called for ID: " . $submissionId . " with data: " . json_encode($updatedData));
    // ✅ Correct SQL Query (Ensure it matches the number of bind variables)
    $query = "UPDATE dbHolidayMealBagForm SET 
                household_size = ?, 
                meal_bag = ?, 
                name = ?, 
                address = ?, 
                phone = ?, 
                photo_release = ?
              WHERE id = ?";

    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Database prepare() failed: " . $conn->error);
    }

    // ✅ Assign variables before binding
    $householdSize = (int)$updatedData["household_size"];
    $mealBag = (string)$updatedData["meal_bag"];
    $name = isset($updatedData["name"]) ? (string)$updatedData["name"] : "";
    $address = isset($updatedData["address"]) ? (string)$updatedData["address"] : "";
    $phone = isset($updatedData["phone"]) ? (string)$updatedData["phone"] : "";
    $photoRelease = isset($updatedData["photo_release"]) ? (int)$updatedData["photo_release"] : 0;
    $id = (int)$submissionId;

    // ✅ Correct `bind_param()` (Ensure the number of variables matches)
    $stmt->bind_param(
        "issssii", // 7 parameters: (int, string, string, string, string, int, int)
        $householdSize,
        $mealBag,
        $name,
        $address,
        $phone,
        $photoRelease,
        $id
    );

    $result = $stmt->execute();
    
    if (!$result) {
        die("Execution failed: " . $stmt->error);
    }

    $stmt->close();
    return $result;
}

function insertHolidayMealBagForm($familyID, $email, $householdSize, $mealBag, $name, $address, $phone, $photoRelease) {
    global $conn; // Ensure we're using the global database connection

    if (!$conn) {
        die("ERROR: Database connection is NULL in insertHolidayMealBagForm.");
    }

    // ✅ Correct SQL Query (Ensure it matches the number of bind variables)
    $query = "INSERT INTO dbHolidayMealBagForm (family_id, email, household_size, meal_bag, name, address, phone, photo_release) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Database prepare() failed: " . $conn->error);
    }

    // ✅ Assign variables before binding
    $familyID = (int)$familyID;
    $email = (string)$email;
    $householdSize = (int)$householdSize;
    $mealBag = (string)$mealBag;
    $name = isset($name) ? (string)$name : "";
    $address = isset($address) ? (string)$address : "";
    $phone = isset($phone) ? (string)$phone : "";
    $photoRelease = isset($photoRelease) ? (int)$photoRelease : 0;

    // ✅ Correct `bind_param()` (Ensure the number of variables matches)
    $stmt->bind_param(
        "isissssi", // 8 parameters: (int, string, int, string, string, string, string, int)
        $familyID,
        $email,
        $householdSize,
        $mealBag,
        $name,
        $address,
        $phone,
        $photoRelease
    );

    error_log("Attempting to insert: " . json_encode([$familyID, $email, $householdSize, $mealBag, $name, $address, $phone, $photoRelease]));

    
    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Insert failed: " . $stmt->error);
        die("Execution failed: " . $stmt->error);
    }else{
        error_log("Insert successful for Family ID: " . $familyID);

    }
    
    $stmt->close();
    return $result;
}

function getHolidayMealBagById($id) {
    $conn = connect(); // Ensure `connect()` establishes the database connection.

    $query = "SELECT * FROM dbHolidayMealBagForm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $formData = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $formData;
}

?>
