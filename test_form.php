<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log the raw POST data
file_put_contents('post_log.txt', print_r($_POST, true));

// Output the POST data
echo "<pre>POST data: ";
print_r($_POST);
echo "</pre>";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Form was submitted via POST";
} else {
    echo "Form was not submitted via POST";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Form</title>
</head>
<body>
    <h1>Test Form</h1>
    <form action="" method="post">
        <label for="test_field">Test Field:</label>
        <input type="text" name="test_field" id="test_field" value="Test Value">
        <button type="submit">Submit</button>
    </form>
</body>
</html>