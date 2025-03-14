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
}

// Retrieve all Holiday Meal Bag submissions
function getHolidayMealBagSubmissions() {
    global $conn;

    $query = "SELECT hmb.*, CONCAT(f.firstName, ' ', f.lastName) AS family_name 
              FROM dbHolidayMealBagForm hmb
              INNER JOIN dbFamily f ON f.id = hmb.family_id";
              
    $result = $conn->query($query);

    if (!$result || $result->num_rows == 0) {
        return [];
    }

    $submissions = $result->fetch_all(MYSQLI_ASSOC);
    return $submissions;
}

// Retrieve all submissions for a specific family ID
function getHolidayMealBagSubmissionsById($familyId) {
    global $conn;

    $query = "SELECT hmb.*, CONCAT(f.firstName, ' ', f.lastName) AS family_name 
          FROM dbHolidayMealBagForm hmb
          INNER JOIN dbFamily f ON f.id = hmb.family_id
          WHERE hmb.family_id=?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("i", $familyId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt->close();
        return [];
    }

    $submissions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $submissions;
}

// Update a Holiday Meal Bag form submission
function updateHolidayMealBagForm($submissionId, $updatedData) {
    global $conn; // Ensure we're using the global database connection

    if (!$conn) {
        die("ERROR: Database connection is NULL in updateHolidayMealBagForm.");
    }

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
    
    $result = $stmt->execute();
    
    if (!$result) {
        die("Execution failed: " . $stmt->error);
    }
    
    $stmt->close();
    return $result;
}

?>
