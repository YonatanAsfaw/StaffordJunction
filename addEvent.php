<?php
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

if ($accessLevel == 4) {
    header('Location: calendar.php?view_only=1');
    die();
} elseif (!in_array($accessLevel, [0, 3])) {
    header('Location: calendar.php?view_only=1');
    die();
}

$date = null;
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $datePattern = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
    $timeStamp = strtotime($date);
    if (!preg_match($datePattern, $date) || !$timeStamp) {
        header('Location: calendar.php');
        die();
    }
}

include_once('database/dbinfo.php'); 
$con = connect();  
$sql = "SELECT * FROM `dbLocations`";
$all_locations = mysqli_query($con, $sql);
$sql = "SELECT * FROM `dbServices`";
$all_services = mysqli_query($con, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('include/input-validation.php');
    require_once('database/dbEvents.php');
    
    $args = sanitize($_POST, null);
    
    $required = array("name", "abbrev-name", "date", "start-time", "description", "location", "service");
    if (!wereRequiredFieldsSubmitted($args, $required)) {
        $missing = [];
        foreach ($required as $field) {
            if ($field === "service") {
                if (!isset($args[$field]) || !is_array($args[$field]) || empty($args[$field])) {
                    $missing[] = $field;
                }
            } elseif (!isset($args[$field]) || trim($args[$field]) === '') {
                $missing[] = $field;
            }
        }
        die();
    }

    $validated = validate12hTimeRangeAndConvertTo24h($args["start-time"], "11:59 PM");
    if (!$validated) {
        die();
    }
    $args['start-time'] = $validated[0];
    $args['date'] = validateDate($args["date"]);
    $abbrevLength = strlen($args['abbrev-name']);
    if (!$args['start-time'] || !$args['date'] || $abbrevLength > 11) {
        die();
    }

    $id = create_event($args);
    if (!$id) {
        die();
    }

    require_once('include/output.php');
    $name = htmlspecialchars_decode($args['name']);
    $startTime = time24hto12h($args['start-time']);
    $date = date('l, F j, Y', strtotime($args['date']));
    require_once('database/dbMessages.php');
    system_message_all_users_except($userID, "A new event was created!", "Exciting news!\r\n\r\nThe [$name](event: $id) event at $startTime on $date was added!\r\nSign up today!");
    header("Location: event.php?id=$id&createSuccess");
    die();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>ODHS Medicine Tracker | Create Event</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Create Appointment</h1>
        <main class="date">
            <h2>New Appointment Form</h2>
            <form id="new-event-form" method="post">
                <label for="name">* Appointment Name </label>
                <input type="text" id="name" name="name" required placeholder="Enter name"> 
                <label for="abbrev-name">* Abbreviated Name</label>
                <input type="text" id="abbrev-name" name="abbrev-name" maxlength="11" required placeholder="Enter name that will appear on calendar">
                <label for="date">* Date </label>
                <input type="date" id="date" name="date" <?php if ($date) echo 'value="' . $date . '"'; ?> min="<?php echo date('Y-m-d'); ?>" required>
                <label for="start-time">* Start Time </label>
                <input type="text" id="start-time" name="start-time" pattern="([1-9]|10|11|12):[0-9][0-9] ?([aApP][mM])" required placeholder="Enter start time. Ex. 12:00 PM">
                <label for="description">* Description </label>
                <input type="text" id="description" name="description" required placeholder="Enter description">
                <fieldset>
                    <label for="name">* Service </label>
                    <ul>
                    <?php 
                        mysqli_data_seek($all_services, 0);
                        while ($service = mysqli_fetch_array($all_services, MYSQLI_ASSOC)) {
                            echo '<li><input class="checkboxes" type="checkbox" name="service[]" value="' . htmlspecialchars($service['id']) . '" required /> ' . htmlspecialchars($service['name']) . '</li>';
                        }
                        if (mysqli_num_rows($all_services) == 0) {
                            echo '<li>No services available</li>';
                        }
                    ?>
                    </ul>
                </fieldset> 
                <label for="location">* Location</label>
                <select id="location" name="location" required>
                    <option value="" disabled selected>Select a location</option>
                    <?php
                    mysqli_data_seek($all_locations, 0);
                    while ($location = mysqli_fetch_array($all_locations, MYSQLI_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($location['id']) . '">' . htmlspecialchars($location['name']) . '</option>';
                    }
                    if (mysqli_num_rows($all_locations) == 0) {
                        echo '<option value="" disabled>No locations available</option>';
                    }
                    ?>
                </select>
                <label for="volunteer">Assign Volunteer (optional)</label>
                <select id="volunteer" name="volunteer">
                    <option value="">None</option>
                    <?php
                    $sql = "SELECT id, firstName AS name FROM dbVolunteers"; // Include id, alias firstName as name
                    $volunteers = mysqli_query($con, $sql);
                    if ($volunteers === false) {
                        echo '<option value="" disabled>Error fetching volunteers: ' . mysqli_error($con) . '</option>';
                    } else {
                        while ($volunteer = mysqli_fetch_array($volunteers, MYSQLI_ASSOC)) {
                            $volunteerId = isset($volunteer['id']) ? htmlspecialchars($volunteer['id']) : '';
                            $volunteerName = isset($volunteer['name']) ? htmlspecialchars($volunteer['name']) : 'Unknown';
                            echo '<option value="' . $volunteerId . '">' . $volunteerName . '</option>';
                        }
                        if (mysqli_num_rows($volunteers) == 0) {
                            echo '<option value="" disabled>No volunteers available</option>';
                        }
                    }
                    ?>
                </select>
                <?php echo "Submit button rendered<br>"; ?> <!-- Temporary debug -->
                <input type="submit" value="Create Event">
            </form>
            <?php if ($date): ?>
                <a class="button cancel" href="calendar.php?month=<?php echo substr($date, 0, 7) ?>" style="margin-top: -.5rem">Return to Calendar</a>
            <?php else: ?>
                <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
            <?php endif ?>
        </main>
    </body>
</html>