<?php 
session_cache_expire(30);
session_start();

// Get access level
$accessLevel = isset($_SESSION['access_level']) ? $_SESSION['access_level'] : 0;

// Access control
if ($accessLevel < 1) { // Guests or invalid sessions
    header('Location: login.php');
    die();
}

// Allow admins (0), level 3 (vmsroot), and volunteers (4) to view
if ($accessLevel == 4) {
    echo "<p>View-only mode for volunteers (access level 4). No modifications allowed.</p>";
    echo "<style>.edit-button, .delete-button, .create-button { display: none; }</style>";
} elseif (!in_array($accessLevel, [0, 3])) { // Redirect other levels (e.g., 1, 2)
    header('Location: calendar.php?view_only=1');
    die();
}

require_once('include/input-validation.php');
$args = sanitize($_GET);
if (isset($args["id"])) {
    $id = $args["id"];
} else {
    header('Location: calendar.php');
    die();
}

include_once('database/dbEvents.php');

// Fetch event
$event_info = fetch_event_by_id($id);
if ($event_info == NULL) {
    echo 'bad event ID';
    die();
}

include_once('database/dbPersons.php');
$user = retrieve_person($_SESSION['_id']);
if ($user === false || $user === null) {
    $active = false; // Fallback to inactive
} else {
    $active = $user->get_status() == 'Active';
}

