<?php
//need to connect
include_once('dbinfo.php');

// constant of all form names
const SEARCHABLE_FORMS = array("Holiday Meal Bag", "School Supplies", "Spring Break Camp Form", 
        "Angel Gifts Wish List", "Child Care Waiver Form", "Field Trip Waiver Form",
        "Program Interest Form", "Program Review", "Brain Builders Student Registration", "Brain Builders Holiday Party",
        "Summer Junction", "Bus Monitor Attendance", "Actual Activity"
     );

function getFormSubmissions($formName, $familyId){
    switch ($formName) {
    case "Holiday Meal Bag":
        require_once("dbHolidayMealBag.php");
        if ($familyId){
            return getHolidayMealBagFormBySubmissionId($familyId);
        }
        return getHolidayMealBagSubmissions();
    case "School Supplies":
        require_once("dbSchoolSuppliesForm.php");
        if ($familyId){
            return getSchoolSuppliesSubmissionsFromFamily($familyId);
        }
        return getSchoolSuppliesSubmissions();
    case "Spring Break Camp Form":
        require_once("dbSpringBreakCampForm.php");
        if ($familyId) {
            return getSpringBreakCampSubmissionsFromFamily($familyId);
        }
        return getSpringBreakCampSubmissions();
    case "Angel Gifts Wish List":
        require_once("dbAngelGiftForm.php");
        if ($familyId) {
            return getAngelGiftSubmissionsFromFamily($familyId);
        }
        return getAngelGiftSubmissions();
    case "Child Care Waiver Form":
        require_once("dbChildCareWaiverForm.php");
        if ($familyId) {
            return getChildCareWaiverSubmissionsFromFamily($familyId);
        }
        return getChildCareWaiverSubmissions();
    case "Field Trip Waiver":
        require_once("dbFieldTripWaiverForm.php");
        if ($familyId) {
            return getFieldTripWaiverSubmissionsFromFamily($familyId);
        }
        return getFieldTripWaiverSubmissions();
    case "Program Interest":
        require_once("dbProgramInterestForm.php");
        if ($familyId) {
            return getProgramInterestSubmissionsFromFamily($familyId);
        }
        return getProgramInterestSubmissions();

    case "Summer Junction":
        require_once("dbSummerJunctionForm.php");
        if ($familyId) {
            return getSummerJunctionSubmissionsFromFamily($familyId);
        }
        return getSummerJunctionSubmissions();
    

    // These need completed backends first
    // case "Brain Builders Student Registration":
    //     require_once(".php");
    //     return getSubmissions();
    // case "Brain Builders Holiday Party":
    //     require_once(".php");
    //     return getSubmissions();
    // case "Summer Junction Registration":
    //     require_once(".php");
    //     return getSubmissions();
    // case "Bus Monitor Attendance":
    //     require_once(".php");
    //     return getSubmissions();
    // case "Actual Activity":
    //     require_once("dbActualActivityForm.php");
    //     return getActualActivitySubmissions();
    //case "Program Review":
    //    require_once("dbProgramReviewForm.php");
    //    if ($familyId) {
    //        return getProgramReviewSubmissionsFromFamily($familyId);
    //    }
    //    return getProgramReviewSubmissions();
    default:
    }
}

function getFormsByFamily($familyId){
    // Names of all forms the family has completed
    $completedFormNames = array();
    foreach (SEARCHABLE_FORMS as $formName) {
        // get the form submissions, which is an array of objects. each object contains completed form data
        $results = getFormSubmissions($formName, $familyId);
        if(!$results){
            continue;
        }
        foreach ($results as $_){
            array_push($completedFormNames, $formName);
        }
    }
    return $completedFormNames;
}
function getPublishedForms() {
    $conn = connect();
    $query = "SELECT form_name FROM dbformstatus WHERE is_published = 1";
    $result = mysqli_query($conn, $query);

    $publishedForms = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $publishedForms[] = trim($row['form_name']); // Trim removes extra spaces
    }

    mysqli_close($conn);
    return $publishedForms;
}



function getAllFormStatuses() {
    $conn = connect();
    $query = "SELECT form_name, is_published FROM dbFormStatus";
    $result = mysqli_query($conn, $query);

    $formStatuses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $formStatuses[] = $row;
    }

    mysqli_close($conn);
    return $formStatuses;
}


function toggleFormPublication($formName) {
    $conn = connect();

    // Check if the form exists before updating
    $checkQuery = "SELECT is_published FROM dbFormStatus WHERE form_name = ?";
    $stmtCheck = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmtCheck, "s", $formName);
    mysqli_stmt_execute($stmtCheck);
    $result = mysqli_stmt_get_result($stmtCheck);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Toggle publication status
        $newStatus = $row['is_published'] == 1 ? 0 : 1;
        $updateQuery = "UPDATE dbFormStatus SET is_published = ? WHERE form_name = ?";
        
        $stmtUpdate = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, "is", $newStatus, $formName);
        $success = mysqli_stmt_execute($stmtUpdate);

        if (!$success) {
            error_log("ERROR: Could not toggle form publication - " . mysqli_error($conn));
        }

        mysqli_stmt_close($stmtUpdate);
    } else {
        // If form doesn't exist, insert it with default status
        $insertQuery = "INSERT INTO dbFormStatus (form_name, is_published) VALUES (?, 1)";
        $stmtInsert = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmtInsert, "s", $formName);
        mysqli_stmt_execute($stmtInsert);
        mysqli_stmt_close($stmtInsert);
    }

    mysqli_stmt_close($stmtCheck);
    mysqli_close($conn);
}
