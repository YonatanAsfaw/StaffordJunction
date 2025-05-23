<?php
/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook, 
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan, 
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker. 
 * This program is part of RMH Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/**
 * @version March 1, 2012
 * @author Oliver Radwan and Allen Tucker
 */

/* 
 * Created for Gwyneth's Gift in 2022 using original Homebase code as a guide
 */


include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Event.php');

/*
 * add an event to dbEvents table: if already there, return false
 */

function add_event($event) {
    if (!$event instanceof Event)
        die("Error: add_event type mismatch");
    $con=connect();
    $query = "SELECT * FROM dbEvents WHERE id = '" . $event->get_id() . "'";
    $result = mysqli_query($con,$query);
    //if there's no entry for this id, add it
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_query($con,'INSERT INTO dbEvents VALUES("' .
                $event->get_id() . '","' .
                $event->get_event_date() . '","' .
                $event->get_venue() . '","' .
                $event->get_event_name() . '","' . 
                $event->get_description() . '","' .
                $event->get_event_id() .            
                '");');							
        mysqli_close($con);
        return true;
    }
    mysqli_close($con);
    return false;
}

/*
 * remove an event from dbEvents table.  If already there, return false
 */

function remove_event($id) {
    $con=connect();
    $query = 'SELECT * FROM dbEvents WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $query = 'DELETE FROM dbEvents WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return true;
}


/*
 * @return an Event from dbEvents table matching a particular id.
 * if not in table, return false
 */

function retrieve_event($id) {
    $con=connect();
    $query = "SELECT * FROM dbEvents WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    // var_dump($result_row);
    $theEvent = make_an_event($result_row);
//    mysqli_close($con);
    return $theEvent;
}

function retrieve_event2($id) {
    $con=connect();
    $query = "SELECT * FROM dbEvents WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
//    var_dump($result_row);
    return $result_row;
}

// not in use, may be useful for future iterations in changing how events are edited (i.e. change the remove and create new event process)
function update_event_date($id, $new_event_date) {
	$con=connect();
	$query = 'UPDATE dbEvents SET event_date = "' . $new_event_date . '" WHERE id = "' . $id . '"';
	$result = mysqli_query($con,$query);
	mysqli_close($con);
	return $result;
}

function make_an_event($result_row) {
	/*
	 ($en, $v, $sd, $description, $ev))
	 */
    $theEvent = new Event(
                    $result_row['event_name'],
                    $result_row['venue'],                   
                    $result_row['event_date'],
                    $result_row['description'],
                    $result_row['event_id'],
                    $result_row['location'],
                    $result_row['service'],
                    $result_row['volunteer']); 
    return $theEvent;
}

// retrieve only those events that match the criteria given in the arguments
function getonlythose_dbEvents($name, $day, $venue) {
   $con=connect();
   $query = "SELECT * FROM dbEvents WHERE event_name LIKE '%" . $event_name . "%'" .
           " AND event_name LIKE '%" . $name . "%'" .
           " AND venue = '" . $venue . "'" . 
           " ORDER BY event_name";
   $result = mysqli_query($con,$query);
   $theEvents = array();
   while ($result_row = mysqli_fetch_assoc($result)) {
       $theEvent = make_an_event($result_row);
       $theEvents[] = $theEvent;
   }
   mysqli_close($con);
   return $theEvents;
}

function fetch_events_in_date_range($start_date, $end_date) {
    $connection = connect();
    $start_date = mysqli_real_escape_string($connection, $start_date);
    $end_date = mysqli_real_escape_string($connection, $end_date);
    $query = "select * from dbEvents
              where date >= '$start_date' and date <= '$end_date'
              order by startTime asc";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    require_once('include/output.php');
    $events = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $key = $result_row['date'];
        if (isset($events[$key])) {
            $events[$key] []= hsc($result_row);
        } else {
            $events[$key] = array(hsc($result_row));
        }
    }
    mysqli_close($connection);
    return $events;
}

function fetch_events_on_date($date) {
    $connection = connect();
    $date = mysqli_real_escape_string($connection, $date);
    $query = "select * from dbEvents
              where date = '$date' order by startTime asc";
    $results = mysqli_query($connection, $query);
    if (!$results) {
        mysqli_close($connection);
        return null;
    }
    require_once('include/output.php');
    $events = [];
    foreach ($results as $row) {
        $events []= hsc($row);
    }
    mysqli_close($connection);
    return $events;
}

