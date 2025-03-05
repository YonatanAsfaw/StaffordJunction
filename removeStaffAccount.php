<?php

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = false;

if (isset($_SESSION['_id'])) {
    require_once('include/input-validation.php');
    require_once('database/dbStaff.php');
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    die();
}

// Admin-only access
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

$staff = null;
$deleteSuccess = false;
$staffList = [];

$itemsPerPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Sorting setup
$sortColumn = $_GET['sort'] ?? 'lastName';
$sortOrder = $_GET['order'] ?? 'asc';

// Fetch all staff sorted by last name, paginated
$staffList = retrieve_all_staff_paginated($sortColumn, $sortOrder, $itemsPerPage, $offset);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $args = sanitize($_POST, null);
    if (isset($args['first-name']) && isset($args['last-name'])) {
        $first_name = $args['first-name'];
        $last_name = $args['last-name'];
        $staff = retrieve_staff_by_name($first_name, $last_name);
    }
    if (isset($args['delete']) && $staff) {
        $deleteSuccess = remove_staff_by_name($staff->getFirstName(), $staff->getLastName());
        if ($deleteSuccess) {
            header("Location: removeStaffAccount.php?deleteSuccess=1");
            exit();
        }
    }
} 

?>

<!DOCTYPE html>
<html>
    <?php if (isset($_GET['deleteSuccess'])): ?>
        <p style="color: green;">Staff member successfully removed.</p>
    <?php endif; ?>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?php require_once('universal.inc') ?>
        <title>Search Staff Account</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/base.css">
        <style>
            .general tbody tr:hover {
                background-color: #cccccc; /* Light grey color */
            }
        </style>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Search Staff Account</h1>

        <form id="search_form" method="POST">
            <label>Enter first and last name to directly search for staff account:</label>
            <div class="search-container">
                <div class="search-label">
                    <label>First Name:</label>
                </div>
                <div>
                    <input type="text" id="first-name" name='first-name'>
                </div>
                <div class="search-label">
                    <label>Last Name:</label>
                </div>
                <div>
                    <input type="text" id="last-name" name='last-name' required>
                </div>
                <button type="submit" class="button_style">Search</button>
            </div>
        </form>

        <!-- Account Summary List -->
        <h3>Account Summary</h3>
        <div class="table-wrapper">
            <table class="general">
                <thead>
                    <tr>
                        <th><a href="?sort=firstName&order=<?= ($sortColumn === 'firstName' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Name</a></th>
                        <th>Date of Birth</th>
                        <th><a href="?sort=jobTitle&order=<?= ($sortColumn === 'jobTitle' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Job Title</a></th>
                        <th><a href="?sort=email&order=<?= ($sortColumn === 'email' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Email</a></th>
                        <th><a href="?sort=phone&order=<?= ($sortColumn === 'phone' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Phone</a></th>
                    </tr>
                </thead>
                <tbody class="standout">
                    <?php foreach ($staffList as $s): ?>
                        <tr onclick="window.location.href='removeStaffAccount.php?first-name=<?= urlencode($s->getFirstName()) ?>&last-name=<?= urlencode($s->getLastName()) ?>'" style="cursor: pointer;">
                            <td><?= htmlspecialchars($s->getFirstName() . " " . $s->getLastName()) ?></td>
                            <td><?= htmlspecialchars($s->getBirthdate()) ?></td>
                            <td><?= htmlspecialchars($s->getJobTitle()) ?></td>
                            <td><?= htmlspecialchars($s->getEmail()) ?></td>
                            <td><?= htmlspecialchars($s->getPhone()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>" class="button_style">Previous</a>
            <?php endif; ?>
            <span>Page <?= $page ?></span>
            <?php if (count($staffList) === $itemsPerPage): ?>
                <a href="?page=<?= $page + 1 ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>" class="button_style">Next</a>
            <?php endif; ?>
        </div>

        <!-- Selected Staff Member for Deletion -->
        <?php if ($staff): ?>
            <h3>Staff Account Information</h3>
            <div id="view-staff" style="margin-left: 20px; margin-right: 20px">
                <main class="general">
                    <fieldset>
                        <legend>General Information</legend>
                        <label>Name</label>
                        <p><?= htmlspecialchars($staff->getFirstName() . " " . $staff->getLastName()) ?></p>
                        <label>Date of Birth</label>
                        <p><?= htmlspecialchars($staff->getBirthdate()) ?></p>
                        <label>Address</label>
                        <p><?= htmlspecialchars($staff->getAddress()) ?></p>
                        <label>Phone</label>
                        <p><?= htmlspecialchars($staff->getPhone()) ?></p>
                        <label>Email</label>
                        <p><?= htmlspecialchars($staff->getEmail()) ?></p>
                        <label>Job Title</label>
                        <p><?= htmlspecialchars($staff->getJobTitle()) ?></p>
                    </fieldset>
                </main>
            </div>
            <form method="POST" onsubmit="return confirm('Are you sure you want to remove this staff member?');">
                <input type="hidden" name="first-name" value="<?= htmlspecialchars($staff->getFirstName()) ?>">
                <input type="hidden" name="last-name" value="<?= htmlspecialchars($staff->getLastName()) ?>">
                <button type="submit" name="delete" class="button_style">Remove Account</button>
            </form>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !$deleteSuccess): ?>
            <p style="color: red;">No staff member found with that name.</p>
        <?php elseif ($deleteSuccess): ?>
            <p style="color: green;">Staff member successfully removed.</p>
        <?php endif; ?>

        <a class="button cancel button_style" href="index.php">Return to Dashboard</a>
    </body>
</html>
