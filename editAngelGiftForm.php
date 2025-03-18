<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once("database/dbAngelGiftForm.php");

// Ensure the user is logged in
if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    die();
}

// Get submission ID
if (!isset($_GET['id']) || !isset($_GET['formName'])) {
    die("Invalid request.");
}

$submissionId = $_GET['id'];
$formName = $_GET['formName'];

// Get existing data
$formData = getAngelGiftFormByChildID($submissionId);

if (!$formData) {
    die("Form data not found.");
}

// Handle form submission (saving edits)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updatedData = [
        "child_id" => $_POST["child_id"], // Ensure child_id is included
        "child_name" => $_POST["child_name"], // Ensure child_name is included
        "gender" => $_POST["gender"],
        "age" => $_POST["age"],
        "email" => $_POST["email"],
        "parent_name" => $_POST["parent_name"],
        "phone" => $_POST["phone"],
        "pants_size" => $_POST["pants_size"] ?? null,
        "shirt_size" => $_POST["shirt_size"] ?? null,
        "shoe_size" => $_POST["shoe_size"] ?? null,
        "coat_size" => $_POST["coat_size"] ?? null,
        "underwear_size" => $_POST["underwear_size"] ?? null,
        "sock_size" => $_POST["sock_size"] ?? null,
        "wants" => $_POST["wants"],
        "interests" => $_POST["interests"],
        "photo_release" => $_POST["photo_release"]
    ];

    // Call update function and handle success
    if (updateAngelGiftForm($updatedData, $submissionId)) {
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
            background-color: #f8f3f0;
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
            border-top: 5px solid #7b1416;
        }
        h2 {
            color: #7b1416;
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
            background-color: #7b1416;
            color: white;
            border: none;
        }
        .submit-btn:hover {
            background-color: #580f11;
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
            <input type="hidden" name="child_id" value="<?php echo htmlspecialchars($formData['child_id']); ?>">
            <input type="hidden" name="child_name" value="<?php echo htmlspecialchars($formData['child_name']); ?>">

            <label>Gender:</label>
            <input type="text" name="gender" value="<?php echo htmlspecialchars($formData['gender']); ?>" required>

            <label>Age:</label>
            <input type="number" name="age" value="<?php echo htmlspecialchars($formData['age']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>

            <label>Parent Name:</label>
            <input type="text" name="parent_name" value="<?php echo htmlspecialchars($formData['parent_name']); ?>" required>

            <label>Phone:</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($formData['phone']); ?>" required>

            <label>Pants Size:</label>
            <input type="text" name="pants_size" value="<?php echo htmlspecialchars($formData['pants_size']); ?>">

            <label>Shirt Size:</label>
            <input type="text" name="shirt_size" value="<?php echo htmlspecialchars($formData['shirt_size']); ?>">

            <label>Shoe Size:</label>
            <input type="text" name="shoe_size" value="<?php echo htmlspecialchars($formData['shoe_size']); ?>">

            <label>Coat Size:</label>
            <input type="text" name="coat_size" value="<?php echo htmlspecialchars($formData['coat_size']); ?>">

            <label>Underwear Size:</label>
            <input type="text" name="underwear_size" value="<?php echo htmlspecialchars($formData['underwear_size']); ?>">

            <label>Sock Size:</label>
            <input type="text" name="sock_size" value="<?php echo htmlspecialchars($formData['sock_size']); ?>">

            <label>Wants:</label>
            <textarea name="wants" required><?php echo htmlspecialchars($formData['wants']); ?></textarea>

            <label>Interests:</label>
            <textarea name="interests" required><?php echo htmlspecialchars($formData['interests']); ?></textarea>

            <label>Photo Release:</label>
            <select name="photo_release">
                <option value="1" <?php if($formData['photo_release'] == 1) echo "selected"; ?>>Yes</option>
                <option value="0" <?php if($formData['photo_release'] == 0) echo "selected"; ?>>No</option>
            </select>

            <button type="submit" class="submit-btn">Save Changes</button>
            <a class="cancel" href="formSearchResult.php?searchByForm=searchByForm&formName=<?php echo urlencode($formName); ?>">Cancel</a>
        </form>
    </div>

</body>
</html>
