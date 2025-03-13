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
require_once("database/dbVolunteerReportForm.php");

$attendanceTrends = get_attendance_trends(); // Fetch attendance trends from the function
$attendanceStats = get_attendance_statistics(); // Fetch attendance statistics
$volunteerHours = get_volunteer_hours_per_event();
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
                <td>
                    <?php 
                        $activityID = $trend['activity_id'];
                        $hours = array_column($volunteerHours, 'total_hours', 'activity_id')[$activityID] ?? 0;
                        echo number_format($hours, 2);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
