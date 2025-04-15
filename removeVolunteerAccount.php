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
    require_once('database/dbVolunteers.php');
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

$volunteerList = [];

$itemsPerPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Sorting setup
$sortColumn = $_GET['sort'] ?? 'lastName';
$sortOrder = $_GET['order'] ?? 'asc';
$totalVolunteers = count_all_volunteers();
$totalPages = ceil($totalVolunteers / $itemsPerPage);

// Fetch all volunteers sorted and paginated
$volunteerList = retrieve_all_volunteers_paginated($sortColumn, $sortOrder, $itemsPerPage, $offset);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $args = sanitize($_POST, null);
    $searchFilters = [];

    if (!empty($args['first-name'])) {
        $searchFilters['first_name'] = $args['first-name'];
    }
    if (!empty($args['last-name'])) {
        $searchFilters['last_name'] = $args['last-name'];
    }
    $totalVolunteers = count_all_volunteers($searchFilters);
    $totalPages = ceil($totalVolunteers / $itemsPerPage);

    // Fetch filtered volunteer list
    $volunteerList = retrieve_all_volunteers_paginated($sortColumn, $sortOrder, $itemsPerPage, $offset, $searchFilters);

    if (empty($volunteerList)) {
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
    <title>Search Volunteer Account</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/base.css">
    <style>
        .general tbody tr:hover {
            background-color: #cccccc;
	}
        .general thead a {
            color: white;
            text-decoration: none;
        }

        .general thead a:hover {
            text-decoration: underline;
            color: #ddd; /* optional hover effect */
        }
    </style>
</head>
<body>
    <?php require_once('header.php') ?>
    <h1>Remove Volunteer Account</h1>
    <form id="search_form" method="POST">
        <label>Enter first and last name to filter volunteer accounts:</label>
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

    <h3>Account Summary</h3>
    <div class="table-wrapper">
        <table class="general">
            <thead>
                <tr>
                    <th><a href="?sort=firstName&order=<?= ($sortColumn === 'firstName' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Name</a></th>
                    <th>Date of Birth</th>
		    <th><a href="?sort=email&order=<?= ($sortColumn === 'email' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Email</a></th>
                    <th><a href="?sort=phone&order=<?= ($sortColumn === 'phone' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Phone</a></th>
                </tr>
            </thead>
            <tbody class="standout">
                <?php foreach ($volunteerList as $v): ?>
                    <tr onclick="window.location.href='volunteerAccount.php?first-name=<?= urlencode($v->getFirstName()) ?>&last-name=<?= urlencode($v->getLastName()) ?>'" style="cursor: pointer;">
                        <td><?= htmlspecialchars($v->getFirstName() . " " . $v->getLastName()) ?></td>
                        <td><?= htmlspecialchars($v->getBirthdate()) ?></td>
			<td><?= htmlspecialchars($v->getEmail()) ?></td>
                        <td><?= htmlspecialchars($v->getCellPhone()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
	</table>
        <?php if (isset($noResults) && $noResults): ?>
            <p style="color: red; font-weight: bold; text-align: center;">
                No volunteer found with that name. Please try again.
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