function fetch_event_by_id($id) {
    $connection = connect();
    $id = mysqli_real_escape_string($connection, $id);
    $query = "select * from dbEvents where id = '$id'";
    $result = mysqli_query($connection, $query);
    $event = mysqli_fetch_assoc($result);
    if ($event) {
        require_once('include/output.php');
        $event = hsc($event);
        mysqli_close($connection);
        return $event;
    }
    mysqli_close($connection);
    return null;
}

function create_event($event) {
    $connection = connect();

    // Sanitize inputs
    $name = mysqli_real_escape_string($connection, $event["name"]);
    $abbrevName = mysqli_real_escape_string($connection, $event["abbrev-name"]);
    $date = $event["date"];
    $startTime = $event["start-time"];
    $description = mysqli_real_escape_string($connection, $event["description"]);
    $location = intval($event["location"]);
    $services = $event["service"];
    $volunteer = isset($event["volunteer"]) && !empty($event["volunteer"]) ? intval($event["volunteer"]) : NULL;
    $completed = "no";
    $endTime = "23:59"; // Default end time

    // Validate locationID
    if ($location <= 0) {
        mysqli_close($connection);
        die("Invalid location ID");
    }

    // Validate volunteerID if provided
    if ($volunteer !== NULL) {
        $checkSql = "SELECT COUNT(*) FROM dbVolunteers WHERE id = ?";
        $stmt = mysqli_prepare($connection, $checkSql);
        mysqli_stmt_bind_param($stmt, "i", $volunteer);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $count = mysqli_fetch_array($result)[0];
        if ($count == 0) {
            mysqli_close($connection);
            die("Invalid volunteer ID");
        }
    }

    // Prepare the INSERT query with endTime
    $query = "
        INSERT INTO dbEvents (name, abbrevName, date, startTime, endTime, description, locationID, capacity, volunteerID, completed)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        mysqli_close($connection);
        die("Prepare failed: " . mysqli_error($connection));
    }

    // Bind parameters with correct type string (10 parameters)
    $capacity = 0; // Default capacity
    mysqli_stmt_bind_param($stmt, "ssssssiiss", $name, $abbrevName, $date, $startTime, $endTime, $description, $location, $capacity, $volunteer, $completed);

    // Execute the query
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        die("Insert failed: " . $error);
    }

    // Get the inserted event ID
    $id = mysqli_insert_id($connection);
    mysqli_stmt_close($stmt);

    // Add services to the event
    add_services_to_event($id, $services);

    mysqli_commit($connection);
    mysqli_close($connection);
    return $id;
}

function add_services_to_event($eventID, $serviceIDs) {
    $connection = connect();
    foreach($serviceIDs as $serviceID) {
        $query = "insert into dbEventsServices (eventID, serviceID) values ('$eventID', '$serviceID')";
        $result = mysqli_query($connection, $query);
        if (!$result) {
            return null;
        }
        $id = mysqli_insert_id($connection);
    }
    mysqli_commit($connection);
    return $id;
}

function update_event($eventID, $eventDetails) {
    $connection = connect();
    $name = $eventDetails["name"];
    $abbrevName = $eventDetails["abbrev-name"];
    $date = $eventDetails["date"];
    $startTime = $eventDetails["start-time"];
    $description = $eventDetails["description"];
    $location = $eventDetails["location"];
    $services = $eventDetails["service"];
    $completed = $eventDetails["completed"];
    $query = "
        update dbEvents set name='$name', abbrevName='$abbrevName', date='$date', startTime='$startTime', description='$description', locationID='$location', completed='$completed'
        where id='$eventID'
    ";
    $result = mysqli_query($connection, $query);
    update_services_for_event($eventID, $services);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $result;
}

