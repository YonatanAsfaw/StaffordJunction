<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

$loggedIn = false;
$accessLevel = 0;
$userID = null;

if (isset($_SESSION['_id'])) {
    require_once('include/input-validation.php');
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    die();
}

// Include necessary database functions
require_once("database/dbActualActivityForm.php");

$attendanceTrends = get_attendance_trends(); // Fetch attendance trends from the function
$attendanceStats = get_attendance_statistics(); // Fetch attendance statistics
$volunteerData = get_volunteer_details_per_event();
$volunteerDetails = $volunteerData['volunteers'];
$totalHoursPerEvent = $volunteerData['totals'];

// Handle CSV Export
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csv_export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_trends.csv"');

    $output = fopen('php://output', 'w');

    // Write overall stats
    fputcsv($output, ['Total Events', 'Total Participants', 'Average Attendance per Event', 'Most Attended Event', 'Max Attendance']);
    fputcsv($output, [
        $attendanceStats['total_events'],
        $attendanceStats['total_attendance'],
        number_format($attendanceStats['avg_attendance'], 2),
        $attendanceStats['most_attended_event'],
        $attendanceStats['max_attendance']
    ]);

    // Write event attendance header
    fputcsv($output, []); // empty line for separation
    fputcsv($output, ['Date', 'Event', 'Total Participants', 'Volunteer Hours']);

    foreach ($attendanceTrends as $trend) {
        $activity_id = $trend['activity_id'];
        $totalHours = isset($totalHoursPerEvent[$activity_id]) ? number_format($totalHoursPerEvent[$activity_id], 2) : '0.00';

        fputcsv($output, [
            $trend['date'],
            $trend['activity'],
            $trend['total_attendance'],
            $totalHours
        ]);

        if (isset($volunteerDetails[$activity_id])) {
            fputcsv($output, ['Volunteer Name', 'Hours']); // Sub-header for volunteer data
            foreach ($volunteerDetails[$activity_id] as $volunteer) {
                fputcsv($output, [
                    $volunteer['volunteer_name'],
                    number_format($volunteer['hours'], 2)
                ]);
            }
        }

        fputcsv($output, []); // spacer row
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Attendance Trends</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once("universal.inc"); ?>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <?php require_once("header.php"); ?>
    
    <h1>Attendance Trends</h1>

    <main class="attendance-trends">
        <form method="post" style="margin-bottom: 1rem;">
            <button class="button" name="csv_export" value="1">Download CSV</button>
        </form>

        <p>Below are the overall attendance trends, including the total number of participants for each event and attendance frequency statistics for volunteers.</p>

        <h2>Overall Attendance Statistics</h2>
        <table>
            <tr>
                <th>Total Events</th>
                <th>Total Participants</th>
                <th>Average Attendance per Event</th>
                <th>Most Attended Event</th>
                <th>Max Attendance</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($attendanceStats['total_events']); ?></td>
                <td><?php echo htmlspecialchars($attendanceStats['total_attendance']); ?></td>
                <td><?php echo number_format($attendanceStats['avg_attendance'], 2); ?></td>
                <td><?php echo htmlspecialchars($attendanceStats['most_attended_event']); ?></td>
                <td><?php echo htmlspecialchars($attendanceStats['max_attendance']); ?></td>
            </tr>
        </table>

        <h2>Event Attendance</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Event</th>
                    <th>Total Participants</th>
                    <th>Volunteer Hours</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($attendanceTrends as $trend): ?>
                <tr>
                    <td><?php echo htmlspecialchars($trend['date']); ?></td>
                    <td><?php echo htmlspecialchars($trend['activity']); ?></td>
                    <td><?php echo htmlspecialchars($trend['total_attendance']); ?></td>
                    <td><strong><?php 
                        $totalHours = isset($totalHoursPerEvent[$trend['activity_id']]) 
                            ? number_format($totalHoursPerEvent[$trend['activity_id']], 2) 
                            : '0.00';
                        echo $totalHours;
                    ?></strong></td>
                </tr>

                <?php if (isset($volunteerDetails[$trend['activity_id']])): ?>
                    <tr>
                        <td colspan="4">
                            <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                                <thead>
                                    <tr style="background-color: #f2f2f2;">
                                        <th style="text-align: left; padding: 5px;">Volunteer Name</th>
                                        <th style="text-align: left; padding: 5px;">Hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($volunteerDetails[$trend['activity_id']] as $volunteer): ?>
                                        <tr>
                                            <td style="padding: 5px;"><?php echo htmlspecialchars($volunteer['volunteer_name']); ?></td>
                                            <td style="padding: 5px;"><?php echo number_format($volunteer['hours'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
