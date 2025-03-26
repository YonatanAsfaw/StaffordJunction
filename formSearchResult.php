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
$excludedColumns = array("", "family_id", "securityQuestion", "securityAnswer", "password", "child_id", "form_id");

$hasSearched = isset($_GET['searchByForm']) || isset($_GET['searchByFamily']);
$selectedFormName = $hasSearched ? ($_GET['formName'] ?? '') : '';
$familyId = $_GET['familyAccount'] ?? null;
$noResults = true;
$searchingByForm = false;
$submissions = [];
$columnNames = [];

if (isset($_GET['searchByForm'])) {
    $familyId = isset($_GET['searchByFamily']) ? $familyId : null;

    $searchingByForm = true;
    $childName = isset($_GET['childName']) ? trim($_GET['childName']) : '';

    if ($selectedFormName === "Child Care Waiver" && !empty($childName)) {
        $submissions = getChildCareWaiverByChildName($childName);
    } else {
        $submissions = getFormSubmissions($selectedFormName, isset($_GET['searchByFamily']) ? $familyId : null);
       

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
                                    echo '<th>' . htmlspecialchars((string) $columnName, ENT_QUOTES, 'UTF-8') . '</th>';
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
                                    }
                                    elseif (stripos(trim($selectedFormName), "child care waiver") !== false) {
                                        $editId = $submission['form_id'] ?? $submission['id'];
                                        $editUrl = "editChildCareWaiverForm.php?form_id=" . htmlspecialchars($editId, ENT_QUOTES, 'UTF-8');
                                    }
                                    
                                    // } elseif ($selectedFormName === "Summer Junction") {
                                    //     // For Summer Junction, use summer_id
                                    //     $editId = $submission['summer_id'] ?? $submission['form_id'] ?? '';
                                    //     $editUrl = "editSummerJunctionForm.php?id=" . htmlspecialchars($editId, ENT_QUOTES, 'UTF-8');
                                    else {
                                        // For other forms, fall back to the default form_id and URL pattern.
                                        $editId = $submission['form_id'] ?? $submission['id'];
                                        $familyIdSanitized = htmlspecialchars($familyId ?? '', ENT_QUOTES, 'UTF-8'); // Ensure $familyId isn't null
                                        $editUrl = "editForm.php?formName=" . urlencode($selectedFormName) .
                                                   "&id=" . htmlspecialchars($editId, ENT_QUOTES, 'UTF-8') .
                                                   "&familyAccount=" . $familyIdSanitized;
                                    }
                                ?>
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
