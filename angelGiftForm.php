<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('database/dbinfo.php'); // Include dbinfo.php to access the connect() function
require_once('database/dbAngelGiftForm.php'); // Include database functions
require_once('header.php');
require('universal.inc');

// Connect to the database and fetch the children for family_id = 4
$conn = connect();
$query = "SELECT * FROM dbChildren WHERE family_id = 4";
$result = $conn->query($query);

$children = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $children[] = $row;
    }
} else {
    echo "No children found.";
}
$conn->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    // Define required fields
    $required_fields = ["email", "parent_name", "phone", "child_id", "gender", "age", "wants", "interests", "photo_release"];
    $missing_fields = [];

    // Check for missing or empty fields
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $missing_fields[] = $field . " (Not Set)";
        } elseif (trim($_POST[$field]) === "") {
            $missing_fields[] = $field . " (Empty)";
        }
    }

    // Show error message if any required field is missing
    if (!empty($missing_fields)) {
        die("<p style='color:red;'>❌ Error: The following required fields are missing or empty: " . implode(", ", $missing_fields) . "</p>");
    }

    // Ensure `photo_release` is always set
    if (!isset($_POST["photo_release"])) {
        $_POST["photo_release"] = "0"; // Default to "No"
    } else {
        $_POST["photo_release"] = (string) $_POST["photo_release"]; // Convert to string
    }

    // Fetch child_name from database if missing
    if (!isset($_POST["child_name"]) || empty($_POST["child_name"])) {
        $conn = connect();
        $query = "SELECT first_name, last_name FROM dbChildren WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_POST["child_id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $child = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        $_POST["child_name"] = $child ? ($child['first_name'] . " " . $child['last_name']) : "Unknown";
    }

    // Build form array
    $form = [
        "child_id" => $_POST["child_id"],
        "child_name" => $_POST["child_name"],
        "gender" => $_POST["gender"],
        "age" => $_POST["age"],
        "email" => $_POST["email"],
        "parent_name" => $_POST["parent_name"],
        "phone" => $_POST["phone"],
        "pants_size" => $_POST["pants_size"] ?? '',
        "shirt_size" => $_POST["shirt_size"] ?? '',
        "shoe_size" => $_POST["shoe_size"] ?? '',
        "coat_size" => $_POST["coat_size"] ?? '',
        "underwear_size" => $_POST["underwear_size"] ?? '',
        "sock_size" => $_POST["sock_size"] ?? '',
        "wants" => $_POST["wants"],
        "interests" => $_POST["interests"],
        "photo_release" => $_POST["photo_release"]
    ];

    // Debugging log before submission
    error_log("DEBUG: Form Data Before Insertion: " . json_encode($form));

    // Submit form to database
    $success = createAngelGiftForm($form);

    if ($success) {
        error_log("DEBUG: Form successfully saved!");
        echo "<p style='color: green;'>✅ Form submitted successfully!</p>";
    } else {
        error_log("ERROR: Form was not saved!");
        echo "<p style='color: red;'>❌ Error: Form was not saved.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Angel Gift Form</title>
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #800020;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .form-container {
            background: white;
            color: #800020;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: 20px auto;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.5);
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #800020;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        button {
            background-color: #800020;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #a52a2a;
        }
        .dashboard-button {
            display: block;
            background-color: #ddd;
            color: #800020;
            text-align: center;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            width: 100%;
            margin-top: 15px;
        }
    </style> -->
</head>
<body>
<h2>Angel Gifts Wish Form</h2>
<div class="form-container">
    <form method="POST">
        <label>Email*</label>
        <input type="email" name="email" required>

        <label>Parent Name*</label>
        <input type="text" name="parent_name" required>

        <label>Phone*</label>
        <input type="tel" name="phone" required>

        <label>Child Name*</label>
        <select name="child_id" required>
            <option disabled selected>Select a child</option>
            <?php 
                foreach ($children as $child) {
                    echo '<option value="' . htmlspecialchars($child['id']) . '">';
                    echo htmlspecialchars($child['first_name'] . " " . $child['last_name']);
                    echo '</option>';
                }
            ?>
        </select>

        <label>Gender*</label>
        <select name="gender" required>
            <option disabled selected>Select gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label>Age*</label>
        <input type="number" name="age" required>
        
        <label>Pants Size</label>
        <input type="text" name="pants_size">

        <label>Shirt Size</label>
        <input type="text" name="shirt_size">

        <label>Shoe Size</label>
        <input type="number" name="shoe_size" min="0">

        <label>Coat Size</label>
        <input type="text" name="coat_size">

        <label>Underwear Size</label>
        <input type="text" name="underwear_size">

        <label>Sock Size</label>
        <input type="text" name="sock_size">


        <label>Wants*</label>
        <textarea name="wants" required></textarea>

        <label>Interests*</label>
        <textarea name="interests" required></textarea>

        <label>Photo Release*</label>
        <div class="radio-group">
            <label><input type="radio" name="photo_release" value="1" required> Yes</label>
            <label><input type="radio" name="photo_release" value="0" required> No</label>
        </div>

        <button type="submit">Submit</button>
        <a class="button cancel" href="index.php">Return to Dashboard</a>
    </form>
</div>

</body>
</html>
