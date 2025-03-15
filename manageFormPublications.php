<?php
session_start();
require_once("database/dbForms.php");

if ($_SESSION['access_level'] < 2) {
    header("Location: index.php");
    exit();
}

// Handle form toggle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formName = $_POST['form_name'];
    toggleFormPublication($formName);
    header("Location: manageFormPublications.php");
    exit();
}

// Fetch all forms with their status
$formStatuses = getAllFormStatuses();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Form Publications</title>
    <?php require('universal.inc'); ?>
</head>
<body>
    <?php require('header.php'); ?>
    <h1>Manage Form Publications</h1>

    <form method="POST">
        <table class="general">
            <thead>
                <tr>
                    <th>Form Name</th>
                    <th>Status</th>
                    <th>Toggle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($formStatuses as $form): ?>
                    <tr>
                        <td><?= htmlspecialchars($form['form_name']); ?></td>
                        <td><?= $form['is_published'] ? 'Published' : 'Not Published'; ?></td>
                        <td>
                            <button type="submit" name="form_name" value="<?= htmlspecialchars($form['form_name']); ?>">
                                <?= $form['is_published'] ? 'Unpublish' : 'Publish'; ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <a class="button cancel" href="index.php">Return to Dashboard</a>
</body>
</html>
