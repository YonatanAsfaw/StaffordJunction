<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = false;
$deleteSuccess = isset($_GET['deleteSuccess']);

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

$staffList = [];

$itemsPerPage = 3;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Sorting setup
$sortColumn = $_GET['sort'] ?? 'lastName';
$sortOrder = $_GET['order'] ?? 'asc';
$totalStaff = count_all_staff();
$totalPages = ceil($totalStaff / $itemsPerPage);

// Fetch all staff sorted by last name, paginated
$staffList = retrieve_all_staff_paginated($sortColumn, $sortOrder, $itemsPerPage, $offset);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $args = sanitize($_POST, null);
    $searchFilters = [];

    if (!empty($args['first-name'])) {
        $searchFilters['first_name'] = $args['first-name'];
    }
    if (!empty($args['last-name'])) {
        $searchFilters['last_name'] = $args['last-name'];
    }
    $totalStaff = count_all_staff($searchFilters);
    $totalPages = ceil($totalStaff / $itemsPerPage);

    // Fetch filtered staff list instead of a single staff member
    $staffList = retrieve_all_staff_paginated($sortColumn, $sortOrder, $itemsPerPage, $offset, $searchFilters);

    if (empty($staffList)) {
        $noResults = true;
    }
}
?>

<!DOCTYPE html>
<html>
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
    <?php if ($deleteSuccess): ?>
        <p style="color: green; text-align: center;">Staff Member Removed Successfully.</p>
    <?php endif; ?>
<body>
    <?php require_once('header.php') ?>
    <h1>Search Staff Account</h1>
    <form id="search_form" method="POST">
        <label>Enter first and last name to filter staff accounts:</label>
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
                <input type="text" id="last-name" name='last-name'>
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
                    <tr onclick="window.location.href='staffAccount.php?first-name=<?= urlencode($s->getFirstName()) ?>&last-name=<?= urlencode($s->getLastName()) ?>'" style="cursor: pointer;">
                        <td><?= htmlspecialchars($s->getFirstName() . " " . $s->getLastName()) ?></td>
                        <td><?= htmlspecialchars($s->getBirthdate()) ?></td>
                        <td><?= htmlspecialchars($s->getJobTitle()) ?></td>
                        <td><?= htmlspecialchars($s->getEmail()) ?></td>
                        <td><?= htmlspecialchars($s->getPhone()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
	</table>
        <?php if (isset($noResults) && $noResults): ?>
            <p style="color: red; font-weight: bold; text-align: center;">
                Sorry, no staff member found with that name! Please double-check your search input and try again.
            </p>
        <?php endif; ?>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination" style="text-align: center; margin-top: 10px;">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&sort=<?= urlencode($sortColumn) ?>&order=<?= urlencode($sortOrder) ?>" class="button_style">Previous</a>
        <?php endif; ?>

        <span>Page <?= $page ?> of <?= $totalPages ?></span>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&sort=<?= urlencode($sortColumn) ?>&order=<?= urlencode($sortOrder) ?>" class="button_style">Next</a>
        <?php endif; ?>
    </div>
</body>
</html>
