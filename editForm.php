<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbForms.php");


if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    die();
}

// Get form details
if (!isset($_GET['id']) || !isset($_GET['formName'])) {
    die("Invalid request.");
}

$submissionId = $_GET['id'];
$formName = $_GET['formName'];
$updateSuccess = false;
$deleteSuccess = false;
$deleteError = false;

// Handle DELETE action first
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    // Load the appropriate database functions based on form name
    switch ($formName) {
        case "Holiday Meal Bag":
            require_once("database/dbHolidayMealBag.php");
            
            // First get the current form data to extract family_id
            $currentData = getHolidayMealBagById($submissionId);
            
            if ($currentData && isset($currentData['family_id'])) {
                $familyId = $currentData['family_id'];
                
                // Try to delete by family_id
                error_log("Attempting to delete Holiday Meal Bag record with family_id: $familyId");
                if (deleteHolidayMealBagForm($familyId)) {
                    error_log("Successfully deleted Holiday Meal Bag record");
                    $deleteSuccess = true;
                    header("Location: formSearchResult.php?searchByForm=searchByForm&formName=" . urlencode($formName) . "&deleted=1");
                    exit();
                } else {
                    error_log("Failed to delete Holiday Meal Bag record with family_id: $familyId");
                    $deleteError = true;
                }
            } else {
                error_log("Could not retrieve family_id for Holiday Meal Bag submission: $submissionId");
                $deleteError = true;
            }
            break;
            
        // Add cases for other form types as needed
        // case "Another Form Type":
        //     require_once("database/dbAnotherForm.php");
        //     if (deleteAnotherForm($submissionId)) {
        //         $deleteSuccess = true;
        //         header("Location: formSearchResult.php?searchByForm=searchByForm&formName=" . urlencode($formName) . "&deleted=1");
        //         exit();
        //     } else {
        //         $deleteError = true;
        //     }
        //     break;
            
        default:
            error_log("No delete handler for form type: $formName");
            $deleteError = true;
    }
}

// Fetch existing form data
$formData = getFormSubmissionById($formName, $submissionId);
error_log("formName received: '$formName'");
error_log("formData result: " . print_r($formData, true));

if (!$formData) {
    die("Form data not found for ID: " . htmlspecialchars($submissionId));
}

// Convert all NULL values to empty strings before displaying
foreach ($formData as $key => $value) {
    $formData[$key] = $value ?? '';
}

// Handle UPDATE (after DELETE since we want DELETE to take precedence)
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete'])) {
    $updatedData = [];

    foreach ($formData as $key => $value) {
        if (isset($_POST[$key])) {
            $updatedData[$key] = $_POST[$key];
        }
    }

    if (updateFormSubmission($formName, $submissionId, $updatedData)) {
        $updateSuccess = true;
        // Refresh form data after update
        $formData = getFormSubmissionById($formName, $submissionId);
        foreach ($formData as $key => $value) {
            $formData[$key] = $value ?? '';
        }
    } else {
        error_log("Failed to update form: $formName, ID: $submissionId");
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && $updateSuccess) {
    if (isset($_GET['id'])) {
        header('Location: formSearch.php?formUpdateSuccess&id=' . $_GET['id']);
        exit();
    } else {
        header('Location: formSearch.php?formUpdateSuccess');
        exit();
    }
}

function getFormSubmissionById($formName, $submissionId) {
    switch ($formName) {
        case "Child Care Waiver":
            require_once("database/dbChildCareWaiverForm.php");
            return getChildCareWaiverById($submissionId);
        case "Holiday Meal Bag":
            require_once("database/dbHolidayMealBag.php");
            return getHolidayMealBagById($submissionId);
        case "School Supplies":
            require_once("database/dbSchoolSuppliesForm.php");
            return getSchoolSuppliesFormById($submissionId);
        case "Angel Gifts Wish List":
            require_once("database/dbAngelGiftForm.php");
            return getAngelGiftById($submissionId);
        case "Spring Break Camp Form":
            require_once("database/dbSpringBreakCampForm.php");
            return getSpringBreakById($submissionId);
        case "Summer Junction":
            require_once("database/dbSummerJunctionForm.php");
            return getSummerJunctionById($submissionId);
        case "Program Interest Form":
            require_once("database/dbProgramInterestForm.php");
            return getProgramInterestFormById($submissionId);
        case "Actual Activity":
            require_once("database/dbActualActivityForm.php");
            return getActualActivityById($submissionId);
        case "Brain Builders Student Registration":
            require_once("database/dbBrainBuildersRegistration.php");
            return getBrainBuildersRegistrationById($submissionId);
        case "Brain Builders Holiday Party":
            require_once("database/dbHolidayPartyForm.php");
            return getHolidayPartyById($submissionId);
        default:
            return null;
    }
}

