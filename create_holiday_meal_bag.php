<?php
require_once("database/dbinfo.php");

global $conn;
if (!$conn) {
    die("Database connection failed.");
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = ""; // Store success/error messages

// Fetch all valid family IDs and names for dropdown
$query = "SELECT id, firstName, lastName FROM dbFamily ORDER BY lastName ASC";
$result = $conn->query($query);
$families = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $familyID = isset($_POST["family_id"]) ? $_POST["family_id"] : null;
    $isNewPerson = ($familyID === "new");

    if ($isNewPerson) {
        $firstName = isset($_POST["first_name"]) ? $_POST["first_name"] : null;
        $lastName = isset($_POST["last_name"]) ? $_POST["last_name"] : null;
        $birthdate = isset($_POST["birthdate"]) ? $_POST["birthdate"] : "2000-01-01"; // Default birthdate

        // Insert new family into dbFamily
        $insertFamily = $conn->prepare("INSERT INTO dbFamily (firstName, lastName, birthdate) VALUES (?, ?, ?)");
        $insertFamily->bind_param("sss", $firstName, $lastName, $birthdate);
        if ($insertFamily->execute()) {
            $familyID = $conn->insert_id; // Get newly created family ID
        } else {
            die("<p class='error'>Error creating new family: " . $insertFamily->error . "</p>");
        }
        $insertFamily->close();
    }

    // Ensure family_id exists before inserting meal bag form
    $checkFamily = $conn->prepare("SELECT id FROM dbFamily WHERE id = ?");
    $checkFamily->bind_param("i", $familyID);
    $checkFamily->execute();
    $checkFamily->store_result();

    if ($checkFamily->num_rows > 0) {
        // Prepare form data
        $email = isset($_POST["email"]) ? $_POST["email"] : '';
        $householdSize = isset($_POST["household_size"]) ? (int)$_POST["household_size"] : 1;
        $mealBag = isset($_POST["meal_bag"]) ? $_POST["meal_bag"] : 'standard';
        $name = isset($_POST["name"]) ? $_POST["name"] : '';
        $address = isset($_POST["address"]) && !empty(trim($_POST["address"])) ? $_POST["address"] : 'Not Provided';
        $phone = isset($_POST["phone"]) && !empty(trim($_POST["phone"])) ? $_POST["phone"] : '';
        $photoRelease = isset($_POST["photo_release"]) ? 1 : 0;
        
        // ✅ Insert into dbHolidayMealBagForm
        $query = "INSERT INTO dbHolidayMealBagForm (family_id, email, household_size, meal_bag, name, address, phone, photo_release) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            die("Database prepare() failed: " . $conn->error);
        }

        $stmt->bind_param("isissssi", $familyID, $email, $householdSize, $mealBag, $name, $address, $phone, $photoRelease);

        if ($stmt->execute()) {
            // Redirect after successful submission
            header("Location: ?success=1");
            exit();
        } else {
            die("<p class='error'>Error: " . $stmt->error . "</p>");
        }

        $stmt->close();
    } else {
        die("<p class='error'>Error: Family ID does not exist. Please select a valid family.</p>");
    }

    $checkFamily->close();
}

// Fetch Last 10 Submitted Forms for Debugging
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
    <title>Create Holiday Meal Bag Form</title>
    <style>
        body {
    background-color: #800020; /* Burgundy */
    color: white;
    font-family: Arial, sans-serif;
    text-align: center;
}

.container {
    max-width: 80%; /* Increased width for a balanced look */
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    color: black;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
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

.success {
    color: green;
    font-weight: bold;
}

.error {
    color: red;
    font-weight: bold;
}

/* ✅ Fix Table Alignment */
.container table {
    width: 100%; /* Ensure the table fills the container */
    margin-top: 20px;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid black;
    text-align: center;
}

/* ✅ Fix Header Row Colors */
th {
    background-color: #800020; /* Burgundy */
    color: white;
}

/* ✅ Fix Alternating Row Colors */
tr:nth-child(even) {
    background-color: #f5f5f5; /* Light gray */
}

tr:nth-child(odd) {
    background-color: white;
}

    </style>
</head>
<body>

<div class="container">
    <h2>Create a New Holiday Meal Bag Form</h2>

    <?php if (isset($_GET['success'])): ?>
        <p class="success">Form submitted successfully!</p>
    <?php endif; ?>

    <form method="POST">
    <label>Family:</label>
    <select name="family_id" required>
        <option value="">-- Select a Family --</option>
        <option value="new">-- New Person --</option>
        <?php foreach ($families as $family): ?>
            <option value="<?= $family['id']; ?>">
                <?= htmlspecialchars($family['firstName'] . " " . $family['lastName']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Address:</label>
    <input type="text" name="address" value="Not Provided">

    <label>Email:</label>
    <input type="email" name="email">

    <label>Household Size:</label>
    <input type="number" name="household_size" min="1">

    <label>Meal Bag Type:</label>
    <select name="meal_bag">
        <option value="standard">Standard</option>
        <option value="vegetarian">Vegetarian</option>
        <option value="halal">Halal</option>
    </select>

    <label>Contact Name:</label>
    <input type="text" name="name">

    <label>Phone:</label>
    <input type="tel" name="phone">

    <label>
        <input type="checkbox" name="photo_release">
        Photo Release Permission
    </label>

    <button type="submit">Submit Form</button>
</form>

</div>

<!-- Display the last 10 submitted forms -->
<div class="container">
    <h3>Last 10 Submitted Forms</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Family Name</th>
            <th>Email</th>
            <th>Household Size</th>
            <th>Meal Bag</th>
            <th>Contact Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Photo Release</th>
        </tr>
        <?php foreach ($forms as $form): ?>
            <tr>
                <td><?= htmlspecialchars($form["id"]); ?></td>
                <td><?= htmlspecialchars($form["family_name"]); ?></td>
                <td><?= htmlspecialchars($form["email"]); ?></td>
                <td><?= htmlspecialchars($form["household_size"]); ?></td>
                <td><?= htmlspecialchars($form["meal_bag"]); ?></td>
                <td><?= htmlspecialchars($form["name"]); ?></td>
                <td><?= htmlspecialchars($form["address"]); ?></td>
                <td><?= htmlspecialchars($form["phone"]); ?></td>
                <td><?= $form["photo_release"] ? "Yes" : "No"; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>



</body>
</html>
