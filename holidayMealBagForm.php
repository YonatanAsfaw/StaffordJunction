<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbinfo.php");
require_once("database/dbFamily.php");
require_once("database/dbHolidayMealBag.php");

// Ensure the user is logged in
if (!isset($_SESSION['_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
$isAdmin = ($_SESSION['access_level'] > 1);

// Get family ID (Admin selects a family, Families use their own ID)
$familyID = $_GET['id'] ?? ($_SESSION['account_type'] === "admin" ? null : $_SESSION['_id']);
if (!$familyID) {
    die("ERROR: No family ID provided.");
}

// Retrieve family data
$family = retrieve_family_by_id($familyID);
if (!$family) {
    die("ERROR: No family found with ID: " . htmlspecialchars($familyID));
}

// Retrieve existing Holiday Meal Bag form data (if any)
$data = get_data_by_family_id($familyID);

// Success/Error messages
$successMessage = "";
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? "";
    $householdSize = (int)($_POST['household_size'] ?? 1);
    $mealBag = $_POST['meal_bag'] ?? "";
    $name = !empty($_POST['name']) ? $_POST['name'] : "Unknown";
    $address = !empty($_POST['address']) ? $_POST['address'] : "Not Provided";
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : "0000000000";    
    $photoRelease = isset($_POST['photo_release']) ? (int)$_POST['photo_release'] : 0; // Defaults to 0 if empty

    // Validate input
    if (empty($email) || empty($householdSize) || empty($mealBag)) {
        $errors[] = "All fields are required.";
    }

    // If no errors, insert or update the form
    if (empty($errors)) {
        if ($data) {
            // Update existing form
            $updateData = [
                "household_size" => $householdSize,
                "meal_bag" => $mealBag,
                "name" => $name,
                "address" => $address,
                "phone" => $phone,
                "photo_release" => $photoRelease
            ];
            $result = updateHolidayMealBagForm($data['id'], $updateData);
            $successMessage = $result ? "Form updated successfully!" : "Error updating form.";
        } else {
            // Insert new form
            $result = insertHolidayMealBagForm($familyID, $email, $householdSize, $mealBag, $name, $address, $phone, $photoRelease);
            $successMessage = $result ? "Form submitted successfully!" : "Error submitting form.";
        }
    }
}

// Fetch Last 10 Submitted Forms for Display
$query = "SELECT hmb.*, CONCAT(f.firstName, ' ', f.lastName) AS family_name 
          FROM dbHolidayMealBagForm hmb
          INNER JOIN dbFamily f ON f.id = hmb.family_id
          ORDER BY hmb.id DESC LIMIT 10";
$result = $conn->query($query);
$forms = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Meal Bag Form</title>
    <style>
        body {
            background-color: #800020; /* Burgundy */
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            color: black;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-weight: bold;
            text-align: left;
            display: block;
        }

        input, select, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #800020;
            width: 100%;
        }

        button {
            background-color: #800020;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #5a0014;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid black;
            text-align: center;
        }

        th {
            background-color: #800020;
            color: white;
            
        } 
        .dashboard-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #800020; /* Burgundy */
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
}

        .dashboard-btn:hover {
            background-color: #5a0014; /* Darker Burgundy */
}

    </style>
</head>
<body>

<div class="container">
    <h2>Create a New Holiday Meal Bag Form</h2>

    <?php if (!empty($errors)): ?>
        <p class="error"><?= htmlspecialchars(implode(", ", $errors)); ?></p>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <p class="success"><?= htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($data['email'] ?? ""); ?>">

        <label>Household Size:</label>
        <input type="number" name="household_size" required value="<?= htmlspecialchars($data['household_size'] ?? 1); ?>">

        <label>Meal Bag Type:</label>
        <select name="meal_bag" required>
            <option value="standard">Standard</option>
            <option value="vegetarian">Vegetarian</option>
            <option value="halal">Halal</option>
        </select>

        <label>Contact Name:</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($data['name'] ?? ""); ?>">

        <label>Address:</label>
        <input type="text" name="address" required value="<?= htmlspecialchars($data['address'] ?? "Not Provided"); ?>">

        <label>Phone:</label>
        <input type="tel" name="phone" required value="<?= htmlspecialchars($data['phone'] ?? ""); ?>">

        <label>
            <input type="checkbox" name="photo_release">
            Photo Release Permission
        </label>

        <button type="submit">Submit Form</button>
        <div style="text-align: center; margin-top: 15px;">
        <a href="index.php" class="dashboard-btn">Return to Dashboard</a>
    </div>
    </form>
</div>

<!-- Display last 10 submitted forms -->
<div class="container">
    <h3>Last 10 Submitted Forms</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Family Name</th>
            <th>Email</th>
            <th>Household Size</th>
        </tr>
        <?php foreach ($forms as $form): ?>
            <tr>
                <td><?= htmlspecialchars($form["id"]); ?></td>
                <td><?= htmlspecialchars($form["family_name"]); ?></td>
                <td><?= htmlspecialchars($form["email"]); ?></td>
                <td><?= htmlspecialchars($form["household_size"]); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