function updateFormSubmission($formName, $submissionId, $updatedData) {
    switch ($formName) {
        case "Child Care Waiver":
            require_once("database/dbChildCareWaiverForm.php");
            return updateChildCareWaiverForm($submissionId, $updatedData);
        case "Holiday Meal Bag":
            require_once("database/dbHolidayMealBag.php");
            return updateHolidayMealBagForm($submissionId, $updatedData);
        case "School Supplies":
            require_once("database/dbSchoolSuppliesForm.php");
            return updateSchoolSuppliesForm($submissionId, $updatedData);
        case "Angel Gifts Wish List":
            require_once("database/dbAngelGiftForm.php");
            return updateAngelGiftForm($submissionId, $updatedData);
        case "Spring Break Camp Form":
            require_once("database/dbSpringBreakCampForm.php");
            return updateSpringBreakCampForm($submissionId, $updatedData);
        case "Summer Junction":
            require_once("database/dbSummerJunctionForm.php");
            return updateSummerJunctionRegistrationForm($submissionId, $updatedData);
        case "Program Interest Form":
            require_once("database/dbProgramInterestForm.php");
            return updateProgramInterestForm($submissionId, $updatedData);
        case "Actual Activity":
            require_once("database/dbActualActivityForm.php");
            return updateActualActivityForm($submissionId, $updatedData);
        case "Brain Builders Student Registration":
            require_once("database/dbBrainBuildersRegistration.php");
            return updateBrainBuildersRegistration($submissionId, $updatedData);
        case "Brain Builders Holiday Party":
            require_once("database/dbHolidayPartyForm.php");
            return updateHolidayPartyForm($submissionId, $updatedData);
        default:
            return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('universal.inc'); 
    require('header.php');?>
    <title>Edit Form - <?php echo htmlspecialchars($formName); ?></title>
</head>
<body>

    <!-- Success Message -->
    <?php if ($deleteError): ?>
    <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
        Error deleting form. Please try again.
    </div>
    <?php endif; ?>

    <div class="container">
        <h2>Edit <?php echo htmlspecialchars($formName); ?> Form</h2>

        <form method="post" action="" id="editForm">
            <?php foreach ($formData as $key => $value): ?>
                <?php if ($key !== 'id' && $key !== 'form_id' && $key !== 'child_id' && $key !== 'family_id'): // Exclude IDs ?>
                    <label><?php echo ucwords(str_replace("_", " ", $key)); ?>:</label>
                    <?php if ($key === 'need_backpack'): ?>
                        <select name="<?php echo $key; ?>">
                            <option value="0" <?php if ($value == '0') echo "selected"; ?>>No</option>
                            <option value="1" <?php if ($value == '1') echo "selected"; ?>>Yes</option>
                        </select>
                    <?php elseif ($key === 'bag_pickup_method'): ?>
                        <select name="<?php echo $key; ?>">
                            <option value="pick_up" <?php if ($value == 'pick_up') echo "selected"; ?>>Pick Up</option>
                            <option value="no_pick_up" <?php if ($value == 'no_pick_up') echo "selected"; ?>>No Pick Up</option>
                            <option value="other" <?php if (!in_array($value, ['pick_up', 'no_pick_up'])) echo "selected"; ?>>Other</option>
                        </select>
                        <input type="text" name="bag_pickup_method_other" value="<?php echo !in_array($value, ['pick_up', 'no_pick_up']) ? htmlspecialchars($value) : ''; ?>" <?php if (in_array($value, ['pick_up', 'no_pick_up'])) echo 'disabled'; ?>>
                    <?php else: ?>
                        <input type="text" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>

            <button type="submit" class="submit-btn">Save Changes</button>
        </form>

        <!-- Separate delete form to avoid conflicts with regular submission -->
        <form method="post" action="" id="deleteForm">
            <input type="hidden" name="delete" value="1">
            <div style="margin-top: 20px;"></div> <!-- Adds vertical space -->

            <button type="submit" class="submit-btn"  onclick="return confirm('Are you sure you want to unenroll? This action cannot be undone.');">
                Unenroll
            </button>
        </form>

        <a class="button cancel button_style" href="formSearchResult.php?searchByForm=searchByForm&formName=<?php echo urlencode($formName); ?>">Back to Search Results</a>
    </div>
</body>
</html>