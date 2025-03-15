<?php
require_once("dbConnect.php"); // Make sure this includes the correct database connection

function updateFormSubmission($formName, $submissionId, $updatedData) {
    $tableName = getTableNameForForm($formName); // This function maps form names to table names
    if (!$tableName) {
        return false;
    }

    $setClause = "";
    $params = [];
    foreach ($updatedData as $key => $value) {
        $setClause .= "`$key` = ?, ";
        $params[] = $value;
    }
    $setClause = rtrim($setClause, ", ");
    $params[] = $submissionId;

    $sql = "UPDATE `$tableName` SET $setClause WHERE id = ?";
    $stmt = dbConnect()->prepare($sql);
    return $stmt->execute($params);
}

function getTableNameForForm($formName) {
    $formTableMap = [
        "Holiday Meal Bag" => "holiday_meal_bag",
        "School Supplies" => "school_supplies",
        "Spring Break" => "spring_break_camp",
        "Angel Gifts Wish List" => "angel_gift_wish_list",
        "Child Care Waiver" => "child_care_waiver",
        "Field Trip Waiver" => "field_trip_waiver",
        "Program Interest" => "program_interest",
    ];
    return $formTableMap[$formName] ?? null;
}
?>
