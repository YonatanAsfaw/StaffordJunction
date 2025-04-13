<?php

use function PHPSTORM_META\type;

include_once('dbinfo.php');

function register($args, $childID) {
    $conn = connect();

    $requiredKeys = [
        'child-first-name', 'child-last-name', 'gender', 'school-name', 'grade', 'birthdate',
        'child-address', 'child-city', 'child-state', 'child-zip', 'child-medical-allergies',
        'child-food-avoidances', 'parent1-name', 'parent1-phone', 'parent1-address', 'parent1-city',
        'parent1-state', 'parent1-zip', 'parent1-email', 'parent1-altPhone', 'parent2-name',
        'parent2-phone', 'parent2-address', 'parent2-city', 'parent2-state', 'parent2-zip',
        'parent2-email', 'parent2-altPhone', 'emergency-name1', 'emergency-relationship1',
        'emergency-phone1', 'emergency-name2', 'emergency-relationship2', 'emergency-phone2',
        'authorized-pu', 'not-authorized-pu', 'primary-language', 'hispanic-latino-spanish', 'race',
        'num-unemployed', 'num-retired', 'num-unemployed-student', 'num-employed-fulltime',
        'num-employed-parttime', 'num-employed-student', 'income', 'other-programs', 'lunch',
        'needs-transportation', 'participation', 'parent-initials', 'signature', 'signature-date',
        'waiver-child-name', 'waiver-dob', 'waiver-parent-name', 'waiver-provider-name',
        'waiver-provider-address', 'waiver-phone-and-fax', 'waiver-signature', 'waiver-date'
    ];

    foreach ($requiredKeys as $key) {
        if (!isset($args[$key]) || empty($args[$key])) {
            error_log("Missing or empty value for key: $key");
        }
    }

    // Check if child already exists
    $query = "SELECT * FROM dbBrainBuildersRegistrationForm WHERE child_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $childID);
    $stmt->execute();
    $res = $stmt->get_result(); 
    $stmt->errno;
    //data_dump($stmt);
    if ($res == null || $res->num_rows == 0) {
        // Insert new record using prepared statements
        $query = "INSERT INTO dbBrainBuildersRegistrationForm (
            child_id, #1
            child_first_name, 
            child_last_name, 
            gender, 
            school_name, #5
            grade,
            birthdate, 
            child_address, 
            child_city, 
            child_state, #10
            child_zip, 
            child_medical_allergies, 
            child_food_avoidances, 
            parent1_name, 
            parent1_phone, #15
            parent1_address, 
            parent1_city, 
            parent1_state, 
            parent1_zip, 
            parent1_email, #20
            parent1_altPhone, 
            parent2_name, 
            parent2_phone,  
            parent2_address, 
            parent2_city, #25
            parent2_state, 
            parent2_zip, 
            parent2_email, 
            parent2_altPhone, 
            emergency_name1, #30
            emergency_relationship1, 
            emergency_phone1, 
            emergency_name2, 
            emergency_relationship2, 
            emergency_phone2, #35
            authorized_pu, 
            not_authorized_pu, 
            primary_language, 
            hispanic_latino_spanish, 
            race, #40
            num_unemployed, 
            num_retired, 
            num_unemployed_student, 
            num_employed_fulltime, 
            num_employed_parttime, #45
            num_employed_student, 
            income, 
            other_programs, 
            lunch, 
            needs_transportation, #50
            participation, 
            parent_initials, 
            signature, 
            signature_date, 
            waiver_child_name, #55
            waiver_dob, 
            waiver_parent_name, 
            waiver_provider_name, 
            waiver_provider_address, 
            waiver_phone_and_fax, #60
            waiver_signature, 
            waiver_date

        ) VALUES (
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?, #30
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?,
                    ?,?,?,?,?, #60
                    ?,?
                  )";
        //print("Params: " . print_r($args, false)) . '<br>';
        //data_dump($stmt);
        $stmt = $conn->prepare($query);
        //data_dump($stmt);
        $stmt->bind_param(
            "issssssssssssssssssssssssssssssssssssssssiiiiiisssssssssssssss",
            $childID,
            $args['child-first-name'],
            $args['child-last-name'],
            $args['gender'],
            $args['school-name'],
            
            $args['grade'],
            $args['birthdate'],
            $args['child-address'],
            $args['child-city'],
            $args['child-state'],
            
            $args['child-zip'],
            $args['child-medical-allergies'],
            $args['child-food-avoidances'],
            $args['parent1-name'],
            $args['parent1-phone'],
            
            $args['parent1-address'],
            $args['parent1-city'],
            $args['parent1-state'],
            $args['parent1-zip'],
            $args['parent1-email'],
            
            $args['parent1-altPhone'],
            $args['parent2-name'],
            $args['parent2-phone'],
            $args['parent2-address'],
            $args['parent2-city'],
            
            $args['parent2-state'],
            $args['parent2-zip'],
            $args['parent2-email'],
            $args['parent2-altPhone'],
            $args['emergency-name1'],
            
            $args['emergency-relationship1'],
            $args['emergency-phone1'],
            $args['emergency-name2'],
            $args['emergency-relationship2'],
            $args['emergency-phone2'],
            
            $args['authorized-pu'],
            $args['not-authorized-pu'],
            $args['primary-language'],
            $args['hispanic-latino-spanish'],
            $args['race'],
            
            $args['num-unemployed'],
            $args['num-retired'],
            $args['num-unemployed-student'],
            $args['num-employed-fulltime'],
            $args['num-employed-parttime'],
            
            $args['num-employed-student'],
            $args['income'],
            $args['other-programs'],
            $args['lunch'],
            $args['needs-transportation'],
            
            $args['participation'],
            $args['parent-initials'],
            $args['signature'],
            $args['signature-date'],
            $args['waiver-child-name'],
            
            $args['waiver-dob'],
            $args['waiver-parent-name'],
            $args['waiver-provider-name'],
            $args['waiver-provider-address'],
            $args['waiver-phone-and-fax'],
            
            $args['waiver-signature'],
            $args['waiver-date']
        );
        $stmt->execute();
        $stmt->close();
        mysqli_close($conn);
        return true;
    } else if ($res->num_rows > 0) {
        // Child already exists, do not insert
        echo '<div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Error: ' . $args['child-first-name'] . ' ' . $args['child-last-name'] . ' is already registered.</div>';
        mysqli_close($conn);
        return false;
    }
    
    mysqli_close($conn);
    return false;
}

function isBrainBuildersRegistrationComplete($childId){
    $conn = connect();
    $query = "SELECT * FROM dbBrainBuildersRegistrationForm where child_id = $childId";
    $res = mysqli_query($conn, $query);

    $complete = $res && mysqli_num_rows($res) > 0;
    mysqli_close($conn);
    return $complete;
}