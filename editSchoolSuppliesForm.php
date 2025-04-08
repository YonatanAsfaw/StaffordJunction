<?php
session_start();
require_once("database/dbSchoolSuppliesForm.php");
require_once("database/dbForms.php");
require('universal.inc');
require_once('header.php');

// Ensure the user is logged in
if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    die();
}

// Check if user is an admin
$isAdmin = ($_SESSION['access_level'] > 1);

// Get submission ID and form name
if (!isset($_GET['id']) || !isset($_GET['formName'])) {
    die("Invalid request.");
}

$submissionId = $_GET['id'];
$formName = $_GET['formName'];

// Get existing data
$formData = getSchoolSuppliesFormById($submissionId);

if (!$formData) {
    die("Form data not found.");
}

// ✅ Ensure only the Admin or the original form submitter can edit
if (!$isAdmin && $_SESSION['_id'] != $formData['user_id']) {
    die("You do not have permission to edit this form.");
}

// Handle form submission (saving edits)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedData = [
        // "household_size" => $_POST["household_size"],
        "child_name" => $_POST["name"],
        "grade" => $_POST["grade"],
        "school" => $_POST["school"],
        "bag_pickup_method" => $_POST["community"],
        "need_backpack" => $_POST["need_backpack"]
    ];

    if (updateSchoolSuppliesForm($submissionId, $updatedData)) {
        echo "<script>alert('Form updated successfully!'); window.location.href='formSearchResult.php?searchByForm=searchByForm&formName=$formName';</script>";
        exit;
    } else {
        echo "<p>Error updating form. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Form - <?php echo htmlspecialchars($formName); ?></title>
</head>
<body>
    <h2>Edit <?php echo htmlspecialchars($formName); ?> Form</h2>
    <form method="post">
        <label>Child Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($formData['child_name']); ?>" required><br>

        <label>Grade:</label>
        <input type="text" name="grade" value="<?php echo htmlspecialchars($formData['grade']); ?>" required><br>

        <label>School:</label>
        <input type="text" name="school" value="<?php echo htmlspecialchars($formData['school']); ?>" required><br>

        <label>Community Bag Pickup Method:</label>
        <input type="text" name="community" value="<?php echo htmlspecialchars($formData['bag_pickup_method']); ?>" required><br>

        <label>Need Backpack:</label>
        <select name="need_backpack">
            <option value="1" <?php if($formData['need_backpack'] == "1") echo "selected"; ?>>Yes</option>
            <option value="0" <?php if($formData['need_backpack'] == "0") echo "selected"; ?>>No</option>
        </select><br>



        <a href="formSearchResult.php?searchByForm=searchByForm&formName=<?php echo urlencode($formName); ?>">Cancel</a>
    </form>
</body>
</html>