function update_event2($eventID, $eventDetails) {
    $connection = connect();
    $name = $eventDetails["name"];
    $abbrevName = $eventDetails["abbrevName"];
    $date = $eventDetails["date"];
    $startTime = $eventDetails["startTime"];
    $endTime = $eventDetails["endTime"];
    $description = $eventDetails["description"];
    $location = $eventDetails["locationID"];
    $capacity = $eventDetails["capacity"];
    $volunteerID = $eventDetails["volunteerID"];
    $completed = $eventDetails["completed"];
    $query = "
        update dbEvents set name='$name', abbrevName='$abbrevName', date='$date', startTime='$startTime', endTime='$endTime', description='$description', locationID='$location', capacity='$capacity', volunteerId='$volunteerID', completed='$completed'
        where id='$eventID'
    ";
    $result = mysqli_query($connection, $query);
    //update_services_for_event($eventID, $services);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $result;
}

function update_services_for_event($eventID, $serviceIDs) {
    $connection = connect();

    $current_services = get_services($eventID);
    foreach($current_services as $curr_serv) {
        $curr_servIDs[] = $curr_serv['id'];
    }

    // add new services
    foreach($serviceIDs as $serviceID) {
        if (!in_array($serviceID, $curr_servIDs)) {
            $query = "insert into dbEventsServices (eventID, serviceID) values ('$eventID', '$serviceID')";
            $result = mysqli_query($connection, $query);
        }
    }
    // remove old services
    foreach($curr_servIDs as $curr_serv) {
        if (!in_array($curr_serv, $serviceIDs)) {
            $query = "delete from dbEventsServices where serviceID='$curr_serv'";
            $result = mysqli_query($connection, $query);
        }
    }
    mysqli_commit($connection);
    return;
}

function find_event($nameLike) {
    $connection = connect();
    $query = "
        select * from dbEvents
        where name like '%$nameLike%'
    ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $all;
}

function fetch_events_in_date_range_as_array($start_date, $end_date) {
    $connection = connect();
    $start_date = mysqli_real_escape_string($connection, $start_date);
    $end_date = mysqli_real_escape_string($connection, $end_date);
    $query = "select * from dbEvents
              where date >= '$start_date' and date <= '$end_date'
              order by date, startTime asc";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $events = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $events;
}

function get_volunteer($id) {
    $connection = connect();
    $query = "select * from dbVolunteers
              where id='$id'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $volunteer = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $volunteer;
}

function get_location($id) {
    $connection = connect();
    $query = "select * from dbLocations
              where id='$id'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $location = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $location;
}

function get_services($eventID) {
    $connection = connect();
    $query = "select * from dbServices AS serv JOIN dbEventsServices AS es ON es.serviceID = serv.id
              where es.eventID='$eventID'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $services = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $services;
}

function get_media($id, $type) {
    $connection = connect();
    $query = "select * from dbEventMedia
              where eventID='$id' and type='$type'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $media = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $media;
}

function get_event_training_media($id) {
    return get_media($id, 'training');
}

function get_post_event_media($id) {
    return get_media($id, 'post');
}

function attach_media($eventID, $type, $url, $format, $description) {
    $query = "insert into dbEventMedia
              (eventID, type, url, format, description)
              values ('$eventID', '$type', '$url', '$format', '$description')";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);
    if (!$result) {
        return false;
    }
    return true;
}

function attach_event_training_media($eventID, $url, $format, $description) {
    return attach_media($eventID, 'training', $url, $format, $description);
}

function attach_post_event_media($eventID, $url, $format, $description) {
    return attach_media($eventID, 'post', $url, $format, $description);
}

function detach_media($mediaID) {
    $query = "delete from dbEventMedia where id='$mediaID'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);
    if ($result) {
        return true;
    }
    return false;
}

