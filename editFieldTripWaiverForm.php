<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbFieldTripWaiverForm.php");

if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    exit();
}

$formId = $_GET['form_id'] ?? null;

if ($formId === null || !is_numeric($formId)) {
    die("Invalid or missing form ID.");
}

$formId = intval($formId);
$formData = getFieldTripWaiverById($formId);

if (!$formData) {
    die("Field Trip Waiver form not found.");
}

$updateSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedData = [
        "child_name" => $_POST["child_name"] ?? null,
        "child_gender" => $_POST["child_gender"] ?? null,
        "child_birthdate" => empty($_POST["child_birthdate"]) ? null : $_POST["child_birthdate"],
        "child_neighborhood" => $_POST["child_neighborhood"] ?? null,
        "child_school" => $_POST["child_school"] ?? null,
        "child_address" => $_POST["child_address"] ?? null,
        "child_city" => $_POST["child_city"] ?? null,
        "child_state" => $_POST["child_state"] ?? null,
        "child_zip" => $_POST["child_zip"] ?? null,
        "parent_email" => $_POST["parent_email"] ?? null,
        "emergency_contact_name_1" => $_POST["emergency_contact_name_1"] ?? null,
        "emergency_contact_relationship_1" => $_POST["emergency_contact_relationship_1"] ?? null,
        "emergency_contact_phone_1" => $_POST["emergency_contact_phone_1"] ?? null,
        "emergency_contact_name_2" => $_POST["emergency_contact_name_2"] ?? null,
        "emergency_contact_relationship_2" => $_POST["emergency_contact_relationship_2"] ?? null,
        "emergency_contact_phone_2" => $_POST["emergency_contact_phone_2"] ?? null,
        "insurance_company" => $_POST["insurance_company"] ?? null,
        "policy_number" => $_POST["policy_number"] ?? null,
        "parent_signature" => $_POST["photo_waiver_signature"] ?? null,
        "signature_date" => empty($_POST["photo_waiver_date"]) ? null : $_POST["photo_waiver_date"]
    ];

    if (updateFieldTripWaiverForm($formId, $updatedData)) {
        $updateSuccess = true;
        $formData = getFieldTripWaiverById($formId);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Field Trip Waiver Form</title>
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
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 500px;
            margin-top: 2rem;
        }
        h2 {
            color: #7b1416;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .submit-btn {
            background-color: #7b1416;
            color: white;
            padding: 12px;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #580f11;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .return-btn {
            display: block;
            background-color: #7b1416;
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            padding: 12px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .return-btn:hover {
            background-color: #580f11;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Field Trip Waiver Form</h2>

        <?php if ($updateSuccess): ?>
            <div class="success-message">Update Successful!</div>
        <?php endif; ?>

        <form method="post">
            <label>Parent Email:</label>
            <input type="email" name="parent_email" value="<?= htmlspecialchars($formData['parent_email'] ?? '') ?>" required>

            <label>Child Name:</label>
            <input type="text" name="child_name" value="<?= htmlspecialchars($formData['child_name'] ?? '') ?>" required>

            <label>Child Gender:</label>
            <select name="child_gender" required>
                <option value="">Select Gender</option>
                <option value="male" <?= $formData['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $formData['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                <option value="other" <?= $formData['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
            </select>

            <label>Birthdate:</label>
            <input type="date" name="child_birthdate" value="<?= htmlspecialchars($formData['birth_date'] ?? '') ?>" required>

            <label>School:</label>
            <input type="text" name="child_school" value="<?= htmlspecialchars($formData['school'] ?? '') ?>" required>

            <label>Neighborhood:</label>
            <input type="text" name="child_neighborhood" value="<?= htmlspecialchars($formData['neighborhood'] ?? '') ?>" required>

            <label>Child Address:</label>
            <input type="text" name="child_address" value="<?= htmlspecialchars($formData['child_address'] ?? '') ?>">

            <label>City:</label>
            <input type="text" name="child_city" value="<?= htmlspecialchars($formData['child_city'] ?? '') ?>">

            <label>State:</label>
            <input type="text" name="child_state" value="<?= htmlspecialchars($formData['child_state'] ?? '') ?>">

            <label>ZIP:</label>
            <input type="text" name="child_zip" value="<?= htmlspecialchars($formData['child_zip'] ?? '') ?>">

            <label>Emergency Contact 1 Name:</label>
            <input type="text" name="emergency_contact_name_1" value="<?= htmlspecialchars($formData['emgcy_contact_name_1'] ?? '') ?>">

            <label>Relationship:</label>
            <input type="text" name="emergency_contact_relationship_1" value="<?= htmlspecialchars($formData['emgcy_contact1_rship'] ?? '') ?>">

            <label>Phone:</label>
            <input type="text" name="emergency_contact_phone_1" value="<?= htmlspecialchars($formData['emgcy_contact1_phone'] ?? '') ?>">

            <label>Emergency Contact 2 Name:</label>
            <input type="text" name="emergency_contact_name_2" value="<?= htmlspecialchars($formData['emgcy_contact_name_2'] ?? '') ?>">

            <label>Relationship:</label>
            <input type="text" name="emergency_contact_relationship_2" value="<?= htmlspecialchars($formData['emgcy_contact2_rship'] ?? '') ?>">

            <label>Phone:</label>
            <input type="text" name="emergency_contact_phone_2" value="<?= htmlspecialchars($formData['emgcy_contact2_phone'] ?? '') ?>">

            <label>Insurance Company:</label>
            <input type="text" name="insurance_company" value="<?= htmlspecialchars($formData['medical_insurance_company'] ?? '') ?>">

            <label>Policy Number:</label>
            <input type="text" name="policy_number" value="<?= htmlspecialchars($formData['policy_number'] ?? '') ?>">

            <label>Photo Waiver Signature:</label>
            <input type="text" name="photo_waiver_signature" value="<?= htmlspecialchars($formData['photo_waiver_signature'] ?? '') ?>" required>

            <label>Photo Waiver Date:</label>
            <input type="date" name="photo_waiver_date" value="<?= htmlspecialchars($formData['photo_waiver_date'] ?? '') ?>" required>

            <button type="submit" class="submit-btn">Save Changes</button>
            

            <a class="return-btn" href="index.php">Return to Dashboard</a>
        </form>
    </div>
</body>
</html>
