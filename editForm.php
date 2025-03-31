<?php
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

// Handle form submission (saving edits)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
        // Redirect to admin dashboard (index.php) after success
        header("Location: index.php?success=1");
        exit();
    } else {
        echo "<p style='color:red;'>Error updating form. Please try again.</p>";
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
            return getSchoolSuppliesFormById($submissionId); // Fixed function name
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
    <title>Edit Form - <?php echo htmlspecialchars($formName); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f3f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            margin: 0;
            flex-direction: column;
            overflow: auto;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 450px;
            text-align: center;
            border-top: 5px solid #7b1416;
            position: relative;
        }
        h2 {
            color: #7b1416;
            font-size: 24px;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        button, .dashboard {
            display: block;
            width: 90%;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn {
            background-color: #7b1416;
            color: white;
            border: none;
        }
        .submit-btn:hover {
            background-color: #580f11;
        }
        .dashboard {
            background-color: white;
            border: 2px solid #4CAF50;
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            line-height: 40px;
        }
        .dashboard:hover {
            background-color: #4CAF50;
            color: white;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            display: none;
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 80%;
            text-align: center;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
    </style>
</head>
<body>

    <!-- Success Message -->
    <div id="successMessage" class="success-message">Update Successful!</div>

    <div class="container">
        <h2>Edit <?php echo htmlspecialchars($formName); ?> Form</h2>

        <form method="post" id="editForm">
            <?php foreach ($formData as $key => $value): ?>
                <?php if ($key !== 'id' && $key !== 'form_id' && $key !== 'child_id'): // Exclude IDs ?>
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
            <a class="dashboard" href="formSearchResult.php?searchByForm=searchByForm&formName=<?php echo urlencode($formName); ?>">Back to Search Results</a>
        </form>
    </div>

    <script>
        document.getElementById("editForm").addEventListener("submit", function(event) {
            event.preventDefault();
            document.getElementById("successMessage").style.display = "block";
            window.scrollTo({ top: 0, behavior: "smooth" });
            setTimeout(() => {
                event.target.submit();
            }, 500);
        });

        <?php if ($updateSuccess): ?>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("successMessage").style.display = "block";
            setTimeout(function() {
                document.getElementById("successMessage").style.display = "none";
            }, 3000);
        });
        <?php endif; ?>
    </script>

</body>
</html>