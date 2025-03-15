<?php
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    ini_set("display_errors",1);
    error_reporting(E_ALL);

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    } 
    // Require admin privileges
    if ($accessLevel < 2) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "Raw POST data:<br>";
        var_dump($_POST);
        require_once('include/input-validation.php');
        require_once('database/dbEvents.php');
        $args = sanitize($_POST, null);
        echo "Sanitized args:<br>";
        var_dump($args);
        $required = array(
            "name", "abbrev-name", "date", "start-time", "description", "location", "service", "animal"
        );
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
            echo "Bad form data: Missing or invalid fields - " . implode(", ", $missing);
            die();
        } else {
            $validated = validate12hTimeRangeAndConvertTo24h($args["start-time"], "11:59 PM");
            if (!$validated) {
                echo 'bad time range';
                die();
            }
            $startTime = $args['start-time'] = $validated[0];
            $date = $args['date'] = validateDate($args["date"]);
            //$capacity = intval($args["capacity"]);
            $abbrevLength = strlen($args['abbrev-name']);
            if (!$startTime || !$date || $abbrevLength > 11){
                echo 'bad args';
                die();
            }
            $id = create_event($args);
            if(!$id){
                echo "Oopsy!";
                die();
            }
            require_once('include/output.php');
            
            $name = htmlspecialchars_decode($args['name']);
            $startTime = time24hto12h($startTime);
            $date = date('l, F j, Y', strtotime($date));
            require_once('database/dbMessages.php');
            system_message_all_users_except($userID, "A new event was created!", "Exciting news!\r\n\r\nThe [$name](event: $id) event at $startTime on $date was added!\r\nSign up today!");
            header("Location: event.php?id=$id&createSuccess");
            die();
        }
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

    // get animal data from database for form
    // Connect to database
    include_once('database/dbinfo.php'); 
    $con=connect();  
    // Get all the animals from animal table
    $sql = "SELECT * FROM `dbAnimals`";
    $all_animals = mysqli_query($con,$sql);
    $sql = "SELECT * FROM `dbLocations`";
    $all_locations = mysqli_query($con,$sql);
    $sql = "SELECT * FROM `dbServices`";
    $all_services = mysqli_query($con,$sql);

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
                <label for="name">* Abbreviated Name</label>
                <input type="text" id="abbrev-name" name="abbrev-name" maxlength="11" required placeholder="Enter name that will appear on calendar">
                <label for="name">* Date </label>
                <input type="date" id="date" name="date" <?php if ($date) echo 'value="' . $date . '"'; ?> min="<?php echo date('Y-m-d'); ?>" required>
                <label for="name">* Start Time </label>
                <input type="text" id="start-time" name="start-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter start time. Ex. 12:00 PM">
                <label for="name">* Description </label>
                <input type="text" id="description" name="description" required placeholder="Enter description">
                <fieldset>
                    <label for="name">* Service </label>
                    <?php 
                        // fetch data from the $all_services variable
                        // and individually display as an option
                        echo '<ul>';
                        while ($service = mysqli_fetch_array(
                                $all_services, MYSQLI_ASSOC)):; 
                            echo '<li><input class="checkboxes" type="checkbox" name="service[]" value="' . $service['id'] . '" required/> ' . $service['name'] . '</li>';
                        endwhile;
                        echo '</ul>';
                    ?>
                </fieldset> 
                <label for="location">* Location</label>
                <select id="location" name="location" required>
                    <option value="" disabled selected>Select a location</option>
                    <?php
                        mysqli_data_seek($all_locations, 0); // Reset pointer
                        while ($location = mysqli_fetch_array($all_locations, MYSQLI_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($location['id']) . '">' . htmlspecialchars($location['name']) . '</option>';
                        }
                        if (mysqli_num_rows($all_locations) == 0) {
                            echo '<option value="" disabled>No locations available</option>';
                        }
                    ?>
                </select>
  
                <label for="animal">* Animal</label>
                <select id="animal" name="animal" required>
                    <option value="" disabled selected>Select an Animal</option>
                    <?php
                        mysqli_data_seek($all_animals, 0); // Reset pointer
                        while ($animal = mysqli_fetch_array($all_animals, MYSQLI_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($animal['id']) . '">' . htmlspecialchars($animal['name']) . '</option>';
                        }
                        if (mysqli_num_rows($all_animals) == 0) {
                            echo '<option value="" disabled>No locations available</option>';
                        }
                    ?>
                </select>
                <p></p>
                <input type="submit" value="Create Event">
            </form>
                <?php if ($date): ?>
                    <a class="button cancel" href="calendar.php?month=<?php echo substr($date, 0, 7) ?>" style="margin-top: -.5rem">Return to Calendar</a>
                <?php else: ?>
                    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
                <?php endif ?>

                <!-- Require at least one checkbox be checked -->
                <script type="text/javascript">
                    $(document).ready(function(){
                        var checkboxes = $('.checkboxes');
                        checkboxes.change(function(){
                            if($('.checkboxes:checked').length>0) {
                                checkboxes.removeAttr('required');
                            } else {
                                checkboxes.attr('required', 'required');
                            }
                        });
                    });
                </script>
        </main>
    </body>
</html>