<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$success = false;
$deleteSuccess = isset($_GET['updateSuccess']);

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

if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

$volunteerList = [];
$itemsPerPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

$sortColumn = $_GET['sort'] ?? 'lastName';
$sortOrder = $_GET['order'] ?? 'asc';
$totalVolunteers = count_all_volunteers();
$totalPages = ceil($totalVolunteers / $itemsPerPage);

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
    <?php require_once('universal.inc') ?>
    <title>Search Volunteer Account</title>
    <link rel="stylesheet" href="css/base.css">
    <style>
        .general tbody tr:hover {
            background-color: #cccccc;
        }
    </style>
</head>
<body>
<?php require_once('header.php') ?>

<h1>Modify Volunteer Account</h1>

<?php if ($deleteSuccess): ?>
    <p style="color: green; text-align: center;">Volunteer Account Updated Successfully.</p>
<?php endif; ?>

<form method="POST">
    <label>Enter first and last name to filter volunteer accounts:</label>
    <div class="search-container">
        <div class="search-label">
            <label>First Name:</label>
        </div>
        <div>
            <input type="text" name="first-name">
        </div>
        <div class="search-label">
            <label>Last Name:</label>
        </div>
        <div>
            <input type="text" name="last-name">
        </div>
        <button type="submit" class="button_style">Search</button>
    </div>
</form>

<h3>Account Summary</h3>
<div class="table-wrapper">
    <table class="general">
        <thead>
            <tr>
                <th><a style="color: white;" href="?sort=firstName&order=<?= ($sortColumn === 'firstName' && $sortOrder === 'asc') ? 'desc' : 'asc' ?>">Name</a></th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($volunteerList as $v): ?>
                <tr onclick="window.location.href='editVolunteerAccount.php?first-name=<?= urlencode($v->getFirstName()) ?>&last-name=<?= urlencode($v->getLastName()) ?>'" style="cursor: pointer;">
                    <td><?= htmlspecialchars($v->getFirstName() . ' ' . $v->getLastName()) ?></td>
                    <td><?= htmlspecialchars($v->getEmail()) ?></td>
                    <td><?= htmlspecialchars($v->getCellPhone()) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($noResults) && $noResults): ?>
        <p style="color: red; text-align: center; font-weight: bold;">No volunteer found with that name. Please double-check your search input and try again.</p>
    <?php endif; ?>
</div>

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
