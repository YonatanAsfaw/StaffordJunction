<?php
/*
 * Copyright 2015 by Allen Tucker. 
 * This program is part of RMHP-Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/*
 * This file is only the connection information for the database.
 * This file will be modified for every installation.
 * @author Max Palmer <mpalmer@bowdoin.edu>
 * @version updated 2/12/08
 */

 function connect() {
    $host = "localhost"; 
    $database = "stafforddb";
    $user = "stafforddb";
    $pass = "stafforddb";

    if ($_SERVER['SERVER_NAME'] == 'jenniferp129.sg-host.com') {
        $user = 'u8sj1xg2scpnb';
        $database = 'dbvswbwbmfnmrx';
        $pass = "362z7x6hkngw";
    } else if ($_SERVER['SERVER_NAME'] == 'gwynethsgiftvms.org') {
        $user = "uybhc603shfl5";
        $pass = "f11kwvhy4yic";
        $database = "dbwgyuabseaoih";
    }

    // ✅ Create a proper connection
    $con = new mysqli($host, $user, $pass, $database);


    // ✅ Check if the connection failed
    if ($con->connect_error) {
        die("Database connection failed: " . $con->connect_error);
    }
    mysqli_autocommit($con, TRUE);
    return $con;
}

$conn = connect();

?>
