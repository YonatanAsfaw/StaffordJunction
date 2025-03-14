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
    $householdSize = (int)($_POST['household'] ?? 0);
    $mealBag = $_POST['meal_bag'] ?? ""; // Ensure meal_bag is correctly retrieved
    $name = $_POST['name'] ?? "";
    $address = $_POST['address'] ?? "";
    $phone = $_POST['phone'] ?? "";
    $photoRelease = isset($_POST['photo_release']) ? (int)$_POST['photo_release'] : 0; // Defaults to 0 if empty


    // ✅ Debug: Check if `meal_bag` is empty
    if (empty($mealBag)) {
        echo "ERROR: meal_bag is empty!";
        exit(); // Stop execution to debug
    }

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Meal Bag Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f3f0; /* Light background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 450px;
            text-align: center;
            border-top: 5px solid #7b1416; /* Burgundy Top Border */
        }
        h1 {
            color: #7b1416; /* Burgundy */
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
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* New Button-Based Selection */
        .choice-group {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .choice-group button {
            background-color: lightgray;
            border: 2px solid #7b1416; /* Burgundy border */
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
            color: #333;
        }
        .choice-group button:hover {
            background-color: #e0e0e0; /* Slightly darker on hover */
        }
        .choice-group button.active {
            background-color: #7b1416; /* Burgundy when selected */
            color: white;
        }

        button {
            background-color: #7b1416; /* Burgundy */
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #580f11; /* Darker Burgundy */
        }
        .cancel {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #7b1416; /* Burgundy */
            font-weight: bold;
        }
        .cancel:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Holiday Meal Bag Form</h1>

        <?php if (!empty($errors)): ?>
            <h3 style="color: red;">Please correct the following errors:</h3>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <h3 style="color: green;"><?php echo htmlspecialchars($successMessage); ?></h3>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($data['email'] ?? $family->getEmail()); ?>">

            <label for="household">Household Size *</label>
            <input type="number" id="household" name="household" required value="<?php echo htmlspecialchars($data['household_size'] ?? 1); ?>">

            <!-- New Selection Buttons -->
            <label>Would you like a Holiday Meal Bag? *</label>
            <div class="choice-group">
                <button type="button" onclick="selectMeal('Thanksgiving')" id="btn-thanksgiving">Thanksgiving</button>
                <button type="button" onclick="selectMeal('Christmas')" id="btn-christmas">Christmas</button>
                <button type="button" onclick="selectMeal('Both')" id="btn-both">Both</button>
            </div>
            <input type="hidden" id="meal_bag" name="meal_bag" value="">

            <button type="submit">Submit</button>
            <a class="cancel" href="<?php echo $isAdmin ? 'index.php' : 'familyAccountDashboard.php'; ?>">Return to Dashboard</a>
        </form>
    </div>

    <script>
        function selectMeal(choice) {
            document.getElementById("meal_bag").value = choice;

            document.getElementById("btn-thanksgiving").classList.remove("active");
            document.getElementById("btn-christmas").classList.remove("active");
            document.getElementById("btn-both").classList.remove("active");

            document.getElementById("btn-" + choice.toLowerCase()).classList.add("active");
        }
    </script>
</body>
</html>
