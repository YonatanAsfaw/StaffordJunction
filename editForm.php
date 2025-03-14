<?php
session_start();
require_once("database/dbHolidayMealBag.php");
require_once("database/dbForms.php");

// Ensure the user is logged in
if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    die();
}

// Get submission ID and form name
if (!isset($_GET['id']) || !isset($_GET['formName'])) {
    die("Invalid request.");
}

$submissionId = $_GET['id'];
$formName = $_GET['formName'];

// Get existing data
$formData = getHolidayMealBagSubmissionsById($submissionId);

if (!$formData) {
    die("Form data not found.");
}

// Handle form submission (saving edits)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedData = [
        "household_size" => $_POST["household_size"],
        "meal_bag" => $_POST["meal_bag"],
        "name" => $_POST["name"],
        "address" => $_POST["address"],
        "phone" => $_POST["phone"],
        "photo_release" => $_POST["photo_release"]
    ];

    if (updateHolidayMealBagForm($submissionId, $updatedData)) {
        echo "<script>alert('Form updated successfully!'); window.location.href='formSearchResult.php?searchByForm=searchByForm&formName=$formName';</script>";
        exit;
    } else {
        echo "<p>Error updating form. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Form - <?php echo htmlspecialchars($formName); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f3f0; /* Light beige background */
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
        h2 {
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
            margin-bottom: 10px;
        }
        button, .cancel {
            display: block;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn {
            background-color: #7b1416; /* Burgundy */
            color: white;
            border: none;
        }
        .submit-btn:hover {
            background-color: #580f11; /* Darker Burgundy */
        }
        .cancel {
            background-color: white;
            border: 2px solid #7b1416;
            color: #7b1416;
            text-decoration: none;
            font-weight: bold;
            line-height: 40px;
        }
        .cancel:hover {
            background-color: #7b1416;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit <?php echo htmlspecialchars($formName); ?> Form</h2>
        <form method="post">
            <label>Household Size:</label>
            <input type="text" name="household_size" value="<?php echo htmlspecialchars($formData[0]['household_size']); ?>" required>

            <label>Meal Bag:</label>
            <input type="text" name="meal_bag" value="<?php echo htmlspecialchars($formData[0]['meal_bag']); ?>" required>

            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($formData[0]['name']); ?>" required>

            <label>Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($formData[0]['address']); ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($formData[0]['phone']); ?>" required>

            <label>Photo Release:</label>
            <select name="photo_release">
                <option value="Yes" <?php if($formData[0]['photo_release'] === "Yes") echo "selected"; ?>>Yes</option>
                <option value="No" <?php if($formData[0]['photo_release'] === "No") echo "selected"; ?>>No</option>
            </select>

            <button type="submit" class="submit-btn">Save Changes</button>
            <a class="cancel" href="formSearchResult.php?searchByForm=searchByForm&formName=<?php echo urlencode($formName); ?>">Cancel</a>
        </form>
    </div>

</body>
</html>
