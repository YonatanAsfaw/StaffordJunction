<?php
session_cache_expire(30);
ini_set('display_startup_errors', 1);
session_start();
ini_set('error_log', __DIR__ . '/php-error.log'); // Log to a local file
ini_set("display_errors",1);
error_reporting(E_ALL);
error_log("🔥 Loaded formSearchResult.php");

if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    die();
}

require_once("database/dbForms.php");
require_once("database/dbChildCareWaiverForm.php");
require_once("database/dbFamily.php");
require_once("domain/Family.php");

$families = find_all_families();
$excludedColumns = array(
    "id", "steam", "family_id", "securityQuestion", "securityAnswer", "password", "child_id", "form_id", "id", 
    "neighborhood", "shirt_size", "child_address", "child_city", "child_state", "child_zip", "child_medical_allergies", 
    "child_food_avoidances", "parent1_first_name", "parent1_last_name", "parent1_address", "parent1_city", 
    "parent1_state", "parent1_zip", "parent1_email", "parent1_cell_phone", "parent1_home_phone", 
    "parent1_work_phone", "parent2_first_name", "parent2_last_name", "parent2_address", "parent2_city", 
    "parent2_state", "parent2_zip", "parent2_email", "parent2_cell_phone", "parent2_home_phone", 
    "parent2_work_phone", "emergency_contact1_name", "emergency_contact1_relationship", "emergency_contact1_phone", 
    "emergency_contact2_name", "emergency_contact2_relationship", "emergency_contact2_phone", "primary_language", 
    "hispanic_latino_spanish", "race", "num_unemployed", "num_retired", "num_unemployed_students", 
    "num_employed_fulltime", "num_employed_parttime", "num_employed_students", "income", "other_programs", 
    "lunch", "insurance", "policy_num", "signature", "signature_date",
    // Newly added values
    "parent_email", "emgcy_contact_name_1", "emgcy_contact1_rship", "emgcy_contact1_phone", 
    "emgcy_contact_name_2", "emgcy_contact2_rship", "emgcy_contact2_phone", "medical_insurance_company", 
    "policy_number", "photo_waiver_signature", "photo_waiver_date", "field_id", "", "medical_notes", "notes", "address", "city", "state",
    "zip", "is_hispanic", "student_name", "spring_id", "email", "gender", "parent1_zip_code", "parent2_zip_code",
    "photo_release", "pants_size", "age", "shoe_size", "coat_size", "underwear_size", "sock_size", "wants",
    "interests", "phone", "parent_name", "dob", "birth_date", "birthdate"
);


$hasSearched = isset($_GET['searchByForm']) || isset($_GET['searchByFamily']);
$selectedFormName = $hasSearched ? ($_GET['formName'] ?? '') : '';
$familyId = $_GET['familyAccount'] ?? null;
$noResults = true;
$searchingByForm = false;
$submissions = [];
$columnNames = [];

$columnNames = !$noResults ? array_keys($submissions[0]) : [];

// Remove 'child_name' ONLY for 'Angels Gift'
if ($selectedFormName === "Angel Gifts Wish List") {
    $excludedColumns[] = "child_name";
}
if ($selectedFormName === "Field Trip Waiver Form") {
    $excludedColumns[] = "child_name";
}

