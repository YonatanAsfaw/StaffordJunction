<?php

if (session_status() == PHP_SESSION_NONE) {
    session_cache_expire(30);
    session_start();
}

ini_set("display_errors", 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('database/dbChildren.php');
require_once('database/dbFamily.php');
require_once('database/dbChildCareWaiverForm.php');
require_once('header.php');
require('universal.inc');

if (!isset($_SESSION['_id']) || empty($_SESSION['_id'])) {
    die("ERROR: No User ID Found. Please log in.");
}

$accessLevel = $_SESSION['access_level'];
$userID = $_SESSION['_id'];

$children = [];
$family = null;

if ($accessLevel > 1) {
    // Admin can access all children
    $children = retrieve_all_children();
} else {
    // Regular user - get only their family's children
    if (!is_numeric($userID)) {
        die("ERROR: Invalid Family ID. Expected an integer but received: " . $userID);
    }
    $family_id = (int) $userID;
    

    $children = retrieve_children_by_family_id($family_id);
    $family = retrieve_family_by_id($family_id);
}

// Pre-fill family data if user is not an admin
$guardian_email = $family ? $family->getEmail() : '';
$guardian_fname = $family ? $family->getFirstName() : '';
$guardian_lname = $family ? $family->getLastName() : '';

$child_id = isset($_GET['child_id']) ? $_GET['child_id'] : null;
$existingForm = $child_id ? getChildCareWaiverData($child_id) : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once("universal.inc"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Care Waiver Form</title>

    <!-- <style>
        .message-box {
            text-align: center;
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, select {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 15px;
            padding: 10px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .return-button {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .return-button:hover {
            background-color: #218838;
        }
    </style> -->
</head>
<body>

<div class="container">
    <h1>Child Care Waiver Form</h1>

    <!-- ✅ Success or Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="message-box success">
            <?= $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="message-box error">
            <?= $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form id="childCareWaiverForm" action="submitChildCareWaiver.php" method="post">
        <input type="hidden" name="form_id" value="<?= $existingForm['id'] ?? '' ?>">

        <label for="name">Child Name *</label>
        <select name="name" id="name" required>
            <option disabled selected>Select a child</option>
            <?php
            if (!empty($children)) {
                foreach ($children as $c) {
                    $id = $c['id'];
                    $name = $c['first_name'] . " " . $c['last_name'];
                    $value = $id . "_" . $name;
                    echo "<option value='$value'>$name</option>";
                }
            } else {
                echo "<option disabled>No children found</option>";
            }
            ?>
        </select>

        <label for="child_dob">Date of Birth *</label>
        <input type="date" name="child_dob" id="child_dob" value="<?= $existingForm['birth_date'] ?? '' ?>" required>

        <label for="child_gender">Gender *</label>
        <input type="text" name="child_gender" id="child_gender" value="<?= $existingForm['gender'] ?? '' ?>" required>

        <label for="child_address">Address *</label>
        <input type="text" name="child_address" id="child_address" value="<?= $existingForm['child_address'] ?? '' ?>" required>

        <label for="child_city">City *</label>
        <input type="text" name="child_city" id="child_city" value="<?= $existingForm['child_city'] ?? '' ?>" required>

        <label for="child_state">State *</label>
        <input type="text" name="child_state" id="child_state" value="<?= $existingForm['child_state'] ?? '' ?>" required>

        <label for="child_zip">Zip *</label>
        <input type="text" name="child_zip" id="child_zip" value="<?= $existingForm['child_zip'] ?? '' ?>" required>

        <h3>Parent 1 Information</h3>
        <label for="parent1_first_name">First Name *</label>
        <input type="text" name="parent1_first_name" value="<?= $existingForm['parent1_first_name'] ?? '' ?>" required>

        <label for="parent1_last_name">Last Name *</label>
        <input type="text" name="parent1_last_name" value="<?= $existingForm['parent1_last_name'] ?? '' ?>" required>

        <label for="parent1_email">Email *</label>
        <input type="email" name="parent1_email" value="<?= $existingForm['parent1_email'] ?? '' ?>" required>

        <label for="parent1_address">Parent 1 Address *</label>
        <input type="text" name="parent1_address" value="<?= $existingForm['parent1_address'] ?? '' ?>" required>

        <label for="parent1_city">Parent 1 City *</label>
        <input type="text" name="parent1_city" value="<?= $existingForm['parent1_city'] ?? '' ?>" required>

        <label for="parent1_state">Parent 1 State *</label>
        <input type="text" name="parent1_state" value="<?= $existingForm['parent1_state'] ?? '' ?>" required>

        <label for="parent1_zip_code">Parent 1 Zip Code *</label>
        <input type="text" name="parent1_zip_code" value="<?= $existingForm['parent1_zip_code'] ?? '' ?>" required>
        
        <label for="parent1_email">Parent 1 Email *</label>
        <input type="email" name="parent1_email" value="<?= $existingForm['parent1_email'] ?? '' ?>" required>
        
        <label for="parent1_cell_phone">Parent 1 Cell Phone *</label>
        <input type="text" name="parent1_cell_phone" value="<?= $existingForm['parent1_cell_phone'] ?? '' ?>" required>

        <label for="parent1_home_phone">Parent 1 Home Phone *</label>
        <input type="text" name="parent1_home_phone" value="<?= $existingForm['parent1_home_phone'] ?? '' ?>" required>

        <label for="parent1_work_phone">Parent 1 Work Phone *</label>
        <input type="text" name="parent1_work_phone" value="<?= $existingForm['parent1_work_phone'] ?? '' ?>" required>

        <h3>Parent 2 Information</h3>
        <label for="parent2_first_name">First Name</label>
        <input type="text" name="parent2_first_name" value="<?= $existingForm['parent2_first_name'] ?? '' ?>">

        <label for="parent2_last_name">Last Name</label>
        <input type="text" name="parent2_last_name" value="<?= $existingForm['parent2_last_name'] ?? '' ?>">

        <label for="parent2_address">Parent 2 Address</label>
        <input type="text" name="parent2_address" value="<?= $existingForm['parent2_address'] ?? '' ?>">

        <label for="parent2_city">Parent 2 City</label>
        <input type="text" name="parent2_city" value="<?= $existingForm['parent2_city'] ?? '' ?>">

        <label for="parent2_state">Parent 2 State</label>
        <input type="text" name="parent2_state" value="<?= $existingForm['parent2_state'] ?? '' ?>">

        <label for="parent2_zip_code">Parent 2 Zip Code</label>
        <input type="text" name="parent2_zip_code" value="<?= $existingForm['parent2_zip_code'] ?? '' ?>">

        <label for="parent2_email">Parent 2 Email</label>
        <input type="email" name="parent2_email" value="<?= $existingForm['parent2_email'] ?? '' ?>">

        <label for="parent2_cell_phone">Parent 2 Cell Phone</label>
        <input type="text" name="parent2_cell_phone" value="<?= $existingForm['parent2_cell_phone'] ?? '' ?>">

        <label for="parent2_home_phone">Parent 2 Home Phone</label>
        <input type="text" name="parent2_home_phone" value="<?= $existingForm['parent2_home_phone'] ?? '' ?>">

        <label for="parent2_work_phone">Parent 2 Work Phone</label>
        <input type="text" name="parent2_work_phone" value="<?= $existingForm['parent2_work_phone'] ?? '' ?>">

        <label for="guardian_signature">Guardian Signature *</label>
        <input type="text" name="guardian_signature" value="<?= $existingForm['parent_guardian_signature'] ?? '' ?>" required>

        <label for="signature_date">Signature Date *</label>
        <input type="date" name="signature_date" value="<?= $existingForm['signature_date'] ?? '' ?>" required>

        <button type="submit">Submit</button>
    </form>

    <a class="button cancel" href="index.php">Return to Dashboard</a>
</div>
