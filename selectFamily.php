<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbFamily.php");

if (!isset($_SESSION["_id"]) || $_SESSION["access_level"] < 2) {
    die("Access Denied. Only admins and staff can select a family.");
}

$families = find_all_families(); // Retrieve all family accounts
$redirectPage = isset($_GET["redirect"]) ? $_GET["redirect"] : "fillForm.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select a Family</title>
    <link rel="stylesheet" href="css/base.css">
</head>
<body>
    <h1>Select a Family</h1>
    
    <form method="GET" action="<?php echo htmlspecialchars($redirectPage); ?>">
        <label for="family_id">Choose a Family:</label>
        <select name="id" id="family_id" required>
            <option value="">-- Select a Family --</option>
            <?php foreach ($families as $family): ?>
                <option value="<?php echo $family->getId(); ?>">
                    <?php echo htmlspecialchars($family->getFirstName() . " " . $family->getLastName()); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Continue</button>
    </form>

    <a class="button cancel" href="index.php">Return to Dashboard</a>
</body>
</html>