function delete_event($id) {
    $query = "delete from dbEvents where id='$id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}

function complete_event($id) {
    $event = retrieve_event2($id);
    $volunteer = get_volunteer($event["volunteerID"])[0];
    $date = $event["date"];
    $event["completed"] = "yes";

    $services = get_services($event["id"]);
    $length = count($services);

    for ($i = 0; $i < $length; $i++) { 
        $check = $services[$i]['name'];
        $dur = $services[$i]['duration_years'];
        if(stripos($check, "spay") !== false || stripos($check, "neuter") !== false){
            $volunteer["spay_neuter_done"] = "yes";
            $volunteer["spay_neuter_date"] = $date;
        }
        else if(stripos($check, "rabie") !== false){
            $volunteer["rabies_given_date"] = $date;
            $volunteer["rabies_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "heartworm") !== false){
            $volunteer["heartworm_given_date"] = $date;
            $volunteer["heartworm_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "distemper 1") !== false){
            $volunteer["distemper1_given_date"] = $date;
            $volunteer["distemper1_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "distemper 2") !== false){
            $volunteer["distemper2_given_date"] = $date;
            $volunteer["distemper2_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "distemper 3") !== false){
            $volunteer["distemper3_given_date"] = $date;
            $volunteer["distemper3_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "microchip") !== false){
            $volunteer["microchip_done"] = "yes";
        }
        else{
            $volunteer["notes"] = $volunteer["notes"]." | ".$check.": ".$date;
        }
    
    }
//    var_dump($event);
    $result = update_volunteer2($volunteer);
    $result = update_event2($event["id"], $event);
    return $result;
}

function update_volunteer2($volunteer) {
    $connection = connect();
    $id = $volunteer['id'];
	$odhsid = $volunteer["odhs_id"];
    $name = $volunteer["name"];
	$breed = $volunteer["breed"];
    $age = $volunteer["age"];
    $gender = $volunteer["gender"];
    $notes = $volunteer["notes"];
    $spay_neuter_done = $volunteer["spay_neuter_done"];
	$spay_neuter_date = $volunteer["spay_neuter_date"];
    if (empty($volunteer["spay_neuter_date"])) {
        $spay_neuter_date = '0000-00-00';
    }
    $rabies_given_date = $volunteer["rabies_given_date"];
    if (empty($volunteer["rabies_given_date"])) {
        $rabies_given_date = '0000-00-00';
    }
	$rabies_due_date = $volunteer["rabies_due_date"];
    if (empty($volunteer["rabies_due_date"])) {
        $rabies_due_date = '0000-00-00';
    }
    $heartworm_given_date = $volunteer["heartworm_given_date"];
    if (empty($volunteer["heartworm_given_date"])) {
        $heartworm_given_date = '0000-00-00';
    }
	$heartworm_due_date = $volunteer["heartworm_due_date"];
    if (empty($volunteer["heartworm_due_date"])) {
        $heartworm_due_date = '0000-00-00';
    }
	$distemper1_given_date = $volunteer["distemper1_given_date"];
    if (empty($volunteer["distemper1_given_date"])) {
        $distemper1_given_date = '0000-00-00';
    }
	$distemper1_due_date = $volunteer["distemper1_due_date"];
    if (empty($volunteer["distemper1_due_date"])) {
        $distemper1_due_date = '0000-00-00';
    }
	$distemper2_given_date = $volunteer["distemper2_given_date"];
    if (empty($volunteer["distemper2_given_date"])) {
        $distemper2_given_date = '0000-00-00';
    }
	$distemper2_due_date = $volunteer["distemper2_due_date"];
    if (empty($volunteer["distemper2_due_date"])) {
        $distemper2_due_date = '0000-00-00';
    }
	$distemper3_given_date = $volunteer["distemper3_given_date"];
    if (empty($volunteer["distemper3_given_date"])) {
        $distemper3_given_date = '0000-00-00';
    }
	$distemper3_due_date = $volunteer["distemper3_due_date"];
    if (empty($volunteer["distemper3_due_date"])) {
        $distemper3_due_date = '0000-00-00';
    }
	$microchip_done = $volunteer["microchip_done"];
    $query = "
        UPDATE dbvolunteers set odhs_id='$odhsid', name='$name', breed='$breed', age='$age', gender='$gender', notes='$notes', spay_neuter_done='$spay_neuter_done', spay_neuter_date='$spay_neuter_date', rabies_given_date='$rabies_given_date', rabies_due_date='$rabies_due_date', heartworm_given_date='$heartworm_given_date', heartworm_due_date='$heartworm_due_date', distemper1_given_date='$distemper1_given_date', distemper1_due_date='$distemper1_due_date', distemper2_given_date='$distemper2_given_date', distemper2_due_date='$distemper2_due_date', distemper3_given_date='$distemper3_given_date', distemper3_due_date='$distemper3_due_date', microchip_done='$microchip_done'
        where id='$id'
        ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    mysqli_commit($connection);
    mysqli_close($connection);
    return $id;
}

?>
