<?php

session_cache_expire(30);
session_start();

$loggedIn = false;
$accessLevel = 0;
$userID = null;

// Search criteria variables
$first_name = null;
$last_name = null;
$gender = null;
$city = null;
$address = null;
$neighborhood = null;
$school = null;
$grade = null;
$income = null;
$is_hispanic = null;
$race = null;

require_once("database/dbFamily.php");
require_once("domain/Family.php");
require_once("database/dbChildren.php");
require_once("domain/Children.php");
include_once('database/dbChildren.php');

// Example usage for GET (optional)
$first_name = $_GET['first_name'] ?? null;
$last_name = $_GET['last_name'] ?? null;
$gender = $_GET['gender'] ?? null;
$school = $_GET['school'] ?? null;
$grade = $_GET['grade'] ?? null;
$is_hispanic = isset($_GET['is_hispanic']) ? (int)$_GET['is_hispanic'] : null;
$race = $_GET['race'] ?? null;

$children = find_children($first_name, $last_name, $gender, $school, $grade, $is_hispanic, $race, $address, $city, $neighborhood, $income);

if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once("include/input-validation.php");

    $args = sanitize($_POST, null);

    if (isset($args['first-name'])) {
        $first_name = $args['first-name'];
    }
    if (isset($args['last-name'])) {
        $last_name = $args['last-name'];
    }
    if (isset($args['address'])) {
        $address = $args['address'];
    }
    if (isset($args['city'])) {
        $city = $args['city'];
    }
    if (isset($args['neighborhood'])) {
        $neighborhood = $args['neighborhood'];
    }
    if (isset($args['school'])) {
        $school = $args['school'];
    }
    if (isset($args['grade'])) {
        $grade = $args['grade'];
    }
    if (isset($args['gender'])) {
        $gender = $args['gender'];
    }
    if (isset($args['income'])) {
        $income = $args['income'];
    }
    if (isset($args['is-hispanic'])) {
        $is_hispanic = $args['is-hispanic'];
    }
    if (isset($args['race'])) {
        $race = $args['race'];
    }

    $children = find_children($first_name, $last_name, $gender, $school, $grade, $is_hispanic, $race, $address, $city, $neighborhood, $income);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php require_once('universal.inc') ?>
    <title>Stafford Junction | Find Family Account</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .general a {
            color: white;
            text-decoration: none;
        }
        .general tbody tr:hover {
            background-color: #cccccc;
        }
    </style>
</head>
<body>
    <?php require_once('header.php') ?>
    <h1>Search Children</h1>

    <form id="formatted_form" method="POST">
        <label>Select any criteria to search for or fill none to view all children</label>

        <div class="search-container">
            <div class="search-label"><label>First Name:</label></div>
            <div><input type="text" id="first-name" name="first-name"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>Last Name:</label></div>
            <div><input type="text" id="last-name" name="last-name"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>Gender:</label></div>
            <div>
                <select name="gender" id="gender">
                    <option value="">--Select--</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>Grade:</label></div>
            <div><input type="text" id="grade" name="grade"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>Neighborhood:</label></div>
            <div><input type="text" id="neighborhood" name="neighborhood"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>Address:</label></div>
            <div><input type="text" id="address" name="address"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>City:</label></div>
            <div><input type="text" id="city" name="city"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>School:</label></div>
            <div><input type="text" id="school" name="school"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>Hispanic, Latino, or Spanish Origin:</label></div>
            <div><input type="checkbox" id="is-hispanic" name="is-hispanic" value="1"></div>
        </div>

        <div class="search-container">
            <div class="search-label"><label for="race">Race</label><br><br></div>
            <div>
                <select id="race" name="race[]" multiple>
                    <option value="Caucasian">Caucasian</option>
                    <option value="Black/African American">Black/African American</option>
                    <option value="Native Indian/Alaska Native">Native Indian/Alaska Native</option>
                    <option value="Native Hawaiian/Pacific Islander">Native Hawaiian/Pacific Islander</option>
                    <option value="Asian">Asian</option>
                    <option value="Multiracial">Multiracial</option>
                    <option value="Other">Other</option>
                </select><br><br>
            </div>
        </div>

        <div class="search-container">
            <div class="search-label"><label>Estimated Household Income:</label></div>
            <div>
                <select id="income" name="income[]" multiple>
                    <option value="Under $15,0000">Under 20,000</option>
                    <option value="$15,000 - $24,999">20,000 - 40,000</option>
                    <option value="$25,000 - $34,999">40,001 - 60,000</option>
                    <option value="$35,000 - $49,999">60,001 - 80,000</option>
                    <option value="$100,000 and above">Over 80,000</option>
                </select>
            </div>
        </div>

        <button type="submit" class="button_style">Search</button>

        <?php
        if (isset($children)) {
            $sortColumn = $_GET['sort'] ?? 'firstName';
            $sortOrder = $_GET['order'] ?? 'asc';

            usort($children, function ($a, $b) use ($sortColumn, $sortOrder) {
                $valueA = strtolower($a->{"get" . ucfirst($sortColumn)}());
                $valueB = strtolower($b->{"get" . ucfirst($sortColumn)}());
                if ($valueA == $valueB) return 0;
                return ($sortOrder === 'asc' ? $valueA > $valueB : $valueA < $valueB) ? 1 : -1;
            });

            echo '<h3>Account Summary</h3>';
            echo '
            <div class="table-wrapper">
                <table class="general">
                    <thead>
                        <tr>
                            <th><a href="?sort=firstName&order=' . ($sortColumn === 'firstName' && $sortOrder === 'asc' ? 'desc' : 'asc') . '">Name</a></th>
                            <th>Birthdate</th>
                            <th><a href="?sort=neighborhood&order=' . ($sortColumn === 'neighborhood' && $sortOrder === 'asc' ? 'desc' : 'asc') . '">Neighborhood</a></th>
                            <th>Address</th>
                            <th><a href="?sort=city&order=' . ($sortColumn === 'city' && $sortOrder === 'asc' ? 'desc' : 'asc') . '">City</a></th>
                            <th><a href="?sort=state&order=' . ($sortColumn === 'state' && $sortOrder === 'asc' ? 'desc' : 'asc') . '">State</a></th>
                            <th><a href="?sort=zip&order=' . ($sortColumn === 'zip' && $sortOrder === 'asc' ? 'desc' : 'asc') . '">Zip</a></th>
                            <th><a href="?sort=school&order=' . ($sortColumn === 'school' && $sortOrder === 'asc' ? 'desc' : 'asc') . '">School</a></th>
                            <th><a href="?sort=grade&order=' . ($sortColumn === 'grade' && $sortOrder === 'asc' ? 'desc' : 'asc') . '">Grade</a></th>
                        </tr>
                    </thead>
                    <tbody class="standout">';
            
            foreach ($children as $c) {
                $id = $c->getID();
                echo "<tr onclick=\"window.location.href='childAccount.php?findChildren=true&id=$id'\" style='cursor: pointer;'>";
                echo '<td>' . htmlspecialchars($c->getFirstName() . " " . $c->getLastName()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getBirthDate()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getNeighborhood()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getAddress()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getCity()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getState()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getZip()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getSchool()) . '</td>';
                echo '<td>' . htmlspecialchars($c->getGrade()) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></div>';
        }
        ?>
    </form>

    <a class="button cancel button_style" href="index.php">Return to Dashboard</a>
</body>
</html>
