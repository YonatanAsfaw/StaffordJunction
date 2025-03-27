<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbFieldTripWaiverForm.php");

if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("Missing submission ID.");
}

$id = intval($_GET['id']);
$formData = getFieldTripWaiverById($id);

if (!$formData) {
    die("Field Trip Waiver form not found.");
}

$updateSuccess = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedData = [
        "parent_email" => $_POST["parent_email"],
        "child_name" => $_POST["child_name"],
        "child_school" => $_POST["child_school"],
        "child_neighborhood" => $_POST["child_neighborhood"],
        "photo_waiver_signature" => $_POST["photo_waiver_signature"],
        "photo_waiver_date" => $_POST["photo_waiver_date"],
        "notes" => $_POST["notes"]
    ];

    if (updateFieldTripWaiverForm($id, $updatedData)) {
        $updateSuccess = true;
        $formData = getFieldTripWaiverById($id); // Refresh after update
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
            <input type="email" name="parent_email" value="<?= htmlspecialchars($formData['parent_email']) ?>" required>

            <label>Child Name:</label>
            <input type="text" name="child_name" value="<?= htmlspecialchars($formData['child_name']) ?>" required>

            <label>School:</label>
            <input type="text" name="child_school" value="<?= htmlspecialchars($formData['child_school']) ?>" required>

            <label>Neighborhood:</label>
            <input type="text" name="child_neighborhood" value="<?= htmlspecialchars($formData['child_neighborhood']) ?>" required>

            <label>Photo Waiver Signature:</label>
            <input type="text" name="photo_waiver_signature" value="<?= htmlspecialchars($formData['photo_waiver_signature']) ?>" required>

            <label>Photo Waiver Date:</label>
            <input type="date" name="photo_waiver_date" value="<?= htmlspecialchars($formData['photo_waiver_date']) ?>" required>

            <label>Notes:</label>
            <textarea name="notes"><?= htmlspecialchars($formData['notes']) ?></textarea>

            <button type="submit" class="submit-btn">Save Changes</button>

            <a class="dashboard return-btn" href="index.php">Return to Dashboard</a>
        </form>
    </div>
</body>
</html>
