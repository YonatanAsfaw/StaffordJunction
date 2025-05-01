<?php
/**
 * @version April 14, 2025 - Combined Program Review Display + CSV Export
 * @author Carlos
 */

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;

if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

require_once('include/input-validation.php');
require_once('database/dbPersons.php');
require_once('database/dbEvents.php');
require_once('include/output.php');
require_once('database/dbinfo.php');

// Check permissions
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

$selected_event_name = $_GET['event'] ?? null;
$reviews = [];

if ($selected_event_name) {
    $connection = connect();
    $safe_event_name = mysqli_real_escape_string($connection, $selected_event_name);
   $query = "
    SELECT prf.*, f.firstName, f.lastName 
    FROM dbProgramReviewForm prf
    JOIN dbFamily f ON prf.family_id = f.id
    WHERE prf.event_name = '$safe_event_name'
";


    $result = mysqli_query($connection, $query);

    if ($result) {
        $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
} else {
    echo "No event selected!";
}

// Handle CSV Export
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csv_export'])) {
    if (empty($reviews)) {
        die("No data available for download.");
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="program_review_' . preg_replace('/\s+/', '_', $selected_event_name) . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($reviews[0]));

    foreach ($reviews as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>Program Review Report</title>
    <style>
        table {
            margin-top: 1rem;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            width: 80%;
        }
        td, th {
            border: 1px solid #333;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: var(--main-color);
            color: var(--button-font-color);
            font-weight: 500;
        }
        tr:nth-child(even) {
            background-color: #f0f0f0;
        }
        @media print {
            tr:nth-child(even) {
                background-color: white;
            }
            button, header {
                display: none;
            }
            table {
                width: 100%;
            }
            a {
                color: black;
            }
        }
        .center_b {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
    </style>
</head>
<body>
<?php require_once('header.php') ?>

<main class="report">
    <h1 style="text-align: center;">Program Review Report</h1>

    <form method="post" class="center_b">
        <a class="button cancel" href="programReviewReport.php">Back to Report Menu</a>
        <?php if (!empty($reviews)): ?>
            <button class="button" name="csv_export" value="1">Download Results (.csv)</button>
        <?php endif; ?>
        <!--<span>Event: <strong><?= htmlspecialchars($selected_event_name, ENT_QUOTES, 'UTF-8') ?></strong></span>-->
    </form>

    <?php if (!empty($reviews)): ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (array_keys($reviews[0]) as $column): ?>
                        <th><?= ucwords(str_replace('_', ' ', htmlspecialchars($column))) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <?php foreach ($review as $value): ?>
                            <td><?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center;">No reviews found for this event.</p>
    <?php endif; ?>
</main>

</body>
</html>
