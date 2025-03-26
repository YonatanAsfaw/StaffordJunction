<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbChildCareWaiverForm.php");

// Redirect if not logged in
if (!isset($_SESSION['_id'])) {
    header("Location: login.php");
    exit();
}

// Check for form ID
if (!isset($_GET['form_id']) || !is_numeric($_GET['form_id'])) {
    die("Missing or invalid form ID.");
}

$form_id = (int) $_GET['form_id'];
$form = getChildCareWaiverById($form_id);

if (!$form) {
    die("Form not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Child Care Waiver</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f3f0;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        background: #ffffff;
        margin: 30px auto;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        color: #7b1416;
        margin-bottom: 25px;
    }

    label {
        font-weight: bold;
        margin-top: 10px;
        display: block;
    }

    input, select, textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 12px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 15px;
    }

    button {
        background-color: #7b1416;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }

    button:hover {
        background-color: #580f11;
    }

    .back {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #7b1416;
        text-decoration: none;
        font-size: 16px;
    }

    .back:hover {
        color: #580f11;
    }

    h3 {
        margin-top: 25px;
        color: #7b1416;
    }
</style>

</head>
<body>
<div class="container">
    <h2>Edit Child Care Waiver Form</h2>
    <form method="POST" action="submitChildCareWaiver.php">
        <input type="hidden" name="form_id" value="<?= $form['id'] ?>">
        <input type="hidden" name="name" value="<?= $form['child_id'] . '_' . $form['child_name'] ?>">

        <label>Date of Birth *</label>
        <input type="date" name="child_dob" value="<?= $form['birth_date'] ?>" required>

        <label>Gender *</label>
        <input type="text" name="child_gender" value="<?= $form['gender'] ?>" required>

        <label>Child Address *</label>
        <input type="text" name="child_address" value="<?= $form['child_address'] ?>" required>

        <label>City *</label>
        <input type="text" name="child_city" value="<?= $form['child_city'] ?>" required>

        <label>State *</label>
        <input type="text" name="child_state" value="<?= $form['child_state'] ?>" required>

        <label>Zip *</label>
        <input type="text" name="child_zip" value="<?= $form['child_zip'] ?>" required>

        <h3>Parent 1</h3>
        <label>First Name *</label>
        <input type="text" name="parent1_first_name" value="<?= $form['parent1_first_name'] ?>" required>

        <label>Last Name *</label>
        <input type="text" name="parent1_last_name" value="<?= $form['parent1_last_name'] ?>" required>

        <label>Email *</label>
        <input type="email" name="parent1_email" value="<?= $form['parent1_email'] ?>" required>

        <label>Address *</label>
        <input type="text" name="parent1_address" value="<?= $form['parent1_address'] ?>" required>

        <label>City *</label>
        <input type="text" name="parent1_city" value="<?= $form['parent1_city'] ?>" required>

        <label>State *</label>
        <input type="text" name="parent1_state" value="<?= $form['parent1_state'] ?>" required>

        <label>Zip *</label>
        <input type="text" name="parent1_zip" value="<?= $form['parent1_zip_code'] ?>" required>

        <label>Cell Phone *</label>
        <input type="text" name="parent1_cell_phone" value="<?= $form['parent1_cell_phone'] ?>" required>

        <label>Home Phone *</label>
        <input type="text" name="parent1_home_phone" value="<?= $form['parent1_home_phone'] ?>" required>

        <label>Work Phone *</label>
        <input type="text" name="parent1_work_phone" value="<?= $form['parent1_work_phone'] ?>" required>

        <h3>Parent 2 (Optional)</h3>
        <label>First Name</label>
        <input type="text" name="parent2_first_name" value="<?= $form['parent2_first_name'] ?>">

        <label>Last Name</label>
        <input type="text" name="parent2_last_name" value="<?= $form['parent2_last_name'] ?>">

        <label>Address</label>
        <input type="text" name="parent2_address" value="<?= $form['parent2_address'] ?>">

        <label>City</label>
        <input type="text" name="parent2_city" value="<?= $form['parent2_city'] ?>">

        <label>State</label>
        <input type="text" name="parent2_state" value="<?= $form['parent2_state'] ?>">

        <label>Zip</label>
        <input type="text" name="parent2_zip" value="<?= $form['parent2_zip_code'] ?>">

        <label>Email</label>
        <input type="email" name="parent2_email" value="<?= $form['parent2_email'] ?>">

        <label>Cell Phone</label>
        <input type="text" name="parent2_cell_phone" value="<?= $form['parent2_cell_phone'] ?>">

        <label>Home Phone</label>
        <input type="text" name="parent2_home_phone" value="<?= $form['parent2_home_phone'] ?>">

        <label>Work Phone</label>
        <input type="text" name="parent2_work_phone" value="<?= $form['parent2_work_phone'] ?>">

        <label>Guardian Signature *</label>
        <input type="text" name="guardian_signature" value="<?= $form['parent_guardian_signature'] ?>" required>

        <label>Signature Date *</label>
        <input type="date" name="signature_date" value="<?= $form['signature_date'] ?>" required>

        <button type="submit">Update Form</button>
    </form>

    <a href="formSearch.php" class="back">← Back to Search Results</a>
</div>
</body>
</html>
