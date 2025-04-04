<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbSpringBreakCampForm.php");
require_once('header.php');

if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("Missing submission ID.");
}

$id = intval($_GET['id']);
$formData = getSpringBreakById($id);

if (!$formData) {
    die("Spring Break Camp form not found.");
}

$updateSuccess = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedData = [
        "email" => $_POST["email"],
        "student_name" => $_POST["student_name"],
        "school_choice" => $_POST["school_choice"],
        "isAttending" => isset($_POST["isAttending"]) ? 1 : 0,
        "waiver_completed" => isset($_POST["waiver_completed"]) ? 1 : 0,
        "notes" => $_POST["notes"]
    ];

    if (updateSpringBreakCampForm($id, $updatedData)) {
        $updateSuccess = true;
        $formData = getSpringBreakById($id); // Refresh after update
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<?php require('universal.inc'); ?>
    <title>Edit Spring Break Camp Form</title>
    <!-- <style>
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
} -->
    <!-- </style> -->
</head>
<body>
    <div class="container">
        <h2>Edit Spring Break Camp Form</h2>

        <?php if ($updateSuccess): ?>
            <div class="success-message">Update Successful!</div>
        <?php endif; ?>

        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>

            <label>Student Name:</label>
            <input type="text" name="student_name" value="<?= htmlspecialchars($formData['student_name']) ?>" required>

            <label>School Choice:</label>
            <input type="text" name="school_choice" value="<?= htmlspecialchars($formData['school_choice']) ?>" required>

            <label>
                <input type="checkbox" name="isAttending" <?= $formData['isAttending'] ? 'checked' : '' ?>>
                Attending?
            </label>

            <label>
                <input type="checkbox" name="waiver_completed" <?= $formData['waiver_completed'] ? 'checked' : '' ?>>
                Waiver Completed?
            </label>

            <label>Notes:</label>
            <textarea name="notes"><?= htmlspecialchars($formData['notes']) ?></textarea>

            <button type="submit" class="submit-btn">Save Changes</button>

            <a class="button cancel button_style" href="index.php">Return to Dashboard</a>

        </form>
    </div>
</body>
</html>
