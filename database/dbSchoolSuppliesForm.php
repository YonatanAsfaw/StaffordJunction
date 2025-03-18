<?php

require_once("dbinfo.php"); 

// ✅ Ensure database connection
global $conn;
if (!$conn) {
    die("Database connection failed in dbSchoolSuppliesForm.php");
}

// ✅ Function to create a new School Supplies form submission
function createBackToSchoolForm($form) {
    $conn = connect();

    $child_data = explode("_", $form['name']);
    $child_id = $child_data[0];
    $child_name = $child_data[1];
    $email = $form["email"];
    $grade = $form["grade"];
    $school = $form["school"];
    $bag_pickup_method = $form["community"];
    $need_backpack = ($form["need_backpack"] === "need_backpack") ? 1 : 0;

    // ✅ Use prepared statements
    $query = "INSERT INTO dbSchoolSuppliesForm (child_id, email, child_name, grade, school, bag_pickup_method, need_backpack)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssi", $child_id, $email, $child_name, $grade, $school, $bag_pickup_method, $need_backpack);
    $result = $stmt->execute();

    if (!$result) {
        return null;
    }

    $id = $stmt->insert_id;
    $stmt->close();
    $conn->close();

    return $id;
}

// ✅ Check if the Back-to-School form has already been completed for the child
function isBackToSchoolFormComplete($childID) {
    $conn = connect();
    $query = "SELECT * FROM dbSchoolSuppliesForm WHERE child_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $childID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $complete = $result->num_rows > 0;
    
    $stmt->close();
    $conn->close();
    
    return $complete;
}

// ✅ Retrieve a School Supplies form by ID
function getSchoolSuppliesFormById($form_id) {
    $conn = connect();
    $query = "SELECT * FROM dbSchoolSuppliesForm WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $form_data = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $form_data;
}

// ✅ Retrieve all School Supplies form submissions for a specific family
function get_school_supplies_form_by_family_id($familyID) {
    $conn = connect();
    $query = "
        SELECT ssf.*
        FROM dbSchoolSuppliesForm ssf
        INNER JOIN dbChildren c ON ssf.child_id = c.id
        WHERE c.family_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $familyID);
    $stmt->execute();
    $result = $stmt->get_result();

    $forms = [];
    while ($row = $result->fetch_assoc()) {
        $forms[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $forms;
}

// ✅ Update School Supplies form submission
function updateSchoolSuppliesForm($submissionId, $updatedData) {
    $conn = connect();

    if (!$conn) {
        die("ERROR: Database connection is NULL in updateSchoolSuppliesForm.");
    }

    $query = "UPDATE dbSchoolSuppliesForm SET 
                student_name = ?, 
                grade = ?, 
                school = ?, 
                parent_name = ?, 
                address = ?, 
                phone = ?, 
                need_backpack = ?, 
                bag_pickup_method = ? 
              WHERE id = ?";

    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Database prepare() failed: " . $conn->error);
    }

    // Assign values before binding
    $studentName = $updatedData["student_name"];
    $grade = $updatedData["grade"];
    $school = $updatedData["school"];
    $parentName = $updatedData["parent_name"];
    $address = $updatedData["address"];
    $phone = $updatedData["phone"];
    $needBackpack = $updatedData["need_backpack"];
    $bagPickupMethod = $updatedData["bag_pickup_method"];
    $id = (int)$submissionId;

    $stmt->bind_param(
        "ssssssssi", 
        $studentName, $grade, $school, $parentName, $address, $phone, $needBackpack, $bagPickupMethod, $id
    );

    $result = $stmt->execute();
    
    if (!$result) {
        die("Execution failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
    
    return $result;
}

// ✅ Delete a specific school supplies form
function deleteSchoolSuppliesForm($form_id) {
    $conn = connect();

    $query = "DELETE FROM dbSchoolSuppliesForm WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $form_id);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $result;
}

// ✅ Retrieve all School Supplies form submissions
function getSchoolSuppliesSubmissions() {
    $conn = connect();
    $query = "SELECT ssf.*, c.firstName, c.lastName
              FROM dbSchoolSuppliesForm ssf
              INNER JOIN dbChildren c ON c.id = ssf.child_id;";
    
    $result = $conn->query($query);
    $submissions = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }
    }

    $conn->close();
    return $submissions;
}

// ✅ Retrieve School Supplies form submissions for a specific family
function getSchoolSuppliesSubmissionsFromFamily($familyId) {
    require_once("dbChildren.php");
    
    $children = retrieve_children_by_family_id($familyId);
    if (!$children) {
        return [];
    }

    $childrenIds = array_map(function ($child) {
        return $child->getId();
    }, $children);

    if (empty($childrenIds)) {
        return [];
    }

    $joinedIds = implode(",", $childrenIds);
    $conn = connect();
    
    $query = "SELECT ssf.*, c.firstName, c.lastName
              FROM dbSchoolSuppliesForm ssf
              INNER JOIN dbChildren c ON ssf.child_id = c.id
              WHERE ssf.child_id IN ($joinedIds)";

    $result = $conn->query($query);
    $submissions = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }
    }

    $conn->close();
    return $submissions;
}

?>