ini_set("display_errors", 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $args = sanitize($_POST);
    $get = sanitize($_GET);
    if (isset($_POST['attach-post-media-submit'])) {
        if (!in_array($accessLevel, [0, 3])) {
            echo 'forbidden';
            die();
        }
        $required = ['url', 'description', 'format', 'id'];
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo "dude, args missing";
            die();
        }
        $type = 'post';
        $format = $args['format'];
        $url = $args['url'];
        if ($format == 'video') {
            $url = convertYouTubeURLToEmbedLink($url);
            if (!$url) {
                echo "bad video link";
                die();
            }
        } elseif (!validateURL($url)) {
            echo "bad url";
            die();
        }
        $eid = $args['id'];
        $description = $args['description'];
        if (!valueConstrainedTo($format, ['link', 'video', 'picture'])) {
            echo "dude, bad format";
            die();
        }
        attach_post_event_media($eid, $url, $format, $description);
        header('Location: event.php?id=' . $id . '&attachSuccess');
        die();
    }
    if (isset($_POST['attach-training-media-submit'])) {
        if (!in_array($accessLevel, [0, 3])) {
            echo 'forbidden';
            die();
        }
        $required = ['url', 'description', 'format', 'id'];
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo "dude, args missing";
            die();
        }
        $type = 'post';
        $format = $args['format'];
        $url = $args['url'];
        if ($format == 'video') {
            $url = convertYouTubeURLToEmbedLink($url);
            if (!$url) {
                echo "bad video link";
                die();
            }
        } elseif (!validateURL($url)) {
            echo "bad url";
            die();
        }
        $eid = $args['id'];
        $description = $args['description'];
        if (!valueConstrainedTo($format, ['link', 'video', 'picture'])) {
            echo "dude, bad format";
            die();
        }
        attach_event_training_media($eid, $url, $format, $description);
        header('Location: event.php?id=' . $id . '&attachSuccess');
        die();
    }
} else {
    if (isset($args["request_type"])) {
        $request_type = $args['request_type'];
        if (!valueConstrainedTo($request_type, ['add self', 'add another', 'remove'])) {
            echo "Bad request";
            die();
        }
        $eventID = $args["id"];

        if ($request_type == 'add self' && $accessLevel >= 1) {
            if (!$active) {
                echo 'forbidden';
                die();
            }
            $volunteerID = $args['selected_id'];
            $person = retrieve_person($volunteerID);
            $name = $person->get_first_name() . ' ' . $person->get_last_name();
            $name = htmlspecialchars_decode($name);
            require_once('database/dbMessages.php');
            require_once('include/output.php');
            $event = fetch_event_by_id($eventID);
            $eventName = htmlspecialchars_decode($event['name']);
            $eventDate = date('l, F j, Y', strtotime($event['date']));
            $eventStart = time24hto12h($event['startTime']);
            $eventEnd = time24hto12h($event['endTime']);
            system_message_all_admins("$name signed up for an event!", "Exciting news!\r\n\r\n$name signed up for the [$eventName](event: $eventID) event from $eventStart to $eventEnd on $eventDate.");
        } elseif ($request_type == 'add another' && in_array($accessLevel, [0, 3])) {
            $volunteerID = strtolower($args['selected_id']);
            if ($volunteerID == 'vmsroot') {
                echo 'invalid user id';
                die();
            }
            require_once('database/dbMessages.php');
            require_once('include/output.php');
            $event = fetch_event_by_id($eventID);
            $eventName = htmlspecialchars_decode($event['name']);
            $eventDate = date('l, F j, Y', strtotime($event['date']));
            $eventStart = time24hto12h($event['startTime']);
            $eventEnd = time24hto12h($event['endTime']);
            send_system_message($volunteerID, 'You were assigned to an event!', "Hello,\r\n\r\nYou were assigned to the [$eventName](event: $eventID) event from $eventStart to $eventEnd on $eventDate.");
        } else {
            header('Location: event.php?id=' . $eventID);
            die();
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <?php require_once('universal.inc'); ?>
    <title>ODHS Medicine Tracker | View Appointment: <?php echo htmlspecialchars($event_info['name']); ?></title>
    <link rel="stylesheet" href="css/event.css" type="text/css" />
    <?php if (in_array($accessLevel, [0, 3])) : ?>
        <script src="js/event.js"></script>
    <?php endif ?>
</head>

<body>
    <?php if (in_array($accessLevel, [0, 3])) : ?>
        <div id="delete-confirmation-wrapper" class="hidden">
            <div id="delete-confirmation">
                <p>Are you sure you want to delete this appointment?</p>
                <p>This action cannot be undone.</p>

                <form method="post" action="deleteEvent.php">
                    <input type="submit" value="Delete Appointment">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                </form>
                <button id="delete-cancel">Cancel</button>
            </div>
        </div>
    <?php endif ?>
    <?php if (in_array($accessLevel, [0, 3])) : ?>
        <div id="complete-confirmation-wrapper" hidden>
            <div id="complete-confirmation">
                <p>Are you sure you want to complete this appointment?</p>
                <p>This action cannot be undone.</p>
                <form method="post" action="completeEvent.php">
                    <input type="submit" value="Complete Appointment">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                </form>
                <button id="complete-cancel">Cancel</button>
            </div>
        </div>
    <?php endif ?>

    <?php require_once('header.php') ?>
    <h1>View Appointment</h1>
    <main class="event-info">
        <?php if (isset($_GET['createSuccess'])): ?>
            <div class="happy-toast">Appointment created successfully!</div>
        <?php endif ?>
        <?php if (isset($_GET['attachSuccess'])): ?>
            <div class="happy-toast">Media attached successfully!</div>
        <?php endif ?>
        <?php if (isset($_GET['removeSuccess'])): ?>
            <div class="happy-toast">Media removed successfully!</div>
        <?php endif ?>
        <?php if (isset($_GET['editSuccess'])): ?>
            <div class="happy-toast">Appointment details updated successfully!</div>
        <?php endif ?>
        <?php    
            require_once('include/output.php');
            $event_name = $event_info['name'];
            $event_date = date('l, F j, Y', strtotime($event_info['date']));
            $event_startTime = time24hto12h($event_info['startTime']);
            $event_location = $event_info['locationID'];
            $event_description = $event_info['description'];
            $event_in_past = strcmp(date('Y-m-d'), $event_info['date']) > 0;
            $event_volunteer_id = $event_info['volunteerID'];
            //$event_volunteer_id = isset($event_info['volunteerID']) ? $event_info['volunteerID'] : null;

            require_once('include/time.php');
            echo '<h2 class="centered">' . htmlspecialchars($event_name) . '</h2>';
        ?>
        <div id="table-wrapper">
            <table class="centered">
                <tbody>
                <tr>	
                    <td class="label">Volunteer</td>
                    <td>
                        <?php 
                             $volunteerData = get_volunteer($event_volunteer_id);
                             $volunteerName = isset($volunteerData[0]["firstName"]) ? htmlspecialchars($volunteerData[0]["firstName"]) : 'No Volunteer Assigned';
                             echo $volunteerName;
                           // if ($event_volunteer_id !== null) {
                             //   $volunteerData = get_volunteer($event_volunteer_id);
                               // $volunteerName = isset($volunteerData[0]["firstName"]) ? htmlspecialchars($volunteerData[0]["firstName"]) : 'No Volunteer Assigned';
                            //} else {
                              //  $volunteerName = 'Mr Volunteer'; //this is just to work for demo need to fix
                            //}
                            //echo $volunteerName;
                            
                        ?>
                    </td>
                </tr>
                <tr>	
                    <td class="label">Date</td>
                    <td><?php echo htmlspecialchars($event_date) ?></td>     		
                </tr>
                <tr>	
                    <td class="label">Time</td>
                    <td><?php echo htmlspecialchars($event_startTime) ?></td>
                </tr>
                <tr>	
                    <td class="label">Service(s)</td>
                    <td>
                        <?php 
                            $services = get_services($id);
                            $length = count($services);
                            for ($i = 0; $i < $length; $i++) { 
                                echo htmlspecialchars($services[$i]['name']);
                                if ($i < $length - 1) {
                                    echo ', ';
                                }
                            }
                        ?>
                    </td>     		
                </tr>
                <tr>	
                    <td class="label">Location</td>
                    <td>
                        <?php 
                            $locations = get_location($event_location);
                            foreach($locations as $location) {
                                echo htmlspecialchars($location['name']);
                            }
                        ?>
                    </td>     		
                </tr>
                <tr>	
                    <td class="label">Location Address</td>
                    <td>
                        <?php 
                            foreach($locations as $location) {
                                echo htmlspecialchars($location['address']);
                            }
                        ?>
                    </td>     		
                </tr>
                <tr>	
                    <td class="label">Description</td>
                    <td><?php echo htmlspecialchars($event_description); ?></td>
                </tr>
                <?php if (in_array($accessLevel, [0, 3])) : ?>
                    <tr>
                        <td colspan="2">
                            <a href="editEvent.php?id=<?php echo htmlspecialchars($id); ?>" class="button edit-button">Edit Appointment Details</a>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if (in_array($accessLevel, [0, 3])) : ?>
                    <?php if ($event_info["completed"] == "no") : ?>
                        <button class="edit-button" onclick="showCompleteConfirmation()">Complete Appointment</button>
                    <?php endif ?>
                    <button class="delete-button" onclick="showDeleteConfirmation()">Delete Appointment</button>
                <?php endif; ?>

                <a href="calendar.php?month=<?php echo substr($event_info['date'], 0, 7) ?>" class="button cancel" style="margin-top: -.5rem">Return to Calendar</a>
            </main>
        </body>
</html>