if (isset($_GET['searchByForm'])) {
    $familyId = isset($_GET['searchByFamily']) ? $familyId : null;

    $searchingByForm = true;
    $childName = isset($_GET['childName']) ? trim($_GET['childName']) : '';

    if ($selectedFormName === "Child Care Waiver" && !empty($childName)) {
        $submissions = getChildCareWaiverByChildName($childName);
    } else {
        $submissions = getFormSubmissions($selectedFormName, isset($_GET['searchByFamily']) ? $familyId : null);
       

    }

    if($_GET['formName'] == "Program Review"){
        header('location: viewFeedback.php');
    }

    error_log("Fetching form: " . $selectedFormName . " | Family ID: " . $familyId);
    error_log("Database Query Result: " . json_encode($submissions));

    $noResults = empty($submissions);
    $columnNames = !$noResults ? array_keys($submissions[0]) : [];
} elseif (isset($_GET['searchByFamily'])) {
    $familyId = intval($_GET['familyAccount'] ?? 0);
    error_log("DEBUG: Using Family ID: " . ($familyId ?: "NULL"));
    $family = retrieve_family_by_id($familyId);
    $formNames = getFormsByFamily($familyId);
    $noResults = empty($formNames);
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>Stafford Junction | View Form Submissions</title>
</head>
<body>
    <?php require_once('header.php') ?>
    <form class="form-search-result-subheader" method="post">
        <a class="button cancel" href="formSearch.php">Back to Search</a>
        <?php if (!$noResults && $searchingByForm): ?>
            <button class="button" id="downloadButton">Download Results (.csv)</button>
        <?php endif; ?>
        <span style="margin-left: 10px;">Viewing results for: 
        <?php 
            echo htmlspecialchars((string) ($selectedFormName ?? ''), ENT_QUOTES, 'UTF-8');
        ?>
        </span>
    </form>

    <div class="form-search-results">
        <?php if (!$noResults): ?>
            <table class="general form-search-results-table">
                <thead>
                    <tr>
                        <?php
                            foreach ($columnNames as $columnName) {
                                if (!in_array($columnName, $excludedColumns)) {
                                    // Replace underscores with spaces and capitalize the first letter of each word
                                    $formattedColumnName = ucwords(str_replace('_', ' ', $columnName));
                                    echo '<th>' . htmlspecialchars($formattedColumnName, ENT_QUOTES, 'UTF-8') . '</th>';
                                }
                            }
                        ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <?php foreach ($submission as $columnName => $column): ?>
                                <?php if (!in_array($columnName, $excludedColumns)): ?>
                                    <td><?php echo htmlspecialchars((string) ($column ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td>
                            <?php
                                    // Grab the correct form ID
                                    $editId = $submission['form_id'] ?? $submission['id'];

                                    error_log("Selected Form Name: " . $selectedFormName);

                                  // Determine the edit ID and URL based on the form type.
                                    if ($selectedFormName === "Spring Break Camp Form") {
                                    // For Spring Break, use spring_id
                                    $editId = $submission['spring_id'] ?? $submission['form_id'] ?? '';
                                    $editUrl = "editSpringBreakCampForm.php?id=" . htmlspecialchars($editId, ENT_QUOTES, 'UTF-8');
                                    } elseif ($selectedFormName === "Field Trip Waiver Form") {
                                        //  Use `form_id` in URL to avoid ID conflict
                                        $editId = $submission['field_id'] ?? $submission['form_id'] ?? ''?? $submission['id'];
                                        $familyIdSanitized = htmlspecialchars($familyId ?? '', ENT_QUOTES, 'UTF-8');
                                        $editUrl = "editFieldTripWaiverForm.php?form_id=" . htmlspecialchars($editId, ENT_QUOTES, 'UTF-8') .
                                                   "&familyAccount=" . $familyIdSanitized;
                                    
                                    
                                    }
                                    elseif (stripos(trim($selectedFormName), "child care waiver") !== false) {
                                        $editId = $submission['form_id'] ?? $submission['id'];
                                        $editUrl = "editChildCareWaiverForm.php?form_id=" . htmlspecialchars($editId, ENT_QUOTES, 'UTF-8');
                                    }
                                    
                              else {
                                        // For other forms, fall back to the default form_id and URL pattern.
                                        $editId = $submission['form_id'] ?? $submission['id'];
                                        $familyIdSanitized = htmlspecialchars($familyId ?? '', ENT_QUOTES, 'UTF-8'); // Ensure $familyId isn't null
                                        $editUrl = "editForm.php?formName=" . urlencode($selectedFormName) .
                                                   "&id=" . htmlspecialchars($editId, ENT_QUOTES, 'UTF-8') .
                                                   "&familyAccount=" . $familyIdSanitized;
                                    }
                                ?>
                                <?php error_log("➡️  Edit URL: " . $editUrl); ?>

                                <a href="<?= $editUrl ?>" class="button">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">No results found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
