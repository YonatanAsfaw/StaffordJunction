<?php 
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
  require_once('include/input-validation.php');
  require_once('database/dbPersons.php');

  if ($accessLevel < 2) {
    header('Location: index.php');
    die();
  }
    // get animal data from database for form
    // Connect to database
    include_once('database/dbinfo.php'); 
    $con=connect();  
    // Get all the event names from program review form table
    $sql = "SELECT DISTINCT `event_name` FROM `dbProgramReviewForm`";
    $all_events = mysqli_query($con,$sql); 
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>ODHS Medicine Tracker | Reports</title>
        <style>
            .report_select{
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 4rem 0;
            }
            @media only screen and (min-width: 1024px) {
                .report_select {
                    /* width: 40%; */
                    width: 35rem;
            }
            main.report {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
	    .column {
		padding: 0 4rem 0 0;
		width: 50%;
	    }
	    .row{
          	display: flex;
            }
	    }
	    .hide {
  		display: none;
	    }

	    .myDIV:hover + .hide {
		display: block;
  		color: red;
	    }
        </style>
    </head>
    <body>
        <?php require_once('header.php');?>
	<h1>Program Review Reports</h1>

    <main class="report">
	<?php
	    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_click"])) {
		$args = sanitize($_POST);
		$report = $args['report_type'];
		$name = $args['name'];
        }
	    ?>
        
	<h2>Generate Report</h2>
	<br>

    <form class="report_select" method="get" action="programReviewReportPage.php">
    <div>
        <label for="name">Select Program For Report</label>
        <select for="name" id="event" name="event" required>
            <?php 
                while ($event = mysqli_fetch_array($all_events, MYSQLI_ASSOC)):; 
            ?>
            <option value="<?php echo $event['event_name'];?>">
                <?php echo $event['event_name'];?>
            </option>
            <?php endwhile; ?>
        </select><br/>
    </div>
    <input type="submit" name="submit_click">
    </form>
    </main>

    </body>

</html>
