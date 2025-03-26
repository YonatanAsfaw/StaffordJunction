<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<p>This page processes form submissions and should not be accessed directly.</p>";
    echo "<p>Please <a href='childCareWaiverForm.php'>return to the form</a> and submit it properly.</p>";
    exit();
}

// Validate Child Name field
if (!isset($_POST['name']) || empty($_POST['name'])) {
    die("<pre>ERROR: 'name' field is missing or empty in form submission.</pre>");
}

// Ensure dbChildCareWaiverForm.php exists and can be loaded
require_once(__DIR__ . "/database/dbChildCareWaiverForm.php");

// Process form submission
$result = createOrUpdateChildCareForm($_POST);

if ($result) {
    // ✅ Redirect back to the form search result page for Child Care Waiver
    header("Location: formSearchResult.php?formName=Child%20Care%20Waiver%20Form&searchByForm=1");
    exit();
} else {
    echo "<pre>Form submission failed. Please check errors.</pre>";
    exit();
}
?>
